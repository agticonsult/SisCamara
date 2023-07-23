<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Expr\Cast\String_;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cpf', 'password', 'email', 'telefone_celular', 'telefone_celular2',
        'id_pessoa','lotacao', 'id_tipo_perfil', 'importado', 'id_importacao', 'tentativa_senha',
        'bloqueadoPorTentativa', 'dataBloqueadoPorTentativa', 'envio_email_recuperacao', 'envio_email_confirmacaoApi',
        'envio_email_confirmacao', 'confirmacao_email', 'dataHoraConfirmacaoEmail', 'validado', 'validadoPorUsuario',
        'validadoEm', 'incluso', 'incluidoPorUsuario', 'incluidoEm', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected static function booted()
    // {
    //     static::creating(fn(User $user) => $user->id = (string) Uuid::uuid4());
    // }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Uuid::uuid4();
            }
        });
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa');
    }
    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }
    public function lot()
    {
        return $this->belongsTo(Municipio::class, 'lotacao');
    }
    public function tipo_perfil()
    {
        return $this->belongsTo(TipoPerfil::class, 'id_tipo_perfil');
    }
    public function permissoes()
    {
        return $this->hasMany(Permissao::class, 'id_user', 'id')->orderBy('ativo', 'desc');
    }
    public function permissoes_ativas()
    {
        return $this->hasMany(Permissao::class, 'id_user', 'id')->where('ativo', '=', 1);
    }
    public function tipo_perfis_ativos()
    {
        return $this->hasMany(PerfilUser::class, 'id_user', 'id')->where('ativo', '=', 1);
    }
    public function temPerfil($id_perfil)
    {
        $tem = Permissao::where('id_perfil', '=', $id_perfil)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$tem){
            return false;
        }
        return true;
    }
    public function ehAdm()
    {
        //realizar a comparação na Model PerfilUser e verifica se tipo perfil é do tipo funcionário
        $eh = PerfilUser::where('id_tipo_perfil', '=', 1)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }
    public function ehFuncionario()
    {
        //realizar a comparação na Model PerfilUser e verifica se tipo perfil é do tipo funcionário
        // $eh = PerfilUser::where('id_tipo_perfil', '=', 2)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();
        $eh = PerfilUser::where(function (Builder $query) {
            return
                $query->where('id_tipo_perfil', '=', 1)
                    ->orWhere('id_tipo_perfil', '=', 2);
                })
            ->where('id_user', '=', $this->id)
            ->where('ativo', '=', 1)
            ->first();

        if (!$eh){
            return false;
        }
        return true;
    }
    public function ehCliente()
    {
        $eh = PerfilUser::where('id_tipo_perfil', '=', 3)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }
    public function ehMembroDeGrupo($id_grupo)
    {
        $pertence = MembroGrupo::where('id_grupo', '=', $id_grupo)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$pertence){
            return false;
        }
        return true;
    }
    public function ehAgricultor()
    {
        $eh = Agricultor::where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }
    public function temPermissao($entidade, $tipoFuncionalidade)
    {
        $e = Entidade::where('nomeEntidade', '=', $entidade)->first();
        $tp = TipoFuncionalidade::where('descricao', '=', $tipoFuncionalidade)->first();

        if (!$e || !$tp){
            return false;
        }

        $funcionalidade = Funcionalidade::where('id_entidade', '=', $e->id)->where('id_tipo_funcionalidade', '=', $tp->id)->where('ativo', '=', 1)->first();

        if (!$funcionalidade){
            return false;
        }

        $permissoes = Permissao::where('id_user', '=', $this->id)->where('ativo', '=', 1)->get();

        foreach ($permissoes as $permissao){

            $tem = $permissao->perfil->temFuncionalidade($funcionalidade);

            if ($tem[0] == true){
                return true;
            }
        }

        return false;
    }

    public function agricultor()
    {
        return $this->hasOne(Agricultor::class, 'id_user', 'id');
    }
    public function temPermissaoAbrangencia($entidade, $tipoFuncionalidade)
    {
        $resposta = array();

        $e = Entidade::where('nomeEntidade', '=', $entidade)->first();
        $tp = TipoFuncionalidade::where('descricao', '=', $tipoFuncionalidade)->first();

        if (!$e || !$tp){
            array_push($resposta, false);
            array_push($resposta, 0);
        }

        $funcionalidade = Funcionalidade::where('id_entidade', '=', $e->id)->where('id_tipo_funcionalidade', '=', $tp->id)->where('ativo', '=', 1)->first();

        if (!$funcionalidade){
            array_push($resposta, false);
            array_push($resposta, 0);
        }

        $permissoes = Permissao::where('id_user', '=', $this->id)->where('ativo', '=', 1)->get();

        $temAcesso = 0;
        $menorIdAbrangencia = 0;

        foreach ($permissoes as $permissao){

            $tem = $permissao->perfil->temFuncionalidade($funcionalidade);

            if ($tem[0] == true){
                $temAcesso = 1;
                if ($menorIdAbrangencia == 0){
                    $menorIdAbrangencia = $tem[1];
                }
                else{
                    if ($tem[1] < $menorIdAbrangencia){
                        $menorIdAbrangencia = $tem[1];
                    }
                }
            }
        }

        if ($temAcesso == 1){
            array_push($resposta, true);
            array_push($resposta, $menorIdAbrangencia);
            return $resposta;
        }

        array_push($resposta, false);
        array_push($resposta, 0);
        return $resposta;

    }
    public function funcionarioVinculado()
    {
        return $this->belongsTo(AgricultorFuncionario::class, 'id_user', 'id');
    }
    public function temAcessoProcesso($id_processo)
    {
        $processo = Processo::where('id', '=', $id_processo)->where('ativo', '=', 1)->first();
        if ($processo){

            if ($processo->cadastradoPorUsuario == $this->id){
                return true;
            }
            else{
                foreach ($processo->grupos_envolvidos_ativos as $gea) {
                    if ($this->ehMembroDeGrupo($gea->id_grupo) == 1){
                        return true;
                    }
                }
            }

        }

        return false;

    }

    // public function estaInscrito($id_evento)
    // {
    //     $estaInscrito = InscricaoEvento::where('id_cliente', '=', $this->id)->where('id_evento', '=', $id_evento)->where('ativo', '=', 1)->first();
    //     if ($estaInscrito){
    //         return true;
    //     }
    //     return false;
    // }

    public function estaInscrito($id_evento)
    {
        $resposta = array();
        $estaInscrito = InscricaoEvento::where('id_cliente', '=', $this->id)->where('id_evento', '=', $id_evento)->where('ativo', '=', 1)->first();
        if ($estaInscrito){
            $resposta = [
                'inscrito' => true,
                'id_status' => $estaInscrito->id_status

            ];
        }
        else {
            $resposta = [
                'inscrito' => false,
                'id_status' => null

            ];
        }
        return $resposta;
    }

    public function avaliado($id_evento)
    {
        $resposta = array();
        $avaliado = InscricaoEvento::where('id_cliente', '=', $this->id)->where('id_evento', '=', $id_evento)->where('avaliado', '=', 1)->where('ativo', '=', 1)->first();
        if ($avaliado){
            $resposta = [
                'avaliado' => true,
                'avaliacao' => $avaliado->avaliacao
            ];
        }
        else {
            $resposta = [
                'avaliado' => false,
                'avaliacao' => null
            ];

        }
        return $resposta;

    }

    public function downloadAcervo($id_acervo)
    {
        $resposta = array();
        $download = 0;
        $avaliado = 0;
        $avaliacao = null;
        $visita_acervo = VisitaAcervo::where('id_usuario', '=', $this->id)->where('id_acervo', '=', $id_acervo)->where('ativo', '=', 1)->first();
        if ($visita_acervo){
            $download = 1;
            if ($visita_acervo->avaliado == 1){
                $avaliado = 1;
                $avaliacao = $visita_acervo->avaliacao;
            }
        }

        $resposta = [
            'download' => $download,
            'avaliado' => $avaliado,
            'avaliacao' => $avaliacao
        ];

        return $resposta;
    }

    public function estaNoChatAPI($id_chat)
    {
        $pertence = ChatParticipante::where('id_user', '=', $this->id)->where('id_chat', '=', $id_chat)->where('ativo', '=', 1)->first();

        if ($pertence) {
            return true;
        }
        return false;
    }

    public function estaInclusoNoChat($id_chat)
    {
        $pertence = ChatParticipante::where('id_user', '=', $this->id)->where('id_chat', '=', $id_chat)->where('ativo', '=', 1)->first();

        if ($pertence) {
            return true;
        }
        return false;
    }
    // public function ehEspecialista()
    // {
    //     $especialista = Especialista::where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();
    //     if ($especialista){
    //         return true;
    //     }
    //     return false;
    // }
    // public function empresas()
    // {
    //     return $this->hasMany(Empresa::class, 'id_user', 'id');
    // }
    // public function empresas_ativas()
    // {
    //     return $this->hasMany(Empresa::class, 'id_user', 'id')->where('ativo', '=', 1);
    // }

    public function foto()
    {
        $resposta = array();
        $foto = FotoPerfil::where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();
        if ($foto){
            $existe = Storage::disk('public')->exists('foto-perfil/'.$foto->nome_hash);
            // $existe = public_path('foto-perfil/'.$foto_perfil->nome_hash);
            if ($existe){
                $resposta = [
                    'tem' => 1,
                    'foto' => $foto
                ];
            }
            else{
                $resposta = [
                    'tem' => 0,
                    'foto' => null
                ];
            }
        }
        else{
            $resposta = [
                'tem' => 0,
                'foto' => null
            ];
        }
        return $resposta;
    }
}
