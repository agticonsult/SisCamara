<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartamentoRequest;
use App\Http\Requests\DepartamentoStoreRequest;
use App\Models\Departamento;
use App\Models\DepartamentoUsuario;
use App\Models\ErrorLog;
use App\Models\PerfilUser;
use App\Models\User;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Departamento', 'Listagem') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();
            $users = User::where('ativo', '=', User::ATIVO)->get();

            $usuarios = array();
            foreach ($users as $user) {
                if ($user->usuarioInterno() == 1) {
                    array_push($usuarios, $user);
                }
            }

            return view('configuracao.departamento.index', compact('usuarios', 'departamentos'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
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
    public function store(DepartamentoRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('Departamento', 'Cadastro') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento = Departamento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            $id_usuarios = $request->id_user;
            foreach ($id_usuarios as $id_usuario) {
                $temUsuario = User::where('id', '=', $id_usuario)->where('ativo', '=', User::ATIVO)->first();
                if ($temUsuario) {
                    DepartamentoUsuario::create([
                        'id_user' => $temUsuario->id,
                        'id_departamento' => $departamento->id,
                        'cadastradoPorUsuario' => Auth::user()->id
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function show(Departamento $departamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Departamento', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento = Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->first();
            if (!$departamento) {
                return redirect()->route('configuracao.departamento.index')->with('erro', 'Não é possível alterar este departamento.');
            }
            $users = User::where('ativo', '=', User::ATIVO)->get();
            $pertecentesDepartamento = DepartamentoUsuario::where('id_departamento', '=', $departamento->id)->where('ativo', '=', DepartamentoUsuario::ATIVO)->get();
            $usuarios = array();
            foreach ($users as $user) {
                if ($user->usuarioInterno() == 1) {
                    array_push($usuarios, $user);
                }
            }

            return view('configuracao.departamento.edit', compact('departamento', 'usuarios', 'pertecentesDepartamento'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function update(DepartamentoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Departamento', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento = Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->first();
            $vincularUsuarioDep = DepartamentoUsuario::where('id_departamento', '=', $departamento->id)->where('ativo', '=', DepartamentoUsuario::ATIVO)->first();

            $id_usuarios = $request->id_user;
            foreach ($id_usuarios as $id_usuario) {
                $temUsuario = User::where('id', '=', $id_usuario)->where('ativo', '=', User::ATIVO)->first();
                if ($temUsuario) {
                    $vincularUsuarioDep->update($request->validated() + [
                        'id_user' => $temUsuario->id,
                    ]);
                }
            }

            return redirect()->route('configuracao.departamento.edit', $departamento->id)->with('success', 'Departamento alterado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Departamento $departamento)
    {
        //
    }
}
