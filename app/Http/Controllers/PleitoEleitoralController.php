<?php

namespace App\Http\Controllers;

use App\Models\CargoEletivo;
use App\Models\ErrorLog;
use App\Models\PleitoCargo;
use App\Models\PleitoEleitoral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PleitoEleitoralController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleitos = PleitoEleitoral::where('ativo', '=', 1)->get();

            return view('configuracao.pleito-eleitoral.index', compact('pleitos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PleitoEleitoralController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $cargo_eletivos = CargoEletivo::where('ativo', '=', 1)->get();

            return view('configuracao.pleito-eleitoral.create', compact('cargo_eletivos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PleitoEleitoralController";
            $erro->funcao = "create";
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
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'ano_pleito' => $request->ano_pleito,
                'inicio_mandato' => $request->inicio_mandato,
                'fim_mandato' => $request->fim_mandato,
                'pleitoEspecial' => $request->pleitoEspecial,
                'dataPrimeiroTurno' => $request->dataPrimeiroTurno,
                'dataSegundoTurno' => $request->dataSegundoTurno,
                'id_cargo_eletivo' => $request->id_cargo_eletivo
            ];
            $rules = [
                'ano_pleito' => 'required',
                'inicio_mandato' => 'required',
                'fim_mandato' => 'required',
                'pleitoEspecial' => 'nullable',
                'dataPrimeiroTurno' => 'required|date',
                'dataSegundoTurno' => 'required|date',
                'id_cargo_eletivo' => 'required'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            if ($request->pleitoEspecial != 0 && $request->pleitoEspecial != 1){
                return redirect()->back()->with('erro', 'Pleito especial inválido.');
            }

            $pleito_eleitoral = new PleitoEleitoral();
            $pleito_eleitoral->ano_pleito = $request->ano_pleito;
            $pleito_eleitoral->inicio_mandato = $request->inicio_mandato;
            $pleito_eleitoral->fim_mandato = $request->fim_mandato;
            $pleito_eleitoral->pleitoEspecial = $request->pleitoEspecial;
            $pleito_eleitoral->dataPrimeiroTurno = $request->dataPrimeiroTurno;
            $pleito_eleitoral->dataSegundoTurno = $request->dataSegundoTurno;
            $pleito_eleitoral->cadastradoPorUsuario = Auth::user()->id;
            $pleito_eleitoral->ativo = 1;
            $pleito_eleitoral->save();

            $id_cargo_eletivos = $request->id_cargo_eletivo;
            foreach ($id_cargo_eletivos as $id_cargo_eletivo){
                $cargo_eletivo = CargoEletivo::where('id', '=', $id_cargo_eletivo)->where('ativo', '=', 1)->first();
                if ($cargo_eletivo){
                    $pleito_cargo = new PleitoCargo();
                    $pleito_cargo->id_pleito_eleitoral = $pleito_eleitoral->id;
                    $pleito_cargo->id_cargo_eletivo = $id_cargo_eletivo;
                    $pleito_cargo->cadastradoPorUsuario = Auth::user()->id;
                    $pleito_cargo->ativo = 1;
                    $pleito_cargo->save();
                }
            }

            return redirect()->route('configuracao.pleito_eleitoral.index')->with('success', 'Cadastro realizado com sucesso');
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
            $erro->controlador = "PleitoEleitoralController";
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
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
            }

            $cargo_eletivos = CargoEletivo::where('ativo', '=', 1)->get();

            return view('configuracao.pleito-eleitoral.edit', compact('pleito_eleitoral', 'cargo_eletivos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PleitoEleitoralController";
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
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $$input = [
                'id' => $request->id,
                'ano_pleito' => $request->ano_pleito,
                'inicio_mandato' => $request->inicio_mandato,
                'fim_mandato' => $request->fim_mandato,
                'pleitoEspecial' => $request->pleitoEspecial,
                'dataPrimeiroTurno' => $request->dataPrimeiroTurno,
                'dataSegundoTurno' => $request->dataSegundoTurno,
            ];
            $rules = [
                'id' => 'required|integer',
                'ano_pleito' => 'required',
                'inicio_mandato' => 'required',
                'fim_mandato' => 'required',
                'pleitoEspecial' => 'nullable',
                'dataPrimeiroTurno' => 'required|date',
                'dataSegundoTurno' => 'required|date',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
            }

            $pleito_eleitoral->ano_pleito = $request->ano_pleito;
            $pleito_eleitoral->inicio_mandato = $request->inicio_mandato;
            $pleito_eleitoral->fim_mandato = $request->fim_mandato;
            $pleito_eleitoral->pleitoEspecial = $request->pleitoEspecial;
            $pleito_eleitoral->dataPrimeiroTurno = $request->dataPrimeiroTurno;
            $pleito_eleitoral->dataSegundoTurno = $request->dataSegundoTurno;
            $pleito_eleitoral->save();

            $id_cargo_eletivos = $request->id_cargo_eletivo;
            foreach ($id_cargo_eletivos as $id_cargo_eletivo){
                $cargo_eletivo = CargoEletivo::where('id', '=', $id_cargo_eletivo)->where('ativo', '=', 1)->first();
                if ($cargo_eletivo){

                    $possuiCargo = PleitoCargo::where('id_pleito_eleitoral', '=', $id)
                        ->where('id_cargo_eletivo', '=', $id_cargo_eletivo)
                        ->where('ativo', '=', 1)
                        ->first();

                    if (!$possuiCargo){
                        $pleito_cargo = new PleitoCargo();
                        $pleito_cargo->id_pleito_eleitoral = $pleito_eleitoral->id;
                        $pleito_cargo->id_cargo_eletivo = $id_cargo_eletivo;
                        $pleito_cargo->cadastradoPorUsuario = Auth::user()->id;
                        $pleito_cargo->ativo = 1;
                        $pleito_cargo->save();
                    }

                }
            }

            return redirect()->route('configuracao.pleito_eleitoral.index')->with('success', 'Alteração realizada com sucesso');
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
            $erro->controlador = "PleitoEleitoralController";
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
            if (Auth::user()->temPermissao('PleitoEleitoral', 'Exclusão') != 1) {
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

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $reparticao->inativadoPorUsuario = Auth::user()->id;
            $reparticao->dataInativado = Carbon::now();
            $reparticao->motivoInativado = $motivo;
            $reparticao->ativo = 0;
            $reparticao->save();

            return redirect()->route('reparticao.index')->with('success', 'Exclusão realizada com sucesso.');
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
