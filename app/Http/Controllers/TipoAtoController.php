<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\TipoAto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TipoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoAtos = TipoAto::where('ativo', '=', 1)->get();

            return view('configuracao.tipo-ato.index', compact('tipoAtos'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "TipoAtoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
            if(Auth::user()->temPermissao('TipoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao
            ];
            $rules = [
                'descricao' => 'required|max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $tipoAto = new TipoAto();
            $tipoAto->descricao = $request->descricao;
            $tipoAto->cadastradoPorUsuario = Auth::user()->id;
            $tipoAto->ativo = 1;
            $tipoAto->save();

            return redirect()->route('configuracao.tipo_ato.index')->with('success', 'Cadastro realizado com sucesso.');

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
            $erro->controlador = "TipoAtoController";
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
            if(Auth::user()->temPermissao('TipoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$tipoAto){
                return redirect()->route('configuracao.tipo_ato.index')->with('erro', 'Não é possível alterar este tipo de ato.');
            }

            return view('configuracao.tipo-ato.edit', compact('tipoAto'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "TipoAtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
            if(Auth::user()->temPermissao('TipoAto', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao
            ];
            $rules = [
                'descricao' => 'required|max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $tipoAto = TipoAto::find($id);
            $tipoAto->descricao = $request->descricao;
            $tipoAto->save();

            return redirect()->route('configuracao.tipo_ato.index')->with('success', 'Alteração realizada com sucesso.');

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
            $erro->controlador = "TipoAtoController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
}
