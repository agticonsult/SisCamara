<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Legislatura;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LegislaturaController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $legislaturas = Legislatura::where('ativo', '=', 1)->get();

            return view('processo-legislativo.legislatura.index', compact('legislaturas'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "LegislaturaController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'inicio_mandato' => $request->inicio_mandato,
                'fim_mandato' => $request->fim_mandato,
            ];
            $rules = [
                'inicio_mandato' => 'required|integer',
                'fim_mandato' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $legislatura = new Legislatura();
            $legislatura->inicio_mandato = $request->inicio_mandato;
            $legislatura->fim_mandato = $request->fim_mandato;
            $legislatura->cadastradoPorUsuario = Auth::user()->id;
            $legislatura->ativo = 1;
            $legislatura->save();

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Cadastro realizado com sucesso.');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "LegislaturaController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $legislatura = Legislatura::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            return view('processo-legislativo.legislatura.edit', compact('legislatura'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "LegislaturaController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $request->id,
                'inicio_mandato' => $request->inicio_mandato,
                'fim_mandato' => $request->fim_mandato,
            ];
            $rules = [
                'id' => 'required|integer',
                'inicio_mandato' => 'required|integer',
                'fim_mandato' => 'required|integer',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $legislatura = Legislatura::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $legislatura->inicio_mandato = $request->inicio_mandato;
            $legislatura->fim_mandato = $request->fim_mandato;
            $legislatura->save();

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "LegislaturaController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Legislatura', 'Exclusão') != 1) {
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

            $legislatura = Legislatura::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $legislatura->inativadoPorUsuario = Auth::user()->id;
            $legislatura->dataInativado = Carbon::now();
            $legislatura->motivoInativado = $motivo;
            $legislatura->ativo = 0;
            $legislatura->save();

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtividadeLazerController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
