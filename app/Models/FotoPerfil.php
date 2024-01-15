<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FotoPerfil extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['nome_original','nome_hash', 'id_user', 'cadastradoPorUsuario',
    'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'foto_perfils';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
