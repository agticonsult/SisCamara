<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Models\ErrorLog;
use App\Models\Estado;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Models\Municipio;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Pessoa;
use App\Models\TipoPerfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\ValidadorCPFService;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = User::where('id', '=', auth()->user()->id)
                ->select(
                    'id', 'cpf', 'email', 'telefone_celular', 'telefone_celular2', 'sexo',
                    'id_pessoa'
                )->first();

            $estados = Estado::where('ativo', '=', 1)->get();
            $municipios = Municipio::where('id_estado', '=', '16')->orderBy('descricao', 'asc')->where('ativo', '=', 1)->get();
            $foto_perfil = FotoPerfil::where('id_user', '=', auth()->user()->id)->where('ativo', '=', 1)->first();

            $temFoto = 0;

            if ($foto_perfil){
                $existe = Storage::disk('public')->exists('foto-perfil/'.$foto_perfil->nome_hash);
                // $existe = public_path('foto-perfil/'.$foto_perfil->nome_hash);
                if ($existe){
                    $temFoto = 1;
                }
            }

            // $municipio = Municipio::where('codIbge', '=', '4113700')->where('ativo', '=', 1)->first();dd($municipio);

            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();

            return view('home.home', compact('user', 'estados', 'municipios', 'foto_perfil', 'temFoto', 'filesize'));
        }
        catch(\Exception $ex){
            // return $ex->getMessage();
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "HomeController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //validação dos campos
            $input = [
                //validação usuário
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email,

                //valiação dados pessoais
                'nomeCompleto' => $request->nomeCompleto,
                'apelidoFantasia' => $request->apelidoFantasia,
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'sexo' => $request->sexo,
                'cep' => $request->cep,
                'endereco' => $request->endereco,
                'bairro' => $request->bairro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'ponto_referencia' => $request->ponto_referencia,
                'id_municipio' => $request->id_municipio
            ];
            $rules = [
                //Usuário
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email',

                //Pessoa
                'nomeCompleto' => 'required|max:255',
                'apelidoFantasia' => 'max:255',
                'dt_nascimento_fundacao' => 'required|date',
                'sexo' => 'max:255',
                'cep' => 'max:255',
                'endereco' => 'max:255',
                'bairro' => 'max:255',
                'numero' => 'max:255',
                'complemento' => 'max:255',
                'ponto_referencia' => 'max:255',
                'id_municipio' => 'required'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            //Busca o usuário no BD
            $user = User::find($id);

            // se cpf antigo é diferente do cpf novo
            if ($user->cpf != preg_replace('/[^0-9]/', '', $request->cpf)){ // mudou o cpf

                // verificar se o novo cpf não está cadastrado no sistema
                $userCpf = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))->first();
                if ($userCpf){
                    return redirect()->back()->with('erro', 'Este CPF já está cadastrado no sistema.')->withInput();
                }

                //verificando se o cpf é valido
                if (!ValidadorCPFService::ehValido($request->cpf)) {
                    return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
                }

            }

            // se email antigo é diferente do email novo
            if ($user->email != $request->email){ // mudou o cpf

                // verificar se o novo email não está cadastrado no sistema
                $userEmail = User::where('email', '=', $request->email)->first();
                if ($userEmail){
                    return redirect()->back()->with('erro', 'Este EMAIL já está cadastrado no sistema.')->withInput();
                }
            }

            $user->email = $request->email;
            $user->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $user->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
            $user->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);
            $user->sexo = $request->sexo;

            if ($request->password != null){

                //verificar se a senha antiga está correta
                if (!Hash::check($request->senha_antiga, $user->password)){
                    return redirect()->back()->with('erro', 'A senha antiga está incorreta.')->withInput();
                }

                //verifica se a confirmação de senha estão ok
                if($request->password != $request->confirmacao){
                    return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
                }

                $tamanho_senha = strlen($request->password);
                if ($tamanho_senha < 6 || $tamanho_senha > 35){
                    return redirect()->back()->with('erro', 'Senha inválida.')->withInput();
                }

                $user->password = Hash::make($request->password);
            }

            //Dados de Pessoa
            if($user->id_pessoa){
                // $pessoa = Pessoa::where('id', '=', $user->id_pessoa)->first();
                $pessoa = Pessoa::find($user->id_pessoa);
                $pessoa->nomeCompleto = $request->nomeCompleto;
                $pessoa->apelidoFantasia = $request->apelidoFantasia;
                $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
                $pessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
                $pessoa->endereco = $request->endereco;
                $pessoa->bairro = $request->bairro;
                $pessoa->numero = $request->numero;
                $pessoa->complemento = $request->complemento;
                $pessoa->ponto_referencia = $request->ponto_referencia;
                $pessoa->id_municipio = $request->id_municipio;
                $pessoa->save();
            }

            $user->ativo = 1;
            $user->save();

            return redirect()->route('home')->with('success', 'Cadastro alterado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "HomeController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }


    // public function indexExterno()
    // {
    //     try{
    //         if(auth()->user()->id_perfil != 3){
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         $user = User::where('id', '=', auth()->user()->id)
    //             ->select(
    //                 'id', 'cpf', 'email', 'telefone_celular', 'telefone_celular2', 'sexo',
    //                 'id_pessoa', 'id_perfil'
    //             )->first();

    //         $estados = Estado::where('ativo', '=', 1)->get();
    //         $municipios = Municipio::where('id_estado', '=', $user->pessoa->id_estado)->orderBy('descricao', 'asc')->where('ativo', '=', 1)->get();
    //         $distritos = Distrito::where('id_municipio', '=', $user->pessoa->id_municipio)->where('ativo', '=', 1)->get();

    //         return view('home.homeExterno', compact('user', 'estados', 'municipios', 'distritos'));
    //     }
    //     catch(\Exception $ex){
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "HomeController";
    //         $erro->funcao = "indexExterno";
    //         if (Auth::check()){
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

    public function alterarPerfil(Request $request)
    {
        try {
            // validação do parâmetro (id_perfil) recebido na request
            if (isset($request->perfil_ativo)){
                if (
                    $request->perfil_ativo != 1 && $request->perfil_ativo != 2 &&
                    $request->perfil_ativo != 3 && $request->perfil_ativo != 4
                ){
                    return redirect()->route('home')->with('erro', 'Perfil inválido.');
                }
                $existePerfilAtivo = TipoPerfil::where('id', '=', $request->perfil_ativo)->where('ativo', '=', 1)->first();
                if (!$existePerfilAtivo){
                    return redirect()->route('home')->with('erro', 'Não autorizado.');
                }
                // verifica se o usuario realmente possui acesso ao perfil recebido na requisição
                $possuiEssePerfil = PerfilUser::where('id_user', '=', auth()->user()->id)
                    ->where('id_tipo_perfil', '=', $request->perfil_ativo)
                    ->where('ativo', '=', 1)
                    ->first();

                if (!$possuiEssePerfil){
                    return redirect()->route('home')->with('erro', 'Não autorizado.');
                }

                $user = User::where('id', '=', Auth::user()->id)->first();
                $user->id_tipo_perfil = $request->perfil_ativo;
                $user->save();
            }

            return redirect()->route('home');
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "HomeController";
            $erro->funcao = "alterarPerfil";
            if (Auth::check()){
                $erro->erro = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    // public function formUpload()
    // {
    //     try {
    //        return view('home.upload');

    //     } catch (\Exception $ex) {
    //         return $ex->getMessage();
    //     }
    // }

    // //Realiza a importação do arquivo
    // public function upload(Request $request)
    // {
    //     try {
    //         //array
    //         $documento = $request->file('documento');
    //         // $documento = $request->documento;
    //         $data = file_get_contents($documento);
    //         // $xmls = simplexml_load_string($data);
    //         // $json = json_encode($xmls);
    //         $array = json_decode($data,TRUE);
    //         // $array = $array['database']['table'];

    //         // dd($array);
    //         //array 2
    //         // $documento2 = $request->file('documento2');
    //         // $data2 = file_get_contents($documento2);
    //         // $xmls2 = simplexml_load_string($data2);
    //         // $json2 = json_encode($xmls2);
    //         // $array_ate_400 = json_decode($json2,TRUE);
    //         // $array_ate_400 = $array_ate_400['database']['table'];

    //         // $script = "INSERT INTO `composicao_familiars` (id_assinante, `id`, `nome`, `idade`, `deficiencia`, `id_parentesco`, `id_beneficiario`, `ativo`, `created_at`, `updated_at`) VALUES <br>";
    //         $script = "";


    //         $municipios = Municipio::where('id_estado', '=', 16)->orderBy('descricao', 'asc')->get();
    //         foreach($municipios as $m){

    //             $nome_mun = $m->descricao;
    //             $id = $m->id;
    //             // dd($nome_mun);

    //             for ($i=0; $i < Count($array); $i++) {

    //                 $selected = $array[$i];
    //                 $municipio = $selected['municipio'];
    //                 $nome = $municipio['nome'];
    //                 // dd($nome);

    //                 // if (strcmp($nome_mun, $nome) !== 1) {
    //                 if ($nome_mun === $nome ) {
    //                     // echo '$var1 is not equal to $var2 in a case sensitive string comparison';
    //                     $descricao = $selected['nome'];

    //                     // $insert = '["descricao" => "' . $descricao . '", "id_municipio" => ' . $id . ', "ativo" => 1], <br>';

    //                     $insert = ' ( \'' . $descricao . '\', ' . $id . ',  true), <br>';

    //                     $script = $script . $insert;
    //                 }

    //             }
    //         }


    //         echo "<h1>SCRIPT</h1>";
    //         echo $script;

    //         // for ($i=2; $i < Count($array_mais_500); $i++) {
    //         //     $selected = $array_mais_500[$i]['column'];

    //         //     $insert = "(
    //         //         $selected[0], '$selected[1]', '$selected[2]', '$selected[3]', '$selected[4]', '$selected[5]', '$selected[6]', '$selected[7]', '$selected[8]',
    //         //         '$selected[9]', '$selected[10]', '$selected[11]', '$selected[12]', '$selected[13]', '$selected[14]', '$selected[15]', '$selected[16]',
    //         //         '$selected[17]', $selected[18], '$selected[19]', $selected[20], '$selected[21]', '$selected[22]', '$selected[23]', '$selected[24]',
    //         //         '$selected[25]', '$selected[26]', '$selected[27]', '$selected[28]', '$selected[29]', $selected[30], '$selected[31]', '$selected[32]',
    //         //         '$selected[33]', '$selected[34]', '$selected[35]', $selected[36], $selected[37], $selected[38], 5, $selected[39], $selected[40],
    //         //         $selected[41], $selected[42], $selected[43], $selected[44], $selected[45]
    //         //     ), <br>";

    //         //     $script = $script . $insert;

    //         // }
    //         // dd([0]['column'][0]);
    //         // $array = $array['beneficiario'];

    //         // $total = Count($array);

    //         // //array 2
    //         // $documento2 = $request->file('documento2');
    //         // $data2 = file_get_contents($documento2);
    //         // $xmls2 = simplexml_load_string($data2);

    //         // $json2 = json_encode($xmls2);
    //         // $array2 = json_decode($json2,TRUE);
    //         // $array2 = $array2['beneficiario'];

    //         // $total2 = Count($array2);

    //         // $script_anexo = "SELECT * FROM users WHERE ";

    //         // for ($i=0; $i < $total; $i++) {
    //         //     $selected = $array[$i];

    //     } catch (\Exception $ex) {
    //         return $ex->getMessage();
    //     }
    // }

    public function information()
    {
        try{
            return view('information');
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "HomeController";
            $erro->funcao = "information";
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
