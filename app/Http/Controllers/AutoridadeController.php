<?php

namespace App\Http\Controllers;

use App\Models\Autoridade;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AutoridadeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Autoridade', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $autoridades = Autoridade::where('ativo', '=', 1)->get();

            return view('configuracao.autoridade.index', compact('autoridades'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AutoridadeController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
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
            if(Auth::user()->temPermissao('Autoridade', 'Cadastro') != 1){
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

            $autoridade = new Autoridade();
            $autoridade->descricao = $request->descricao;
            $autoridade->cadastradoPorUsuario = Auth::user()->id;
            $autoridade->ativo = 1;
            $autoridade->save();

            return redirect()->route('configuracao.autoridade.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AutoridadeController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
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
            if(Auth::user()->temPermissao('Autoridade', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $autoridade = Autoridade::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$autoridade){
                return redirect()->route('configuracao.autoridade.index')->with('erro', 'Não é possível alterar esta autoridade.');
            }

            return view('configuracao.autoridade.edit', compact('autoridade'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AutoridadeController', 'edit');
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
            if(Auth::user()->temPermissao('Autoridade', 'Alteração') != 1){
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

            $autoridade = Autoridade::find($id);
            $autoridade->descricao = $request->descricao;
            $autoridade->save();

            return redirect()->route('configuracao.autoridade.index')->with('success', 'Alteração realizada com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AutoridadeController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
            if (Auth::user()->temPermissao('Autoridade', 'Exclusão') != 1) {
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

            $autoridade = Autoridade::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$autoridade){
                return redirect()->back()->with('erro', 'Não é possível excluir esta autoridade.')->withInput();
            }

            $autoridade->inativadoPorUsuario = Auth::user()->id;
            $autoridade->dataInativado = Carbon::now();
            $autoridade->motivoInativado = $motivo;
            $autoridade->ativo = 0;
            $autoridade->save();

            return redirect()->route('configuracao.autoridade.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AutoridadeController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
