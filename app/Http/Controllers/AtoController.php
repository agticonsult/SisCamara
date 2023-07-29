<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use App\Models\Grupo;
use App\Models\LinhaAto;
use App\Models\TipoAto;
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
                'id_evento' => $request->id_evento

            ];
            $rules = [
                'id_evento' => 'required|integer'
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

            $ato = new Ato();
            $ato->titulo = $request->titulo;
            $ato->ano = $request->ano;
            $ato->numero = $request->numero;
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
                    $linha_ato->id_ato = $ato->id;
                    $linha_ato->cadastradoPorUsuario = Auth::user()->id;
                    $linha_ato->ativo = 1;
                    $linha_ato->save();
                }
            }

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

    public function show(Ato $ato)
    {
        //
    }

    public function edit(Ato $ato)
    {
        //
    }

    public function update(Request $request, Ato $ato)
    {
        //
    }

    public function destroy(Ato $ato)
    {
        //
    }
}
