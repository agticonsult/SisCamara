<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Grupo extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'grupos';

    const ATIVO = 1;
    const INATIVO = 0;

    const ADMINISTRADOR = 1;
    const INTERNO = 2;
    const EXTERNO = 3;
    const POLITICO = 4;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    // public function finalidade()
    // {
    //     return $this->belongsTo(FinalidadeGrupo::class, 'id_finalidade');
    // }
    // public function membros_ativos(){
    //     return $this->hasMany(MembroGrupo::class, 'id_grupo', 'id')->where('ativo', '=', 1);
    // }
    // public function membros(){
    //     return $this->hasMany(MembroGrupo::class, 'id_grupo', 'id');
    // }
    // public function perfil_ativos(){
    //     return $this->hasMany(PerfilGrupo::class, 'id_grupo', 'id')->where('ativo', '=', 1);
    // }
    // public function perfils(){
    //     return $this->hasMany(PerfilGrupo::class, 'id_grupo', 'id');
    // }
    // public function usuario()
    // {
    //     return $this->belongsTo(User::class, 'id_user');
    // }

    // public function perfilgrupo(){
    //     return $this->hasMany(PerfilGrupo::class, 'id_grupo', 'id')->where('ativo', '=', 1);
    //  }

    // public function ehDoPrograma($id_programa)
    // {
    //     $tem = ProgramaGrupo::where('id_grupo', '=', $this->id)->where('id_programa', '=', $id_programa)->where('ativo', '=', 1)->first();

    //     if ($tem){
    //         return true;
    //     }

    //     return false;
    // }
    // public function ehDoProcesso($id_processo)
    // {
    //     $tem = ProcessoGrupo::where('id_grupo', '=', $this->id)->where('id_processo', '=', $id_processo)->where('ativo', '=', 1)->first();

    //     if ($tem){
    //         return true;
    //     }

    //     return false;

    // }

    // public function ehDoEvento($id_evento)
    // {
    //     $tem = EventoGrupo::where('id_grupo', '=', $this->id)->where('id_evento', '=', $id_evento)->where('ativo', '=', 1)->first();

    //     if ($tem){
    //         return true;
    //     }

    //     return false;

    // }
    // public function chat(){
    //     return $this->hasOne(Chat::class, 'id_grupo', 'id')->where('ativo', '=', 1);
    // }
}
