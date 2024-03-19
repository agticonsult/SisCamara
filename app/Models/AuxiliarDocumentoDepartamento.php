<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AuxiliarDocumentoDepartamento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id_documento', 'id_departamento', 'ordem', 'atual', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'auxiliar_documento_departamentos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function documento()
    {
        return $this->belongsTo(DepartamentoDocumento::class, 'id_documento');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}
