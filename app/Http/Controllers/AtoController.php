<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use App\Models\Grupo;
use App\Models\LinhaAto;
use App\Models\TipoAto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AtoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $atos = Ato::where('ativo', '=', 1)->get();

            return view('ato.index', compact('atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $grupos = Grupo::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();

            return view('ato.create', compact('grupos', 'tipo_atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'titulo' => $request->titulo,
                'ano' => $request->ano,
                'numero' => $request->numero,
                'id_grupo' => $request->id_grupo,
                'id_tipo_ato' => $request->id_tipo_ato,
                'subtitulo' => $request->subtitulo,
                'corpo_texto' => $request->corpo_texto
            ];
            $rules = [
                'titulo' => 'required',
                'ano' => 'required|integer',
                'numero' => 'required',
                'id_grupo' => 'required',
                'id_tipo_ato' => 'required',
                'subtitulo' => 'required',
                'corpo_texto' => 'required',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $grupo = Grupo::where('id', '=', $request->id_grupo)->where('ativo', '=', 1)->first();
            if (!$grupo){
                return redirect()->back()->with('erro', 'Grupo inválido.');
            }

            $tipo_ato = Grupo::where('id', '=', $request->id_tipo_ato)->where('ativo', '=', 1)->first();
            if (!$tipo_ato){
                return redirect()->back()->with('erro', 'Tipo de ato inválido.');
            }

            $altera_dispositivo = 0;
            if (isset($request->altera_dispositivo)){
                if ($request->altera_dispositivo == 'on'){
                    $altera_dispositivo = 1;
                }
            }

            $ato = new Ato();
            $ato->titulo = $request->titulo;
            $ato->ano = $request->ano;
            $ato->numero = $request->numero;
            $ato->altera_dispositivo = $altera_dispositivo;
            $ato->id_grupo = $request->id_grupo;
            $ato->id_tipo_ato = $request->id_tipo_ato;
            $ato->subtitulo = $request->subtitulo;
            $ato->cadastradoPorUsuario = Auth::user()->id;
            $ato->ativo = 1;
            $ato->save();

            $corpo_texto = $request->corpo_texto;
            $corpo_texto_alterado = preg_replace('/\r/', '', $corpo_texto);
            $array_corpo_texto = explode("\n", $corpo_texto_alterado);

            for ($i = 0; $i < Count($array_corpo_texto); $i++){
                if ($array_corpo_texto[$i] != ""){
                    $linha_ato = new LinhaAto();
                    $linha_ato->ordem = $i + 1;
                    $linha_ato->texto = $array_corpo_texto[$i];
                    $linha_ato->alterado = 0;
                    $linha_ato->id_ato_principal = $ato->id;
                    $linha_ato->id_tipo_linha = 1;
                    $linha_ato->cadastradoPorUsuario = Auth::user()->id;
                    $linha_ato->ativo = 1;
                    $linha_ato->save();
                }
            }

            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
            date_default_timezone_set('America/Campo_Grande');

            return redirect()->route('ato.index')->with('success', 'Cadastro realizado com sucesso.');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "store";
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
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            // dd($ato->todas_linhas_ativas());

            return view('ato.show', compact('ato'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $ato = Ato::where('id', '=', $id)->where('ativo', '=', 1)->first();
            $atos_relacionados = Ato::where('altera_dispositivo', '=', 1)->where('ativo', '=', 1)->get();
            // dd($ato->todas_linhas_ativas());

            return view('ato.edit', compact('ato', 'atos_relacionados'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function alterarLinha(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_linha_ato' => $request->id_linha_ato,
                'corpo_texto' => $request->corpo_texto,
            ];
            $rules = [
                'id_linha_ato' => 'required|integer',
                'corpo_texto' => 'required',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $linha_antiga = LinhaAto::where('id', '=', $request->id_linha_ato)->where('ativo', '=', 1)->first();
            if (!$linha_antiga){
                return redirect()->back()->with('erro', 'Linha inválida.');
            }

            $linha_antiga->alterado = 1;
            $linha_antiga->save();

            // dd($request->all());
            $linha_ato = new LinhaAto();
            $linha_ato->ordem = $linha_antiga->ordem;
            $linha_ato->sub_ordem = $linha_antiga->sub_ordem + 1;
            $linha_ato->texto = $request->corpo_texto;
            $linha_ato->id_ato_principal = $linha_antiga->id_ato_principal;
            $linha_ato->id_tipo_linha = 2;
            $linha_ato->cadastradoPorUsuario = Auth::user()->id;
            $linha_ato->ativo = 1;
            $linha_ato->save();

            return redirect()->route('ato.index')->with('success', 'Alteração realizada com sucesso.');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy()
    {
        //
    }
}
