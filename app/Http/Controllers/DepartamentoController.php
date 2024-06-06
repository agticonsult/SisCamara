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
use Carbon\Carbon;
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

            $departamentos = Departamento::retornaDepartamentosAtivos();
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
            $departamento->usuarios()->attach($id_usuarios, [
                'created_at' => Carbon::now()
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

            $departamento = Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->with('usuarios')->first();
            if (!$departamento) {
                return redirect()->route('configuracao.departamento.index')->with('erro', 'Não é possível alterar este departamento.');
            }
            $users = User::where('ativo', '=', User::ATIVO)->get();

            return view('configuracao.departamento.edit', compact('departamento', 'users'));

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

            $departamento = Departamento::retornaDepartamentoAtivo($id);
            $departamento->update($request->validated());

            if (isset($request->usuario_selecionados)) {
                $id_usuarios = $request->usuario_selecionados;
                foreach($id_usuarios as $usuario) {
                    $departamento->usuarios()->attach($usuario, [
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            return redirect()->route('configuracao.departamento.edit', $departamento->id)->with('success', 'Departamento alterado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Departamento', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $departamento = Departamento::retornaDepartamentoAtivo($id);
            if (!$departamento) {
                return redirect()->route('configuracao.departamento.index')->with('erro', 'Não é possível excluir este departamento.');
            }
            $departamento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Departamento::INATIVO
            ]);

            // $departamentoUsuarios = DepartamentoUsuario::where('id_departamento', '=', $departamento->id)->where('ativo', '=', DepartamentoUsuario::ATIVO)->get();
            // foreach ($departamentoUsuarios as $departamentoUsuario) {
            //     $departamentoUsuario->update([
            //         'ativo' => DepartamentoUsuario::INATIVO
            //     ]);
            // }
            $departamento->usuarios()->update([
                'departamento_usuarios.ativo' => DepartamentoUsuario::INATIVO,
                'departamento_usuarios.updated_at' => Carbon::now(),
                'departamento_usuarios.inativadoPorUsuario' => Auth::user()->id,
                'departamento_usuarios.dataInativado' => Carbon::now()
            ]);

            return redirect()->route('configuracao.departamento.index')->with('success', 'Departamento excluído com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function desvincularUsuario($id)
    {
        try{
            if(Auth::user()->temPermissao('Departamento', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $desvincularUsuario = DepartamentoUsuario::where('id_user', '=', $id)->Where('ativo', '=', DepartamentoUsuario::ATIVO)->first();
            $desvincularUsuario->update([
                'ativo' => DepartamentoUsuario::INATIVO,
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now()
            ]);

            return redirect()->back()->with('success', 'Usuário desvinculado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'desvincularUsuario');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
