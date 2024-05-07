<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Entidade;
use App\Models\Funcionalidade;
use App\Models\Perfil;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GestaoAdministrativaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();
            $perfis = Perfil::where('ativo', '=', Perfil::ATIVO)->get();
            $funcionalidades = Funcionalidade::where('ativo', '=', Funcionalidade::ATIVO)->get();
            $entidades = Entidade::where('ativo', '=', Entidade::ATIVO)->get();
            // $tfs = TipoFuncionalidade::where('ativo', '=', TipoFuncionalidade::ATIVO)->get();
            $tipo_perfis = Perfil::where('id', '!=', Perfil::USUARIO_ADM)->where('ativo', '=', Perfil::ATIVO)->get();

            return view('configuracao.gestao-adiministrativa.index', compact('departamentos', 'perfis', 'funcionalidades', 'tipo_perfis'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
