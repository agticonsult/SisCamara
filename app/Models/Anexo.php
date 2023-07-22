<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Anexo extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome_original', 'nome_hash', 'justificativa', 'assunto', 'diretorio',
        'id_processo', 'id_tipo_anexo', 'cadastradoPorUsuario', 'inativadoPorUsuario',
        'dataInativado', 'motivoInativado', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'anexos';

    public function processo()
    {
        return $this->belongsTo(Processo::class ,'id_processo');
    }
    public function tipo_anexo()
    {
        return $this->belongsTo(TipoAnexo::class ,'id_tipo_anexo');
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

