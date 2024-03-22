<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartamentoDocumentoRequest;
use App\Http\Requests\StatusDepartamentoDocRequest;
use App\Models\AuxiliarDocumentoDepartamento;
use App\Models\Departamento;
use App\Models\DepartamentoDocumento;
use App\Models\DepartamentoTramitacao;
use App\Models\HistoricoMovimentacaoDoc;
use App\Models\StatusDepartamentoDocumento;
use App\Models\TipoDocumento;
use App\Models\TipoWorkflow;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepartamentoDocumentoController extends Controller
{
    public function index()
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentoDocumentos = DepartamentoDocumento::retornaDocumentosDepAtivos();

            return view('departamento-documento.index', compact('departamentoDocumentos'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $statusDepDocs = StatusDepartamentoDocumento::retornaStatusAtivos();
            $tipo_workflows = TipoWorkflow::where('ativo', '=', TipoWorkflow::ATIVO)->get();
            $departamentos = DepartamentoTramitacao::where('ativo', '=', DepartamentoTramitacao::ATIVO)->get();

            return view('departamento-documento.create', compact('tipoDocumentos', 'statusDepDocs', 'tipo_workflows', 'departamentos'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(DepartamentoDocumentoRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $timestamp = Carbon::now()->timestamp;
            $protocolo = $timestamp . '/' . rand(100000, 999999);

            $depDoc = DepartamentoDocumento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id,
                'protocolo' => $protocolo
            ]);

            //registrando histórico de movimentação do documento
            HistoricoMovimentacaoDoc::create([
                'id_status' => DepartamentoDocumento::CRIACAO_DOC,
                'id_documento' => $depDoc->id,
                'id_usuario' => Auth::user()->id
            ]);

            $departamentos = DepartamentoTramitacao::where('id_tipo_documento', '=', $request->id_tipo_documento)->where('ativo', '=', DepartamentoTramitacao::ATIVO)->get();
            if ($depDoc->id_tipo_workflow == 1) { //automática
                foreach ($departamentos as $key => $departamento) {
                    if ($key == 0) {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $depDoc->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => $key + 1,
                            'atual' => true
                        ]);
                    }
                    else {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $depDoc->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => $key + 1,
                            'atual' => false
                        ]);
                    }
                }
            }

            if ($depDoc->id_tipo_workflow == 2) { //manual
                foreach ($departamentos as $departamento) {
                    if ($departamento->id_departamento == $request->id_departamento) {
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $depDoc->id,
                            'id_departamento' => $departamento->id_departamento,
                            'ordem' => 1,
                            'atual' => true
                        ]);
                    }
                    else{
                        AuxiliarDocumentoDepartamento::create([
                            'id_documento' => $depDoc->id,
                            'id_departamento' => $departamento->id_departamento,
                            // 'ordem' => 1,
                            'atual' => false
                        ]);
                    }
                }
            }

            return redirect()->route('departamento_documento.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show(DepartamentoDocumento $departamentoDocumento)
    {
        //
    }

    public function edit($id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentoDocumentoEdit = DepartamentoDocumento::retornaDocumentoDepAtivo($id);

            if (!$departamentoDocumentoEdit) {
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            $historicoMovimentacao = HistoricoMovimentacaoDoc::retornaUltimoHistoricoMovStatusAtivo($departamentoDocumentoEdit->id);
            $todoHistoricoMovDocumento = HistoricoMovimentacaoDoc::retornaHistoricoMovAtivo($departamentoDocumentoEdit->id);
            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $statusDepDocs = StatusDepartamentoDocumento::retornaStatusAtivos();

            // lista departamentos da tramitação
            $departamentos = AuxiliarDocumentoDepartamento::where('id_documento', $departamentoDocumentoEdit->id)
                ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                ->orderByRaw('ISNULL(ordem), ordem')
                ->get();

            $proximoDep = null; // próximo departamento
            $depAnterior = $departamentoDocumentoEdit->dep_anterior(); // departamento anterior
            $departamentoTramitacao = []; // lista de departamentos para enviar ao Aprovar, no caso de tramitação manual
            $aptoFinalizar = true; // documento está apto a finalizar
            $aptoAprovar = true; // documento está apto a aprovar

            if ($departamentoDocumentoEdit->id_tipo_workflow == 1) { // tramitação automática

                $proximoDep = $departamentoDocumentoEdit->proximo_dep();

                // se houver proximo departamento, não é possível Finalizar
                if ($proximoDep) {
                    $aptoFinalizar = false;
                }
            }
            if ($departamentoDocumentoEdit->id_tipo_workflow == 2) { // tramitação manual

                // seleciona departamentos para o select de Aprovação, somente departamentos sem ordem definida e que não seja o atual
                $departamentoTramitacao = AuxiliarDocumentoDepartamento::where('id_documento', $departamentoDocumentoEdit->id)
                    ->whereNull('ordem')
                    ->where('atual', 0)
                    ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->get();

                // se não houver próximo departamento, não é possível Aprovar, somente Finalizar e Reprovar
                if (count($departamentoTramitacao) == 0) {
                    $aptoAprovar = false;
                }
            }

            return view('departamento-documento.edit', compact(
                'departamentoDocumentoEdit', 'historicoMovimentacao', 'todoHistoricoMovDocumento', 'tipoDocumentos',
                'statusDepDocs', 'departamentos', 'proximoDep', 'depAnterior', 'departamentoTramitacao', 'aptoFinalizar', 'aptoAprovar'
            ));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function aprovar(Request $request, $id, $id_tipo_workflow)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_departamento' => $request->id_departamento,
                'parecer' => $request->parecer
            ];
            $rules = [
                'id_departamento' => Rule::requiredIf($id_tipo_workflow == 2),
                'parecer' => 'nullable|max:255'
            ];
            $messages = [
                'id_departamento.required' => 'Selecione o departamento.',

                'parecer.max' => 'O parecer deve ter no máximo 255 caracteres.'
            ];

            $validar = Validator::make($input, $rules, $messages);
            $validar->validate();

            $documento = DepartamentoDocumento::retornaDocumentoDepAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            if ($id_tipo_workflow == 1) { // tramitação automática

                $departamento_atual = $documento->dep_atual();

                if (!$departamento_atual) {
                    ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DepartamentoDocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $proximo_departamento = $documento->proximo_dep();

                if (!$proximo_departamento) {
                    ErrorLogService::salvar('Erro ao encontrar o próximo departamento do documento', 'DepartamentoDocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $departamento_atual->update([
                    'atual' => false
                ]);

                $proximo_departamento->update([
                    'atual' => true
                ]);

                HistoricoMovimentacaoDoc::create([
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
                    ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DepartamentoDocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $proximo_departamento = AuxiliarDocumentoDepartamento::where('id_documento', $documento->id)
                    ->where('id_departamento', $request->id_departamento)
                    ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                    ->first();

                if (!$proximo_departamento) {
                    ErrorLogService::salvar('Erro ao encontrar o próximo departamento do documento', 'DepartamentoDocumentoController', 'aprovar');
                    return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
                }

                $departamento_atual->update([
                    'atual' => false
                ]);

                $proximo_departamento->update([
                    'ordem' => $departamento_atual->ordem + 1,
                    'atual' => true
                ]);

                HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 1,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);
            }

            return back()->with('success',
                'Aprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $proximo_departamento->departamento->descricao . '.');
        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'aprovar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function reprovar(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'parecer' => $request->parecer
            ];
            $rules = [
                'parecer' => 'nullable|max:255'
            ];
            $messages = [
                'parecer.max' => 'O parecer deve ter no máximo 255 caracteres.'
            ];

            $validar = Validator::make($input, $rules, $messages);
            $validar->validate();

            $documento = DepartamentoDocumento::retornaDocumentoDepAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            $departamento_atual = $documento->dep_atual();

            if (!$departamento_atual) {
                ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DepartamentoDocumentoController', 'reprovar');
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

                HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                return back()->with('success', 'Reprovação realizada com sucesso, o documento foi encaminhado ao autor.');

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

                HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                return back()->with('success',
                    'Reprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $departamento_anterior->departamento->descricao . '.');
            }
        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'reprovar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function finalizar(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'parecer' => $request->parecer
            ];
            $rules = [
                'parecer' => 'nullable|max:255'
            ];
            $messages = [
                'parecer.max' => 'O parecer deve ter no máximo 255 caracteres.'
            ];

            $validar = Validator::make($input, $rules, $messages);
            $validar->validate();

            $documento = DepartamentoDocumento::retornaDocumentoDepAtivo($id);

            if (!$documento) {
                return back()->with('erro', 'Documento não encontrado.');
            }

            $departamento_atual = $documento->dep_atual();

            if (!$departamento_atual) {
                ErrorLogService::salvar('Erro ao encontrar o departamento atual do documento', 'DepartamentoDocumentoController', 'finalizar');
                return redirect()->back()->with('erro', 'Houve um erro ao encontrar um departamento, atualize a página e tente novamente.');
            }

            $departamento_anterior = $documento->dep_anterior();

            if (!$departamento_anterior) { // se não houver departamento anterior reprova o documento e devolve para o autor

                $departamento_atual->update([
                    'ordem' => null,
                    'atual' => false
                ]);

                $documento->update([
                    'reprovado_em_tramitacao' => true
                ]);

                HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                return back()->with('success', 'Reprovação realizada com sucesso, o documento foi encaminhado ao autor.');

            }else { // se houver departamento anterior tramita normalmente

                $departamento_atual->update([
                    'ordem' => null,
                    'atual' => false
                ]);

                $departamento_anterior->update([
                    'atual' => true
                ]);

                HistoricoMovimentacaoDoc::create([
                    'parecer' => $request->parecer,
                    'id_documento' => $documento->id,
                    'id_usuario' => Auth::user()->id,
                    'id_status' => 2,
                    'id_departamento' => $departamento_atual->id_departamento
                ]);

                return back()->with('success',
                    'Reprovação realizada com sucesso, o documento foi encaminhado ao departamento ' . $departamento_anterior->departamento->descricao . '.');
            }
        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'finalizar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(DepartamentoDocumento $departamentoDocumento)
    {
        //
    }

    public function getDepartamentos(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Cadastro') != 1){
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
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'getDepartamentos');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }

    }

}
