<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilStoreRequest;
use App\Models\ErrorLog;
use App\Models\Perfil;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PerfilController extends Controller
{
    use ApiResponser;

    public function funcionalidades($id_perfil)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $perfil = Perfil::where('id', '=', $id_perfil)->where('ativo', '=', Perfil::ATIVO)->first();
            if (!$perfil){
                return $this->error('Erro, perfil não encontrado. Contate o administrador do sistema', 403);
            }

            $funcionalidades = array();
            foreach ($perfil->funcionalidades_ativas as $pf){
                $ent = $pf->funcionalidade->entidade->descricao;
                $tf = $pf->funcionalidade->tipo_funcionalidade->descricao;
                $func = $ent . ' - ' . $tf;
                array_push($funcionalidades, $func);
            }

            return $this->success([
                'funcionalidades' => $funcionalidades
            ]);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PerfilController', 'funcionalidades');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PerfilStoreRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }
            if ($request->id_tipo_perfil == 1){
                return redirect()->route('perfil_funcionalidade.index')->with('warning', 'Não é possível cadastrar perfil de Administrador novamente.')->withInput();
            }

            $existePerfil = Perfil::where('descricao', '=', $request->descricao)->first();
            if ($existePerfil){
                return redirect()->route('perfil_funcionalidade.index')->with('erro', 'Este perfil já existe.')->withInput();
            }

            Perfil::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('perfil_funcionalidade.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PerfilController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function show(Perfil $perfil)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function edit(Perfil $perfil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perfil $perfil)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function destroy(Perfil $perfil)
    {
        //
    }
}
