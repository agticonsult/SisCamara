<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecebimentoDocFormRequest;
use App\Http\Requests\RecimentoDocFormRequest;
use App\Models\Departamento;
use App\Models\Entidade;
use App\Models\Funcionalidade;
use App\Models\GestaoAdministrativa;
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
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $gestoesAdm = GestaoAdministrativa::where('ativo', '=', GestaoAdministrativa::ATIVO)->get();
            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();
            $departamentosArray = array();

            foreach ($departamentos as $departamento) {
                if ($departamento->estaVinculadoGestaoAdm() == false) {
                    array_push($departamentosArray, $departamento);
                }
            }

            return view('configuracao.gestao-adiministrativa.index', compact('departamentos', 'departamentosArray', 'gestoesAdm'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecebimentoDocFormRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->aprovacaoCadastro == 1 && $request->aprovacaoCadastro == 0) {
                return redirect()->back()->with('warning', 'Opção inválida de APROVAÇÃO DE CADASTRO.');
            }
            if ($request->recebimentoDocumento == 1 && $request->recebimentoDocumento == 0) {
                return redirect()->back()->with('warning', 'Opção inválida de RECEBIMENTO DE DOCUMENTO.');
            }

            GestaoAdministrativa::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('configuracao.gestao_administrativa.index')->with('success', 'Cadastro realizada com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'storeRecebimento');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
