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
        'ano_pleito', 'pleitoEspecial', 'dataPrimeiroTurno', 'dataSegundoTurno', 'id_legislatura',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'pleito_eleitorals';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }

    public function legislatura()
    {
        return $this->belongsTo(Legislatura::class, 'id_legislatura');
    }

    public function cargos_eletivos_ativos()
    {
        $cargos_eletivos = PleitoCargo::where('id_pleito_eleitoral', '=', $this->id)->where('ativo', '=', PleitoCargo::ATIVO)->get();
        return $cargos_eletivos;
    }

    public function cargos_eletivos()
    {
        $cargos_eletivos = PleitoCargo::where('id_pleito_eleitoral', '=', $this->id)->get();
        return $cargos_eletivos;
    }
}


