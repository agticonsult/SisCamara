<?php

namespace App\Http\Controllers;

use App\Models\AssuntoAto;
use App\Models\Ato;
use App\Models\ClassificacaoAto;
use App\Models\ErrorLog;
use App\Models\FormaPublicacaoAto;
use App\Models\LinhaAto;
use App\Models\OrgaoAto;
use App\Models\TipoAto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtoPublicoController extends Controller
{
    public function index()
    {
        try {
            $atos = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
                ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
                ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
                ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
                ->where('atos.ativo', '=', 1)
                ->select(
                    'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                    'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
                )
                ->get();

            $classificacaos = ClassificacaoAto::where('ativo', '=', 1)->get();
            $assuntos = AssuntoAto::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();
            $orgaos = OrgaoAto::where('ativo', '=', 1)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', 1)->get();

            return view('ato.publico.index', compact('atos', 'classificacaos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoPublicoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show($id)
    {
        try {
            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            // dd($ato->todas_linhas_ativas());

            return view('ato.publico.show', compact('ato'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "show";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function buscaLivre(Request $request, Ato $ato)
    {
        try {

            if ($request->palavra == null && $request->exclusao == null){
                return redirect()->route('web_publica.ato.index');
            }

            if ($request->palavra != null && $request->exclusao != null){

                // atos com título
                $atos_titulo = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
                    ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
                    ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
                    ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
                    ->where('atos.titulo', 'LIKE', '%'.$request->palavra.'%')
                    ->where('atos.ativo', '=', 1)
                    ->select(
                        'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                        'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
                    )
                    ->get();

                // linhas com texto
                $linhas_texto = LinhaAto::where('texto', 'LIKE', '%'.$request->palavra.'%')
                    ->where('ativo', '=', 1)
                    ->get();

                // atos com as palavras no texto ou no título
                $atos = $atos_titulo->toArray();
                foreach ($linhas_texto as $linha_texto) {
                    $tem = 0;
                    for ($i=0; $i<Count($atos); $i++){
                        if ($atos[$i]['id'] == $linha_texto->id_ato_principal){
                            $tem = 1;
                            break;
                        }
                    }
                    if ($tem == 0){
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

                // excluindo atos com título
                $atos_titulo_excluidos = Ato::where('titulo', 'LIKE', '%'.$request->exclusao.'%')
                    ->where('ativo', '=', 1)
                    ->get();

                // excluindo atos com texto
                $linhas_texto_excluidos = LinhaAto::where('texto', 'LIKE', '%'.$request->exclusao.'%')
                    ->where('ativo', '=', 1)
                    ->get();

                // excluindo atos com titulo = $request->exclusao
                foreach ($atos_titulo_excluidos as $ato_titulo_excluido) {
                    for ($i=0; $i<Count($atos); $i++){
                        if ($atos[$i]['id'] == $ato_titulo_excluido->id){
                            unset($atos[$i]);
                            break;
                        }
                    }
                }

                $i = 0;
                // excluindo atos com texto = $request->exclusao
                foreach ($linhas_texto_excluidos as $linha_texto_excluido) {

                    foreach ($atos as $ato) {
                        if ($atos[$i]['id'] == $linha_texto_excluido->id_ato_principal){
                            unset($atos[$i]);
                            break;
                        }
                        $i++;
                    }
                }
            }
            else{

                if ($request->palavra != null){
                    // atos com título
                    $atos_titulo = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
                        ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
                        ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
                        ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
                        ->where('atos.titulo', 'LIKE', '%'.$request->palavra.'%')
                        ->where('atos.ativo', '=', 1)
                        ->select(
                            'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                            'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
                        )
                        ->get();

                    // linhas com texto
                    $linhas_texto = LinhaAto::where('texto', 'LIKE', '%'.$request->palavra.'%')
                        ->where('ativo', '=', 1)
                        // ->select('id_ato_principal as id')
                        ->get();

                    // atos com as palavras no texto ou no título
                    $atos = $atos_titulo->toArray();
                    foreach ($linhas_texto as $linha_texto) {
                        $tem = 0;
                        for ($i=0; $i<Count($atos); $i++){
                            if ($atos[$i]['id'] == $linha_texto->id_ato_principal){
                                $tem = 1;
                                break;
                            }
                        }
                        if ($tem == 0){
                            $linha_texto_ato_principal = [
                                'id' => $linha_texto->id_ato_principal,
                                'titulo' => $linha_texto->ato_principal->titulo,
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
                }

                if ($request->exclusao != null){

                    $i = 0;

                    $todos_atos = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
                        ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
                        ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
                        ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
                        ->where('atos.ativo', '=', 1)
                        ->select(
                            'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                            'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
                        )
                        ->get();
                    $atos = $todos_atos->toArray();

                    // excluindo atos com título
                    $atos_titulo_excluidos = Ato::leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
                        ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
                        ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
                        ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
                        ->where('atos.titulo', 'LIKE', '%'.$request->exclusao.'%')
                        ->where('atos.ativo', '=', 1)
                        ->select(
                            'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
                            'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
                        )
                        ->get();

                    // excluindo atos com texto
                    $linhas_texto_excluidos = LinhaAto::where('texto', 'LIKE', '%'.$request->exclusao.'%')
                        ->where('ativo', '=', 1)
                        ->get();

                    $i = 0;
                    // excluindo atos com titulo = $request->exclusao
                    foreach ($atos_titulo_excluidos as $ato_titulo_excluido) {
                        foreach ($atos as $ato) {
                            if ($atos[$i]['id'] == $ato_titulo_excluido->id){
                                unset($atos[$i]);
                                break;
                            }
                            $i++;
                        }
                    }

                    $i = 0;
                    // excluindo atos com texto = $request->exclusao
                    foreach ($linhas_texto_excluidos as $linha_texto_excluido) {
                        foreach ($atos as $ato) {
                            if ($ato['id'] == $linha_texto_excluido->id_ato_principal){
                                unset($atos[$i]);
                                break;
                            }
                            $i++;
                        }
                    }
                }
            }

            $classificacaos = ClassificacaoAto::where('ativo', '=', 1)->get();
            $assuntos = AssuntoAto::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();
            $orgaos = OrgaoAto::where('ativo', '=', 1)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', 1)->get();

            return view('ato.publico.index', compact('atos', 'classificacaos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoPublicoController";
            $erro->funcao = "buscar";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function buscaEspecifica(Request $request, Ato $ato)
    {
        try {
            $filtros = $request->except('_method', '_token');
            $atos = $ato->buscar($filtros);
            $classificacaos = ClassificacaoAto::where('ativo', '=', 1)->get();
            $assuntos = AssuntoAto::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();
            $orgaos = OrgaoAto::where('ativo', '=', 1)->get();
            $forma_publicacaos = FormaPublicacaoAto::where('ativo', '=', 1)->get();

            return view('ato.publico.index', compact('atos', 'filtros', 'classificacaos', 'assuntos', 'tipo_atos', 'orgaos', 'forma_publicacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoPublicoController";
            $erro->funcao = "buscar";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}


