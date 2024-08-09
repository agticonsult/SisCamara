<?php

namespace App\Http\Controllers\Auth;

use App\Models\Email;
use App\Models\ErrorLog;
use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\User;
use App\Services\EmailService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    use ApiResponser;

    public function passwordReset1()
    {
        try {
            return view('auth.passwordReset1');

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
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function passwordReset2(Request $request)
    {
        try {
            $input = [
                // 'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email
            ];
            $rules = [
                // 'cpf' => 'required|min:11|max:11',
                'email' => 'required|email'
            ];
            $messages = [
                // 'cpf.required' => 'O CPF é obrigatório.',
                // 'cpf.max' => 'CPF: Máximo 11 caracteres.',

                'email.required' => 'O email é obrigatório.',
                'email.max' => 'Máximo 255 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            //busca id e nome no bd
            $user = User::where('email', '=', $request->email)
                ->where('ativo', '=', 1)
                ->select('id', 'id_pessoa', 'email', 'envio_email_recuperacao', 'bloqueadoPorTentativa')
                ->first();

            if($user){
                if ($user->envio_email_recuperacao == 3){
                    return redirect()->back()->with('erro', 'Já foram enviados 3 emails para recuperar a senha. Não é permitido enviar mais.')->withInput();
                }

                if($user->bloqueadoPorTentativa == 1 || $user->bloqueadoPorTentativa == true){
                    return redirect()->route('login')->with('erro', 'Usuário bloqueado por excesso de tentativas.')->withInput();
                }

                $agora = Carbon::now();

                //não pode enviar email antes de expirar
                $emails = Email::where('recebidoPorUsuario', '=', $user->id)
                    ->where('id_tipo_email', '=', 1)
                    ->where('expiradoEm', '>', $agora)
                    ->get();

                //Se expiradoEm > agora está valido
                //Se agora > expiradoEm não está valido
                if (Count($emails) != 0){
                    foreach($emails as $e){
                        $e->expirado = 1;
                        $e->save();
                    }
                }

                $link = URL::temporarySignedRoute('passwordReset3', now()->addMinutes(20), [Crypt::encrypt($user->id)]);

                $details = [
                    'assunto' => 'Alteração de senha',
                    'body' => 'Segue abaixo o link',
                    'cliente' => $user->pessoa->nome,
                    'link' => $link,
                ];

                //Configurações de e-mail
                EmailService::configuracaoEmail();

                EmailService::linkEncaminhadoEmail($user, $link);

                Mail::to($user->email)->send(new PasswordReset($details));

                $user->envio_email_recuperacao++;
                $user->save();

                return redirect()->route('login')->with('success', 'Email enviado. Por favor! Cheque sua caixa de spam.');
            }
            else{
                return redirect()->back()->with('erro', 'Usuário não encontrado.')->withInput();
            }

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
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }

    }

    public function passwordReset2API(Request $request)
    {
        try {
            $input = [
                // 'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email
            ];
            $rules = [
                // 'cpf' => 'required|min:11|max:11',
                'email' => 'required|email'
            ];
            $messages = [
                // 'cpf.required' => 'O CPF é obrigatório.',
                // 'cpf.max' => 'CPF: Máximo 11 caracteres.',

                'email.required' => 'O email é obrigatório.',
                'email.max' => 'Máximo 255 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            //busca id e nome no bd
            $user = User::where('email', '=', $request->email)
                ->where('ativo', '=', 1)
                ->select('id', 'id_pessoa', 'email', 'envio_email_recuperacao', 'bloqueadoPorTentativa')
                ->first();

            if($user){
                if ($user->envio_email_recuperacao == 3){
                    return $this->error('Já foram enviados 3 emails para recuperar a senha. Não é permitido enviar mais.', 403);
                }

                if($user->bloqueadoPorTentativa == 1 || $user->bloqueadoPorTentativa == true){
                    return $this->error('Usuário bloqueado por excesso de tentativas.', 403);
                }

                $agora = Carbon::now();

                //não pode enviar email antes de expirar
                $emails = Email::where('recebidoPorUsuario', '=', $user->id)
                    ->where('id_tipo_email', '=', 1)
                    ->where('expiradoEm', '>', $agora)
                    ->get();

                //Se expiradoEm > agora está valido
                //Se agora > expiradoEm não está valido
                if (Count($emails) != 0){
                    foreach($emails as $e){
                        $e->expirado = 1;
                        $e->save();
                    }
                }

                $link = URL::temporarySignedRoute('passwordReset3', now()->addMinutes(20), [Crypt::encrypt($user->id)]);

                $details = [
                    'assunto' => 'Alteração de senha',
                    'body' => 'Segue abaixo o link',
                    'cliente' => $user->pessoa->nome,
                    'link' => $link,
                ];

                //Configurações de e-mail
                EmailService::configuracaoEmail();

                EmailService::linkEncaminhadoEmail($user, $link);

                Mail::to($user->email)->send(new PasswordReset($details));

                $user->envio_email_recuperacao++;
                $user->save();

                return $this->success(['Email enviado. Por favor! Cheque sua caixa de spam.']);
            }
            else{
                return $this->error('Usuário não encontrado.', 403);
            }

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            // return redirect()->back()
            //     ->withErrors($message)
            //     ->withInput();
            return $this->errorValidation($message, 403);
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            return $this->error($ex->getMessage(), 500);
            // return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }

    }

    public function passwordReset3(Request $request, $id)
    {
        try{
            $currentURL = URL::full();
            $route = Route::current();

            $email = Email::where('link', 'LIKE', $currentURL)
                ->first();

            if ($email){
                if ($email->expirado == 1 || ! $request->hasValidSignature()){
                    return view('mail.expired');
                }

                $user = User::find(Crypt::decrypt($id));
                return view('mail.passwordUpdate', compact('user'));
            }
            else{
                return view('mail.expired');
            }

        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function passwordReset4(Request $request, $id){
        try{
            $input = [
                'password' => $request->password,
                'confirmacao' => $request->confirmacao
            ];
            $rules = [
                'password' => 'required|min:6|max:35',
                'confirmacao' => 'required|min:6|max:35'
            ];
            $messages = [
                'password.required' => 'A senha é obrigatório.',
                'password.min' => 'Senha: Minímo de 6 caracteres.',
                'password.max' => 'Senha: Máximo de 35 caracteres.',

                'confirmacao.required' => 'A senha é obrigatório.',
                'confirmacao.min' => 'Confirmação: Minímo de 6 caracteres.',
                'confirmacao.max' => 'Confirmação: Máximo de 35 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem!');
            }

            $user = User::find(Crypt::decrypt($id));

            if($user){
                $user->password = Hash::make($request->password);
                $user->envio_email_recuperacao = 0;
                $user->save();
                return redirect()->route('login')->with('success', 'Alteração realizada com sucesso!');
            }
            else{
                return redirect()->back()->with('erro', 'Usuário não encontrado.')->withInput();
            }

        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

}

