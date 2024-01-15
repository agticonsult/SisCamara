<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ErrorLog extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['erro', 'controlador', 'funcao', 'cadastradoPorUsuario'];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'error_logs';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario() {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
}
