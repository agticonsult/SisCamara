<?php

namespace App\Http\Controllers;

use App\Http\Requests\PleitoEleitoralRequest;
use App\Models\CargoEletivo;
use App\Models\Legislatura;
use App\Models\PleitoCargo;
use App\Models\PleitoEleitoral;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class PleitoEleitoralController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $pleitos = PleitoEleitoral::where('ativo', '=', PleitoEleitoral::ATIVO)->get();

            return view('processo-legislativo.pleito-eleitoral.index', compact('pleitos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $cargo_eletivos = CargoEletivo::where('ativo', '=', CargoEletivo::ATIVO)->get();
            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();

            return view('processo-legislativo.pleito-eleitoral.create', compact('cargo_eletivos', 'legislaturas'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(PleitoEleitoralRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Cadastro') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            if ($request->pleitoEspecial != 0 && $request->pleitoEspecial != 1){
                Alert::toast('Pleito especial inválido.','error');
                return redirect()->back();
            }

            $pleitoEleitoral = PleitoEleitoral::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            $id_cargo_eletivos = $request->id_cargo_eletivo;
            foreach ($id_cargo_eletivos as $id_cargo_eletivo){
                $cargo_eletivo = CargoEletivo::where('id', '=', $id_cargo_eletivo)->where('ativo', '=', CargoEletivo::ATIVO)->first();
                if ($cargo_eletivo) {
                    PleitoCargo::create([
                        'id_cargo_eletivo' => $id_cargo_eletivo,
                        'id_pleito_eleitoral' => $pleitoEleitoral->id,
                        'cadastradoPorUsuario' => Auth::user()->id
                    ]);
                }
            }
            Alert::toast('Cadastro realizado com sucesso','success');
            return redirect()->route('processo_legislativo.pleito_eleitoral.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $cargo_eletivos = CargoEletivo::where('ativo', '=', CargoEletivo::ATIVO)->get();
            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();
            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                Alert::toast('Pleito especial inválido.','error');
                return redirect()->back();
            }

            return view('processo-legislativo.pleito-eleitoral.edit', compact('pleito_eleitoral', 'cargo_eletivos', 'legislaturas'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('PleitoEleitoral', 'Alteração') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $input = [
                'id' => $request->id,
                'ano_pleito' => $request->ano_pleito,
                'id_legislatura' => $request->id_legislatura,
                'pleitoEspecial' => $request->pleitoEspecial,
                'dataPrimeiroTurno' => $request->dataPrimeiroTurno,
                'dataSegundoTurno' => $request->dataSegundoTurno,
            ];
            $rules = [
                'id' => 'required|integer',
                'ano_pleito' => 'required',
                'id_legislatura' => 'required|integer',
                'pleitoEspecial' => 'nullable',
                'dataPrimeiroTurno' => 'required|date',
                'dataSegundoTurno' => 'required|date',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                Alert::toast('Pleito eleitoral inválido.','error');
                return redirect()->back();
            }

            $legislatura = Legislatura::where('id', '=', $request->id_legislatura)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                Alert::toast('Legislatura inválida.','error');
                return redirect()->back();
            }

            $pleito_eleitoral->ano_pleito = $request->ano_pleito;
            $pleito_eleitoral->id_legislatura = $request->id_legislatura;
            $pleito_eleitoral->pleitoEspecial = $request->pleitoEspecial;
            $pleito_eleitoral->dataPrimeiroTurno = $request->dataPrimeiroTurno;
            $pleito_eleitoral->dataSegundoTurno = $request->dataSegundoTurno;
            $pleito_eleitoral->save();

            if ($request->id_cargo_eletivo != null){
                $id_cargo_eletivos = $request->id_cargo_eletivo;
                foreach ($id_cargo_eletivos as $id_cargo_eletivo){
                    $cargo_eletivo = CargoEletivo::where('id', '=', $id_cargo_eletivo)->where('ativo', '=', CargoEletivo::ATIVO)->first();
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
            }
            Alert::toast('Alteração realizado com sucesso','success');
            return redirect()->route('processo_legislativo.pleito_eleitoral.edit', $pleito_eleitoral->id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('PleitoEleitoral', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                Alert::toast('Pleito eleitoral inválido.','error');
                return redirect()->back();
            }

            $pleito_eleitoral->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => PleitoEleitoral::INATIVO
            ]);

            foreach ($pleito_eleitoral->cargos_eletivos_ativos() as $pleito_cargo_ativo) {
                $pleito_cargo_ativo->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => PleitoCargo::INATIVO
                ]);

            }
            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('processo_legislativo.pleito_eleitoral.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                return $this->error('Pleito eleitoral inválido. Contate o administrador do sistema!', 403);
            }

            $pleito_cargos = $pleito_eleitoral->cargos_eletivos_ativos();
            $cargos_eletivos = [];
            foreach ($pleito_cargos as $pleito_cargo) {
                $cargo_eletivo = [
                    'id' => $pleito_cargo->id_cargo_eletivo,
                    'descricao' => $pleito_cargo->cargo_eletivo->descricao
                ];
                array_push($cargos_eletivos, $cargo_eletivo);
            }

            return $this->success($cargos_eletivos);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'get');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }

    public function destroyCargoEletivo(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('PleitoEleitoral', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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

            $pleito_cargo = PleitoCargo::where('id', '=', $id)->where('ativo', '=', PleitoCargo::ATIVO)->first();
            if (!$pleito_cargo){
                Alert::toast('Cargo eletivo inválido.','error');
                return redirect()->back();
            }

            $pleito_cargo->inativadoPorUsuario = Auth::user()->id;
            $pleito_cargo->dataInativado = Carbon::now();
            $pleito_cargo->motivoInativado = $motivo;
            $pleito_cargo->ativo = 0;
            $pleito_cargo->save();

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('processo_legislativo.pleito_eleitoral.edit', $pleito_cargo->id_pleito_eleitoral);
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PleitoEleitoralController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
