<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssuntoAtoRequest;
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

            $assuntoAtos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();

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
    public function store(AssuntoAtoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            AssuntoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Cadastro realizado com sucesso.');

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

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
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
    public function update(AssuntoAtoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
            $assunto->update($request->validated());

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Alteração realizada com sucesso.');

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

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
            if (!$assunto){
                return redirect()->back()->with('erro', 'Não é possível excluir este assunto.')->withInput();
            }

            $assunto->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => AssuntoAto::INATIVO
            ]);

            return redirect()->route('configuracao.assunto_ato.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
