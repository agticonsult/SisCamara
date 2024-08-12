<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartamentoRequest;
use App\Models\Departamento;
use App\Models\DepartamentoUsuario;
use App\Models\User;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
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
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
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
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
            }

            $departamento = Departamento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            $id_usuarios = $request->usuario_selecionados;
            $departamento->usuarios()->attach($id_usuarios, [
                'created_at' => Carbon::now()
            ]);

            Alert::toast('Cadastro realizado com sucesso!', 'success');
            return redirect()->back();

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'store');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
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
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
            }

            $departamento = Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->with('usuarios')->first();
            if (!$departamento) {
                Alert::toast('Não é possível alterar este departamento.','error');
                return redirect()->route('configuracao.departamento.index');
            }
            $users = User::where('ativo', '=', User::ATIVO)->get();

            return view('configuracao.departamento.edit', compact('departamento', 'users'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'edit');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
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
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
            }

            $departamento = Departamento::retornaDepartamentoAtivo($id);
            $departamento->update($request->validated());

            if (isset($request->usuario_selecionados)) {
                foreach ($request->usuario_selecionados as $Idusuario) {
                    $desvincularUsuario = DepartamentoUsuario::where('id_user', '=', $Idusuario)->Where('ativo', '=', DepartamentoUsuario::ATIVO)->first();
                    if ($desvincularUsuario) {
                        $desvincularUsuario->update([
                            'ativo' => DepartamentoUsuario::INATIVO,
                            'inativadoPorUsuario' => Auth::user()->id,
                            'dataInativado' => Carbon::now()
                        ]);
                    }
                }
            }
            Alert::toast('Departamento alterado com sucesso.', 'success');
            return redirect()->route('configuracao.departamento.edit', $departamento->id);

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'update');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
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
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $departamento = Departamento::retornaDepartamentoAtivo($id);
            if (!$departamento) {
                Alert::toast('Não é possível excluir este departamento.','error');
                return redirect()->route('configuracao.departamento.index');
            }
            $departamento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Departamento::INATIVO
            ]);

            $departamento->usuarios()->update([
                'departamento_usuarios.ativo' => DepartamentoUsuario::INATIVO,
                'departamento_usuarios.updated_at' => Carbon::now(),
                'departamento_usuarios.inativadoPorUsuario' => Auth::user()->id,
                'departamento_usuarios.dataInativado' => Carbon::now()
            ]);
            Alert::toast('Departamento alterado com sucesso.', 'success');
            return redirect()->route('configuracao.departamento.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function vincularUsuario(Request $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('Departamento', 'Exclusão') != 1) {
                Alert::toast('Acesso negado.','error');
                return redirect()->back();
            }

            $departamento = Departamento::retornaDepartamentoAtivo($id);
            if (isset($request->vincular_usuarios)) {
                foreach($request->vincular_usuarios as $vincularUsuario) {
                    $departamento->usuarios()->attach($vincularUsuario, [
                        'created_at' => Carbon::now()
                    ]);
                }
            }
            Alert::toast('Usuário vinculado com sucesso.', 'success');
            return redirect()->back();

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoController', 'desvincularUsuario');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
