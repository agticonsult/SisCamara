<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TipoVotacao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'descricao', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'tipo_votacaos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
}
