<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;
    use \OwenIt\Auditing\Audit;

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    ];

    public function getSerializedDate($date)
    {
        return $this->serializeDate($date);
    }

    protected $table = 'audits';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function buscar($filtro = null){

        $resultados = $this->where(function($query) use($filtro){

            if(empty(collect($filtro)->except('_token', '_method') === null)){
                $query->get();
            }

            if(isset($filtro['user_id'])){
                $query->where('user_id', '=', $filtro['user_id']);
            }

            if(isset($filtro['event'])){
                $query->where('event', '=', $filtro['event']);
            }

        })->where(function($subquery) use($filtro){

            if(isset($filtro['data'])){
                $subquery->where('created_at', 'ILIKE', $filtro['data'] . '%');
            }

        })
        ->get();

        return $resultados;
    }
}
