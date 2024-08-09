<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtoStoreRequest;
use App\Http\Requests\AtoUpdateRequest;
use App\Models\AnexoAto;
use App\Models\AssuntoAto;
use App\Models\Ato;
use App\Models\AtoRelacionado;
use App\Models\ClassificacaoAto;
use App\Models\Filesize;
use App\Models\FormaPublicacaoAto;
use App\Models\LinhaAto;
use App\Models\OrgaoAto;
use App\Models\TipoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class AtoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $atos = Ato::retornaAtosAtivos();

            return view('ato.index', compact('atos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'index');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $classificacaos = ClassificacaoAto::where('ativo', '=', ClassificacaoAto::ATIVO)->get();
            // $grupos = Grupo::where('ativo', '=', Grupo::ATIVO)->get();
            $assuntos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();
            $tipo_atos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();
            $orgaos = OrgaoAto::where('ativo', '=', OrgaoAto::ATIVO)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', FormaPublicacaoAto::ATIVO)->get();
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', Filesize::ATIVO)->first();

            return view('ato.create', compact('classificacaos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos', 'filesize'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'create');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
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
                        'alterado' => LinhaAto::LINHA_NAO_ALTERADO,
                        'id_ato_principal' => $ato->id,
                        'id_tipo_linha' => LinhaAto::TEXTO_ORIGINAL,
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

                    Alert::toast('Cadastro realizado com sucesso!', 'success');
                    return redirect()->route('ato.index')->with('info-anexo', $result);
                }
            }
            Alert::toast('Cadastro realizado com sucesso!', 'success');
            return redirect()->route('ato.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'store');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $ato = Ato::retornaAtoAtivo($id);

            return view('ato.show', compact('ato'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'show');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $ato = Ato::retornaAtoAtivo($id);
            if (!$ato){
                Alert::toast('Ato inválido.','error');
                return redirect()->back();
            }

            $atos_relacionados = Ato::retornaAtosRelacionadosAtivos();

            return view('ato.edit', compact('ato', 'atos_relacionados'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'edit');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function editCorpoTexto($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $ato = Ato::retornaAtoAtivo($id);
            $atos_relacionados = Ato::retornaAtosRelacionadosAtivos();

            return view('ato.edit', compact('ato', 'atos_relacionados'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'editCorpoTexto');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function alterarLinha(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
                Alert::toast('Ato que contém a alteração inválido.','error');
                return redirect()->back();
            }

            $linha_antiga = LinhaAto::where('id', '=', $request->id_linha_ato)->where('id_ato_principal', '=', $id)->where('ativo', '=', LinhaAto::ATIVO)->first();
            if (!$linha_antiga){
                Alert::toast('Não é possível alterar esta linha.','error');
                return redirect()->back();
            }

            $linha_antiga->update([
                'alterado' => LinhaAto::LINHA_ALTERADO
            ]);

            LinhaAto::create([
                'ordem' => $linha_antiga->ordem,
                'sub_ordem' => $linha_antiga->sub_ordem + 1,
                'texto' => $request->corpo_texto,
                'alterado' => LinhaAto::LINHA_NAO_ALTERADO,
                'id_ato_principal' => $linha_antiga->id_ato_principal,
                'id_ato_add' => $request->id_ato_add,
                'id_tipo_linha' => LinhaAto::TEXTO_ADICIONADO,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            AtoRelacionado::create([
                'id_ato_principal' => $linha_antiga->id_ato_principal,
                'id_ato_relacionado' => $request->id_ato_add,
                'cadastradoPorUsuario' => Auth::user()->id,
            ]);

            Alert::toast('Alteração realizado com sucesso!', 'success');
            return redirect()->route('ato.corpo_texto.edit', $linha_antiga->id_ato_principal);
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'alterarLinha');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function editDadosGerais($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $ato = Ato::retornaAtoAtivo($id);
            $atos_relacionados = Ato::retornaAtosRelacionadosAtivos();
            if (!$ato){
                Alert::toast('Ato inválido.','error');
                return redirect()->back();
            }

            $classificacaos = ClassificacaoAto::where('ativo', '=', ClassificacaoAto::ATIVO)->get();
            $assuntos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();
            $tipo_atos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();
            $orgaos = OrgaoAto::where('ativo', '=', OrgaoAto::ATIVO)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', FormaPublicacaoAto::ATIVO)->get();

            return view('ato.edit', compact('ato', 'atos_relacionados', 'classificacaos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'editDadosGerais');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
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

            $ato = Ato::retornaAtoAtivo($id);
            if (!$ato){
                Alert::toast('Ato inválido.','error');
                return redirect()->back();
            }

            $ato->update($request->validated() + [
                'altera_dispositivo' => $altera_dispositivo
            ]);
            Alert::toast('Alteração realizado com sucesso!', 'success');
            return redirect()->route('ato.dados_gerais.edit', $ato->id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'updateDadosGerais');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $ato = Ato::retornaAtoAtivo($id);
            if (!$ato){
                Alert::toast('Não é possível excluir este assunto.','error');
                return redirect()->back()->withInput();
            }

            $ato->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Ato::INATIVO
            ]);

            //  inativando ato relacionado
            $atoRelacionado = AtoRelacionado::where('id_ato_relacionado', '=', $id)->where('ativo', '=', AtoRelacionado::ATIVO)->first();
            if($atoRelacionado != null) {
                $atoRelacionado->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => AtoRelacionado::INATIVO
                ]);

                //inativando linha do ato
                $linhaAto = LinhaAto::where('id_ato_add', '=', $id)->where('ativo', '=', LinhaAto::ATIVO)->first();
                $linhaAto->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => LinhaAto::INATIVO
                ]);

            }

            $anexoAto = AnexoAto::where('id_ato', '=', $id)->where('ativo', '=', AnexoAto::ATIVO)->first();
            if($anexoAto != null) {
                $anexoAto->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => AnexoAto::INATIVO
                ]);
            }
            Alert::toast('Exclusão realizado com sucesso!', 'success');
            return redirect()->route('ato.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
