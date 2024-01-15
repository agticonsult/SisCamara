<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PleitoCargo extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_pleito_eleitoral', 'id_cargo_eletivo', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'pleito_cargos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function pleito_eleitoral()
    {
        return $this->belongsTo(PleitoEleitoral::class, 'id_pleito_eleitoral');
    }
    public function cargo_eletivo()
    {
        return $this->belongsTo(CargoEletivo::class, 'id_cargo_eletivo');
    }
    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }
}


