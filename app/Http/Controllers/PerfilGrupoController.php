<?php

namespace App\Http\Controllers;

use App\Models\Permissao;
use App\Models\perfil_grupo;
use App\Models\Grupo;
use App\Models\PerfilGrupo;
use App\Models\Perfil;
use App\Models\ErrorLog;
use App\Models\MembroGrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PerfilGrupoController extends Controller
{
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
    public function create($id_perfil, $id_grupo)
    {
        try {
            if (Auth::user()->temPermissao('Grupo', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [];
            $rules = [];

            // $validarUsuario = Validator::make($input, $rules);
            // $validarUsuario->validate();


            if (Count($id_perfil) != 0) {


                for ($i = 0; $i < Count($id_perfil); $i++) {

                    $perfilg = new perfil_grupo();
                    $perfilg->id_perfil = $id_perfil[$i];
                    $perfilg->id_grupo = $id_grupo;
                    $grupo = Perfil::where('id', '=', $id_perfil[$i])->first();
                    $perfilg->perfis = $grupo->descricao;
                    $perfilg->alteradoPorUsuario = Auth::user()->id;
                    $perfilg->ativo = 1;
                    $perfilg->save();
                }
            }





            return redirect()->route('gerenciamento.grupo_usuario.index')->with('success', 'Alteração de perfil realizada com sucesso.');
        } catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        } catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'não foi possivel adicionar  o(os) perfis.Contate o administrador do sistema.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)                                                                          //adicionado
    {




        dd($request->all());

        // try {
        //     if (Auth::user()->temPermissao('Grupo', 'Cadastro') != 1){
        //         return redirect()->back()->with('erro', 'Acesso negado.');
        //     }
        //    // dd($request->all());

        //     $input = [
        //         'id_perfil' => $request->id_perfil

        //     ];
        //     $rules = [
        //         'id_perfil' => 'numeric|required|max:2'
        //     ];

        //     $validarperfil = Validator::make($input, $rules);
        //     $validarperfil->validate();

        //     //encontrando o prefil relacionado ao id
        //     $perfil = Perfil::where('id', '=', $request->id_perfil)->where('ativo', '=', 1)->first();
        //     if (!$perfil){
        //         return redirect()->route('grupo_usuario.index')->with('erro', 'Não é possível adicionar este perfil.');
        //     }

        //     //validando se o grupo estiver vazio
        //     $perfil = Grupo::where('ativo', '=', 1)->get();
        //     if (!$perfil){
        //         return redirect()->route('grupo_usuario.index')->with('erro', 'nenhum grupo foi criado.');
        //     }


        //     $grupo = new Grupo();
        //     $grupo->id_perfilgrupo = $request->id_perfil;
        //     $grupo->cadastradoPorUsuario = Auth::user()->id;
        //     $grupo->save();

        //     return redirect()->route('grupo_usuario.index')->with('success', 'perfil adicionado com sucesso.');

        // }
        // catch (ValidationException $e ) {
        //     $message = $e->errors();
        //     return redirect()->back()
        //         ->withErrors($message)
        //         ->withInput();
        // }
        // catch(\Exception $ex){
        //     $erro = new ErrorLog();
        //     $erro->erro = $ex->getMessage();
        //     if (Auth::check()){
        //         $erro->cadastradoPorUsuario = auth()->user()->id;
        //     }
        //     $erro->save();
        //     return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        // }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\perfil_grupo  $perfil_grupo
     * @return \Illuminate\Http\Response
     */
    public function show(perfil_grupo $perfil_grupo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\perfil_grupo  $perfil_grupo
     * @return \Illuminate\Http\Response
     */
    public function edit(perfil_grupo $perfil_grupo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\perfil_grupo  $perfil_grupo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, perfil_grupo $perfil_grupo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\perfil_grupo  $perfil_grupo
     * @return \Illuminate\Http\Response
     */
    public function destroy(perfil_grupo $perfil_grupo)
    {
        //
    }


    public function inativarPerfil(Request $request, $id_grupo)
    {
        try {
            if (Auth::user()->temPermissao('Grupo', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_perfil' => $request->id_perfil,
                'nome_perfil' => $request->nome_perfil,
                'motivo' => $request->motivo
            ];
            $rules = [
                'id_perfil' => 'required|integer|max:255',
                'nome_perfil' => 'required|string|max:255',
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $perfil = PerfilGrupo::where('id', '=', $request->id_perfil)->where('id_grupo', '=', $id_grupo)->where('ativo', '=', 1)->first();


            // dd($perfil);

            if (!$perfil) {
                return redirect()->back()->with('erro', 'Não é possível inativar este perfil.');
            }



            $perfil->inativadoPorUsuario = Auth::user()->id;
            $perfil->dataInativado = Carbon::now();
            $perfil->motivoInativado = $motivo;
            $perfil->ativo = 0;
            $perfil->save();
            //desativar membro

            // $membrosg=Grupo::where('id','=',$id_grupo)->where('ativo','=',1)->with('membros')->get();
            //  // dd($membrosg);

            // foreach ($membrosg->membros->id_user as $membrosdogrupom ) {
            //      dd($membrosdogrupom);
            // }

            $membrogrupo = MembroGrupo::where('id_grupo', '=', $id_grupo)->where('ativo', '=', 1)->get();
            $result = array();
           $resultt = false;
            foreach ($membrogrupo as $key) {

                $tempermissao = Permissao::where('id_user', '=', $key->id_user)
                    ->where('id_perfil', '=', $perfil->id_perfil)
                    ->where('id_grupo', '=', $id_grupo)
                    ->where('ativo', '=', 1)->first();

                if (!$tempermissao) {
                    $resultt = true;
                    //  return redirect()->back()->with('erro', 'nenhum membro do grupo relacionado a este perfil.');

                } else {
                    $tempermissao->inativadoPorUsuario = Auth::user()->id;
                    $tempermissao->dataInativado = Carbon::now();
                    $tempermissao->motivoInativado = $motivo;
                    $tempermissao->ativo = 0;
                    $tempermissao->save();
                }
            }


            if ($resultt == true) {
                array_push($result, "Alguns usuários podem ter perfis anteriores a este grupo.");
            }


            // foreach ($grupo->membros_ativos as $ma) {
            //     $tempermissao= Permissao::where('id_user','=',$ma->id_user)->where('id_perfil', '=', $perfil->id_perfil)->where('ativo', '=', 1)->first();
            //    // dd($tempermissao);

            //     if (!$tempermissao) {

            //         $perfiladd = new Permissao();
            //         $perfiladd->id_user = $ma->id_user;
            //         $perfiladd->id_perfil=$id_perfil[$i];
            //         $perfiladd->id_perfil_grupo=$perfilg->id;
            //         $perfiladd->cadastradoPorUsuario= Auth::user()->id;
            //         $perfiladd->ativo = 1;

            //         $perfiladd->save();

            //     }

            // }










            return redirect()->route('gerenciamento.grupo_usuario.edit', $perfil->id_grupo)->with('info-grupo-import', $result)->with('success', 'perfil inativado com sucesso.');
        } catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        } catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
