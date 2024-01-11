<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Departamento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'descricao', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoIntivaado', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamentos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
