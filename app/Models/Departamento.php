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
        'descricao', 'id_coordenador', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoIntivaado', 'ativo'
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
        return $this->belongsTo(User::class, 'id_coordenador');
    }
    public function usuarios()
    {
        // return $this->belongsToMany(User::class, 'departamento_usuarios', 'id_departamento', 'id_user')->withPivot('cadastradoPorUsuario');
        return $this->belongsToMany(User::class, 'departamento_usuarios', 'id_departamento', 'id_user')->wherePivot('ativo', '=', DepartamentoUsuario::ATIVO);
    }

    public static function retornaDepartamentosAtivos()
    {
        return Departamento::where('ativo', '=', Departamento::ATIVO)->get();
    }
    public static function retornaDepartamentoAtivo($id)
    {
        return Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->first();
    }
}
