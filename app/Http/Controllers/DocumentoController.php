<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentoRequest;
use App\Http\Requests\StatusDocRequest;
use App\Models\AnexoHistoricoMovimentacao;
use App\Models\AuxiliarDocumentoDepartamento;
use App\Models\Departamento;
use App\Models\Documento;
use App\Models\DepartamentoTramitacao;
use App\Models\Filesize;
use App\Models\HistoricoMovimentacaoDoc;
use App\Models\StatusDocumento;
use App\Models\TipoDocumento;
use App\Models\TipoWorkflow;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

use function RingCentral\Psr7\mimetype_from_filename;

class DocumentoController extends Controller
{
    public function index()
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documentos = Documento::retornaDocumentosDepAtivos();

            return view('documento.index', compact('documentos'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $tipo_workflows = TipoWorkflow::where('ativo', '=', TipoWorkflow::ATIVO)->get();

            return view('documento.create', compact('tipoDocumentos', 'tipo_workflows'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(DocumentoRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $timestamp = Carbon::now()->timestamp;
            $protocolo = $timestamp . '/' . rand(100000, 999999);

            $documento = Documento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id,
                'protocolo' => $protocolo
            ]);

            //registrando histórico de movimentação do documento
            HistoricoMovimentacaoDoc::create([
                'id_status' => Documento::CRIACAO_DOC,
                'id_documento' => $documento->id,
                'id_usuario' => Auth::user()->id
            ]);

            $departamentos = DepartamentoTramitacao::where('id_tipo_documento', '=', $request->id_tipo_documento)->where('ativo', '=', DepartamentoTramitacao::ATIVO)->get();
            if ($documento->id_tipo_workflow == 1) { //automática
                foreach ($departamentos as $key => $departamento) {
                    if ($key == 0) {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $documento->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => $key + 1,
                            'atual' => true
                        ]);
                    }
                    else {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $documento->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => $key + 1,
                            'atual' => false
                        ]);
                    }
                }
            }

            if ($documento->id_tipo_workflow == 2) { //manual
                foreach ($departamentos as $departamento) {
                    if ($departamento->id_departamento == $request->id_departamento) {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $documento->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => 1,
                            'atual' => true
                        ]);
                    }
                    else{
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $documento->id,
                            'id_departamento' => $departamento->id_departamento,
                            // 'ordem' => 1,
                            'atual' => false
                        ]);
                    }
                }
            }

            return redirect()->route('documento.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show($id)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documentoEdit = Documento::retornaDocumentoAtivo($id);

            if (!$documentoEdit) {
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            $historicoMovimentacao = HistoricoMovimentacaoDoc::retornaUltimoHistoricoMovStatusAtivo($documentoEdit->id);
            $todoHistoricoMovDocumento = HistoricoMovimentacaoDoc::retornaHistoricoMovAtivo($documentoEdit->id);
            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $statusDocs = StatusDocumento::retornaStatusAtivos();

            // lista departamentos da tramitação
            $departamentos = AuxiliarDocumentoDepartamento::where('id_documento', $documentoEdit->id)
                ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                ->orderByRaw('ISNULL(ordem), ordem')
                ->get();

            $proximoDep = null; // próximo departamento
            $depAnterior = $documentoEdit->dep_anterior(); // departamento anterior
            $departamentoTramitacao = []; // lista de departamentos para enviar ao Aprovar, no caso de tramitação manual
            $aptoFinalizar = true; // documento está apto a finalizar
            $aptoAprovar = true; // documento está apto a aprovar

            if ($documentoEdit->id_tipo_workflow == 1) { // tramitação automática

                $proximoDep = $documentoEdit->proximo_dep();

                // se houver proximo departamento, não é possível Finalizar
                if ($proximoDep) {
                    $aptoFinalizar = false;
                }
            }
            if ($documentoEdit->id_tipo_workflow == 2) { // tramitação manual

                // seleciona departamentos para o select de Aprovação, somente departamentos sem ordem definida e que não seja o atual
                $departamentoTramitacao = AuxiliarDocumentoDepartamento::where('id_documento', $documentoEdit->id)
                    ->whereNull('ordem')
                    ->where('atual', 0)
                    ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->get();

                // se não houver próximo departamento, não é possível Aprovar, somente Finalizar e Reprovar
                if (count($departamentoTramitacao) == 0) {
                    $aptoAprovar = false;
                }
            }

            return view('documento.acompanhar', compact(
                'documentoEdit', 'historicoMovimentacao', 'todoHistoricoMovDocumento', 'tipoDocumentos',
                'statusDocs', 'departamentos', 'proximoDep', 'depAnterior', 'departamentoTramitacao', 'aptoFinalizar', 'aptoAprovar'
            ));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {

        try{
            if(Auth::user()->temPermissao('Documento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documento = Documento::retornaDocumentoAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if(!$documento->reprovado_em_tramitacao){
                return redirect()->back()->with('erro', 'O documento só pode ser editado se for reprovado em tramitação.');
            }

            $departamentos = AuxiliarDocumentoDepartamento::where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                ->where('id_documento', '=', $id)
                ->get();

            return view('documento.edit', compact('documento', 'departamentos'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(DocumentoRequest $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documento = Documento::retornaDocumentoAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if(!$documento->reprovado_em_tramitacao){
                return redirect()->back()->with('erro', 'O documento só pode ser editado se for reprovado em tramitação.');
            }

            $validated = $request->validated();

            $documento->update([
                'titulo' => $validated['titulo'],
                'conteudo' => $validated['conteudo'],
                'reprovado_em_tramitacao' => false
            ]);

            // registrando histórico de movimentação do documento
            HistoricoMovimentacaoDoc::create([
                'id_status' => Documento::ATUALIZACAO_DOC,
                'id_documento' => $documento->id,
                'id_usuario' => Auth::user()->id
            ]);

            if ($documento->id_tipo_workflow == 1) { // tramitação automatica

                $depAuxiliar = AuxiliarDocumentoDepartamento::where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->where('id_documento', $id)
                    ->where('ordem', 1)
                    ->first();

                $depAuxiliar->update([
                    'atual' => true
                ]);
            }

            if ($documento->id_tipo_workflow == 2) { // tramitação manual

                $depAuxiliar = AuxiliarDocumentoDepartamento::where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->where('id_documento', $id)
                    ->where('id_departamento', $validated['id_departamento'])
                    ->first();

                $depAuxiliar->update([
                    'atual' => true,
                    'ordem' => 1
                ]);
            }

            return redirect()->route('documento.index')->with('success', 'Alteração realizada com sucesso.');
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'aprovar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function aprovar(Request $request, $id, $id_tipo_workflow)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_departamento' => $request->id_departamento
            ];
            $rules = [
                'id_departamento' => Rule::requiredIf($id_tipo_workflow == 2)
            ];
            $messages = [
                'id_departamento.required' => 'Selecione o departamento.'
            ];

            $validar = Validator::make($input, $rules, $messages);
            $validar->validate();

            $documento = Documento::retornaDocumentoAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if($documento->podeTramitar(auth()->user()->id) == false){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($id_tipo_workflow == 1) { // tramitação automática

                $departamento_atual = $documento->dep_atual();

                if (!$departamento_atual) {
                    ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $proximo_departamento = $documento->proximo_dep();

                if (!$proximo_departamento) {
                    ErrorLogService::salvar('Erro ao encontrar o próximo departamento do documento', 'DocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $departamento_atual->update([
                    'atual' => false
                ]);

                $proximo_departamento->update([
                    'atual' => true
                ]);

                $movimentacao = HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 1,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);
            }

            if ($id_tipo_workflow == 2) { // tramitação manual

                $departamento_atual = $documento->dep_atual();

                if (!$departamento_atual) {
                    ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $proximo_departamento = AuxiliarDocumentoDepartamento::where('id_documento', $documento->id)
                    ->where('id_departamento', $request->id_departamento)
                    ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->first();

                if (!$proximo_departamento) {
                    ErrorLogService::salvar('Erro ao encontrar o próximo departamento do documento', 'DocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $departamento_atual->update([
                    'atual' => false
                ]);

                $proximo_departamento->update([
                    'ordem' => $departamento_atual->ordem + 1,
                    'atual' => true
                ]);

                $movimentacao = HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 1,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);
            }

            $anexo = $request->file('anexo');
            $respostaAnexo = array();

            // se houver anexo faz o tratamento, se não houver finaliza a função
            if ($anexo) {

                $max_filesize = Filesize::where('id_tipo_filesize', '=', 3)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }else{
                            $mb = 2;
                        }
                    }else{
                        $mb = 2;
                    }
                }else{
                    $mb = 2;
                }

                if ($anexo->isValid()) {
                    if (filesize($anexo) <= 1048576 * $mb){

                        $nome_original = $anexo->getClientOriginalName();
                        $extensao = $anexo->getClientOriginalExtension();

                        if (
                            $extensao == 'txt' ||
                            $extensao == 'pdf' ||
                            $extensao == 'xls' ||
                            $extensao == 'xlsx' ||
                            $extensao == 'doc' ||
                            $extensao == 'docx' ||
                            $extensao == 'odt'
                        ) {

                            // $nome_hash = Carbon::now()->timestamp;
                            $nome_hash = Uuid::uuid4();
                            $nome_hash = $nome_hash . '.' . $extensao;
                            $upload = $anexo->storeAs('public/anexos-historico-movimentacao-doc/', $nome_hash);

                            if ($upload) {
                                $file = new AnexoHistoricoMovimentacao();
                                $file->nome_original = $nome_original;
                                $file->nome_hash = $nome_hash;
                                $file->diretorio = 'public/anexos-historico-movimentacao-doc';
                                $file->id_movimentacao = $movimentacao->id;
                                $file->ativo = 1;
                                $file->save();

                                $respostaAnexo['sucesso'] = true;
                            }else {
                                $respostaAnexo['sucesso'] = false;
                                $respostaAnexo['mensagem'] = 'falha ao salvar o arquivo';
                            }
                        }else {
                            $respostaAnexo['sucesso'] = false;
                            $respostaAnexo['mensagem'] = 'extensão inválida';
                        }
                    }else{
                        $respostaAnexo['sucesso'] = false;
                        $respostaAnexo['mensagem'] = 'arquivo maior que ' . $mb . 'MB';
                    }
                }else {
                    $respostaAnexo['sucesso'] = false;
                    $respostaAnexo['mensagem'] = 'arquivo inválido';
                }
            }else {
                $respostaAnexo['sucesso'] = true;
            }

            // se deu erro no anexo, mostra o erro
            if ($respostaAnexo['sucesso']) {
                return back()->with('success',
                    'Aprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $proximo_departamento->departamento->descricao . '.');
            }else {
                return back()->with('warning',
                    'Aprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $proximo_departamento->departamento->descricao . '. Mas houve um erro no anexo: ' . $respostaAnexo['mensagem']);
            }
        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'aprovar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function reprovar(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documento = Documento::retornaDocumentoAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if($documento->podeTramitar(auth()->user()->id) == false){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento_atual = $documento->dep_atual();

            if (!$departamento_atual) {
                ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DocumentoController', 'reprovar');
                return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
            }

            $departamento_anterior = $documento->dep_anterior();

            if (!$departamento_anterior) { // se não houver departamento anterior reprova o documento e devolve para o autor

                if ($documento->id_tipo_workflow == 1) {
                    $departamento_atual->update([
                        'atual' => false
                    ]);
                }

                if ($documento->id_tipo_workflow == 2) {
                    $departamento_atual->update([
                        'ordem' => null,
                        'atual' => false
                    ]);
                }

                $documento->update([
                    'reprovado_em_tramitacao' => true
                ]);

                $movimentacao = HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                $departamento_destino = null;

            }else { // se houver departamento anterior tramita normalmente

                if ($documento->id_tipo_workflow == 1) {
                    $departamento_atual->update([
                        'atual' => false
                    ]);
                }

                if ($documento->id_tipo_workflow == 2) {
                    $departamento_atual->update([
                        'ordem' => null,
                        'atual' => false
                    ]);
                }

                $departamento_anterior->update([
                    'atual' => true
                ]);

                $movimentacao = HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                $departamento_destino = $departamento_anterior->departamento->descricao;
            }

            $anexo = $request->file('anexo');
            $respostaAnexo = array();

            // se houver anexo faz o tratamento, se não houver finaliza a função
            if ($anexo) {

                $max_filesize = Filesize::where('id_tipo_filesize', '=', 3)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }else{
                            $mb = 2;
                        }
                    }else{
                        $mb = 2;
                    }
                }else{
                    $mb = 2;
                }

                if ($anexo->isValid()) {
                    if (filesize($anexo) <= 1048576 * $mb){

                        $nome_original = $anexo->getClientOriginalName();
                        $extensao = $anexo->getClientOriginalExtension();

                        if (
                            $extensao == 'txt' ||
                            $extensao == 'pdf' ||
                            $extensao == 'xls' ||
                            $extensao == 'xlsx' ||
                            $extensao == 'doc' ||
                            $extensao == 'docx' ||
                            $extensao == 'odt'
                        ) {

                            // $nome_hash = Carbon::now()->timestamp;
                            $nome_hash = Uuid::uuid4();
                            $nome_hash = $nome_hash . '.' . $extensao;
                            $upload = $anexo->storeAs('public/anexos-historico-movimentacao-doc/', $nome_hash);

                            if ($upload) {
                                $file = new AnexoHistoricoMovimentacao();
                                $file->nome_original = $nome_original;
                                $file->nome_hash = $nome_hash;
                                $file->diretorio = 'public/anexos-historico-movimentacao-doc';
                                $file->id_movimentacao = $movimentacao->id;
                                $file->ativo = 1;
                                $file->save();

                                $respostaAnexo['sucesso'] = true;
                            }else {
                                $respostaAnexo['sucesso'] = false;
                                $respostaAnexo['mensagem'] = 'falha ao salvar o arquivo';
                            }
                        }else {
                            $respostaAnexo['sucesso'] = false;
                            $respostaAnexo['mensagem'] = 'extensão inválida';
                        }
                    }else{
                        $respostaAnexo['sucesso'] = false;
                        $respostaAnexo['mensagem'] = 'arquivo maior que ' . $mb . 'MB';
                    }
                }else {
                    $respostaAnexo['sucesso'] = false;
                    $respostaAnexo['mensagem'] = 'arquivo inválido';
                }
            }else {
                $respostaAnexo['sucesso'] = true;
            }

            // se deu erro no anexo, mostra o erro
            if ($respostaAnexo['sucesso']) {

                if ($departamento_destino == null) {
                    return back()->with('success', 'Reprovação realizada com sucesso, o documento foi encaminhado ao autor.');
                }else {
                    return back()->with('success',
                        'Reprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $departamento_destino . '.');
                }

            }else {
                if ($departamento_destino == null) {
                    return back()->with('warning', 'Reprovação realizada com sucesso, o documento foi encaminhado ao autor. Mas houve um erro no anexo: ' . $respostaAnexo['mensagem']);
                }else {
                    return back()->with('warning',
                        'Reprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $departamento_destino . '. Mas houve um erro no anexo: ' . $respostaAnexo['mensagem']);
                }
            }
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'reprovar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function finalizar(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documento = Documento::retornaDocumentoAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if($documento->podeTramitar(auth()->user()->id) == false){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento_atual = $documento->dep_atual();

            if (!$departamento_atual) {
                ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DocumentoController', 'finalizar');
                return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
            }

            $departamento_atual->update([
                'atual' => false
            ]);

            $documento->update([
                'finalizado' => true
            ]);

            $movimentacao = HistoricoMovimentacaoDoc::create([
                'parecer' => $request->parecer,
                'id_documento' => $documento->id,
                'id_usuario' => Auth::user()->id,
                'id_status' => 4,
                'id_departamento' => $departamento_atual->id_departamento
            ]);

            $anexo = $request->file('anexo');
            $respostaAnexo = array();

            // se houver anexo faz o tratamento, se não houver finaliza a função
            if ($anexo) {

                $max_filesize = Filesize::where('id_tipo_filesize', '=', 3)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }else{
                            $mb = 2;
                        }
                    }else{
                        $mb = 2;
                    }
                }else{
                    $mb = 2;
                }

                if ($anexo->isValid()) {
                    if (filesize($anexo) <= 1048576 * $mb){

                        $nome_original = $anexo->getClientOriginalName();
                        $extensao = $anexo->getClientOriginalExtension();

                        if (
                            $extensao == 'txt' ||
                            $extensao == 'pdf' ||
                            $extensao == 'xls' ||
                            $extensao == 'xlsx' ||
                            $extensao == 'doc' ||
                            $extensao == 'docx' ||
                            $extensao == 'odt'
                        ) {

                            // $nome_hash = Carbon::now()->timestamp;
                            $nome_hash = Uuid::uuid4();
                            $nome_hash = $nome_hash . '.' . $extensao;
                            $upload = $anexo->storeAs('public/anexos-historico-movimentacao-doc/', $nome_hash);

                            if ($upload) {
                                $file = new AnexoHistoricoMovimentacao();
                                $file->nome_original = $nome_original;
                                $file->nome_hash = $nome_hash;
                                $file->diretorio = 'public/anexos-historico-movimentacao-doc';
                                $file->id_movimentacao = $movimentacao->id;
                                $file->ativo = 1;
                                $file->save();

                                $respostaAnexo['sucesso'] = true;
                            }else {
                                $respostaAnexo['sucesso'] = false;
                                $respostaAnexo['mensagem'] = 'falha ao salvar o arquivo';
                            }
                        }else {
                            $respostaAnexo['sucesso'] = false;
                            $respostaAnexo['mensagem'] = 'extensão inválida';
                        }
                    }else{
                        $respostaAnexo['sucesso'] = false;
                        $respostaAnexo['mensagem'] = 'arquivo maior que ' . $mb . 'MB';
                    }
                }else {
                    $respostaAnexo['sucesso'] = false;
                    $respostaAnexo['mensagem'] = 'arquivo inválido';
                }
            }else {
                $respostaAnexo['sucesso'] = true;
            }

            // se deu erro no anexo, mostra o erro
            if ($respostaAnexo['sucesso']) {
                return back()->with('success', 'O documento foi finalizado com sucesso.');
            }else {
                return back()->with('warning', 'O documento foi finalizado com sucesso. Mas houve um erro no anexo: ' . $respostaAnexo['mensagem']);
            }
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'finalizar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Documento $documento)
    {
        //
    }

    public function getDepartamentos(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('Documento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->ajax()) {
                $departamentos = DepartamentoTramitacao::where('id_tipo_documento', '=', $id)->get();
                $array = [];

                foreach ($departamentos as $dep) {
                    array_push($array, [
                        'id' => $dep->id_departamento,
                        'descricao' => $dep->departamento->descricao
                    ]);
                }

                return response()->json($array);
            }

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'getDepartamentos');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }

    }

    public function obterAnexo($id_anexo)
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $file = AnexoHistoricoMovimentacao::where('ativo', 1)->find($id_anexo);

            if (!$file) {
                return back()->with('erro', 'Arquivo não encontrado.');
            }

            $path = $file->diretorio.'/'.$file->nome_hash;
            $existe = Storage::disk()->exists($path);

            if ($existe){
                return Storage::download($path, $file->nome_original);
            }else {
                return back()->with('erro', 'Arquivo não encontrado, no diretório.');
            }
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'obterAnexo');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
