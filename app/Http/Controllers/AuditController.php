<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Auditoria', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $audits = Auditoria::all();
            $users = User::where('ativo', '=', 1)->get();

            return view('audit.index', compact('audits', 'users'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AuditController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function buscar(Request $request, Auditoria $audit)
    {
        try {
            if(Auth::user()->temPermissao('Auditoria', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $this->repository = $audit;

            $audits = $this->repository->buscar($request->all());

            $users = User::where('ativo', '=', 1)->get();

            return view('audit.index', compact('audits', 'users'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AuditController";
            $erro->funcao = "buscar";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
