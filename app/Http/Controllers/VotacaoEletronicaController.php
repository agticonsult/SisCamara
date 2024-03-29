<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotacaoEletronicaRequest;
use App\Models\AgentePolitico;
use App\Models\CargoEletivo;
use App\Models\ErrorLog;
use App\Models\Legislatura;
use App\Models\ModeloProposicao;
use App\Models\Proposicao;
use App\Models\TipoVotacao;
use App\Models\Vereador;
use App\Models\VereadorVotacao;
use App\Models\VotacaoEletronica;
use App\Services\ErrorLogService;
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

            $votacaos = VotacaoEletronica::where('ativo', '=', VotacaoEletronica::ATIVO)->get();

            return view('votacao-eletronica.index', compact('votacaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $proposicaos = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', TipoVotacao::ATIVO)->get();
            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();
            $proposicoes = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();

            return view('votacao-eletronica.create', compact('proposicaos', 'tipo_votacaos', 'legislaturas', 'proposicoes'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(VotacaoEletronicaRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereadores = AgentePolitico::leftJoin('pleito_eleitorals', 'pleito_eleitorals.id', '=', 'agente_politicos.id_pleito_eleitoral')
                ->where('pleito_eleitorals.id_legislatura', '=', $request->id_legislatura)
                ->where('agente_politicos.ativo', '=', 1)
                ->select('agente_politicos.*')
                ->get();

            if (Count($vereadores) == 0){
                return redirect()->back()->with('erro', 'Não há POLÍTICOS cadastrador para realizar a votação!');
            }

            $votacao = VotacaoEletronica::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            foreach ($vereadores as $vereador){
                VereadorVotacao::create([
                    'id_vereador' => $vereador->id,
                    'id_votacao' => $votacao->id,
                    'cadastradoPorUsuario' => Auth::user()->id
                ]);
            }

            return redirect()->route('votacao_eletronica.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function resultado($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            $vereadorVotacaos = VereadorVotacao::where('id_votacao', $id)->where('ativo', '=', 1)->get();
            $votosSim = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Sim')->where('ativo', '=', 1)->count();
            $votosNao = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Não')->where('ativo', '=', 1)->count();
            $votosAbs = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Abstenção')->where('ativo', '=', 1)->count();

            return view('votacao-eletronica.resultado', compact('votacao', 'vereadorVotacaos', 'votosSim', 'votosNao', 'votosAbs'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'show');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();
            $proposicaos = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', TipoVotacao::ATIVO)->get();

            return view('votacao-eletronica.edit', compact('votacao', 'proposicaos', 'tipo_votacaos', 'legislaturas'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'edit');
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

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação eletrônica inválida.');
            }

            $tipo_votacao = TipoVotacao::where('id', '=', $request->id_tipo_votacao)->where('ativo', '=', TipoVotacao::ATIVO)->first();
            if (!$tipo_votacao){
                return redirect()->back()->with('erro', 'Tipo de votação inválido!');
            }

            $proposicao = Proposicao::where('id', '=', $request->id_proposicao)->where('ativo', '=', Proposicao::ATIVO)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposição inválida!');
            }

            $legislatura = Legislatura::where('id', '=', $request->id_legislatura)->where('ativo', '=', Legislatura::ATIVO)->first();
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
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'update');
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

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
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
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function gerenciar($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoIniciada != 1){
                $votacao->votacaoIniciada = 1;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->save();
            }

            return view('votacao-eletronica.votacao', compact('votacao'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    //lado público
    public function indexPublico()
    {
        try{
            $votacaos = VotacaoEletronica::where('ativo', '=', VotacaoEletronica::ATIVO)->get();

            return view('votacao-eletronica.publico.indexPublico', compact('votacaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvarPublico($ex->getMessage(), 'VotacaoEletronicaController', 'indexPublico');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function resultadoPublico($id)
    {
        try {
            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            $vereadorVotacaos = VereadorVotacao::where('id_votacao','=', $id)->where('ativo', '=', 1)->get();
            $votosSim = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Sim')->where('ativo', '=', VereadorVotacao::ATIVO)->count();
            $votosNao = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Não')->where('ativo', '=', VereadorVotacao::ATIVO)->count();
            $votosAbs = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Abstenção')->where('ativo', '=', VereadorVotacao::ATIVO)->count();

            return view('votacao-eletronica.publico.resultadoPublico', compact('votacao', 'vereadorVotacaos', 'votosSim', 'votosNao', 'votosAbs'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvarPublico($ex->getMessage(), 'VotacaoEletronicaController', 'resultadoPublico');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }


}
