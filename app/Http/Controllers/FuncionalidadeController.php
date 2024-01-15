<?php

namespace App\Http\Controllers;

use App\Models\Funcionalidade;
use App\Models\ErrorLog;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FuncionalidadeController extends Controller
{

    public function store(Request $request)
    {
        try {
            if (Auth::user()->id_perfil != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_entidade' => $request->id_entidade,
                'id_tipo_funcionalidade' => $request->id_tipo_funcionalidade
            ];
            $rules = [
                'id_entidade' => 'required|max:255',
                'id_tipo_funcionalidade' => 'required|max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $existe = Funcionalidade::where('id_entidade', '=', $request->id_entidade)->where('id_tipo_funcionalidade', '=', $request->id_tipo_funcionalidade)->first();

            if ($existe){
                return redirect()->route('perfil_funcionalidade.index')->with('erro', 'Esta Funcionalidade já existe.')->withInput();
            }

            $funcionalidade = new Funcionalidade();
            $funcionalidade->id_entidade = $request->id_entidade;
            $funcionalidade->id_tipo_funcionalidade = $request->id_tipo_funcionalidade;
            $funcionalidade->cadastradoPorUsuario = Auth::user()->id;
            $funcionalidade->ativo = 1;
            $funcionalidade->save();

            return redirect()->route('perfil_funcionalidade.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FuncionalidadeController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
