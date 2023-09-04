<?php

namespace Database\Seeders;

use App\Models\TipoEvento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Realiza a importação dos arquivos sql
         * referentes ao Estado do Paraná
         */
        // $file_path1 = resource_path('sql/mesorregiao.sql');
        // DB::unprepared(file_get_contents($file_path1));

        // //Dados inseridos com sucesso, exibe a mensagem no terminal
        // $this->command->info('mesorregiao table seeded!');


        // User::factory(10)->create();
        $this->call(GrupoSeeder::class);
        $this->call(PerfilTableSeeder::class);
        $this->call(PessoaUserTableSeeder::class);
        $this->call(TipoEmailTableSeeder::class);
        $this->call(FinalidadeGrupoSeeder::class);
        $this->call(FuncionalidadeTableSeeder::class);
        $this->call(PerfilFuncionalidadeTableSeeder::class);
        $this->call(TipoAnexoTableSeeder::class);
        $this->call(FilesizeTableSeeder::class);
        // $this->call(SeederTeste::class);
        $this->call(TipoAtoTableSeeder::class);
        $this->call(TipoLinhaAtoTableSeeder::class);
        $this->call(AssuntoAtoTableSeeder::class);
        $this->call(AutoridadeSeeder::class);
        $this->call(PublicacaoAtoSeeder::class);
        $this->call(PublicacaoAtoSeeder::class);
        $this->call(FormaPublicacaoTableSeeder::class);
        $this->call(OrgaoAtoTableSeeder::class);
        $this->call(ClassificacaoAtoTableSeeder::class);
        $this->call(TipoReparticaoTableSeeder::class);
        $this->call(LocalizacaoDocumentoTableSeeder::class);
        $this->call(StatusDocumentoTableSeeder::class);
        $this->call(CargoEletivoTableSeeder::class);
        $this->call(PleitoEleitoralTableSeeder::class);
    }
}
