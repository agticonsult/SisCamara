<?php

namespace App\Http\Controllers;

use App\Models\AssuntoAto;
use App\Models\ErrorLog;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AssuntoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $assuntoAtos = AssuntoAto::where('ativo', '=', 1)->get();

            return view('configuracao.assunto-ato.index', compact('assuntoAtos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'index');
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
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
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

            $assuntoAto = new AssuntoAto();
            $assuntoAto->descricao = $request->descricao;
            $assuntoAto->cadastradoPorUsuario = Auth::user()->id;
            $assuntoAto->ativo = 1;
            $assuntoAto->save();

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'store');
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
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$assunto){
                return redirect()->route('configuracao.assunto_ato.index')->with('erro', 'Não é possível alterar este assunto.');
            }

            return view('configuracao.assunto-ato.edit', compact('assunto'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'edit');
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
            if(Auth::user()->temPermissao('AssuntoAto', 'Alteração') != 1){
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

            $assunto = AssuntoAto::find($id);
            $assunto->descricao = $request->descricao;
            $assunto->save();

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Alteração realizada com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AssuntoAto', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$assunto){
                return redirect()->back()->with('erro', 'Não é possível excluir este assunto.')->withInput();
            }

            $assunto->inativadoPorUsuario = Auth::user()->id;
            $assunto->dataInativado = Carbon::now();
            $assunto->motivoInativado = $motivo;
            $assunto->ativo = 0;
            $assunto->save();

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
