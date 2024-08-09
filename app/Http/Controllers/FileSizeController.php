<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Filesize;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class FileSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (Auth::user()->temPermissao('Filesize', 'Listagem') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $files = Filesize::where('ativo', '=', Filesize::ATIVO)->with('tipo_filesize')->get();
            return view('configuracao.tamanho-anexo.index', compact('files'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'FilesizeController', 'index');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            if (Auth::user()->temPermissao('Filesize', 'Alteração') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $input = [
                'file_id' => $request->file_id,
                'file_mb' => $request->file_mb
            ];
            $rules = [
                'file_id' => 'required|integer',
                'file_mb' => 'required|integer|max:255'
            ];

            $validarArquivo = Validator::make($input, $rules);
            $validarArquivo->validate();

            $mb = (integer) $request->file_mb;

            if ($mb < 0 || $mb == 0) {
                Alert::toast('Tamanho inválido.','error');
                return redirect()->route('configuracao.tamanho_anexo.index');
            }

            $filesize = Filesize::where('id', '=', $request->file_id)->where('ativo', '=', Filesize::ATIVO)->first();
            if (!$filesize) {
                Alert::toast('Não foi possível realizar esta alteração.','error');
                return redirect()->route('configuracao.tamanho_anexo.index');
            }

            $filesize->mb = $request->file_mb;
            $filesize->save();

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.tamanho_anexo.index');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'FilesizeController', 'update');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }

    }
}
