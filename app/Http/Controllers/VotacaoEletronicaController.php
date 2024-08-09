<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotacaoEletronicaRequestStore;
use App\Http\Requests\VotacaoEletronicaUpdateRequest;
use App\Models\AgentePolitico;
use App\Models\Legislatura;
use App\Models\Proposicao;
use App\Models\TipoVotacao;
use App\Models\VereadorVotacao;
use App\Models\VotacaoEletronica;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class VotacaoEletronicaController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacaos = VotacaoEletronica::where('ativo', '=', VotacaoEletronica::ATIVO)->get();

            return view('votacao-eletronica.index', compact('votacaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $proposicaos = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', TipoVotacao::ATIVO)->get();
            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();
            $proposicoes = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();

            return view('votacao-eletronica.create', compact('proposicaos', 'tipo_votacaos', 'legislaturas', 'proposicoes'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(VotacaoEletronicaRequestStore $request)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $vereadores = AgentePolitico::leftJoin('pleito_eleitorals', 'pleito_eleitorals.id', '=', 'agente_politicos.id_pleito_eleitoral')
                ->where('pleito_eleitorals.id_legislatura', '=', $request->id_legislatura)
                ->where('agente_politicos.ativo', '=', 1)
                ->select('agente_politicos.*')
            ->get();

            if (Count($vereadores) == 0){
                Alert::toast('Não há POLÍTICOS cadastrador para realizar a votação!','error');
                return redirect()->back();
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

            Alert::toast('Cadastro realizado com sucesso','success');
            return redirect()->route('votacao_eletronica.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function resultado($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida','error');
                return redirect()->back();
            }

            $vereadorVotacaos = VereadorVotacao::where('id_votacao', $id)->where('ativo', '=', 1)->get();
            $votosSim = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Sim')->where('ativo', '=', 1)->count();
            $votosNao = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Não')->where('ativo', '=', 1)->count();
            $votosAbs = VereadorVotacao::where('id_votacao', $id)->where('voto', '=', 'Abstenção')->where('ativo', '=', 1)->count();

            return view('votacao-eletronica.resultado', compact('votacao', 'vereadorVotacaos', 'votosSim', 'votosNao', 'votosAbs'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'show');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida','error');
                return redirect()->back();
            }

            $legislaturas = Legislatura::where('ativo', '=', Legislatura::ATIVO)->get();
            $proposicaos = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', TipoVotacao::ATIVO)->get();

            return view('votacao-eletronica.edit', compact('votacao', 'proposicaos', 'tipo_votacaos', 'legislaturas'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(VotacaoEletronicaUpdateRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::findOrFail($id);
            $votacao->update($request->validated());

            Alert::toast('Alteração realizada com sucesso','success');
            return redirect()->route('votacao_eletronica.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('VotacaoEletronica', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
            }

            $votacao->inativadoPorUsuario = Auth::user()->id;
            $votacao->dataInativado = Carbon::now();
            $votacao->motivoInativado = $motivo;
            $votacao->ativo = 0;
            $votacao->save();

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('votacao_eletronica.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VotacaoEletronicaController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function gerenciar($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
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
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
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
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function resultadoPublico($id)
    {
        try {
            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
            }

            $vereadorVotacaos = VereadorVotacao::where('id_votacao','=', $id)->where('ativo', '=', 1)->get();
            $votosSim = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Sim')->where('ativo', '=', VereadorVotacao::ATIVO)->count();
            $votosNao = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Não')->where('ativo', '=', VereadorVotacao::ATIVO)->count();
            $votosAbs = VereadorVotacao::where('id_votacao','=', $id)->where('voto', '=', 'Abstenção')->where('ativo', '=', VereadorVotacao::ATIVO)->count();

            return view('votacao-eletronica.publico.resultadoPublico', compact('votacao', 'vereadorVotacaos', 'votosSim', 'votosNao', 'votosAbs'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvarPublico($ex->getMessage(), 'VotacaoEletronicaController', 'resultadoPublico');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }


}
