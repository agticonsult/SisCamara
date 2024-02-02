<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegislaturaRequest;
use App\Models\ErrorLog;
use App\Models\Legislatura;
use App\Services\ErrorLogService;
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

            $legislaturas = Legislatura::retornaLegislaturasAtivas();

            return view('processo-legislativo.legislatura.index', compact('legislaturas'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(LegislaturaRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Cadastro') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            Legislatura::create($request->validated() + [
                'cadastradoPorUsuario' =>  Auth::user()->id
            ]);

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Cadastro realizado com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            return view('processo-legislativo.legislatura.edit', compact('legislatura'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(LegislaturaRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $legislatura->update($request->validated());

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Legislatura', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $legislatura->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Legislatura::INATIVO
            ]);

            return redirect()->route('processo_legislativo.legislatura.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
