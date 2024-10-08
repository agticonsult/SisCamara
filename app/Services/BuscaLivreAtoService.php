<?php

namespace App\Services;

use App\Models\Ato;
use App\Models\LinhaAto;

class BuscaLivreAtoService
{
    public static function processarAtos($palavra, $exclusao)
    {
        $atos_titulo = self::getAtosComTitulo($palavra);
        $linhas_texto = self::getLinhasComTexto($palavra);

        $atos = self::mergeAtosELinhas($atos_titulo, $linhas_texto);

        $atos = self::excluirAtosComTitulo($atos, $exclusao);
        $atos = self::excluirAtosComTexto($atos, $exclusao);

        return $atos;
    }

    private static function getAtosComTitulo($palavra)
    {
        return Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
            ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
            ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
            ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
            ->where('atos.titulo', 'LIKE', '%'.$palavra.'%')
            ->where('atos.ativo', '=', 1)
            ->select(
                'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
            )
        ->get();
    }

    private static function getLinhasComTexto($palavra)
    {
        return LinhaAto::where('texto', 'LIKE', '%'.$palavra.'%')
            ->where('ativo', '=', 1)
        ->get();
    }

    private static function mergeAtosELinhas($atos_titulo, $linhas_texto)
    {
        $atos = $atos_titulo->toArray();

        foreach ($linhas_texto as $linha_texto) {
            $tem = 0;
            for ($i = 0; $i < count($atos); $i++) {
                if ($atos[$i]['id'] == $linha_texto->id_ato_principal) {
                    $tem = 1;
                    break;
                }
            }
            if ($tem == 0) {
                $linha_texto_ato_principal = [
                    'id' => $linha_texto->id_ato_principal,
                    'numero' => $linha_texto->ato_principal->numero,
                    'data_publicacao' => $linha_texto->ato_principal->data_publicacao,
                    'created_at' => $linha_texto->ato_principal->created_at,
                    'altera_dispositivo' => $linha_texto->ato_principal->altera_dispositivo,
                    'assunto' => $linha_texto->ato_principal->id_assunto != null ? $linha_texto->ato_principal->assunto->descricao : 'não informado',
                    'tipo_ato' => $linha_texto->ato_principal->id_tipo_ato != null ? $linha_texto->ato_principal->tipo_ato->descricao : 'não informado',
                    'orgao' => $linha_texto->ato_principal->id_orgao != null ? $linha_texto->ato_principal->orgao->descricao : 'não informado',
                    'forma_publicacao' => $linha_texto->ato_principal->id_forma_publicacao != null ? $linha_texto->ato_principal->forma_publicacao->descricao : 'não informado'
                ];
                array_push($atos, $linha_texto_ato_principal);
            }
        }

        return $atos;
    }

    private static function excluirAtosComTitulo($atos, $exclusao)
    {
        $atos_titulo_excluidos = Ato::where('titulo', 'LIKE', '%'.$exclusao.'%')
            ->where('ativo', '=', 1)
        ->get();

        foreach ($atos_titulo_excluidos as $ato_titulo_excluido) {
            for ($i = 0; $i < count($atos); $i++) {
                if ($atos[$i]['id'] == $ato_titulo_excluido->id) {
                    unset($atos[$i]);
                    break;
                }
            }
        }

        return array_values($atos);
    }

    private static function excluirAtosComTexto($atos, $exclusao)
    {
        $linhas_texto_excluidos = LinhaAto::where('texto', 'LIKE', '%'.$exclusao.'%')
            ->where('ativo', '=', 1)
        ->get();

        foreach ($linhas_texto_excluidos as $linha_texto_excluido) {
            $i = 0;
            foreach ($atos as $ato) {
                if ($ato['id'] == $linha_texto_excluido->id_ato_principal) {
                    unset($atos[$i]);
                    break;
                }
                $i++;
            }
        }

        return array_values($atos);
    }
}
