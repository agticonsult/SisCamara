<?php

namespace App\Http\Controllers;

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
    public function store(DepartamentoStoreRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('Departamento', 'Cadastro') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento = Departamento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            DepartamentoUsuario::create($request->validated() + [
                'id_departamento' => $departamento->id,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

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
            $users = User::where('ativo', '=', User::ATIVO)->get();

            $usuarios = array();
            foreach ($users as $user) {
                if ($user->usuarioInterno() == 1) {
                    array_push($usuarios, $user);
                }
            }

            return view('configuracao.departamento.edit', compact('departamento', 'usuarios'));

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
    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Departamento', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamento = Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->first();
            $users = User::where('ativo', '=', User::ATIVO)->get();

            $usuarios = array();
            foreach ($users as $user) {
                if ($user->usuarioInterno() == 1) {
                    array_push($usuarios, $user);
                }
            }

            return view('configuracao.departamento.edit', compact('departamento', 'usuarios'));

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
