<?php

namespace App\Http\Controllers;

use App\Models\CargoEletivo;
use App\Models\ErrorLog;
use App\Models\PleitoEleitoral;
use App\Models\User;
use App\Models\Vereador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VereadorController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Vereador', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereadores = Vereador::where('ativo', '=', 1)->get();

            return view('vereador.index', compact('vereadores'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', 1)->get();
            $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->where('users.ativo', '=', 1)
                ->select('users.id', 'users.id_pessoa')
                ->orderBy('pessoas.nomeCompleto', 'asc')
                ->get();

            $usuarios = array();

            foreach ($users as $user) {

                if ($user->ehVereador() == 0){
                    array_push($usuarios, $user);
                }

            }


            return view('vereador.create', compact('pleito_eleitorals', 'usuarios'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Listagem') != 1){
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
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
            }

            $cargo_eletivos = CargoEletivo::where('ativo', '=', 1)->get();

            return view('vereador.edit', compact('pleito_eleitoral', 'cargo_eletivos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Alteração') != 1){
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
            $erro->controlador = "VereadorController";
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
            if (Auth::user()->temPermissao('Vereador', 'Exclusão') != 1) {
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

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
            }

            $pleito_eleitoral->inativadoPorUsuario = Auth::user()->id;
            $pleito_eleitoral->dataInativado = Carbon::now();
            $pleito_eleitoral->motivoInativado = $motivo;
            $pleito_eleitoral->ativo = 0;
            $pleito_eleitoral->save();

            foreach ($pleito_eleitoral->cargos_eletivos_ativos() as $pleito_cargo_ativo){
                $pleito_cargo_ativo->inativadoPorUsuario = Auth::user()->id;
                $pleito_cargo_ativo->dataInativado = Carbon::now();
                $pleito_cargo_ativo->motivoInativado = $motivo;
                $pleito_cargo_ativo->ativo = 0;
                $pleito_cargo_ativo->save();
            }

            return redirect()->route('configuracao.pleito_eleitoral.index')->with('success', 'Exclusão realizada com sucesso.');
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
