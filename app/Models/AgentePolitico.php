<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class AgentePolitico extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'inicio_mandato', 'fim_mandato', 'id_legilslatura', 'id_cargo_eletivo', 'id_pleito_eleitoral', 'id_user', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'agente_politicos';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function legislatura()
    {
        return $this->belongsTo(Legislatura::class, 'id_legislatura');
    }
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
    public function imagem()
    {
        $resposta = array();
        $imagem = FotoPerfil::where('id_user', '=', $this->id_user)->where('ativo', '=', 1)->first();
        if ($imagem){
            $existe = Storage::disk('public')->exists('foto-perfil/' . $imagem->nome_hash);
            if ($existe){
                $resposta = [
                    'tem' => 1,
                    'imagem' => $imagem
                ];
            }
            else{
                $resposta = [
                    'tem' => 0,
                    'imagem' => null
                ];
            }
        }
        else{
            $resposta = [
                'tem' => 0,
                'imagem' => null
            ];
        }
        return $resposta;
    }

}
