<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pessoa extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'pessoaJuridica', 'nomeCompleto', 'apelidoFantasia', 'dt_nascimento_fundacao', 'cep',
        'endereco', 'bairro', 'numero', 'complemento', 'ponto_referencia', 'id_municipio',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'pessoas';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function setNomeAttribute($value)
    {
        $this->attributes['nomeCompleto'] = trim($value);
    }

    // public function municipio()
    // {
    //     return $this->belongsTo(Municipio::class, 'id_municipio');
    // }
    // public function usuario(){
    //     return $this->hasOne(User::class, 'id_pessoa', 'id');
    // }
    // public function organizacao(){
    //     return $this->hasOne(Organizacao::class, 'id_pj', 'id');
    // }

}

