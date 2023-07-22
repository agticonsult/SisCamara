<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Permissao extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_user', 'id_perfil', 'cadastradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo','id_grupo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'permissaos';

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    // public function municipio()
    // {
    //     return $this->belongsTo(Municipio::class, 'lotacao');
    // }
    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }
}
