<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Certificado extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome_original', 'nome_hash', 'diretorio', 'password', 'data_validade', 'tipo', 'nome_cert', 'id_user'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'certificados';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
