<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Reparticao;
use App\Models\TipoReparticao;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReparticaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticaos = Reparticao::where('ativo', '=', Reparticao::ATIVO)->get();

            return view('reparticao.index', compact('reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.create', compact('tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao,
                'id_tipo_reparticao' => $request->id_tipo_reparticao,
            ];
            $rules = [
                'descricao' => 'required',
                'id_tipo_reparticao' => 'required|integer',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $tipo_reparticao = TipoReparticao::where('id', '=', $request->id_tipo_reparticao)->where('ativo', '=', 1)->first();
            if (!$tipo_reparticao){
                return redirect()->back()->with('erro', 'Tipo de repartição inválida.');
            }

            $reparticao = new Reparticao();
            $reparticao->descricao = $request->descricao;
            $reparticao->id_tipo_reparticao = $request->id_tipo_reparticao;
            $reparticao->cadastradoPorUsuario = Auth::user()->id;
            $reparticao->ativo = 1;
            $reparticao->save();

            return redirect()->route('reparticao.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.edit', compact('reparticao', 'tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'descricao' => $request->descricao,
                'id_tipo_reparticao' => $request->id_tipo_reparticao,
            ];
            $rules = [
                'id' => 'required|integer',
                'descricao' => 'required',
                'id_tipo_reparticao' => 'required|integer',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $tipo_reparticao = TipoReparticao::where('id', '=', $request->id_tipo_reparticao)->where('ativo', '=', 1)->first();
            if (!$tipo_reparticao){
                return redirect()->back()->with('erro', 'Tipo de repartição inválida.');
            }

            $reparticao->descricao = $request->descricao;
            $reparticao->id_tipo_reparticao = $request->id_tipo_reparticao;
            $reparticao->save();

            return redirect()->route('reparticao.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Reparticao', 'Exclusão') != 1) {
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
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
