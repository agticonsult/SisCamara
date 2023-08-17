<?php

namespace App\Http\Controllers;

use App\Models\AnexoAto;
use App\Models\Ato;
use App\Models\ErrorLog;
use App\Models\Filesize;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class AnexoAtoController extends Controller
{

    public function store(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AnexoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'anexo[]' => $request->anexo
            ];
            $rules = [
                'anexo[]' => 'required'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $arquivos = $request->file('anexo');
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

                return redirect()->route('ato.anexos.edit', $id)->with('success', 'Cadastro realizado com sucesso')->with('info-anexo', $result);
            }
            else{
                return redirect()->back()->with('erro', 'Nenhum arquivo encontrado.');
            }

        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AnexoProcessoController";
            $erro->funcao = "store";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();

            return view('ato.edit', compact('ato', 'filesize'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AnexoAtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AnexoAto', 'Exclusão') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'anexo_id' => $request->anexo_id,
                'anexo_nome' => $request->anexo_nome,
                'motivo' => $request->motivo
            ];
            $rules = [
                'anexo_id' => 'required|integer|max:255',
                'anexo_nome' => 'required|string|max:255',
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == ""){
                $motivo = "Exclusão pelo usuário.";
            }

            $aa = AnexoAto::where('id', '=', $request->anexo_id)->where('id_ato', '=', $id)->where('ativo', '=', 1)->first();

            if (!$aa){
                return redirect()->back()->with('erro', 'Não é possível excluir este anexo.')->withInput();
            }

            $aa->inativadoPorUsuario = Auth::user()->id;
            $aa->dataInativado = Carbon::now();
            $aa->motivoInativado = $motivo;
            $aa->ativo = 0;
            $aa->save();

            return redirect()->route('ato.anexos.edit', $id)->with('success', 'Anexo excluído com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AnexoAtoController";
            $erro->funcao = "destroy";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
