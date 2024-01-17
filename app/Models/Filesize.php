<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Filesize extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'mb', 'id_tipo_filesize', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'filesizes';

    const ATIVO = 1;
    const INATIVO = 0;

    const ANEXO_ATO = 1;
    const FOTO_PERFIL = 2;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipo_filesize()
    {
        return $this->belongsTo(TipoFilesize::class, 'id_tipo_filesize');
    }
}


