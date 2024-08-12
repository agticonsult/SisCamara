<?php

namespace App\Http\Controllers;

use App\Models\AnexoAto;
use App\Models\Ato;
use App\Models\Filesize;
use App\Services\AnexoAtoService;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class AnexoAtoController extends Controller
{

    public function store(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AnexoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $input = [
                'anexo' => $request->anexo
            ];
            $rules = [
                'anexo' => 'required'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                Alert::toast('Ato inválido.','error');
                return redirect()->back();
            }

            AnexoAtoService::processarArquivos($request, $ato);

            return redirect()->route('ato.anexos.edit', $ato->id);

        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AnexoAtoController', 'store');
            Alert::toast($ex->getMessage(),'error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                Alert::toast('Ato inválido.','error');
                return redirect()->back();
            }
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();

            return view('ato.edit', compact('ato', 'filesize'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AnexoAtoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AnexoAto', 'Exclusão') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
                Alert::toast('Não é possível excluir este anexo.','error');
                return redirect()->back();
            }

            $aa->inativadoPorUsuario = Auth::user()->id;
            $aa->dataInativado = Carbon::now();
            $aa->motivoInativado = $motivo;
            $aa->ativo = 0;
            $aa->save();

            Alert::toast('Anexo excluído com sucesso.','success');
            return redirect()->route('ato.anexos.edit', $id);

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AnexoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
