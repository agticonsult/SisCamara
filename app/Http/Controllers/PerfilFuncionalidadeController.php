<?php

namespace App\Http\Controllers;

use App\Models\Abrangencia;
use App\Models\Entidade;
use App\Models\ErrorLog;
use App\Models\Funcionalidade;
use App\Models\Perfil;
use App\Models\PerfilFuncionalidade;
use App\Models\TipoFuncionalidade;
use App\Models\TipoPerfil;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as FacadesRoute;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PerfilFuncionalidadeController extends Controller
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

            $pfs = Perfil::where('ativo', '=', Perfil::ATIVO)->get();
            $perfis = Perfil::where('ativo', '=', Perfil::ATIVO)->get();
            $funcionalidades = Funcionalidade::where('ativo', '=', Funcionalidade::ATIVO)->get();
            $entidades = Entidade::where('ativo', '=', Entidade::ATIVO)->get();
            // $tfs = TipoFuncionalidade::where('ativo', '=', TipoFuncionalidade::ATIVO)->get();
            $tipo_perfis = Perfil::where('id', '!=', Perfil::USUARIO_ADM)->where('ativo', '=', Perfil::ATIVO)->get();

            return view('perfil-funcionalidade.index', compact('pfs', 'perfis', 'funcionalidades', 'tipo_perfis'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PerfilFuncionalidadeController', 'index');
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
    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_perfil' => $request->id_perfil,
                'id_funcionalidade' => $request->id_funcionalidade
            ];
            $rules = [
                'id_perfil' => 'required|max:255',
                'id_funcionalidade' => 'required'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $funcionalidades = $request->id_funcionalidade;

            for ($i=0; $i < Count($funcionalidades); $i++) {
                $func = Funcionalidade::where('id', '=', $funcionalidades[$i])->where('ativo', '=', 1)->first();
                if ($func){
                    // se não for funcionalidade deste perfil, inclui
                    if ($func->ehFuncionalidadeDoPerfil($request->id_perfil) == 0){
                        $pf = new PerfilFuncionalidade();
                        $pf->id_perfil = $request->id_perfil;
                        $pf->id_funcionalidade = $funcionalidades[$i];
                        $pf->cadastradoPorUsuario = Auth::user()->id;
                        $pf->ativo = 1;
                        $pf->save();
                    }
                }
            }

            return redirect()->route('perfil_funcionalidade.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PerfilFuncionalidadeController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $perfil = Perfil::where('id', '=', $id)->where('ativo', '=', 1)->with('funcionalidades')->first();

            if (!$perfil){
                return redirect()->route('perfil_funcionalidade.index')->with('erro', 'Não é possível alterar este perfil.');
            }

            $fs = Funcionalidade::where('ativo', '=', 1)->get();
            $funcs = array();

            foreach ($fs as $f){
                if ($f->ehFuncionalidadeDoPerfil($perfil->id) == false){
                    array_push($funcs, $f);
                }
            }

            return view('perfil-funcionalidade.edit', compact('perfil', 'funcs'));
        }
        catch(\Exception $ex){
            return $ex->getMessage();
            // ErrorLogService::salvar($ex->getMessage(), 'PerfilFuncionalidadeController', 'edit');
            // return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao,
                'id_abrangencia' => $request->id_abrangencia,
                'funcionalidade_id' => $request->funcionalidade_id
            ];
            $rules = [
                'descricao' => 'required|max:255',
                'id_abrangencia' => 'required',
                'funcionalidade_id' => 'required'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $perfil = Perfil::find($id);
            $perfil->descricao = $request->descricao;
            $perfil->id_abrangencia = $request->id_abrangencia;
            $perfil->save();

            $funcionalidades = $request->funcionalidade_id;

            for ($i=0; $i < Count($funcionalidades); $i++) {

                $func = Funcionalidade::where('id', '=', $funcionalidades[$i])->where('ativo', '=', 1)->first();

                if ($func){

                    // se não for funcionalidade deste perfil, inclui
                    if ($func->ehFuncionalidadeDoPerfil($id) == 0){

                        $pf = new PerfilFuncionalidade();
                        $pf->id_perfil = $id;
                        $pf->id_funcionalidade = $funcionalidades[$i];
                        $pf->cadastradoPorUsuario = Auth::user()->id;
                        $pf->ativo = 1;
                        $pf->save();

                    }
                }
            }

            return redirect()->route('perfil_funcionalidade.index')->with('success', 'Alteração realizada com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PerfilFuncionalidadeController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function inativarFuncionalidade(Request $request, $id_perfil)
    {
        try {
            if(Auth::user()->temPermissao('Perfil', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_func' => $request->id_func,
                'nome_func' => $request->nome_func,
                'motivo' => $request->motivo
            ];
            $rules = [
                'id_func' => 'required|integer|max:255',
                'nome_func' => 'required|string|max:255',
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == ""){
                $motivo = "Desativação pelo usuário.";
            }

            $pf = PerfilFuncionalidade::where('id', '=', $request->id_func)->where('id_perfil', '=', $id_perfil)->first();

            if (!$pf){
                return redirect()->back()->with('erro', 'Não é possível inativar esta funcionalidade.')->withInput();
            }

            $pf->inativadoPorUsuario = Auth::user()->id;
            $pf->dataInativado = Carbon::now();
            $pf->motivoInativado = $motivo;
            $pf->ativo = 0;
            $pf->save();

            return redirect()->route('perfil_funcionalidade.edit', $id_perfil)->with('success', 'Funcionalidade inativada com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PerfilFuncionalidadeController', 'inativarFuncionalidade');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
