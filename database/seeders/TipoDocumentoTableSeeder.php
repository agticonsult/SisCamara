<?php

namespace Database\Seeders;

use App\Models\DepartamentoTramitacao;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::orderBy('cpf')->get();
        DB::table('tipo_documentos')->insert([
            ['nome' => 'Solicitação de informações', 'tipoDocumento' => 'Memorando', 'nivel' => 2, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => TipoDocumento::ATIVO],
            ['nome' => 'Designação de servidores', 'tipoDocumento' => 'Portaria', 'nivel' => 2, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => TipoDocumento::ATIVO]
        ]);
        DB::table('departamento_tramitacaos')->insert([
            ['id_tipo_documento' => 1, 'id_departamento' => 1, 'ordem' => 1, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => DepartamentoTramitacao::ATIVO],
            ['id_tipo_documento' => 1, 'id_departamento' => 2, 'ordem' => 2, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => DepartamentoTramitacao::ATIVO],
            ['id_tipo_documento' => 2, 'id_departamento' => 2, 'ordem' => 1, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => DepartamentoTramitacao::ATIVO],
            ['id_tipo_documento' => 2, 'id_departamento' => 1, 'ordem' => 2, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => DepartamentoTramitacao::ATIVO],
        ]);
    }
}
