<?php

namespace App\Utils;

use App\Models\Filesize;
use App\Models\FotoPerfil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class UploadFotoUtil
{
    public static function identificadorFileUpload($request, $usuario)
    {
        if (self::imagemEhValida($request)) {
            $mb = self::getMaxFileSize(Filesize::FOTO_PERFIL, 2);

            if (self::isFileSizeValid($request->file('fImage'), $mb)) {
                $nome_original = $request->file('fImage')->getClientOriginalName();
                $extensao = $request->file('fImage')->extension();

                if (self::verificarExtensao($extensao)) {
                    $nome_hash = self::salvarImagemDiretorio($request->file('fImage'), $extensao);

                    if ($nome_hash) {
                        self::desativarFotoExistente($usuario->id);
                        self::salvarImagem($nome_original, $nome_hash, $usuario->id);
                    }
                    else {
                        throw new Exception('Ocorreu um erro ao salvar a foto de perfil.');
                    }
                }
                else {
                    throw new Exception('Extensão de imagem inválida. Extensões permitidas: .png, .jpg e .jpeg');
                }
            }
            else {
                throw new Exception('Arquivo maior que ' . $mb . 'MB');
            }
        }
    }

    private static function imagemEhValida($request)
    {
        return $request->hasFile('fImage') && $request->file('fImage')->isValid();
    }

    private static function getMaxFileSize($fileType, $defaultSize)
    {
        $max_filesize = Filesize::where('id_tipo_filesize', $fileType)
            ->where('ativo', Filesize::ATIVO)
        ->value('mb');

        return is_int($max_filesize) ? $max_filesize : $defaultSize;
    }

    private static function isFileSizeValid($file, $maxSizeMb)
    {
        return $file->getSize() <= 1048576 * $maxSizeMb;
    }

    private static function verificarExtensao($extensao)
    {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        return in_array($extensao, $allowed_extensions);
    }

    private static function salvarImagemDiretorio($file, $extensao)
    {
        $nome_hash = Uuid::uuid4() . '-' . Carbon::now()->timestamp . '.' . $extensao;
        $file->storeAs('public/foto-perfil/', $nome_hash);
        return $nome_hash;
    }

    private static function salvarImagem($nome_original, $nome_hash, $id_usuario)
    {
        FotoPerfil::create([
            'nome_original' => $nome_original,
            'nome_hash' => $nome_hash,
            'id_user' => $id_usuario,
            'cadastradoPorUsuario' => Auth::user()->id
        ]);
    }

    private static function desativarFotoExistente($id)
    {
        $fotos = FotoPerfil::where('id_user', $id)
            ->where('ativo', FotoPerfil::ATIVO)
        ->get();

        foreach ($fotos as $foto) {
            if ($foto) {
                $foto->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => "Alteração de foto de perfil pelo usuário",
                    'ativo' => FotoPerfil::INATIVO
                ]);
            }
        }
    }
}
