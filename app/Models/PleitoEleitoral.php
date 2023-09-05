<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PleitoEleitoral extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'ano_pleito', 'inicio_mandato', 'fim_mandato', 'pleitoEspecial', 'dataPrimeiroTurno', 'dataSegundoTurno',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'pleito_eleitorals';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function cargos_eletivos_ativos()
    {
        $cargos_eletivos = PleitoCargo::where('id_pleito_eleitoral', '=', $this->id)->where('ativo', '=', 1)->get();
        return $cargos_eletivos;
    }
    public function cargos_eletivos()
    {
        $cargos_eletivos = PleitoCargo::where('id_pleito_eleitoral', '=', $this->id)->get();
        return $cargos_eletivos;
    }
}


