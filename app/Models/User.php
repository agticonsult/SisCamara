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
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cpf', 'password', 'email', 'telefone_celular', 'telefone_celular2',
        'id_pessoa', 'importado', 'id_importacao', 'tentativa_senha',
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
    public function permissoes()
    {
        return $this->hasMany(Permissao::class, 'id_user', 'id')->orderBy('ativo', 'desc');
    }
    public function permissoes_ativas()
    {
        return $this->hasMany(Permissao::class, 'id_user', 'id')->where('ativo', '=', 1);
    }
    public function temPerfil($id_perfil)
    {
        $tem = Permissao::where('id_perfil', '=', $id_perfil)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$tem){
            return false;
        }
        return true;
    }
    // public function ehMembroDeGrupo($id_grupo)
    // {
    //     $pertence = MembroGrupo::where('id_grupo', '=', $id_grupo)->where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

    //     if (!$pertence){
    //         return false;
    //     }
    //     return true;
    // }
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

    public function ehVereador()
    {
        $eh = Vereador::where('id_user', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }

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
