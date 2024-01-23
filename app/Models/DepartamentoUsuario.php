<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DepartamentoUsuario extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_user', 'id_departamento', 'inativadoPorUsuario', 'dataInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamento_usuarios';

    const ATIVO = 1;
    const INATIVO = 0;

    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}
