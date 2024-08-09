<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposicaoStoreRequest;
use App\Http\Requests\ProposicaoUpdateRequest;
use App\Models\Proposicao;
use App\Models\LocalizacaoProposicao;
use App\Models\ModeloProposicao;
use App\Models\StatusProposicao;
use App\Models\TextoProposicao;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProposicaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $proposicaos = Proposicao::where('ativo', '=', Proposicao::ATIVO)->get();

            return view('proposicao.index', compact('proposicaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelos = ModeloProposicao::where('ativo', '=', ModeloProposicao::ATIVO)->get();

            return view('proposicao.create', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(ProposicaoStoreRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $proposicao = Proposicao::create($request->validated() + [
                'id_status' => 1,
                'id_localizacao' => 1,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            $texto_proposicao = $request->texto_proposicao;
            $texto_proposicao_alterado = preg_replace('/\r/', '', $texto_proposicao);
            $array_texto_proposicao = explode("\n", $texto_proposicao_alterado);

            for ($i = 0; $i < Count($array_texto_proposicao); $i++){
                if ($array_texto_proposicao[$i] != ""){
                    TextoProposicao::create([
                        'ordem' => $i + 1,
                        'texto' => $array_texto_proposicao[$i],
                        'alterado' => TextoProposicao::TEXTO_NAO_ALTERADO,
                        'id_proposicao' => $proposicao->id,
                        'cadastradoPorUsuario' => Auth::user()->id
                    ]);
                }
            }
            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('proposicao.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', Proposicao::ATIVO)->first();
            if (!$proposicao){
                Alert::toast('Proposicao inválido.','error');
                return redirect()->back();
            }

            $modelos = ModeloProposicao::where('ativo', '=', ModeloProposicao::ATIVO)->get();
            $localizacaos = LocalizacaoProposicao::where('ativo', '=', LocalizacaoProposicao::ATIVO)->get();
            $statuses = StatusProposicao::where('ativo', '=', StatusProposicao::ATIVO)->get();

            return view('proposicao.edit', compact('proposicao', 'localizacaos', 'statuses'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(ProposicaoUpdateRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', Proposicao::ATIVO)->first();
            if (!$proposicao){
                Alert::toast('Proposicao inválido.','error');
                return redirect()->back();
            }

            $proposicao->update($request->validated());
            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('proposicao.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Proposicao', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', Proposicao::ATIVO)->first();
            if (!$proposicao){
                Alert::toast('Proposicao inválido.','error');
                return redirect()->back();
            }

            $proposicao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Proposicao::isUnguarded()
            ]);
            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('proposicao.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ProposicaoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
