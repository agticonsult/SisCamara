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
        'id_pleito', 'id_cargo_eletivo', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'pleito_cargos';

    public function pleito()
    {
        return $this->belongsTo(PleitoEleitoral::class, 'id_pleito');
    }
    public function cargo()
    {
        return $this->belongsTo(CargoEletivo::class, 'id_cargo');
    }
    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
}


