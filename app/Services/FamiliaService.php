<?php

namespace App\Services;

use App\Models\AcessoServico;
use App\Models\Agricultor;
use App\Models\AtividadeLazer;
use App\Models\Auditoria;
use App\Models\AvaliacaoSucessaoFamiliar;
use App\Models\ComposicaoFamiliar;
use App\Models\DocFamiliar;
use App\Models\Entrevista;
use App\Models\ErrorLog;
use App\Models\FinalidadeServicoContratado;
use App\Models\FonteRenda;
use App\Models\Frequencia;
use App\Models\HistoricoEntrevista;
use App\Models\IntegracaoSocial;
use App\Models\IntegranteFamilia;
use App\Models\LocalizacaoLazer;
use App\Models\LocalizacaoServico;
use App\Models\MeioTransporte;
use App\Models\NaturezaServico;
use App\Models\OutraRenda;
use App\Models\PartAtivProd;
use App\Models\ParticipacaoOrganizacao;
use App\Models\Pessoa;
use App\Models\Pretensao;
use App\Models\ServicoAcessado;
use App\Models\ServicoContratado;
use App\Models\SucessaoFamiliar;
use App\Models\TipoMeioTransporte;
use App\Models\TipoServicoContratado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FamiliaService
{
    // Lado funcionário
    public static function composicaoFamiliarStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $resposta = [];

            $input = [
                'nome' => $dados['nome'],
                'cpf' => preg_replace('/[^0-9]/', '', $dados['cpf']),
                'dt_nascimento_fundacao' => $dados['dt_nascimento_fundacao'],
                'id_relacao_familiar' => $dados['id_relacao_familiar'],
                'id_situacao_ocupacional' => $dados['id_situacao_ocupacional'],
                'tempo_dedicado' => $dados['tempo_dedicado'],
                'demanda_doc' => $dados['demanda_doc'],
                'part_ativ_prod' => $dados['part_ativ_prod'],
            ];
            $rules = [
                'nome' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'dt_nascimento_fundacao' => 'required|max:10',
                'id_relacao_familiar' => 'required|integer|max:255',
                'id_situacao_ocupacional' => 'required|integer|max:255',
                'tempo_dedicado' => 'required|integer|max:100',
                'demanda_doc' => 'required|integer',
                'part_ativ_prod' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            if ($dados['tempo_dedicado'] < 0 || $dados['tempo_dedicado'] > 100) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tempo dedicado inválido! Informe um valor de 0 a 100.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if(!ValidadorCPFService::ehValido($dados['cpf'])) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'CPF inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            // Tem familiar na UPF?
            $temFamiliar = ComposicaoFamiliar::leftJoin('pessoas', 'pessoas.id', '=', 'composicao_familiars.id_pessoa')
                ->where('composicao_familiars.id_cliente', '=', $id_cliente)
                ->where(function (Builder $query) use ($dados) {
                        return
                            $query->where('pessoas.nome', '=', $dados['nome'])
                                ->orWhere('composicao_familiars.cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']));
                    })
                ->where('composicao_familiars.ativo', '=', 1)
                ->first();

            if ($temFamiliar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar já cadastrado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            // Tem familiar no sistema?
            $temFamiliarSistema = ComposicaoFamiliar::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))
                ->where('ativo', '=', 1)
                ->first();

            if ($temFamiliarSistema){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este CPF já está cadastrado no sistema.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['demanda_doc'] == 1) {
                $demanda_doc = 1;

                $input3 = [
                    'id_doc_familiar[]' => $dados['id_doc_familiar'],
                ];
                $rules3 = [
                    'id_doc_familiar[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_doc_familiar'] as $idf) {

                    $idf = intval($idf);
                    $temIDF = DocFamiliar::where('id', '=', $idf)->where('ativo', '=', 1)->first();

                    if (!$temIDF) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Demanda de documento do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_doc_familiar = json_encode($dados['id_doc_familiar']);
            }
            else{
                $demanda_doc = 0;
                $id_doc_familiar = null;
            }

            $part_ativ_prod_sempre = 0;
            if ($dados['part_ativ_prod'] == 1 || $dados['part_ativ_prod'] == 2) {
                $part_ativ_prod = 1;

                if ($part_ativ_prod == 1) {
                    $part_ativ_prod_sempre = 1;
                }

                $input3 = [
                    'id_part_ativ_prod[]' => $dados['id_part_ativ_prod'],
                ];
                $rules3 = [
                    'id_part_ativ_prod[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_part_ativ_prod'] as $ipap) {

                    $ipap = intval($ipap);
                    $temIPAP = PartAtivProd::where('id', '=', $ipap)->where('ativo', '=', 1)->first();

                    if (!$temIPAP) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Participação na atividade produtiva do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_part_ativ_prod = json_encode($dados['id_part_ativ_prod']);
            }
            else{
                $part_ativ_prod = 0;
                $id_part_ativ_prod = null;
            }

            $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))->first();
            if ($user) {
                $ehUsuario = 1;
                $cpf = $user->cpf;
                $id_user = $user->id;
                $id_pessoa = $user->id_pessoa;
            }
            else{
                $pessoa = new Pessoa();
                $pessoa->pessoaJuridica = 0;
                $pessoa->nome = $dados['nome'];
                $pessoa->id_municipio = $agricultor->usuario->pessoa->id_municipio;
                $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                $pessoa->cadastradoPorUsuario = Auth::user()->id;
                $pessoa->ativo = 1;
                $pessoa->save();

                $ehUsuario = 0;
                $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                $id_user = null;
                $id_pessoa = $pessoa->id;
            }

            $familiar = new ComposicaoFamiliar();
            $familiar->id_relacao_familiar = $dados['id_relacao_familiar'];
            $familiar->id_situacao_ocupacional = $dados['id_situacao_ocupacional'];
            $familiar->tempo_dedicado = $dados['tempo_dedicado'];
            $familiar->id_entrevista = $entrevista->id;
            $familiar->id_cliente = $id_cliente;
            $familiar->id_pessoa = $id_pessoa;
            $familiar->demanda_doc = $demanda_doc;
            $familiar->id_doc_familiar = $id_doc_familiar;
            $familiar->part_ativ_prod = $part_ativ_prod;
            $familiar->part_ativ_prod_sempre = $part_ativ_prod_sempre;
            $familiar->id_part_ativ_prod = $id_part_ativ_prod;
            $familiar->cpf = $cpf;
            $familiar->ehUsuario = $ehUsuario;
            $familiar->id_user = $id_user;
            $familiar->cadastradoPorUsuario = Auth::user()->id;
            $familiar->validado = 0;
            $familiar->ativo = 1;
            $familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function composicaoFamiliarUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $resposta = [];

            $input = [
                'nome' => $dados['nome'],
                'cpf' => preg_replace('/[^0-9]/', '', $dados['cpf']),
                'dt_nascimento_fundacao' => $dados['dt_nascimento_fundacao'],
                'id_relacao_familiar' => $dados['id_relacao_familiar'],
                'id_situacao_ocupacional' => $dados['id_situacao_ocupacional'],
                'tempo_dedicado' => $dados['tempo_dedicado'],
                'demanda_doc' => $dados['demanda_doc'],
                'part_ativ_prod' => $dados['part_ativ_prod'],
            ];
            $rules = [
                'nome' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'dt_nascimento_fundacao' => 'required|max:10',
                'id_relacao_familiar' => 'required|integer|max:255',
                'id_situacao_ocupacional' => 'required|integer|max:255',
                'tempo_dedicado' => 'required|integer|max:100',
                'demanda_doc' => 'required|integer',
                'part_ativ_prod' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $familiar = ComposicaoFamiliar::where('id', '=', $dados['id_familiar'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este cadastro já foi VALIDADO e NÃO pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['tempo_dedicado'] < 0 || $dados['tempo_dedicado'] > 100) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tempo dedicado inválido! Informe um valor de 0 a 100.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if(!ValidadorCPFService::ehValido($dados['cpf'])) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'CPF inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['demanda_doc'] == 1){
                $demanda_doc = 1;

                $input3 = [
                    'id_doc_familiar[]' => $dados['id_doc_familiar'],
                ];
                $rules3 = [
                    'id_doc_familiar[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_doc_familiar'] as $idf) {

                    $idf = intval($idf);
                    $temIDF = DocFamiliar::where('id', '=', $idf)->where('ativo', '=', 1)->first();

                    if (!$temIDF) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Demanda de documento do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_doc_familiar = json_encode($dados['id_doc_familiar']);
            }
            else{
                $demanda_doc = 0;
                $id_doc_familiar = null;
            }

            $part_ativ_prod_sempre = 0;
            if ($dados['part_ativ_prod'] == 1 || $dados['part_ativ_prod'] == 2){
                $part_ativ_prod = 1;

                if ($dados['part_ativ_prod'] == 1){
                    $part_ativ_prod_sempre = 1;
                }

                $input3 = [
                    'id_part_ativ_prod[]' => $dados['id_part_ativ_prod'],
                ];
                $rules3 = [
                    'id_part_ativ_prod[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_part_ativ_prod'] as $ipap) {

                    $ipap = intval($ipap);
                    $temIPAP = PartAtivProd::where('id', '=', $ipap)->where('ativo', '=', 1)->first();

                    if (!$temIPAP) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Participação na atividade produtiva do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_part_ativ_prod = json_encode($dados['id_part_ativ_prod']);
            }
            else{
                $part_ativ_prod = 0;
                $id_part_ativ_prod = null;
            }

            //dados antigos
            $ehUsuario = $familiar->ehUsuario;
            $cpf = $familiar->cpf;
            $id_user = $familiar->id_user;
            $id_pessoa = $familiar->id_pessoa;

            if ($familiar->cpf != preg_replace('/[^0-9]/', '', $dados['cpf'])) { // trocou cpf

                // Tem familiar no sistema?
                $temFamiliarSistema = ComposicaoFamiliar::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))
                    ->where('ativo', '=', 1)
                    ->first();

                if ($temFamiliarSistema){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Este CPF já está cadastrado no sistema.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }

                $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))->first();
                if ($user) {
                    $ehUsuario = 1;
                    $cpf = $user->cpf;
                    $id_user = $user->id;
                    $id_pessoa = $user->id_pessoa;
                }
                else{
                    if ($familiar->ehUsuario == 1){
                        // era usuário e agora não é mais
                        $pessoa = new Pessoa();
                        $pessoa->pessoaJuridica = 0;
                        $pessoa->nome = $dados['nome'];
                        $pessoa->id_municipio = $agricultor->usuario->pessoa->id_municipio;
                        $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                        $pessoa->cadastradoPorUsuario = Auth::user()->id;
                        $pessoa->ativo = 1;
                        $pessoa->save();
                    }
                    else{
                        // não era e continua não sendo usuário
                        $pessoa = Pessoa::find($familiar->id_pessoa);
                        $pessoa->nome = $dados['nome'];
                        $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                        $pessoa->save();
                    }

                    $ehUsuario = 0;
                    $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                    $id_user = null;
                    $id_pessoa = $pessoa->id;
                }
            }

            $familiar->id_relacao_familiar = $dados['id_relacao_familiar'];
            $familiar->id_situacao_ocupacional = $dados['id_situacao_ocupacional'];
            $familiar->tempo_dedicado = $dados['tempo_dedicado'];
            $familiar->id_pessoa = $id_pessoa;
            $familiar->demanda_doc = $demanda_doc;
            $familiar->id_doc_familiar = $id_doc_familiar;
            $familiar->part_ativ_prod = $part_ativ_prod;
            $familiar->part_ativ_prod_sempre = $part_ativ_prod_sempre;
            $familiar->id_part_ativ_prod = $id_part_ativ_prod;
            $familiar->cpf = $cpf;
            $familiar->ehUsuario = $ehUsuario;
            $familiar->id_user = $id_user;
            $familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function composicaoFamiliarDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $familiar = ComposicaoFamiliar::where('id', '=', $dados['id_familiar'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$familiar){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $familiar->inativadoPorUsuario = Auth::user()->id;
            $familiar->dataInativado = Carbon::now();
            $familiar->motivoInativado = $dados['motivo'];
            $familiar->ativo = 0;
            $familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function servicoContratadoStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo_servico' => $dados['id_tipo_servico'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade']),
                'valor_unitario' => $dados['valor_unitario']
            ];
            $rules = [
                'id_tipo_servico' => 'required|integer|max:255',
                'quantidade' => 'required|integer',
                'valor_unitario' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $tipo_servico = TipoServicoContratado::where('id', '=', $dados['id_tipo_servico'])->where('ativo', '=', 1)->first();
            if (!$tipo_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tipo de serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach($dados['id_finalidade'] as $finalidade) {

                $finalidade = intval($finalidade);
                $temFinalidade = FinalidadeServicoContratado::where('id', '=', $finalidade)->where('ativo', '=', 1)->first();

                if (!$temFinalidade) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Finalidade inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_finalidade = json_encode($dados['id_finalidade']);

            $valor_unitario = str_replace(".", "", $dados['valor_unitario']);
            $valor_unitario = str_replace(",", ".", $valor_unitario);

            $servico_contratado = new ServicoContratado();
            $servico_contratado->id_cliente = $id_cliente;
            $servico_contratado->id_entrevista = $entrevista->id;
            $servico_contratado->id_tipo_servico = $dados['id_tipo_servico'];
            $servico_contratado->id_finalidade = $id_finalidade;
            $servico_contratado->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $servico_contratado->valor_unitario = $valor_unitario;
            $servico_contratado->cadastradoPorUsuario = Auth::user()->id;
            $servico_contratado->validado = 0;
            $servico_contratado->ativo = 1;
            $servico_contratado->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico_contratado->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function servicoContratadoUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo_servico' => $dados['id_tipo_servico'],
                // 'id_finalidade' => $dados['id_finalidade'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade']),
                'valor_unitario' => $dados['valor_unitario']
            ];
            $rules = [
                'id_tipo_servico' => 'required|integer|max:255',
                // 'id_finalidade' => 'required|integer|max:255',
                'quantidade' => 'required|integer',
                'valor_unitario' => 'required'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $servico = ServicoContratado::where('id', '=', $dados['id_servico'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($servico->validado == 1){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $tipo_servico = TipoServicoContratado::where('id', '=', $dados['id_tipo_servico'])->where('ativo', '=', 1)->first();
            if (!$tipo_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tipo de serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach($dados['id_finalidade'] as $finalidade) {

                $finalidade = intval($finalidade);
                $temFinalidade = FinalidadeServicoContratado::where('id', '=', $finalidade)->where('ativo', '=', 1)->first();

                if (!$temFinalidade) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Finalidade inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_finalidade = json_encode($dados['id_finalidade']);

            $valor_unitario = str_replace(".", "", $dados['valor_unitario']);
            $valor_unitario = str_replace(",", ".", $valor_unitario);

            $servico->id_tipo_servico = $dados['id_tipo_servico'];
            $servico->id_finalidade = $id_finalidade;
            $servico->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $servico->valor_unitario = $valor_unitario;
            $servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function servicoContratadoDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{
             $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];
            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $servico = ServicoContratado::where('id', '=', $dados['id_servico'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$servico){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado já está validado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $servico->inativadoPorUsuario = Auth::user()->id;
            $servico->dataInativado = Carbon::now();
            $servico->motivoInativado = $dados['motivo'];
            $servico->ativo = 0;
            $servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function outraRendaStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'ano' => $dados['ano'],
                'renda_anual' => $dados['renda_anual']
            ];
            $rules = [
                'ano' => 'required|integer',
                'renda_anual' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_fonte_renda'] as $fonte) {

                $fonte = intval($fonte);
                $temFonte = FonteRenda::where('id', '=', $fonte)->where('ativo', '=', 1)->first();

                if (!$temFonte){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Fonte de renda inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_fonte_renda = json_encode($dados['id_fonte_renda']);

            $renda_anual = str_replace(".", "", $dados['renda_anual']);
            $renda_anual = str_replace(",", ".", $renda_anual);

            $despesas = null;
            if ($dados['despesas'] != null){
                $despesas = str_replace(".", "", $dados['despesas']);
                $despesas = str_replace(",", ".", $despesas);
            }

            $outra_renda = new OutraRenda();
            $outra_renda->id_cliente = $id_cliente;
            $outra_renda->id_entrevista = $entrevista->id;
            $outra_renda->id_fonte_renda = $id_fonte_renda;
            $outra_renda->ano = $dados['ano'];
            $outra_renda->renda_anual = $renda_anual;
            $outra_renda->despesas = $despesas;
            $outra_renda->cadastradoPorUsuario = Auth::user()->id;
            $outra_renda->validado = 0;
            $outra_renda->ativo = 1;
            $outra_renda->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function outraRendaUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'ano' => $dados['ano'],
                'renda_anual' => $dados['renda_anual']
            ];
            $rules = [
                'ano' => 'required|integer',
                'renda_anual' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $outra_renda = OutraRenda::where('id', '=', $dados['id_renda'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$outra_renda) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($outra_renda->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_fonte_renda'] as $fonte) {

                $fonte = intval($fonte);
                $temFonte = FonteRenda::where('id', '=', $fonte)->where('ativo', '=', 1)->first();

                if (!$temFonte){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Fonte de renda inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_fonte_renda = json_encode($dados['id_fonte_renda']);

            $renda_anual = str_replace(".", "", $dados['renda_anual']);
            $renda_anual = str_replace(",", ".", $renda_anual);

            $despesas = null;
            if ($dados['despesas'] != null) {
                $despesas = str_replace(".", "", $dados['despesas']);
                $despesas = str_replace(",", ".", $despesas);
            }

            $outra_renda->id_fonte_renda = $id_fonte_renda;
            $outra_renda->renda_anual = $renda_anual;
            $outra_renda->despesas = $despesas;
            $outra_renda->ano = $dados['ano'];
            $outra_renda->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function outraRendaDestroy($id_cliente, $entrevista, Array $dados)
    {
        try {
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $outra_renda = OutraRenda::where('id', '=', $dados['id_renda'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$outra_renda) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($outra_renda->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $outra_renda->inativadoPorUsuario = Auth::user()->id;
            $outra_renda->dataInativado = Carbon::now();
            $outra_renda->motivoInativado = $dados['motivo'];
            $outra_renda->ativo = 0;
            $outra_renda->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function meioTransporteStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo' => $dados['id_tipo'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade'])
            ];
            $rules = [
                'id_tipo' => 'required|integer|max:255',
                'quantidade' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $tipo = TipoMeioTransporte::where('id', '=', $dados['id_tipo'])->where('ativo', '=', 1)->first();
            if (!$tipo) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte = new MeioTransporte();
            $meio_transporte->id_cliente = $id_cliente;
            $meio_transporte->id_entrevista = $entrevista->id;
            $meio_transporte->id_tipo = $dados['id_tipo'];
            $meio_transporte->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $meio_transporte->cadastradoPorUsuario = Auth::user()->id;
            $meio_transporte->validado = 0;
            $meio_transporte->ativo = 1;
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function meioTransporteUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{

            $input = [
                'id_tipo' => $dados['id_tipo'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade'])
            ];
            $rules = [
                'id_tipo' => 'required|integer|max:255',
                'quantidade' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $meio_transporte = MeioTransporte::where('id', '=', $dados['id_mt'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$meio_transporte) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($meio_transporte->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $tipo = TipoMeioTransporte::where('id', '=', $dados['id_tipo'])->where('ativo', '=', 1)->first();
            if (!$tipo) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte->id_tipo = $dados['id_tipo'];
            $meio_transporte->quantidade = $dados['quantidade'];
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function meioTransporteDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{

            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $meio_transporte = MeioTransporte::where('id', '=', $dados['id_mt'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$meio_transporte) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($meio_transporte->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte->inativadoPorUsuario = Auth::user()->id;
            $meio_transporte->dataInativado = Carbon::now();
            $meio_transporte->motivoInativado = $dados['motivo'];
            $meio_transporte->ativo = 0;
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function acessoServicoStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_servico_acessado' => $dados['id_servico_acessado'],
                'servico_satisfatorio' => $dados['servico_satisfatorio'],
            ];
            $rules = [
                'id_servico_acessado' => 'required|integer|max:255',
                'servico_satisfatorio' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $sa = ServicoAcessado::where('id', '=', $dados['id_servico_acessado'])->where('ativo', '=', 1)->first();
            if (!$sa) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço acessado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_natureza_servico'] as $natureza) {

                $natureza = intval($natureza);
                $temNatureza = NaturezaServico::where('id', '=', $natureza)->where('ativo', '=', 1)->first();

                if (!$temNatureza) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Natureza do serviço inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_natureza_servico = json_encode($dados['id_natureza_servico']);

            foreach ($dados['id_localizacao_servico'] as $localizacao) {

                $localizacao = intval($localizacao);
                $temLocalizacao = LocalizacaoServico::where('id', '=', $localizacao)->where('ativo', '=', 1)->first();

                if (!$temLocalizacao) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Localização do serviço inválida.',
                        'codigoErro' => 403
                    ];
                     return $resposta;
                }
            }

            $id_localizacao_servico = json_encode($dados['id_localizacao_servico']);

            $acesso_servico = new AcessoServico();
            $acesso_servico->id_cliente = $id_cliente;
            $acesso_servico->id_entrevista = $entrevista->id;
            $acesso_servico->servico_satisfatorio = $dados['servico_satisfatorio'];
            $acesso_servico->id_servico_acessado = $dados['id_servico_acessado'];
            $acesso_servico->id_natureza_servico = $id_natureza_servico;
            $acesso_servico->id_localizacao_servico = $id_localizacao_servico;
            $acesso_servico->cadastradoPorUsuario = Auth::user()->id;
            $acesso_servico->validado = 0;
            $acesso_servico->ativo = 1;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function acessoServicoUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_servico_acessado' => $dados['id_servico_acessado'],
                'servico_satisfatorio' => $dados['servico_satisfatorio'],
            ];
            $rules = [
                'id_servico_acessado' => 'required|integer|max:255',
                'servico_satisfatorio' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $acesso_servico = AcessoServico::where('id', '=', $dados['id_acesso'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$acesso_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($acesso_servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este cadastro já foi VALIDADO e NÃO pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sa = ServicoAcessado::where('id', '=', $dados['id_servico_acessado'])->where('ativo', '=', 1)->first();
            if (!$sa) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço acessado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_natureza_servico'] as $natureza) {

                $natureza = intval($natureza);
                $temNatureza = NaturezaServico::where('id', '=', $natureza)->where('ativo', '=', 1)->first();

                if (!$temNatureza) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Natureza do serviço inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }
            $id_natureza_servico = json_encode($dados['id_natureza_servico']);

            foreach ($dados['id_localizacao_servico'] as $localizacao) {

                $localizacao = intval($localizacao);
                $temLocalizacao = LocalizacaoServico::where('id', '=', $localizacao)->where('ativo', '=', 1)->first();

                if (!$temLocalizacao) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Localização do serviço inválida.',
                        'codigoErro' => 403
                    ];
                     return $resposta;
                }
            }

            $id_localizacao_servico = json_encode($dados['id_localizacao_servico']);

            $acesso_servico->servico_satisfatorio = $dados['servico_satisfatorio'];
            $acesso_servico->id_servico_acessado = $dados['id_servico_acessado'];
            $acesso_servico->id_natureza_servico = $id_natureza_servico;
            $acesso_servico->id_localizacao_servico = $id_localizacao_servico;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function acessoServicoDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $acesso_servico = AcessoServico::where('id', '=', $dados['id_acesso'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$acesso_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($acesso_servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $acesso_servico->inativadoPorUsuario = Auth::user()->id;
            $acesso_servico->dataInativado = Carbon::now();
            $acesso_servico->motivoInativado = $dados['motivo'];
            $acesso_servico->ativo = 0;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function integracaoSocialStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_integrante' => $dados['id_integrante'],
                'id_frequencia' => $dados['id_frequencia'],
            ];
            $rules = [
                'id_integrante' => 'required|integer|max:255',
                'id_frequencia' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_participacao_org'] as $id_partic) {

                $id_partic = intval($id_partic);
                $participacao_org = ParticipacaoOrganizacao::where('id', '=', $id_partic)->where('ativo', '=', 1)->first();

                if (!$participacao_org){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Participação na organização inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_participacao_org = json_encode($dados['id_participacao_org']);

            $frequencia = Frequencia::where('id', '=', $dados['id_frequencia'])->where('ativo', '=', 1)->first();
            if (!$frequencia) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Frequência na organização inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integrante = IntegranteFamilia::where('id', '=', $dados['id_integrante'])->where('ativo', '=', 1)->first();
            if (!$integrante) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integrante familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }


            $integracao_social = new IntegracaoSocial();
            $integracao_social->id_cliente = $id_cliente;
            $integracao_social->id_entrevista = $entrevista->id;
            $integracao_social->id_participacao_org = $id_participacao_org;
            $integracao_social->id_frequencia = $dados['id_frequencia'];
            $integracao_social->id_integrante = $dados['id_integrante'];
            $integracao_social->funcao = $dados['funcao'];
            $integracao_social->cadastradoPorUsuario = Auth::user()->id;
            $integracao_social->validado = 0;
            $integracao_social->ativo = 1;
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function integracaoSocialUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{

            $input = [
                'id_integrante' => $dados['id_integrante'],
                'id_frequencia' => $dados['id_frequencia'],
            ];

            $rules = [
                'id_integrante' => 'required|integer|max:255',
                'id_frequencia' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_participacao_org'] as $id_partic) {

                $id_partic = intval($id_partic);
                $participacao_org = ParticipacaoOrganizacao::where('id', '=', $id_partic)->where('ativo', '=', 1)->first();

                if (!$participacao_org){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Participação na organização inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_participacao_org = json_encode($dados['id_participacao_org']);

            $frequencia = Frequencia::where('id', '=', $dados['id_frequencia'])->where('ativo', '=', 1)->first();
            if (!$frequencia) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Frequência na organização inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integrante = IntegranteFamilia::where('id', '=', $dados['id_integrante'])->where('ativo', '=', 1)->first();
            if (!$integrante) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integrante familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integracao_social = IntegracaoSocial::where('id', '=', $dados['id_integracao'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$integracao_social) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($integracao_social->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integracao_social->id_participacao_org = $id_participacao_org;
            $integracao_social->id_frequencia = $dados['id_frequencia'];
            $integracao_social->id_integrante = $dados['id_integrante'];
            $integracao_social->funcao = $dados['funcao'];
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração de cadastro realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;


        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }

    }
    public static function integracaoSocialDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{

            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $integracao_social = IntegracaoSocial::where('id', '=', $dados['id_integracao'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$integracao_social) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($integracao_social->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integracao_social->inativadoPorUsuario = Auth::user()->id;
            $integracao_social->dataInativado = Carbon::now();
            $integracao_social->motivoInativado = $dados['motivo'];
            $integracao_social->ativo = 0;
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function sucessaoFamiliarStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'numero_filho' => $dados['numero_filho'],
                'id_avaliacao' => $dados['id_avaliacao'],
                'id_pretensao' => $dados['id_pretensao'],
            ];
            $rules = [
                'numero_filho' => 'required|integer',
                'id_avaliacao' => 'required|integer',
                'id_pretensao' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $pretensao = Pretensao::where('id', '=', $dados['id_pretensao'])->where('ativo', '=', 1)->first();
            if (!$pretensao) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Pretensão inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $asf = AvaliacaoSucessaoFamiliar::where('id', '=', $dados['id_avaliacao'])->where('ativo', '=', 1)->first();
            if (!$asf) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Avaliação inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar = new SucessaoFamiliar();
            $sucessao_familiar->numero_filho = $dados['numero_filho'];
            $sucessao_familiar->id_avaliacao = $dados['id_avaliacao'];
            $sucessao_familiar->id_pretensao = $dados['id_pretensao'];
            $sucessao_familiar->id_cliente = $id_cliente;
            $sucessao_familiar->id_entrevista = $entrevista->id;
            $sucessao_familiar->cadastradoPorUsuario = Auth::user()->id;
            $sucessao_familiar->validado = 0;
            $sucessao_familiar->ativo = 1;
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function sucessaoFamiliarUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'numero_filho' => $dados['numero_filho'],
                'id_avaliacao' => $dados['id_avaliacao'],
                'id_pretensao' => $dados['id_pretensao'],
            ];
            $rules = [
                'numero_filho' => 'required|integer',
                'id_avaliacao' => 'required|integer',
                'id_pretensao' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $sucessao_familiar = SucessaoFamiliar::where('id', '=', $dados['id_sf'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$sucessao_familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($sucessao_familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $pretensao = Pretensao::where('id', '=', $dados['id_pretensao'])->where('ativo', '=', 1)->first();
            if (!$pretensao) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Pretensão inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $asf = AvaliacaoSucessaoFamiliar::where('id', '=', $dados['id_avaliacao'])->where('ativo', '=', 1)->first();
            if (!$asf) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Avaliação inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar->numero_filho = $dados['numero_filho'];
            $sucessao_familiar->id_avaliacao = $dados['id_avaliacao'];
            $sucessao_familiar->id_pretensao = $dados['id_pretensao'];
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro alterado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function sucessaoFamiliarDestroy($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $sucessao_familiar = SucessaoFamiliar::where('id', '=', $dados['id_sf'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$sucessao_familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($sucessao_familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar->inativadoPorUsuario = Auth::user()->id;
            $sucessao_familiar->dataInativado = Carbon::now();
            $sucessao_familiar->motivoInativado = $dados['motivo'];
            $sucessao_familiar->ativo = 0;
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function atividadeLazerStore($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_tipo_atividade' => $dados['id_tipo_atividade'],
                'id_frequencia' => $dados['id_frequencia'],
                'tempo' => preg_replace('/[^0-9]/', '', $dados['tempo'])
            ];
            $rules = [
                'id_tipo_atividade' => 'required|integer',
                'id_frequencia' => 'required|integer',
                'tempo' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_localizacao'] as $local) {

                $local = intval($local);
                $temlocal = LocalizacaoLazer::where('id', '=', $local)->where('ativo', '=', 1)->first();

                if (!$temlocal) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Onde realiza as atividades está inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_localizacao = json_encode($dados['id_localizacao']);

            $atividade_lazer = new AtividadeLazer();
            $atividade_lazer->id_cliente = $id_cliente;
            $atividade_lazer->id_entrevista = $entrevista->id;
            $atividade_lazer->id_localizacao = $id_localizacao;
            $atividade_lazer->tempo = $dados['tempo'];
            $atividade_lazer->id_frequencia = $dados['id_frequencia'];
            $atividade_lazer->id_tipo_atividade = $dados['id_tipo_atividade'];
            $atividade_lazer->cadastradoPorUsuario = Auth::user()->id;
            $atividade_lazer->validado = 0;
            $atividade_lazer->ativo = 1;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function atividadeLazerUpdate($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_tipo_atividade' => $dados['id_tipo_atividade'],
                'id_frequencia' => $dados['id_frequencia'],
                'tempo' => preg_replace('/[^0-9]/', '', $dados['tempo'])
            ];
            $rules = [
                'id_tipo_atividade' => 'required|integer',
                'id_frequencia' => 'required|integer',
                'tempo' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $atividade_lazer = AtividadeLazer::where('id', '=', $dados['id_al'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$atividade_lazer) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($atividade_lazer->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_localizacao'] as $local) {

                $local = intval($local);
                $temlocal = LocalizacaoLazer::where('id', '=', $local)->where('ativo', '=', 1)->first();

                if (!$temlocal) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Onde realiza as atividades está inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_localizacao = json_encode($dados['id_localizacao']);

            $atividade_lazer->id_cliente = $id_cliente;
            $atividade_lazer->id_entrevista = $entrevista->id;
            $atividade_lazer->id_localizacao = $id_localizacao;
            $atividade_lazer->tempo = $dados['tempo'];
            $atividade_lazer->id_frequencia = $dados['id_frequencia'];
            $atividade_lazer->id_tipo_atividade = $dados['id_tipo_atividade'];
            $atividade_lazer->cadastradoPorUsuario = Auth::user()->id;
            $atividade_lazer->validado = 0;
            $atividade_lazer->ativo = 1;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function atividadeLazerDestroy($id_cliente, $entrevista, Array $dados)
    {
        try {
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $atividade_lazer = AtividadeLazer::where('id', '=', $dados['id_al'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$atividade_lazer) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($atividade_lazer->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $atividade_lazer->inativadoPorUsuario = Auth::user()->id;
            $atividade_lazer->dataInativado = Carbon::now();
            $atividade_lazer->motivoInativado = $dados['motivo'];
            $atividade_lazer->ativo = 0;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    // Lado cliente
    public static function composicaoFamiliarStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $resposta = [];

            $input = [
                'nome' => $dados['nome'],
                'cpf' => preg_replace('/[^0-9]/', '', $dados['cpf']),
                'dt_nascimento_fundacao' => $dados['dt_nascimento_fundacao'],
                'id_relacao_familiar' => $dados['id_relacao_familiar'],
                'id_situacao_ocupacional' => $dados['id_situacao_ocupacional'],
                'tempo_dedicado' => $dados['tempo_dedicado'],
                'demanda_doc' => $dados['demanda_doc'],
                'part_ativ_prod' => $dados['part_ativ_prod'],
            ];
            $rules = [
                'nome' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'dt_nascimento_fundacao' => 'required|max:10',
                'id_relacao_familiar' => 'required|integer|max:255',
                'id_situacao_ocupacional' => 'required|integer|max:255',
                'tempo_dedicado' => 'required|integer|max:100',
                'demanda_doc' => 'required|integer',
                'part_ativ_prod' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            if ($dados['tempo_dedicado'] < 0 || $dados['tempo_dedicado'] > 100) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tempo dedicado inválido! Informe um valor de 0 a 100.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if(!ValidadorCPFService::ehValido($dados['cpf'])) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'CPF inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            // Tem familiar na UPF?
            $temFamiliar = ComposicaoFamiliar::leftJoin('pessoas', 'pessoas.id', '=', 'composicao_familiars.id_pessoa')
                ->where('composicao_familiars.id_cliente', '=', $id_cliente)
                ->where(function (Builder $query) use ($dados) {
                        return
                            $query->where('pessoas.nome', '=', $dados['nome'])
                                ->orWhere('composicao_familiars.cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']));
                    })
                ->where('composicao_familiars.ativo', '=', 1)
                ->first();

            if ($temFamiliar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar já cadastrado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            // Tem familiar no sistema?
            $temFamiliarSistema = ComposicaoFamiliar::where('cpf', '=', preg_replace('/[^0-9]/', '', $$dados['cpf']))
                ->where('ativo', '=', 1)
                ->first();

            if ($temFamiliarSistema){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este CPF já está cadastrado no sistema.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['demanda_doc'] == 1) {
                $demanda_doc = 1;

                $input3 = [
                    'id_doc_familiar[]' => $dados['id_doc_familiar'],
                ];
                $rules3 = [
                    'id_doc_familiar[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_doc_familiar'] as $idf) {

                    $idf = intval($idf);
                    $temIDF = DocFamiliar::where('id', '=', $idf)->where('ativo', '=', 1)->first();

                    if (!$temIDF) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Demanda de documento do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_doc_familiar = json_encode($dados['id_doc_familiar']);
            }
            else{
                $demanda_doc = 0;
                $id_doc_familiar = null;
            }

            $part_ativ_prod_sempre = 0;
            if ($dados['part_ativ_prod'] == 1 || $dados['part_ativ_prod'] == 2) {
                $part_ativ_prod = 1;

                if ($part_ativ_prod == 1) {
                    $part_ativ_prod_sempre = 1;
                }

                $input3 = [
                    'id_part_ativ_prod[]' => $dados['id_part_ativ_prod'],
                ];
                $rules3 = [
                    'id_part_ativ_prod[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_part_ativ_prod'] as $ipap) {

                    $ipap = intval($ipap);
                    $temIPAP = PartAtivProd::where('id', '=', $ipap)->where('ativo', '=', 1)->first();

                    if (!$temIPAP) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Participação na atividade produtiva do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_part_ativ_prod = json_encode($dados['id_part_ativ_prod']);
            }
            else{
                $part_ativ_prod = 0;
                $id_part_ativ_prod = null;
            }

            $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))->first();
            if ($user) {
                $ehUsuario = 1;
                $cpf = $user->cpf;
                $id_user = $user->id;
                $id_pessoa = $user->id_pessoa;
            }
            else{
                $pessoa = new Pessoa();
                $pessoa->pessoaJuridica = 0;
                $pessoa->nome = $dados['nome'];
                $pessoa->id_municipio = $agricultor->usuario->pessoa->id_municipio;
                $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                $pessoa->cadastradoPorUsuario = Auth::user()->id;
                $pessoa->ativo = 1;
                $pessoa->save();

                $ehUsuario = 0;
                $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                $id_user = null;
                $id_pessoa = $pessoa->id;
            }

            $familiar = new ComposicaoFamiliar();
            $familiar->id_relacao_familiar = $dados['id_relacao_familiar'];
            $familiar->id_situacao_ocupacional = $dados['id_situacao_ocupacional'];
            $familiar->tempo_dedicado = $dados['tempo_dedicado'];
            $familiar->id_entrevista = $entrevista->id;
            $familiar->id_cliente = $id_cliente;
            $familiar->id_pessoa = $id_pessoa;
            $familiar->demanda_doc = $demanda_doc;
            $familiar->id_doc_familiar = $id_doc_familiar;
            $familiar->part_ativ_prod = $part_ativ_prod;
            $familiar->part_ativ_prod_sempre = $part_ativ_prod_sempre;
            $familiar->id_part_ativ_prod = $id_part_ativ_prod;
            $familiar->cpf = $cpf;
            $familiar->ehUsuario = $ehUsuario;
            $familiar->id_user = $id_user;
            $familiar->cadastradoPorUsuario = Auth::user()->id;
            $familiar->validado = 0;
            $familiar->ativo = 1;
            $familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function composicaoFamiliarUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $resposta = [];

            $input = [
                'nome' => $dados['nome'],
                'cpf' => preg_replace('/[^0-9]/', '', $dados['cpf']),
                'dt_nascimento_fundacao' => $dados['dt_nascimento_fundacao'],
                'id_relacao_familiar' => $dados['id_relacao_familiar'],
                'id_situacao_ocupacional' => $dados['id_situacao_ocupacional'],
                'tempo_dedicado' => $dados['tempo_dedicado'],
                'demanda_doc' => $dados['demanda_doc'],
                'part_ativ_prod' => $dados['part_ativ_prod'],
            ];
            $rules = [
                'nome' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'dt_nascimento_fundacao' => 'required|max:10',
                'id_relacao_familiar' => 'required|integer|max:255',
                'id_situacao_ocupacional' => 'required|integer|max:255',
                'tempo_dedicado' => 'required|integer|max:100',
                'demanda_doc' => 'required|integer',
                'part_ativ_prod' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $familiar = ComposicaoFamiliar::where('id', '=', $dados['id_familiar'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este cadastro já foi VALIDADO e NÃO pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['tempo_dedicado'] < 0 || $dados['tempo_dedicado'] > 100) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tempo dedicado inválido! Informe um valor de 0 a 100.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if(!ValidadorCPFService::ehValido($dados['cpf'])) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'CPF inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($dados['demanda_doc'] == 1){
                $demanda_doc = 1;

                $input3 = [
                    'id_doc_familiar[]' => $dados['id_doc_familiar'],
                ];
                $rules3 = [
                    'id_doc_familiar[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_doc_familiar'] as $idf) {

                    $idf = intval($idf);
                    $temIDF = DocFamiliar::where('id', '=', $idf)->where('ativo', '=', 1)->first();

                    if (!$temIDF) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Demanda de documento do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_doc_familiar = json_encode($dados['id_doc_familiar']);
            }
            else{
                $demanda_doc = 0;
                $id_doc_familiar = null;
            }

            $part_ativ_prod_sempre = 0;
            if ($dados['part_ativ_prod'] == 1 || $dados['part_ativ_prod'] == 2){
                $part_ativ_prod = 1;

                if ($dados['part_ativ_prod'] == 1){
                    $part_ativ_prod_sempre = 1;
                }

                $input3 = [
                    'id_part_ativ_prod[]' => $dados['id_part_ativ_prod'],
                ];
                $rules3 = [
                    'id_part_ativ_prod[]' => 'required',
                ];

                $validar3 = Validator::make($input3, $rules3);
                $validar3->validate();

                foreach ($dados['id_part_ativ_prod'] as $ipap) {

                    $ipap = intval($ipap);
                    $temIPAP = PartAtivProd::where('id', '=', $ipap)->where('ativo', '=', 1)->first();

                    if (!$temIPAP) {
                        $resposta = [
                            'success' => 0,
                            'msg' => 'Participação na atividade produtiva do familiar inválida.',
                            'codigoErro' => 403
                        ];
                        return $resposta;
                    }
                }

                $id_part_ativ_prod = json_encode($dados['id_part_ativ_prod']);
            }
            else{
                $part_ativ_prod = 0;
                $id_part_ativ_prod = null;
            }

            //dados antigos
            $ehUsuario = $familiar->ehUsuario;
            $cpf = $familiar->cpf;
            $id_user = $familiar->id_user;
            $id_pessoa = $familiar->id_pessoa;

            if ($familiar->cpf != preg_replace('/[^0-9]/', '', $dados['cpf'])) { // trocou cpf

                // Tem familiar no sistema?
                $temFamiliarSistema = ComposicaoFamiliar::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))
                    ->where('ativo', '=', 1)
                    ->first();

                if ($temFamiliarSistema){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Este CPF já está cadastrado no sistema.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }

                $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $dados['cpf']))->first();
                if ($user) {
                    $ehUsuario = 1;
                    $cpf = $user->cpf;
                    $id_user = $user->id;
                    $id_pessoa = $user->id_pessoa;
                }
                else{
                    if ($familiar->ehUsuario == 1){
                        // era usuário e agora não é mais
                        $pessoa = new Pessoa();
                        $pessoa->pessoaJuridica = 0;
                        $pessoa->nome = $dados['nome'];
                        $pessoa->id_municipio = $agricultor->usuario->pessoa->id_municipio;
                        $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                        $pessoa->cadastradoPorUsuario = Auth::user()->id;
                        $pessoa->ativo = 1;
                        $pessoa->save();
                    }
                    else{
                        // não era e continua não sendo usuário
                        $pessoa = Pessoa::find($familiar->id_pessoa);
                        $pessoa->nome = $dados['nome'];
                        $pessoa->dt_nascimento_fundacao = $dados['dt_nascimento_fundacao'];
                        $pessoa->save();
                    }

                    $ehUsuario = 0;
                    $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
                    $id_user = null;
                    $id_pessoa = $pessoa->id;
                }
            }

            $familiar->id_relacao_familiar = $dados['id_relacao_familiar'];
            $familiar->id_situacao_ocupacional = $dados['id_situacao_ocupacional'];
            $familiar->tempo_dedicado = $dados['tempo_dedicado'];
            $familiar->id_pessoa = $id_pessoa;
            $familiar->demanda_doc = $demanda_doc;
            $familiar->id_doc_familiar = $id_doc_familiar;
            $familiar->part_ativ_prod = $part_ativ_prod;
            $familiar->part_ativ_prod_sempre = $part_ativ_prod_sempre;
            $familiar->id_part_ativ_prod = $id_part_ativ_prod;
            $familiar->cpf = $cpf;
            $familiar->ehUsuario = $ehUsuario;
            $familiar->id_user = $id_user;
            $familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function composicaoFamiliarDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $familiar = ComposicaoFamiliar::where('id', '=', $dados['id_familiar'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$familiar){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Familiar inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $familiar->inativadoPorUsuario = Auth::user()->id;
            $familiar->dataInativado = Carbon::now();
            $familiar->motivoInativado = $dados['motivo'];
            $familiar->ativo = 0;
            $familiar->save();


            $audit = Auditoria::where('auditable_type', '=', "App\Models\ComposicaoFamiliar")
                ->where('auditable_id', '=', $familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function servicoContratadoStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo_servico' => $dados['id_tipo_servico'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade']),
                'valor_unitario' => $dados['valor_unitario']
            ];
            $rules = [
                'id_tipo_servico' => 'required|integer|max:255',
                'quantidade' => 'required|integer',
                'valor_unitario' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $tipo_servico = TipoServicoContratado::where('id', '=', $dados['id_tipo_servico'])->where('ativo', '=', 1)->first();
            if (!$tipo_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tipo de serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach($dados['id_finalidade'] as $finalidade) {

                $finalidade = intval($finalidade);
                $temFinalidade = FinalidadeServicoContratado::where('id', '=', $finalidade)->where('ativo', '=', 1)->first();

                if (!$temFinalidade) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Finalidade inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_finalidade = json_encode($dados['id_finalidade']);

            $valor_unitario = str_replace(".", "", $dados['valor_unitario']);
            $valor_unitario = str_replace(",", ".", $valor_unitario);

            $servico_contratado = new ServicoContratado();
            $servico_contratado->id_cliente = $id_cliente;
            $servico_contratado->id_entrevista = $entrevista->id;
            $servico_contratado->id_tipo_servico = $dados['id_tipo_servico'];
            $servico_contratado->id_finalidade = $id_finalidade;
            $servico_contratado->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $servico_contratado->valor_unitario = $valor_unitario;
            $servico_contratado->cadastradoPorUsuario = Auth::user()->id;
            $servico_contratado->validado = 0;
            $servico_contratado->ativo = 1;
            $servico_contratado->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico_contratado->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function servicoContratadoUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo_servico' => $dados['id_tipo_servico'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade']),
                'valor_unitario' => $dados['valor_unitario']
            ];
            $rules = [
                'id_tipo_servico' => 'required|integer|max:255',
                'quantidade' => 'required|integer',
                'valor_unitario' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $servico = ServicoContratado::where('id', '=', $dados['id_servico'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($servico->validado == 1){
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $tipo_servico = TipoServicoContratado::where('id', '=', $dados['id_tipo_servico'])->where('ativo', '=', 1)->first();
            if (!$tipo_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Tipo de serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach($dados['id_finalidade'] as $finalidade) {

                $finalidade = intval($finalidade);
                $temFinalidade = FinalidadeServicoContratado::where('id', '=', $finalidade)->where('ativo', '=', 1)->first();

                if (!$temFinalidade) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Finalidade inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_finalidade = json_encode($dados['id_finalidade']);

            $valor_unitario = str_replace(".", "", $dados['valor_unitario']);
            $valor_unitario = str_replace(",", ".", $valor_unitario);

            $servico->id_tipo_servico = $dados['id_tipo_servico'];
            $servico->id_finalidade = $id_finalidade;
            $servico->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $servico->valor_unitario = $valor_unitario;
            $servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function servicoContratadoDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
             $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];
            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $servico = ServicoContratado::where('id', '=', $dados['id_servico'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço contratado já está validado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $servico->inativadoPorUsuario = Auth::user()->id;
            $servico->dataInativado = Carbon::now();
            $servico->motivoInativado = $dados['motivo'];
            $servico->ativo = 0;
            $servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\ServicoContratado")
                ->where('auditable_id', '=', $servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function outraRendaStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'ano' => $dados['ano'],
                'renda_anual' => $dados['renda_anual']
            ];
            $rules = [
                'ano' => 'required|integer',
                'renda_anual' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_fonte_renda'] as $fonte) {

                $fonte = intval($fonte);
                $temFonte = FonteRenda::where('id', '=', $fonte)->where('ativo', '=', 1)->first();

                if (!$temFonte){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Fonte de renda inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_fonte_renda = json_encode($dados['id_fonte_renda']);

            $renda_anual = str_replace(".", "", $dados['renda_anual']);
            $renda_anual = str_replace(",", ".", $renda_anual);

            $despesas = null;
            if ($dados['despesas'] != null) {
                $despesas = str_replace(".", "", $dados['despesas']);
                $despesas = str_replace(",", ".", $despesas);
            }

            $outra_renda = new OutraRenda();
            $outra_renda->id_cliente = $id_cliente;
            $outra_renda->id_entrevista = $entrevista->id;
            $outra_renda->id_fonte_renda = $id_fonte_renda;
            $outra_renda->ano = $dados['ano'];
            $outra_renda->renda_anual = $renda_anual;
            $outra_renda->despesas = $despesas;
            $outra_renda->cadastradoPorUsuario = Auth::user()->id;
            $outra_renda->validado = 0;
            $outra_renda->ativo = 1;
            $outra_renda->save();


            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function outraRendaUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'ano' => $dados['ano'],
                'renda_anual' => $dados['renda_anual']
            ];
            $rules = [
                'ano' => 'required|integer',
                'renda_anual' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $outra_renda = OutraRenda::where('id', '=', $dados['id_renda'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$outra_renda) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($outra_renda->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_fonte_renda'] as $fonte) {

                $fonte = intval($fonte);
                $temFonte = FonteRenda::where('id', '=', $fonte)->where('ativo', '=', 1)->first();

                if (!$temFonte){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Fonte de renda inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_fonte_renda = json_encode($dados['id_fonte_renda']);

            $renda_anual = str_replace(".", "", $dados['renda_anual']);
            $renda_anual = str_replace(",", ".", $renda_anual);

            $despesas = null;
            if ($dados['despesas'] != null){
                $despesas = str_replace(".", "", $dados['despesas']);
                $despesas = str_replace(",", ".", $despesas);
            }

            $outra_renda->id_fonte_renda = $id_fonte_renda;
            $outra_renda->renda_anual = $renda_anual;
            $outra_renda->despesas = $despesas;
            $outra_renda->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function outraRendaDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try {
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $outra_renda = OutraRenda::where('id', '=', $dados['id_renda'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$outra_renda) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($outra_renda->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Outra renda já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $outra_renda->inativadoPorUsuario = Auth::user()->id;
            $outra_renda->dataInativado = Carbon::now();
            $outra_renda->motivoInativado = $dados['motivo'];
            $outra_renda->ativo = 0;
            $outra_renda->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\OutraRenda")
                ->where('auditable_id', '=', $outra_renda->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function meioTransporteStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo' => $dados['id_tipo'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade'])
            ];
            $rules = [
                'id_tipo' => 'required|integer|max:255',
                'quantidade' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $tipo = TipoMeioTransporte::where('id', '=', $dados['id_tipo'])->where('ativo', '=', 1)->first();
            if (!$tipo) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte = new MeioTransporte();
            $meio_transporte->id_cliente = $id_cliente;
            $meio_transporte->id_entrevista = $entrevista->id;
            $meio_transporte->id_tipo = $dados['id_tipo'];
            $meio_transporte->quantidade = preg_replace('/[.-]/i', '', $dados['quantidade']);
            $meio_transporte->cadastradoPorUsuario = Auth::user()->id;
            $meio_transporte->validado = 0;
            $meio_transporte->ativo = 1;
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function meioTransporteUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_tipo' => $dados['id_tipo'],
                'quantidade' => preg_replace('/[.-]/i', '', $dados['quantidade'])
            ];
            $rules = [
                'id_tipo' => 'required|integer|max:255',
                'quantidade' => 'required|integer'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $meio_transporte = MeioTransporte::where('id', '=', $dados['id_mt'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$meio_transporte) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($meio_transporte->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $tipo = TipoMeioTransporte::where('id', '=', $dados['id_tipo'])->where('ativo', '=', 1)->first();
            if (!$tipo) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte->id_tipo = $dados['id_tipo'];
            $meio_transporte->quantidade = $dados['quantidade'];
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function meioTransporteDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $meio_transporte = MeioTransporte::where('id', '=', $dados['id_mt'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$meio_transporte) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($meio_transporte->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Meio de transporte já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $meio_transporte->inativadoPorUsuario = Auth::user()->id;
            $meio_transporte->dataInativado = Carbon::now();
            $meio_transporte->motivoInativado = $dados['motivo'];
            $meio_transporte->ativo = 0;
            $meio_transporte->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\MeioTransporte")
                ->where('auditable_id', '=', $meio_transporte->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function acessoServicoStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_servico_acessado' => $dados['id_servico_acessado'],
                'servico_satisfatorio' => $dados['servico_satisfatorio'],
            ];
            $rules = [
                'id_servico_acessado' => 'required|integer|max:255',
                'servico_satisfatorio' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $sa = ServicoAcessado::where('id', '=', $dados['id_servico_acessado'])->where('ativo', '=', 1)->first();
            if (!$sa) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço acessado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_natureza_servico'] as $natureza) {

                $natureza = intval($natureza);
                $temNatureza = NaturezaServico::where('id', '=', $natureza)->where('ativo', '=', 1)->first();

                if (!$temNatureza) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Natureza do serviço inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_natureza_servico = json_encode($dados['id_natureza_servico']);

            foreach ($dados['id_localizacao_servico'] as $localizacao) {

                $localizacao = intval($localizacao);
                $temLocalizacao = LocalizacaoServico::where('id', '=', $localizacao)->where('ativo', '=', 1)->first();

                if (!$temLocalizacao) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Localização do serviço inválida.',
                        'codigoErro' => 403
                    ];
                     return $resposta;
                }
            }

            $id_localizacao_servico = json_encode($dados['id_localizacao_servico']);

            $acesso_servico = new AcessoServico();
            $acesso_servico->id_cliente = $id_cliente;
            $acesso_servico->id_entrevista = $entrevista->id;
            $acesso_servico->servico_satisfatorio = $dados['servico_satisfatorio'];
            $acesso_servico->id_servico_acessado = $dados['id_servico_acessado'];
            $acesso_servico->id_natureza_servico = $id_natureza_servico;
            $acesso_servico->id_localizacao_servico = $id_localizacao_servico;
            $acesso_servico->cadastradoPorUsuario = Auth::user()->id;
            $acesso_servico->validado = 0;
            $acesso_servico->ativo = 1;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastrado realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function acessoServicoUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_servico_acessado' => $dados['id_servico_acessado'],
                'servico_satisfatorio' => $dados['servico_satisfatorio'],
            ];
            $rules = [
                'id_servico_acessado' => 'required|integer|max:255',
                'servico_satisfatorio' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $acesso_servico = AcessoServico::where('id', '=', $dados['id_acesso'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$acesso_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($acesso_servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Este cadastro já foi VALIDADO e NÃO pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sa = ServicoAcessado::where('id', '=', $dados['id_servico_acessado'])->where('ativo', '=', 1)->first();
            if (!$sa) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Serviço acessado inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_natureza_servico'] as $natureza) {

                $natureza = intval($natureza);
                $temNatureza = NaturezaServico::where('id', '=', $natureza)->where('ativo', '=', 1)->first();

                if (!$temNatureza) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Natureza do serviço inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }
            $id_natureza_servico = json_encode($dados['id_natureza_servico']);

            foreach ($dados['id_localizacao_servico'] as $localizacao) {

                $localizacao = intval($localizacao);
                $temLocalizacao = LocalizacaoServico::where('id', '=', $localizacao)->where('ativo', '=', 1)->first();

                if (!$temLocalizacao) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Localização do serviço inválida.',
                        'codigoErro' => 403
                    ];
                     return $resposta;
                }
            }

            $id_localizacao_servico = json_encode($dados['id_localizacao_servico']);

            $acesso_servico->servico_satisfatorio = $dados['servico_satisfatorio'];
            $acesso_servico->id_servico_acessado = $dados['id_servico_acessado'];
            $acesso_servico->id_natureza_servico = $id_natureza_servico;
            $acesso_servico->id_localizacao_servico = $id_localizacao_servico;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 2;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function acessoServicoDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $acesso_servico = AcessoServico::where('id', '=', $dados['id_acesso'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$acesso_servico) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($acesso_servico->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Acesso ao serviço já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $acesso_servico->inativadoPorUsuario = Auth::user()->id;
            $acesso_servico->dataInativado = Carbon::now();
            $acesso_servico->motivoInativado = $dados['motivo'];
            $acesso_servico->ativo = 0;
            $acesso_servico->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AcessoServico")
                ->where('auditable_id', '=', $acesso_servico->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 0;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function integracaoSocialStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_integrante' => $dados['id_integrante'],
                'id_frequencia' => $dados['id_frequencia'],
            ];
            $rules = [
                'id_integrante' => 'required|integer|max:255',
                'id_frequencia' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_participacao_org'] as $id_partic) {

                $id_partic = intval($id_partic);
                $participacao_org = ParticipacaoOrganizacao::where('id', '=', $id_partic)->where('ativo', '=', 1)->first();

                if (!$participacao_org){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Participação na organização inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_participacao_org = json_encode($dados['id_participacao_org']);

            $integracao_social = new IntegracaoSocial();
            $integracao_social->id_cliente = $id_cliente;
            $integracao_social->id_entrevista = $entrevista->id;
            $integracao_social->funcao = $dados['funcao'];
            $integracao_social->id_participacao_org = $id_participacao_org;
            $integracao_social->cadastradoPorUsuario = Auth::user()->id;
            $integracao_social->validado = 0;
            $integracao_social->ativo = 1;
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function integracaoSocialUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'id_integrante' => $dados['id_integrante'],
                'id_frequencia' => $dados['id_frequencia'],
            ];
            $rules = [
                'id_integrante' => 'required|integer|max:255',
                'id_frequencia' => 'required|integer|max:255',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_participacao_org'] as $id_partic) {

                $id_partic = intval($id_partic);
                $participacao_org = ParticipacaoOrganizacao::where('id', '=', $id_partic)->where('ativo', '=', 1)->first();

                if (!$participacao_org){
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Participação na organização inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_participacao_org = json_encode($dados['id_participacao_org']);

            $integracao_social = IntegracaoSocial::where('id', '=', $dados['id_integracao'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$integracao_social) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($integracao_social->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integracao_social->id_participacao_org = $id_participacao_org;
            $integracao_social->funcao = $dados['funcao'];
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração de cadastro realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;


        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }

    }
    public static function integracaoSocialDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $integracao_social = IntegracaoSocial::where('id', '=', $dados['id_integracao'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$integracao_social) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($integracao_social->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Integração Social já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $integracao_social->inativadoPorUsuario = Auth::user()->id;
            $integracao_social->dataInativado = Carbon::now();
            $integracao_social->motivoInativado = $dados['motivo'];
            $integracao_social->ativo = 0;
            $integracao_social->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\IntegracaoSocial")
                ->where('auditable_id', '=', $integracao_social->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function sucessaoFamiliarStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'numero_filho' => $dados['numero_filho'],
                'id_avaliacao' => $dados['id_avaliacao'],
                'id_pretensao' => $dados['id_pretensao'],
            ];
            $rules = [
                'numero_filho' => 'required|integer',
                'id_avaliacao' => 'required|integer',
                'id_pretensao' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $pretensao = Pretensao::where('id', '=', $dados['id_pretensao'])->where('ativo', '=', 1)->first();
            if (!$pretensao) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Pretensão inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $asf = AvaliacaoSucessaoFamiliar::where('id', '=', $dados['id_avaliacao'])->where('ativo', '=', 1)->first();
            if (!$asf) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Avaliação inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar = new SucessaoFamiliar();
            $sucessao_familiar->numero_filho = $dados['numero_filho'];
            $sucessao_familiar->id_avaliacao = $dados['id_avaliacao'];
            $sucessao_familiar->id_pretensao = $dados['id_pretensao'];
            $sucessao_familiar->id_cliente = $id_cliente;
            $sucessao_familiar->id_entrevista = $entrevista->id;
            $sucessao_familiar->cadastradoPorUsuario = Auth::user()->id;
            $sucessao_familiar->validado = 0;
            $sucessao_familiar->ativo = 1;
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function sucessaoFamiliarUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try{
            $input = [
                'numero_filho' => $dados['numero_filho'],
                'id_avaliacao' => $dados['id_avaliacao'],
                'id_pretensao' => $dados['id_pretensao'],
            ];
            $rules = [
                'numero_filho' => 'required|integer',
                'id_avaliacao' => 'required|integer',
                'id_pretensao' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $sucessao_familiar = SucessaoFamiliar::where('id', '=', $dados['id_sf'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$sucessao_familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($sucessao_familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $pretensao = Pretensao::where('id', '=', $dados['id_pretensao'])->where('ativo', '=', 1)->first();
            if (!$pretensao) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Pretensão inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $asf = AvaliacaoSucessaoFamiliar::where('id', '=', $dados['id_avaliacao'])->where('ativo', '=', 1)->first();
            if (!$asf) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Avaliação inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar->numero_filho = $dados['numero_filho'];
            $sucessao_familiar->id_avaliacao = $dados['id_avaliacao'];
            $sucessao_familiar->id_pretensao = $dados['id_pretensao'];
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit){
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro alterado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function sucessaoFamiliarDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try{
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário via API.";
            }

            $sucessao_familiar = SucessaoFamiliar::where('id', '=', $dados['id_sf'])
                ->where('id_cliente', '=', $id_cliente)
                ->where('ativo', '=', 1)
                ->first();

            if (!$sucessao_familiar) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($sucessao_familiar->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Sucessão Familiar já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $sucessao_familiar->inativadoPorUsuario = Auth::user()->id;
            $sucessao_familiar->dataInativado = Carbon::now();
            $sucessao_familiar->motivoInativado = $dados['motivo'];
            $sucessao_familiar->ativo = 0;
            $sucessao_familiar->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\SucessaoFamiliar")
                ->where('auditable_id', '=', $sucessao_familiar->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

    public static function atividadeLazerStoreExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_tipo_atividade' => $dados['id_tipo_atividade'],
                'id_frequencia' => $dados['id_frequencia'],
                'tempo' => preg_replace('/[^0-9]/', '', $dados['tempo'])
            ];
            $rules = [
                'id_tipo_atividade' => 'required|integer',
                'id_frequencia' => 'required|integer',
                'tempo' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            foreach ($dados['id_localizacao'] as $local) {

                $local = intval($local);
                $temlocal = LocalizacaoLazer::where('id', '=', $local)->where('ativo', '=', 1)->first();

                if (!$temlocal) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Onde realiza as atividades está inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_localizacao = json_encode($dados['id_localizacao']);

            $atividade_lazer = new AtividadeLazer();
            $atividade_lazer->id_cliente = $id_cliente;
            $atividade_lazer->id_entrevista = $entrevista->id;
            $atividade_lazer->id_localizacao = $id_localizacao;
            $atividade_lazer->tempo = $dados['tempo'];
            $atividade_lazer->id_frequencia = $dados['id_frequencia'];
            $atividade_lazer->id_tipo_atividade = $dados['id_tipo_atividade'];
            $atividade_lazer->cadastradoPorUsuario = Auth::user()->id;
            $atividade_lazer->validado = 0;
            $atividade_lazer->ativo = 1;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Cadastro realizado via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function atividadeLazerUpdateExterno($id_cliente, $entrevista, $agricultor, Array $dados)
    {
        try {
            $input = [
                'id_tipo_atividade' => $dados['id_tipo_atividade'],
                'id_frequencia' => $dados['id_frequencia'],
                'tempo' => preg_replace('/[^0-9]/', '', $dados['tempo'])
            ];
            $rules = [
                'id_tipo_atividade' => 'required|integer',
                'id_frequencia' => 'required|integer',
                'tempo' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $atividade_lazer = AtividadeLazer::where('id', '=', $dados['id_al'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$atividade_lazer) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer inválido.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($atividade_lazer->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            foreach ($dados['id_localizacao'] as $local) {

                $local = intval($local);
                $temlocal = LocalizacaoLazer::where('id', '=', $local)->where('ativo', '=', 1)->first();

                if (!$temlocal) {
                    $resposta = [
                        'success' => 0,
                        'msg' => 'Onde realiza as atividades está inválida.',
                        'codigoErro' => 403
                    ];
                    return $resposta;
                }
            }

            $id_localizacao = json_encode($dados['id_localizacao']);

            $atividade_lazer->id_cliente = $id_cliente;
            $atividade_lazer->id_entrevista = $entrevista->id;
            $atividade_lazer->id_localizacao = $id_localizacao;
            $atividade_lazer->tempo = $dados['tempo'];
            $atividade_lazer->id_frequencia = $dados['id_frequencia'];
            $atividade_lazer->id_tipo_atividade = $dados['id_tipo_atividade'];
            $atividade_lazer->cadastradoPorUsuario = Auth::user()->id;
            $atividade_lazer->validado = 0;
            $atividade_lazer->ativo = 1;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 1;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Alteração realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }
    public static function atividadeLazerDestroyExterno($id_cliente, $entrevista, Array $dados)
    {
        try {
            $input = [
                'motivo' => $dados['motivo']
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $dados['motivo'] = $dados['motivo'];

            if ($dados['motivo'] == null || $dados['motivo'] == "") {
                $dados['motivo'] = "Exclusão pelo usuário.";
            }

            $atividade_lazer = AtividadeLazer::where('id', '=', $dados['id_al'])->where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->first();
            if (!$atividade_lazer) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade de lazer inválida.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            if ($atividade_lazer->validado == 1) {
                $resposta = [
                    'success' => 0,
                    'msg' => 'Atividade Lazer já está validado e não pode mais ser alterado.',
                    'codigoErro' => 403
                ];
                return $resposta;
            }

            $atividade_lazer->inativadoPorUsuario = Auth::user()->id;
            $atividade_lazer->dataInativado = Carbon::now();
            $atividade_lazer->motivoInativado = $dados['motivo'];
            $atividade_lazer->ativo = 0;
            $atividade_lazer->save();

            $audit = Auditoria::where('auditable_type', '=', "App\Models\AtividadeLazer")
                ->where('auditable_id', '=', $atividade_lazer->id)
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            $historico = new HistoricoEntrevista();
            $historico->id_entrevista = $entrevista->id;

            if ($audit) {
                $historico->id_auditoria = $audit->id;
                $historico->entidade = $audit->auditable_type;
            }

            $historico->id_evento = 3;
            $historico->ehCliente = 1;
            $historico->cadastradoPorUsuario = Auth::user()->id;
            $historico->save();

            $resposta = [
                'success' => 1,
                'msg' => 'Exclusão realizada via API com sucesso.',
                'codigoErro' => null
            ];
            return $resposta;

        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            $resposta = [
                'success' => 0,
                'msg' => $message,
                'codigoErro' => 403
            ];
            return $resposta;
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            $resposta = [
                'success' => 0,
                'msg' => $ex->getMessage(),
                'codigoErro' => 500
            ];
            return $resposta;
        }
    }

}


