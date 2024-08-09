<?php

namespace App\Services;

use App\Models\AuxiliarDocumentoDepartamento;

class DepartamentoTramitacaoService
{
    public static function tramitacaoAutomatica($documento, $departamentos)
    {
        foreach ($departamentos as $key => $departamento) {
            if ($key == 0) {
                AuxiliarDocumentoDepartamento::create([
                    'id_documento' => $documento->id,
                    'id_departamento' => $departamento->id_departamento,
                    'ordem' => $key + 1,
                    'atual' => true
                ]);
            }
            else {
                AuxiliarDocumentoDepartamento::create([
                    'id_documento' => $documento->id,
                    'id_departamento' => $departamento->id_departamento,
                    'ordem' => $key + 1,
                    'atual' => false
                ]);
            }
        }
    }

    public static function tramitacaoManual($documento, $departamentos, $id_departamento)
    {
        foreach ($departamentos as $departamento) {
            if ($departamento->id_departamento == $id_departamento) {
                AuxiliarDocumentoDepartamento::create([
                    'id_documento' => $documento->id,
                    'id_departamento' => $departamento->id_departamento,
                    'ordem' => 1,
                    'atual' => true
                ]);
            }
            else{
                AuxiliarDocumentoDepartamento::create([
                    'id_documento' => $documento->id,
                    'id_departamento' => $departamento->id_departamento,
                    // 'ordem' => 1,
                    'atual' => false
                ]);
            }
        }
    }
}
