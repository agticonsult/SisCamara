<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class ExportAtoController extends Controller
{
    // Original
    public function pdfOriginal($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $mpdf = new Mpdf();

            $html = view('ato.pdf.original', compact('ato'));

            $mpdf->WriteHTML($html);

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO ORIGINAL-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_arquivo = $versao . $tipo_ato . ' N. ' . $numero . ' de ' . $de;

            return $mpdf->Output($nome_arquivo . '.pdf', 'I');
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfOriginal";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function htmlOriginal($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'html';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO ORIGINAL-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                $inicioHTML = '
                    <!doctype html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">

                        <style>

                            .titulo {
                                text-align: center;
                            }
                        </style>

                        <title>Documento</title>
                    </head>
                    <body>
                    <h2 class="titulo"> Câmara Municipal de XXXXXX</h2>
                ';
                $fimHTML = '
                    </body>
                    </html>
                ';
                fwrite($arquivo, $inicioHTML);
                fwrite($arquivo, $nome_arquivo.PHP_EOL);
                fwrite($arquivo, '<p>' . $ato->titulo . '</p>');
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        $linha = '<p>' . $linha_original_ativa->texto . '</p>';
                        // $linha = $linha_original_ativa->texto;
                        fwrite($arquivo, $linha.PHP_EOL);
                    }
                }

                fwrite($arquivo, $fimHTML);
                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfOriginal";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function textoOriginal($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'txt';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO ORIGINAL-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                fwrite($arquivo, $nome_arquivo.PHP_EOL.PHP_EOL);
                fwrite($arquivo, $ato->titulo.PHP_EOL.PHP_EOL);
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        fwrite($arquivo, $linha_original_ativa->texto.PHP_EOL);
                    }
                }

                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfOriginal";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function docOriginal($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'doc';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO ORIGINAL-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                $inicioHTML = '
                    <!doctype html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">

                        <style>

                            .titulo {
                                text-align: center;
                            }
                        </style>

                        <title>Documento</title>
                    </head>
                    <body>
                    <h2 class="titulo"> Câmara Municipal de XXXXXX</h2>
                ';
                $fimHTML = '
                    </body>
                    </html>
                ';
                fwrite($arquivo, $inicioHTML);
                fwrite($arquivo, $nome_arquivo.PHP_EOL);
                fwrite($arquivo, '<p>' . $ato->titulo . '</p>');
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        $linha = '<p>' . $linha_original_ativa->texto . '</p>';
                        // $linha = $linha_original_ativa->texto;
                        fwrite($arquivo, $linha.PHP_EOL);
                    }
                }

                fwrite($arquivo, $fimHTML);
                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfOriginal";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    // Consolidada
    public function pdfConsolidada($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            $mpdf = new Mpdf();

            $html = view('ato.pdf.consolidada', compact('ato'));

            $mpdf->WriteHTML($html);

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO CONSOLIDADA-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_arquivo = $versao . $tipo_ato . ' N. ' . $numero . ' de ' . $de;

            return $mpdf->Output($nome_arquivo . '.pdf', 'I');
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfConsolidada";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function htmlConsolidada($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'html';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO CONSOLIDADA-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                $inicioHTML = '
                    <!doctype html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">

                        <style>

                            .titulo {
                                text-align: center;
                            }
                        </style>

                        <title>Documento</title>
                    </head>
                    <body>
                    <h2 class="titulo"> Câmara Municipal de XXXXXX</h2>
                ';
                $fimHTML = '
                    </body>
                    </html>
                ';
                fwrite($arquivo, $inicioHTML);
                fwrite($arquivo, $nome_arquivo.PHP_EOL);
                fwrite($arquivo, '<p>' . $ato->titulo . '</p>');
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        $linha = '<p>' . $linha_original_ativa->texto . '</p>';
                        // $linha = $linha_original_ativa->texto;
                        fwrite($arquivo, $linha.PHP_EOL);
                    }
                }

                fwrite($arquivo, $fimHTML);
                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfConsolidada";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function textoConsolidada($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'txt';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO CONSOLIDADA-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                fwrite($arquivo, $nome_arquivo.PHP_EOL.PHP_EOL);
                fwrite($arquivo, $ato->titulo.PHP_EOL.PHP_EOL);
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        fwrite($arquivo, $linha_original_ativa->texto.PHP_EOL);
                    }
                }

                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfConsolidada";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function docConsolidada($id)
    {
        try {
            if (Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$ato){
                return redirect()->back()->with('erro', 'Ato inválido.');
            }

            // $path = storage_path('app/public/Ato/Export/temp');
            // Verificando se existe o diretório /anexos
            // Caso não exista, crie
            $dir_anexo = storage_path('app/public/Ato/Export/temp');
            $existe_dir_anexo = File::isDirectory($dir_anexo);

            if (!$existe_dir_anexo){

                $criandoDiretorioAnexo = File::makeDirectory($dir_anexo);

                if (!$criandoDiretorioAnexo){
                    return back()->with('erro', 'Contate o administrador do sistema');
                }
            }


            $now = Carbon::now();
            $extensao = 'doc';

            // nome do arquivo
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            $versao = 'VERSAO CONSOLIDADA-';
            $tipo_ato = 'Tipo de ato não informado';
            $numero = 'não informado';
            $de = 'Tipo de ato não informado';

            if ($ato->id_tipo_ato != null){
                $tipo_ato = $ato->tipo_ato->descricao;
            }

            if ($ato->numero != null){
                $numero = $ato->numero;
            }

            if ($ato->created_at != null){
                $de = strftime('%d de %B de %Y', strtotime($ato->created_at));
            }

            $nome_hash = $now->timestamp;
            $nome_arquivo = $tipo_ato . ' N. ' . $numero . ' de ' . $de;
            $nome_original = $nome_hash . ' - ' . $versao . $nome_arquivo . '.' . $extensao;
            $arquivo = fopen($dir_anexo . '/' . $nome_original,'w');

            if ($arquivo){

                $inicioHTML = '
                    <!doctype html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport"
                            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">

                        <style>

                            .titulo {
                                text-align: center;
                            }
                        </style>

                        <title>Documento</title>
                    </head>
                    <body>
                    <h2 class="titulo"> Câmara Municipal de XXXXXX</h2>
                ';
                $fimHTML = '
                    </body>
                    </html>
                ';
                fwrite($arquivo, $inicioHTML);
                fwrite($arquivo, $nome_arquivo.PHP_EOL);
                fwrite($arquivo, '<p>' . $ato->titulo . '</p>');
                if (Count($ato->linhas_originais_ativas()) != 0){
                    foreach($ato->linhas_originais_ativas() as $linha_original_ativa){
                        $linha = '<p>' . $linha_original_ativa->texto . '</p>';
                        // $linha = $linha_original_ativa->texto;
                        fwrite($arquivo, $linha.PHP_EOL);
                    }
                }

                fwrite($arquivo, $fimHTML);
                fclose($arquivo);

                // Pode baixar se criou corretamente
                $existe = Storage::disk('public')->exists('Ato/Export/temp/');

                // dd($existe);
                if ($existe){
                    $path = storage_path('app/public/Ato/Export/temp/'.$nome_original);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename=' . basename($path));
                    readfile($path);

                    File::delete($path);
                }
                else{
                    return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Ocorreu um erro gerar o arquivo HTML.');
            }
        }
        catch(\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ExportAtoController";
            $erro->funcao = "pdfConsolidada";
            if (Auth::check()) {
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
