<?php

namespace App\Http\Controllers;

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

            $atos = Ato::where('ativo', '=', 1)->get();

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
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $classificacaos = ClassificacaoAto::where('ativo', '=', 1)->get();
            $grupos = Grupo::where('ativo', '=', 1)->get();
            $assuntos = AssuntoAto::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();
            $orgaos = OrgaoAto::where('ativo', '=', 1)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', 1)->get();
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();

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

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                // Texto
                'titulo' => $request->titulo,
                'subtitulo' => $request->subtitulo,
                'corpo_texto' => $request->corpo_texto,

                // Dados Gerais
                'id_classificacao' => $request->id_classificacao,
                'ano' => $request->ano,
                'numero' => $request->numero,
                'id_grupo' => $request->id_grupo,
                'id_tipo_ato' => $request->id_tipo_ato,
                'id_assunto' => $request->id_assunto,
                'id_orgao' => $request->id_orgao,
                'id_forma_publicacao' => $request->id_forma_publicacao,
                'data_publicacao' => $request->data_publicacao,

                // Anexos
                'arquivo[]' => $request->arquivo
            ];
            $rules = [
                // Texto
                'titulo' => 'required',
                'subtitulo' => 'nullable',
                'corpo_texto' => 'required',

                // Dados Gerais
                'id_classificacao' => 'required|integer',
                'ano' => 'required|integer',
                'numero' => 'required',
                'id_grupo' => 'required|integer',
                'id_tipo_ato' => 'required|integer',
                'id_assunto' => 'required|integer',
                'id_orgao' => 'required|integer',
                'id_forma_publicacao' => 'nullable|integer',
                'data_publicacao' => 'nullable|date',

                // Anexos
                'arquivo[]' => 'nullable',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $classificacao = ClassificacaoAto::where('id', '=', $request->id_classificacao)->where('ativo', '=', 1)->first();
            if (!$classificacao){
                return redirect()->back()->with('erro', 'Classificação inválida.');
            }

            $grupo = Grupo::where('id', '=', $request->id_grupo)->where('ativo', '=', 1)->first();
            if (!$grupo){
                return redirect()->back()->with('erro', 'Grupo inválido.');
            }

            $tipo_ato = Grupo::where('id', '=', $request->id_tipo_ato)->where('ativo', '=', 1)->first();
            if (!$tipo_ato){
                return redirect()->back()->with('erro', 'Tipo de ato inválido.');
            }

            $orgao = OrgaoAto::where('id', '=', $request->id_orgao)->where('ativo', '=', 1)->first();
            if (!$orgao){
                return redirect()->back()->with('erro', 'Órgão que editou o ato inválido.');
            }

            if ($request->id_forma_publicacao != null){
                $forma_publicacao = Grupo::where('id', '=', $request->id_forma_publicacao)->where('ativo', '=', 1)->first();
                if (!$forma_publicacao){
                    return redirect()->back()->with('erro', 'Forma de publicação do ato inválido.');
                }
            }

            $altera_dispositivo = 0;
            if (isset($request->altera_dispositivo)){
                if ($request->altera_dispositivo == 'on'){
                    $altera_dispositivo = 1;
                }
            }

            $ato = new Ato();
            $ato->titulo = $request->titulo;
            $ato->subtitulo = $request->subtitulo;
            $ato->ano = $request->ano;
            $ato->numero = $request->numero;
            $ato->id_classificacao = $request->id_classificacao;
            $ato->id_grupo = $request->id_grupo;
            $ato->id_tipo_ato = $request->id_tipo_ato;
            $ato->id_assunto = $request->id_assunto;
            $ato->id_orgao = $request->id_orgao;
            $ato->id_forma_publicacao = $request->id_forma_publicacao;
            $ato->data_publicacao = $request->data_publicacao;
            $ato->altera_dispositivo = $altera_dispositivo;
            $ato->cadastradoPorUsuario = Auth::user()->id;
            $ato->ativo = 1;
            $ato->save();

            $corpo_texto = $request->corpo_texto;
            $corpo_texto_alterado = preg_replace('/\r/', '', $corpo_texto);
            $array_corpo_texto = explode("\n", $corpo_texto_alterado);

            for ($i = 0; $i < Count($array_corpo_texto); $i++){
                if ($array_corpo_texto[$i] != ""){
                    $linha_ato = new LinhaAto();
                    $linha_ato->ordem = $i + 1;
                    $linha_ato->texto = $array_corpo_texto[$i];
                    $linha_ato->alterado = 0;
                    $linha_ato->id_ato_principal = $ato->id;
                    $linha_ato->id_tipo_linha = 1;
                    $linha_ato->cadastradoPorUsuario = Auth::user()->id;
                    $linha_ato->ativo = 1;
                    $linha_ato->save();
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
                                    $extensao == 'png' ||
                                    $extensao == 'mp3' ||
                                    $extensao == 'mp4' ||
                                    $extensao == 'mkv'
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

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            // dd($ato->todas_linhas_ativas());

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

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', 1)->get();
            // dd($ato->todas_linhas_ativas());

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

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', 1)->get();
            // dd($ato->todas_linhas_ativas());

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

            $ato_add = Ato::where('id', '=', $request->id_ato_add)->where('ativo', '=', 1)->first();
            if (!$ato_add){
                return redirect()->back()->with('erro', 'Ato que contém a alteração inválido.');
            }

            $linha_antiga = LinhaAto::where('id', '=', $request->id_linha_ato)->where('id_ato_principal', '=', $id)->where('ativo', '=', 1)->first();
            if (!$linha_antiga){
                return redirect()->back()->with('erro', 'Não é possível alterar esta linha.');
            }

            $linha_antiga->alterado = 1;
            $linha_antiga->save();

            // dd($request->all());
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

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }
            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', 1)->get();

            $classificacaos = ClassificacaoAto::where('ativo', '=', 1)->get();
            $grupos = Grupo::where('ativo', '=', 1)->get();
            $assuntos = AssuntoAto::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();
            $orgaos = OrgaoAto::where('ativo', '=', 1)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', 1)->get();
            // dd($ato->todas_linhas_ativas());

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

    public function updateDadosGerais(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                // Dados Gerais
                'id_classificacao' => $request->id_classificacao,
                'ano' => $request->ano,
                'numero' => $request->numero,
                'id_grupo' => $request->id_grupo,
                'id_tipo_ato' => $request->id_tipo_ato,
                'id_assunto' => $request->id_assunto,
                'id_orgao' => $request->id_orgao,
                'id_forma_publicacao' => $request->id_forma_publicacao,
                'data_publicacao' => $request->data_publicacao,
            ];
            $rules = [
                'id' => 'required|integer',
                // Dados Gerais
                'id_classificacao' => 'required|integer',
                'ano' => 'required|integer',
                'numero' => 'required',
                'id_grupo' => 'required|integer',
                'id_tipo_ato' => 'required|integer',
                'id_assunto' => 'required|integer',
                'id_orgao' => 'required|integer',
                'id_forma_publicacao' => 'nullable|integer',
                'data_publicacao' => 'nullable|date',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $classificacao = ClassificacaoAto::where('id', '=', $request->id_classificacao)->where('ativo', '=', 1)->first();
            if (!$classificacao){
                return redirect()->back()->with('erro', 'Classificação inválida.');
            }

            $grupo = Grupo::where('id', '=', $request->id_grupo)->where('ativo', '=', 1)->first();
            if (!$grupo){
                return redirect()->back()->with('erro', 'Grupo inválido.');
            }

            $tipo_ato = Grupo::where('id', '=', $request->id_tipo_ato)->where('ativo', '=', 1)->first();
            if (!$tipo_ato){
                return redirect()->back()->with('erro', 'Tipo de ato inválido.');
            }

            $orgao = OrgaoAto::where('id', '=', $request->id_orgao)->where('ativo', '=', 1)->first();
            if (!$orgao){
                return redirect()->back()->with('erro', 'Órgão que editou o ato inválido.');
            }

            if ($request->id_forma_publicacao != null){
                $forma_publicacao = Grupo::where('id', '=', $request->id_forma_publicacao)->where('ativo', '=', 1)->first();
                if (!$forma_publicacao){
                    return redirect()->back()->with('erro', 'Forma de publicação do ato inválido.');
                }
            }

            $altera_dispositivo = 0;
            if (isset($request->altera_dispositivo)){
                if ($request->altera_dispositivo == 'on'){
                    $altera_dispositivo = 1;
                }
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $ato->ano = $request->ano;
            $ato->numero = $request->numero;
            $ato->id_classificacao = $request->id_classificacao;
            $ato->id_grupo = $request->id_grupo;
            $ato->id_tipo_ato = $request->id_tipo_ato;
            $ato->id_assunto = $request->id_assunto;
            $ato->id_orgao = $request->id_orgao;
            $ato->id_forma_publicacao = $request->id_forma_publicacao;
            $ato->data_publicacao = $request->data_publicacao;
            $ato->altera_dispositivo = $altera_dispositivo;
            $ato->save();

            return redirect()->route('ato.dados_gerais.edit', $ato->id)->with('success', 'Alteração realizada com sucesso');
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

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();

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

            $anexoAto = AnexoAto::where('id_ato', '=', $id)->where('ativo', '=', 1)->first();

            if($anexoAto != null) {
                $anexoAto = AnexoAto::where('id_ato', '=', $id)->where('ativo', '=', 1)->first();
                $anexoAto->inativadoPorUsuario = Auth::user()->id;
                $anexoAto->dataInativado = Carbon::now();
                $anexoAto->motivoInativado = $motivo;
                $anexoAto->ativo = 0;
                $anexoAto->save();
            }

            return redirect()->route('ato.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
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
