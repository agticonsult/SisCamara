<?php

namespace App\Services;

use App\Models\Ato;
use App\Models\LinhaAto;

class BuscaPalavraExclusaoService
{
    public static function processarAtosComExclusao($exclusao)
    {
        $atos = self::getTodosAtos();
        $atos = self::excluirAtosComTitulo($atos, $exclusao);
        $atos = self::excluirAtosComTexto($atos, $exclusao);

        return $atos;
    }

    private static function getTodosAtos()
    {
        return Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
            ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
            ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
            ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
            ->where('atos.ativo', '=', 1)
            ->select(
                'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao'
            )
            ->get()
        ->toArray();
    }

    private static function excluirAtosComTitulo($atos, $exclusao)
    {
        $atos_titulo_excluidos = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
            ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
            ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
            ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
            ->where('atos.titulo', 'LIKE', '%'.$exclusao.'%')
            ->where('atos.ativo', '=', 1)
            ->select(
                'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao'
            )
            ->get();

        foreach ($atos_titulo_excluidos as $ato_titulo_excluido) {
            $atos = array_filter($atos, function($ato) use ($ato_titulo_excluido) {
                return $ato['id'] !== $ato_titulo_excluido->id;
            });
        }

        return array_values($atos); // Reindexar array
    }

    private static function excluirAtosComTexto($atos, $exclusao)
    {
        $linhas_texto_excluidos = LinhaAto::where('texto', 'LIKE', '%'.$exclusao.'%')
            ->where('ativo', '=', LinhaAto::ATIVO)
            ->get();

        foreach ($linhas_texto_excluidos as $linha_texto_excluido) {
            $atos = array_filter($atos, function($ato) use ($linha_texto_excluido) {
                return $ato['id'] !== $linha_texto_excluido->id_ato_principal;
            });
        }

        return array_values($atos); // Reindexar array
    }
}
