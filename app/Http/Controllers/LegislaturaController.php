<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegislaturaRequest;
use App\Models\Legislatura;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class LegislaturaController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $legislaturas = Legislatura::retornaLegislaturasAtivas();

            return view('processo-legislativo.legislatura.index', compact('legislaturas'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'index');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function store(LegislaturaRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Cadastro') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            Legislatura::create($request->validated() + [
                'cadastradoPorUsuario' =>  Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('processo_legislativo.legislatura.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'store');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                Alert::toast('Legislatura inválida.','error');
                return redirect()->back();
            }

            return view('processo-legislativo.legislatura.edit', compact('legislatura'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'edit');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function update(LegislaturaRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Legislatura', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                Alert::toast('Legislatura inválida.','error');
                return redirect()->back();
            }

            $legislatura->update($request->validated());

            Alert::toast('Alteração realizada com sucesso','success');
            return redirect()->route('processo_legislativo.legislatura.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'update');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Legislatura', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $legislatura = Legislatura::retornaLegislaturaAtiva($id);
            if (!$legislatura){
                Alert::toast('Legislatura inválida.','error');
                return redirect()->back();
            }

            $legislatura->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Legislatura::INATIVO
            ]);

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('processo_legislativo.legislatura.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'LegislaturaController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
