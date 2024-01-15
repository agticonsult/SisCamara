<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\ErrorLog;
use App\Models\User;
use App\Services\ErrorLogService;
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
            $users = User::where('ativo', '=', User::ATIVO)->get();

            return view('audit.index', compact('audits', 'users'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AuditController', 'index');
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

            $users = User::where('ativo', '=', User::ATIVO)->get();

            return view('audit.index', compact('audits', 'users'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AuditController', 'buscar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
