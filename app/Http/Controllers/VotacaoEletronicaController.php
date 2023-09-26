<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\CargoEletivo;
use App\Models\ErrorLog;
use App\Models\Legislatura;
use App\Models\Proposicao;
use App\Models\TipoVotacao;
use App\Models\Vereador;
use App\Models\VereadorVotacao;
use App\Models\VotacaoEletronica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VotacaoEletronicaController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacaos = VotacaoEletronica::where('ativo', '=', 1)->get();

            return view('votacao-eletronica.index', compact('votacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
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
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $proposicaos = Proposicao::where('ativo', '=', 1)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', 1)->get();
            $legislaturas = Legislatura::where('ativo', '=', 1)->get();

            return view('votacao-eletronica.create', compact('proposicaos', 'tipo_votacaos', 'legislaturas'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
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
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'data' => $request->data,
                'id_tipo_votacao' => $request->id_tipo_votacao,
                'id_proposicao' => $request->id_proposicao,
                'id_legislatura' => $request->id_legislatura
            ];
            $rules = [
                'data' => 'required|date',
                'id_tipo_votacao' => 'required|integer',
                'id_proposicao' => 'required|integer',
                'id_legislatura' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $vereadores = AgentePolitico::leftJoin('pleito_eleitorals', 'pleito_eleitorals.id', '=', 'agente_politicos.id_pleito_eleitoral')
                ->where('pleito_eleitorals.id_legislatura', '=', $votacao->id_legislatura)
                ->where('agente_politicos.ativo', '=', 1)
                ->select('agente_politicos.*')
                ->get();

            if (Count($vereadores) == 0){
                return redirect()->back()->with('erro', 'Não há vereadores cadastrador para realizar a votação!');
            }

            $tipo_votacao = TipoVotacao::where('id', '=', $request->id_tipo_votacao)->where('ativo', '=', 1)->first();
            if (!$tipo_votacao){
                return redirect()->back()->with('erro', 'Tipo de votação inválido!');
            }

            $proposicao = Proposicao::where('id', '=', $request->id_proposicao)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposição inválida!');
            }

            $legislatura = Legislatura::where('id', '=', $request->id_legislatura)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $votacao = new VotacaoEletronica();
            $votacao->data = $request->data;
            $votacao->id_tipo_votacao = $request->id_tipo_votacao;
            $votacao->id_proposicao = $request->id_proposicao;
            $votacao->id_legislatura = $request->id_legislatura;
            $votacao->cadastradoPorUsuario = Auth::user()->id;
            $votacao->ativo = 1;
            $votacao->save();

            foreach ($vereadores as $vereador){
                $vereador_votacao = new VereadorVotacao();
                $vereador_votacao->id_vereador = $vereador->id;
                $vereador_votacao->id_votacao = $votacao->id;
                $vereador_votacao->cadastradoPorUsuario = Auth::user()->id;
                $vereador_votacao->ativo = 1;
                $vereador_votacao->save();
            }

            return redirect()->route('votacao_eletronica.index')->with('success', 'Cadastro realizado com sucesso');
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
            $erro->controlador = "VotacaoEletronicaController";
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
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            $proposicaos = Proposicao::where('ativo', '=', 1)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', 1)->get();

            return view('votacao-eletronica.edit', compact('votacao', 'proposicaos', 'tipo_votacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
            $erro->funcao = "edit";
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
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'data' => $request->data,
                'id_tipo_votacao' => $request->id_tipo_votacao,
                'id_proposicao' => $request->id_proposicao,
                'id_legislatura' => $request->id_legislatura
            ];
            $rules = [
                'id' => 'required|integer',
                'data' => 'required|date',
                'id_tipo_votacao' => 'required|integer',
                'id_proposicao' => 'required|integer',
                'id_legislatura' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação eletrônica inválida.');
            }

            $tipo_votacao = TipoVotacao::where('id', '=', $request->id_tipo_votacao)->where('ativo', '=', 1)->first();
            if (!$tipo_votacao){
                return redirect()->back()->with('erro', 'Tipo de votação inválido!');
            }

            $proposicao = Proposicao::where('id', '=', $request->id_proposicao)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposição inválida!');
            }

            $legislatura = Legislatura::where('id', '=', $request->id_legislatura)->where('ativo', '=', 1)->first();
            if (!$legislatura){
                return redirect()->back()->with('erro', 'Legislatura inválida.');
            }

            $votacao->data = $request->data;
            $votacao->id_tipo_votacao = $request->id_tipo_votacao;
            $votacao->id_proposicao = $request->id_proposicao;
            $votacao->id_legislatura = $request->id_legislatura;
            $votacao->save();

            return redirect()->route('votacao_eletronica.index')->with('success', 'Alteração realizada com sucesso');
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
            $erro->controlador = "VotacaoEletronicaController";
            $erro->funcao = "update";
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
            if (Auth::user()->temPermissao('VotacaoEletronica', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação eletrônica inválida.');
            }

            $votacao->inativadoPorUsuario = Auth::user()->id;
            $votacao->dataInativado = Carbon::now();
            $votacao->motivoInativado = $motivo;
            $votacao->ativo = 0;
            $votacao->save();

            return redirect()->route('votacao_eletronica.index')->with('success', 'Exclusão realizada com sucesso.');
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
