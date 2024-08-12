<?php

namespace App\Services;

use App\Models\AnexoAto;
use App\Models\Filesize;
use Exception;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class AnexoAtoService
{
    public static function processarArquivos($request, $ato)
    {
        $arquivo = $request->file('anexo');
        $mb = self::getMaxFilesize();
        self::handleArquivos($arquivo, $ato, $mb);

        Alert::toast('Cadastro realizado com sucesso','success');
    }

    private static function getMaxFilesize()
    {
        $max_filesize = Filesize::where('id_tipo_filesize', Filesize::ANEXO_ATO)->where('ativo', Filesize::ATIVO)->first();
        if ($max_filesize && $max_filesize->mb && is_int($max_filesize->mb)) {
            return $max_filesize->mb;
        }

        return 2; // valor padrão
    }

    private static function handleArquivos($arquivo, $ato, $mb)
    {
        $nome_original = $arquivo->getClientOriginalName();
        if (self::arquivoValido($arquivo, $mb)) {
            $extensao = $arquivo->extension();
            if (self::extensaoValida($extensao)) {
                self::salvarArquivo($arquivo, $ato, $extensao, $nome_original);
            }
            else {
                throw new Exception('Extensão inválida.');
            }
        }
        else {
            throw new Exception('arquivo inválido ou maior que ' . $mb . 'MB');
        }

    }

    private static function arquivoValido($arquivo, $mb)
    {
        return $arquivo->isValid() && (filesize($arquivo) <= 1048576 * $mb);
    }

    private static function extensaoValida($extensao)
    {
        $extensoesValidas = ['txt', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'odt', 'jpg', 'jpeg', 'png'];
        return in_array($extensao, $extensoesValidas);
    }

    private static function salvarArquivo($arquivo, $ato, $extensao, $nome_original)
    {
        $nome_hash = Uuid::uuid4() . '-' . $extensao;
        $upload = $arquivo->storeAs('public/Ato/Anexo/', $nome_hash);

        if ($upload) {
            self::criarAnexo($nome_original, $nome_hash, $ato->id);
        }
        else {
            Alert::toast('falha ao salvar o arquivo','error');
        }
    }

    private static function criarAnexo($nome_original, $nome_hash, $id_ato)
    {
        AnexoAto::create([
            'nome_original' => $nome_original,
            'nome_hash' => $nome_hash,
            'diretorio' => 'public/Ato/Anexo',
            'id_ato' => $id_ato,
            'cadastradoPorUsuario' => Auth::user()->id,
        ]);
    }
}
