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
            $atos = Ato::where('ativo', '=', 1)->get();
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
                $atos_titulo = Ato::where('titulo', 'LIKE', '%'.$request->palavra.'%')->where('ativo', '=', 1)->get();

                // linhas com texto
                $linhas_texto = LinhaAto::where('texto', 'LIKE', '%'.$request->palavra.'%')
                    ->where('ativo', '=', 1)
                    // ->select('id_ato_principal as id')
                    ->get();

                $array_atos_titulo_texto = [];
                foreach ($linhas_texto as $linha_texto) {
                    foreach ($atos_titulo as $ato_titulo) {
                        if ($linha_texto->id_ato_principal == $ato_titulo->id){

                        }
                    }

                }

                dd($atos_titulo, $linhas_texto);

                // $ato_palavras = Ato::leftJoin('linha_atos', 'linha_atos.id_ato_principal', '=', 'atos.id')
                //     ->where(function (Builder $query) use ($request) {
                //         return
                //             $query->where('atos.titulo', 'LIKE', $request->palavra)
                //                 ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf));
                //             })
                //     ->
                dd('Tem os 2', $request->all());
            }
            dd($request->all());
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
            dd($ex->getMessage());
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
            dd($ex->getMessage());
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
