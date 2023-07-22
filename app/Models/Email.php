<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Email extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'enviadoEm', 'expiradoEm', 'expirarMin', 'expirarHora', 'link', 'emailRecebido', 'emailEnviado', 'expirado', 'recebidoPorUsuario', 'id_tipo_email',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'emails';

    public function cad_usuario() {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipo_email() {
        return $this->belongsTo(TipoEmail::class, 'id_tipo_email');
    }
}
