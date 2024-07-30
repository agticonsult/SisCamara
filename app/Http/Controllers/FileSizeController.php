<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Filesize;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $files = Filesize::where('ativo', '=', Filesize::ATIVO)->with('tipo_filesize')->get();
            return view('configuracao.tamanho-anexo.index', compact('files'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'FilesizeController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
                return redirect()->back()->with('erro', 'Acesso negado.');
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
                return redirect()->route('configuracao.tamanho_anexo.index')->with('erro', 'Tamanho inválido.');
            }

            $filesize = Filesize::where('id', '=', $request->file_id)->where('ativo', '=', Filesize::ATIVO)->first();

            if (!$filesize) {
                return redirect()->route('configuracao.tamanho_anexo.index')->with('erro', 'Não foi possível realizar esta alteração.');
            }

            $filesize->mb = $request->file_mb;
            $filesize->save();

            return redirect()->route('configuracao.tamanho_anexo.index')->with('success', 'Alteração realizada com sucesso.');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'FilesizeController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();

        }



    }
}
