<?php

Namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pessoa;
use App\Models\Permissao;
use App\Models\FotoPerfil;
use App\Models\PerfilUser;
use App\Models\AgentePolitico;
use App\Models\Perfil;
use App\Models\VereadorVotacao;
use App\Utils\UploadFotoUtil;
use Illuminate\Support\Facades\Auth;

class AgentePoliticoService
{
    /*
    * Deleta logicamente um agente político e seus relacionamentos
    */
    public static function deletarAgentePolitico($request, $id)
    {
        $agente_politico = AgentePolitico::where('id_user', $id)->where('ativo', AgentePolitico::ATIVO)->first();
        $vereador_votacao = VereadorVotacao::where('id_vereador', $agente_politico->id)->where('ativo', VereadorVotacao::ATIVO)->first();
        $pessoa = Pessoa::find($agente_politico->usuario->id_pessoa);
        $usuario = User::find($agente_politico->id_user);
        $foto_perfil = FotoPerfil::where('id_user', $agente_politico->id_user)->where('ativo', FotoPerfil::ATIVO)->first();
        $perfil_user = PerfilUser::where('id_user', $agente_politico->id_user)->where('ativo', PerfilUser::ATIVO)->first();
        $permissao = Permissao::where('id_user', $agente_politico->id_user)->where('ativo', Permissao::ATIVO)->first();

        if ($vereador_votacao) {
            $vereador_votacao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => VereadorVotacao::INATIVO
            ]);
        }

        if ($agente_politico) {
            $agente_politico->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => AgentePolitico::INATIVO
            ]);
        }

        if ($pessoa) {
            $pessoa->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => Pessoa::INATIVO
            ]);
        }

        if ($usuario) {
            $usuario->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => User::INATIVO
            ]);
        }

        if ($foto_perfil) {
            $foto_perfil->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => FotoPerfil::INATIVO
            ]);
        }
        if ($perfil_user) {
            $perfil_user->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => User::INATIVO
            ]);
        }

        if ($permissao) {
            $permissao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => User::INATIVO
            ]);
        }
    }

    /*
    * Salva um novo agente político, usuário, pessoa, perfil e permissão
    */
    public static function salvarAgentePolitico($request, $pleito_cargo)
    {
        $novaPessoa = Pessoa::create($request->validated() + [
            'cadastradoPorUsuario' => Auth::user()->id,
            'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA
        ]);

        $novoUsuario = User::create($request->validated() + [
            'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
            'id_pessoa' => $novaPessoa->id,
            'confirmacao_email' => User::EMAIL_CONFIRMADO,
            'cadastroAprovado' => User::USUARIO_APROVADO,
            'aprovadoPorUsuario' => Auth::user()->id,
            'aprovadoEm' => Carbon::now()
        ]);

        if ($request->fImage) {
            UploadFotoUtil::identificadorFileUpload($request, $novoUsuario);
        }

        PerfilUser::create([
            'id_user' => $novoUsuario->id,
            'id_tipo_perfil' => Perfil::USUARIO_POLITICO,
            'cadastradoPorUsuario' => $novoUsuario->id,
        ]);

        Permissao::create([
            'id_user' => $novoUsuario->id,
            'id_perfil' => Perfil::USUARIO_POLITICO,
            'cadastradoPorUsuario' => Auth::user()->id
        ]);

        AgentePolitico::create($request->validated() + [
            'id_legislatura' => $pleito_cargo->pleito_eleitoral->id_legislatura,
            'id_user' => $novoUsuario->id,
            'cadastradoPorUsuario' => Auth::user()->id
        ]);

    }

    public static function agentePoliticos()
    {
        $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
            ->where('users.ativo', '=', 1)
            ->select('users.id', 'users.id_pessoa')
            ->orderBy('pessoas.nome', 'asc')
        ->get();

        $usuarios = array();
        foreach ($users as $user) {
            if ($user->ehAgentePolitico() == 0){
                array_push($usuarios, $user);
            }
        }

        return $usuarios;
    }
}
