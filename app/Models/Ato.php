<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Ato extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'titulo', 'ano', 'numero', 'descricao', 'id_grupo', 'id_tipo_ato', 'cadastradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'atos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class ,'id_grupo');
    }
    public function tipo_ato()
    {
        return $this->belongsTo(TipoAto::class ,'id_tipo_ato');
    }
}


