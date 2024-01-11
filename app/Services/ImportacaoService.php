<?php

namespace App\Services;

use App\Models\ErrorLog;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\PessoaTemp;
use App\Models\User;
use App\Models\UserTemp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ImportacaoService
{
    public static function importacaoUser($id_importacao, UserTemp $user_temp)
    {
        try {
            $resposta = array();

            //varifica se já existe um email ativo cadastrado no BD
            // $verifica_user = User::where('email', '=', $user_temp->email)
            //     ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $user_temp->cpf))
            //     ->select('id', 'email', 'cpf')
            //     ->first();
            $verifica_user = User::where(function (Builder $query) use ($user_temp) {
                return
                    $query->where('email', '=', $user_temp->email)
                        ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $user_temp->cpf));
                    })
                ->select('id', 'email', 'cpf')
                ->first();

            //existe um email cadastrado?
            if($verifica_user){
                array_push($resposta, 0);
                array_push($resposta, 'Já existe um usuário cadastrado com esse email e/ou CPF.');
                return $resposta;
            }

            $pessoaTemp = PessoaTemp::find($user_temp->id_pessoa);

            if ($pessoaTemp->id_municipio == null || $pessoaTemp->id_municipio == ""){
                array_push($resposta, 0);
                array_push($resposta, 'O município é obrigatório!');
                return $resposta;
            }

            if(!ValidadorCPFService::ehValido($user_temp->cpf)){
                array_push($resposta, 0);
                array_push($resposta, 'Este CPF é inválido.');
                return $resposta;
            }

            //nova Pessoa
            $novaPessoa = new Pessoa();
            $novaPessoa->pessoaJuridica = 0;
            $novaPessoa->nome = $pessoaTemp->nome;
            $novaPessoa->apelidoFantasia = $pessoaTemp->apelidoFantasia;
            $novaPessoa->dt_nascimento_fundacao = $pessoaTemp->dt_nascimento_fundacao;
            $novaPessoa->cep = preg_replace('/[^0-9]/', '',$pessoaTemp->cep);
            $novaPessoa->endereco = $pessoaTemp->endereco;
            $novaPessoa->bairro = $pessoaTemp->bairro;
            $novaPessoa->numero = $pessoaTemp->numero;
            $novaPessoa->complemento = $pessoaTemp->complemento;
            $novaPessoa->ponto_referencia = $pessoaTemp->ponto_referencia;
            $novaPessoa->id_municipio = $pessoaTemp->id_municipio;
            $novaPessoa->cadastradoPorUsuario = Auth::user()->id;
            $novaPessoa->ativo = 1;
            $novaPessoa->save();

            //novo Usuário
            $novoUsuario = new User();
            $novoUsuario->cpf = preg_replace('/[^0-9]/', '', $user_temp->cpf);
            $novoUsuario->email = $user_temp->email;
            $novoUsuario->telefone_celular = preg_replace('/[^0-9]/', '', $user_temp->telefone_celular);
            $novoUsuario->telefone_celular2 = preg_replace('/[^0-9]/', '', $user_temp->telefone_celular2);
            $novoUsuario->sexo = $user_temp->sexo;

            if ($user_temp->perfil->id_tipo_perfil == 2){
                $novoUsuario->lotacao = $pessoaTemp->id_municipio;
            }

            $novoUsuario->password = Hash::make('123456');
            $novoUsuario->id_pessoa = $novaPessoa->id;
            $novoUsuario->bloqueadoPorTentativa = 0;
            $novoUsuario->confirmacao_email = 1;
            $novoUsuario->ativo = 1;
            $novoUsuario->save();

            // adicionando tipo_perfil cliente ao usuário
            $perfil_user = new PerfilUser();
            $perfil_user->id_user = $novoUsuario->id;
            $perfil_user->id_tipo_perfil = $user_temp->perfil->id_tipo_perfil;
            $perfil_user->cadastradoPorUsuario = Auth::user()->id;
            $perfil_user->ativo = 1;
            $perfil_user->save();

            // adicionando perfil cliente aos perfis ativos do usuário
            $permissao = new Permissao();
            $permissao->id_user = $novoUsuario->id;
            $permissao->id_perfil = $user_temp->id_perfil;
            $permissao->cadastradoPorUsuario = Auth::user()->id;
            $permissao->ativo = 1;
            $permissao->save();

            array_push($resposta, 1);
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            array_push($resposta, 0);
            array_push($resposta, 'Contate o administrador do sistema.');
            return $resposta;
        }
    }

    // public static function importacaoEmpresa($id_importacao, EmpresaTemp $empresa_temp)
    // {
    //     try {
    //         $empresaExterno = new Empresa();
    //         $empresaExterno->nome = $empresa_temp->nome;
    //         $empresaExterno->cnpj = preg_replace('/[^0-9]/', '', $empresa_temp->cnpj);
    //         $empresaExterno->cep = preg_replace('/[^0-9]/', '', $empresa_temp->cep);
    //         $empresaExterno->endereco = $empresa_temp->endereco;
    //         $empresaExterno->numero = $empresa_temp->numero;
    //         $empresaExterno->bairro = $empresa_temp->bairro;
    //         $empresaExterno->complemento = $empresa_temp->complemento;
    //         $empresaExterno->ponto_referencia = $empresa_temp->ponto_referencia;
    //         $empresaExterno->id_user = $empresa_temp->id_user;

    //         $empresaExterno->importado = 1;
    //         $empresaExterno->id_importacao = $id_importacao;

    //         $empresaExterno->cadastradoPorUsuario = auth()->user()->id;
    //         $empresaExterno->ativo = 1;
    //         $empresaExterno->save();

    //         return 1;
    //     }
    //     catch (\Exception $ex) {
    //         return 0;
    //     }
    // }
}
