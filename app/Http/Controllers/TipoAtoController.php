<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoAtoRequest;
use App\Models\ErrorLog;
use App\Models\TipoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
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

            $tipoAtos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();

            return view('configuracao.tipo-ato.index', compact('tipoAtos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'index');
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
    public function store(TipoAtoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            TipoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('configuracao.tipo_ato.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'store');
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

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            if (!$tipoAto){
                return redirect()->route('configuracao.tipo_ato.index')->with('erro', 'Não é possível alterar este tipo de ato.');
            }

            return view('configuracao.tipo-ato.edit', compact('tipoAto'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'edit');
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
    public function update(TipoAtoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            $tipoAto->update($request->validated());

            return redirect()->route('configuracao.tipo_ato.index')->with('success', 'Alteração realizada com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'update');
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
            if (Auth::user()->temPermissao('TipoAto', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            if (!$tipoAto){
                return redirect()->back()->with('erro', 'Não é possível excluir este tipo de ato.')->withInput();
            }

            $tipoAto->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => TipoAto::ATIVO
            ]);

            return redirect()->route('configuracao.tipo_ato.index')->with('success', 'Exclusão realizada com sucesso.');
            
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
