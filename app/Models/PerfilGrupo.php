<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Auditor;

class PerfilGrupo extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_grupo', 'id_perfil', 'cadastradoPorUsuario','alteradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'perfil_grupos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class,'id_grupo');
    }
    public function perfil()
    {
        return $this->belongsTo(Perfil::class,'id_perfil');
    }
    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }


}
