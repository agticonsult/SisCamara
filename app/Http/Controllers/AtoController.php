<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtoStoreRequest;
use App\Http\Requests\AtoUpdateRequest;
use App\Models\AnexoAto;
use App\Models\AssuntoAto;
use App\Models\Ato;
use App\Models\AtoRelacionado;
use App\Models\ClassificacaoAto;
use App\Models\ErrorLog;
use App\Models\Filesize;
use App\Models\FormaPublicacaoAto;
use App\Models\Grupo;
use App\Models\LinhaAto;
use App\Models\OrgaoAto;
use App\Models\TipoAto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class AtoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $atos = Ato::where('ativo', '=', Ato::ATIVO)->get();
            // dd($atos);

            return view('ato.index', compact('atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $classificacaos = ClassificacaoAto::where('ativo', '=', ClassificacaoAto::ATIVO)->get();
            $grupos = Grupo::where('ativo', '=', Grupo::ATIVO)->get();
            $assuntos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();
            $tipo_atos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();
            $orgaos = OrgaoAto::where('ativo', '=', OrgaoAto::ATIVO)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', FormaPublicacaoAto::ATIVO)->get();
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', Filesize::ATIVO)->first();

            return view('ato.create', compact('classificacaos', 'grupos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos', 'filesize'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(AtoStoreRequest $request)
    {
        try {
            $altera_dispositivo = 0;
            if (isset($request->altera_dispositivo)){
                if ($request->altera_dispositivo == 'on'){
                    $altera_dispositivo = 1;
                }
            }
            //nova forma de implementaçao para persistir os dados no BD
            $ato = Ato::create($request->validated() + [
                'altera_dispositivo' => $altera_dispositivo,
                'cadastradoPorUsuario' => Auth::user()->id,
                // 'ativo' => Ato::ATIVO //default diretamente da migration
            ]);

            $corpo_texto = $request->corpo_texto;
            $corpo_texto_alterado = preg_replace('/\r/', '', $corpo_texto);
            $array_corpo_texto = explode("\n", $corpo_texto_alterado);

            for ($i = 0; $i < Count($array_corpo_texto); $i++){
                if ($array_corpo_texto[$i] != ""){
                    LinhaAto::create([
                        'ordem' => $i + 1,
                        'texto' => $array_corpo_texto[$i],
                        'alterado' => 0,
                        'id_ato_principal' => $ato->id,
                        'id_tipo_linha' => 1,
                        'cadastradoPorUsuario' => Auth::user()->id,
                    ]);

                }
            }

            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $arquivos = $request->file('anexo');
            if ($arquivos != null){
                if (Count($arquivos) != 0){
                    $max_filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();
                    if ($max_filesize){
                        if ($max_filesize->mb != null){
                            if (is_int($max_filesize->mb)){
                                $mb = $max_filesize->mb;
                            }
                            else{
                                $mb = 2;
                            }
                        }
                        else{
                            $mb = 2;
                        }
                    }
                    else{
                        $mb = 2;
                    }

                    $count = 0;
                    $resultados = array();

                    foreach ($arquivos as $arquivo) {
                        $filezinho = array();
                        $valido = 0;
                        $nome_original = $arquivo->getClientOriginalName();
                        array_push($filezinho, $nome_original);

                        // if ($arquivo->isValid() && (filesize($arquivo) <= 2097152)) {
                        if ($arquivo->isValid()) {
                            if (filesize($arquivo) <= 1048576 * $mb){

                                $extensao = $arquivo->extension();

                                if (
                                    $extensao == 'txt' ||
                                    $extensao == 'pdf' ||
                                    $extensao == 'xls' ||
                                    $extensao == 'xlsx' ||
                                    $extensao == 'doc' ||
                                    $extensao == 'docx' ||
                                    $extensao == 'odt' ||
                                    $extensao == 'jpg' ||
                                    $extensao == 'jpeg' ||
                                    $extensao == 'png'
                                    // $extensao == 'mp3' ||
                                    // $extensao == 'mp4' ||
                                    // $extensao == 'mkv'
                                ) {
                                    $valido = 1;
                                }

                                if ($valido == 1) {
                                    $nome_hash = Uuid::uuid4();
                                    $nome_hash = $nome_hash . '-' . $count . '.' . $extensao;
                                    $upload = $arquivo->storeAs('public/Ato/Anexo/', $nome_hash);

                                    if ($upload) {
                                        $file = new AnexoAto();
                                        $file->nome_original = $nome_original;
                                        $file->nome_hash = $nome_hash;
                                        $file->diretorio = 'public/Ato/Anexo';
                                        $file->id_ato = $ato->id;
                                        $file->cadastradoPorUsuario = Auth::user()->id;
                                        $file->ativo = 1;
                                        $file->save();

                                        array_push($filezinho, 'arquivo adicionado com sucesso');
                                        $count++;
                                    }
                                    else {
                                        array_push($filezinho, 'falha ao salvar o arquivo');
                                    }
                                }
                                else {
                                    array_push($filezinho, 'extensão inválida');
                                }
                            }
                            else{
                                array_push($filezinho, 'arquivo maior que ' . $mb . 'MB');
                            }
                        }
                        else {
                            array_push($filezinho, 'arquivo inválido');
                        }

                        array_push($resultados, $filezinho);
                    }

                    $result = array();
                    for ($i=0; $i<Count($resultados); $i++) {
                        $selected = $resultados[$i];
                        $resultadoTexto = $selected[0] . ": " . $selected[1];
                        array_push($result, $resultadoTexto);
                    }

                    return redirect()->route('ato.index')->with('success', 'Cadastro realizado com sucesso')->with('info-anexo', $result);
                }
            }

            return redirect()->route('ato.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();

            return view('ato.show', compact('ato'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "show";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', Ato::ATIVO)->get();

            return view('ato.edit', compact('ato', 'atos_relacionados'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function editCorpoTexto($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();
            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', Ato::ATIVO)->get();

            return view('ato.edit', compact('ato', 'atos_relacionados'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "editCorpoTexto";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function alterarLinha(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $request->id,
                'id_ato_add' => $request->id_ato_add,
                'id_linha_ato' => $request->id_linha_ato,
                'corpo_texto' => $request->corpo_texto,
            ];
            $rules = [
                'id' => 'required|integer',
                'id_ato_add' => 'required|integer',
                'id_linha_ato' => 'required|integer',
                'corpo_texto' => 'required',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $ato_add = Ato::where('id', '=', $request->id_ato_add)->where('ativo', '=', Ato::ATIVO)->first();
            if (!$ato_add){
                return redirect()->back()->with('erro', 'Ato que contém a alteração inválido.');
            }

            $linha_antiga = LinhaAto::where('id', '=', $request->id_linha_ato)->where('id_ato_principal', '=', $id)->where('ativo', '=', LinhaAto::ATIVO)->first();
            if (!$linha_antiga){
                return redirect()->back()->with('erro', 'Não é possível alterar esta linha.');
            }

            $linha_antiga->alterado = 1;
            $linha_antiga->save();

            $linha_ato = new LinhaAto();
            $linha_ato->ordem = $linha_antiga->ordem;
            $linha_ato->sub_ordem = $linha_antiga->sub_ordem + 1;
            $linha_ato->texto = $request->corpo_texto;
            $linha_ato->alterado = 0;
            $linha_ato->id_ato_principal = $linha_antiga->id_ato_principal;
            $linha_ato->id_ato_add = $request->id_ato_add;
            $linha_ato->id_tipo_linha = 2;
            $linha_ato->cadastradoPorUsuario = Auth::user()->id;
            $linha_ato->ativo = 1;
            $linha_ato->save();

            $ato_relacionado = new AtoRelacionado();
            $ato_relacionado->id_ato_principal = $linha_antiga->id_ato_principal;
            $ato_relacionado->id_ato_relacionado = $request->id_ato_add;
            $ato_relacionado->cadastradoPorUsuario = Auth::user()->id;
            $ato_relacionado->ativo = 1;
            $ato_relacionado->save();

            return redirect()->route('ato.corpo_texto.edit', $linha_antiga->id_ato_principal)->with('success', 'Alteração realizada com sucesso.');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "alterarLinha";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function editDadosGerais($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }
            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', Ato::ATIVO)->get();

            $classificacaos = ClassificacaoAto::where('ativo', '=', ClassificacaoAto::ATIVO)->get();
            $grupos = Grupo::where('ativo', '=', Grupo::ATIVO)->get();
            $assuntos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();
            $tipo_atos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();
            $orgaos = OrgaoAto::where('ativo', '=', OrgaoAto::ATIVO)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', FormaPublicacaoAto::ATIVO)->get();

            return view('ato.edit', compact('ato', 'atos_relacionados', 'classificacaos', 'grupos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "editDadosGerais";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function updateDadosGerais(AtoUpdateRequest $request, $id)
    {
        try {

            $altera_dispositivo = 0;
            if (isset($request->altera_dispositivo)){
                if ($request->altera_dispositivo == 'on'){
                    $altera_dispositivo = 1;
                }
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $ato->update($request->validated() + [
                'altera_dispositivo' => $altera_dispositivo
            ]);

            return redirect()->route('ato.dados_gerais.edit', $ato->id)->with('success', 'Alteração realizada com sucesso');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "updateDadosGerais";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', Ato::ATIVO)->first();

            if (!$ato){
                return redirect()->back()->with('erro', 'Não é possível excluir este assunto.')->withInput();
            }

            // inativando o ato
            $ato->inativadoPorUsuario = Auth::user()->id;
            $ato->dataInativado = Carbon::now();
            $ato->motivoInativado = $motivo;
            $ato->ativo = 0;
            $ato->save();

            //  inativando ato relacionado
            $atoRelacionado = AtoRelacionado::where('id_ato_relacionado', '=', $id)->where('ativo', '=', 1)->first();

            if($atoRelacionado != null) {
                $atoRelacionado->inativadoPorUsuario = Auth::user()->id;
                $atoRelacionado->dataInativado = Carbon::now();
                $atoRelacionado->motivoInativado = $motivo;
                $atoRelacionado->ativo = 0;
                $atoRelacionado->save();

                //inativando linha do ato
                $linhaAto = LinhaAto::where('id_ato_add', '=', $id)->where('ativo', '=', 1)->first();
                $linhaAto->inativadoPorUsuario = Auth::user()->id;
                $linhaAto->dataInativado = Carbon::now();
                $linhaAto->motivoInativado = $motivo;
                $linhaAto->ativo = 0;
                $linhaAto->save();

            }

            $anexoAto = AnexoAto::where('id_ato', '=', $id)->where('ativo', '=', AnexoAto::ATIVO)->first();

            if($anexoAto != null) {
                // $anexoAto = AnexoAto::where('id_ato', '=', $id)->where('ativo', '=', AnexoAto::ATIVO)->first();
                $anexoAto->inativadoPorUsuario = Auth::user()->id;
                $anexoAto->dataInativado = Carbon::now();
                $anexoAto->motivoInativado = $motivo;
                $anexoAto->ativo = 0;
                $anexoAto->save();
            }

            return redirect()->route('ato.index')->with('success', 'Exclusão realizada com sucesso.');
            
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "destroy";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
