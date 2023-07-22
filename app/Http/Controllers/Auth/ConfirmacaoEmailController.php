<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmacaoEmail;
use App\Models\Email;
use App\Models\ErrorLog;
use App\Models\User;
use App\Services\EmailService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfirmacaoEmailController extends Controller
{
    use ApiResponser;

    public function encaminharLink()
    {
        try {
           return view('auth.confirmacaoEmail');

        } catch (ValidationException $e ) {
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

    // metodo para enviar email de confirmacao de cadastro
    public function linkEncaminhado(Request $request)
    {
        try {
            //validação dos campos
            $input = [
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email
            ];
            $rules = [
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email'
            ];
            $messages = [
                'cpf.required' => 'O CPF é obrigatório.',
                'cpf.max' => 'CPF: Máximo 11 caracteres.',

                'email.required' => 'O email é obrigatório.',
                'email.max' => 'Máximo 255 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            //realiza a busca no BD o email informado
            $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
                    ->where('email', '=', $request->email)
                    ->where('ativo', '=', 1)
                    ->select('id', 'email', 'id_pessoa', 'confirmacao_email', 'envio_email_confirmacao', 'envio_email_recuperacao')
                    ->first();

            // se existir usuario ativo
            if($user){

                //verificar se o e-mail está ativo, caso esteja redireciona para tela de login
                if($user->confirmacao_email == 1 || $user->confirmacao_email == true){
                    return redirect()->route('login')->with('erro', 'Usuário já foi confirmado por e-mail e as credencias já estão válidas para acessar o sistema.');
                }

                // se exceder 3 tentativas de encaminhar link, será bloqueado o e-mail cadastrado
                if($user->envio_email_confirmacao == 3){
                    return redirect()->route('login')->with('erro', 'Já foram enviados 3 links de confirmação de cadastro por email. Não é permitido enviar mais links.')->withInput();
                }

                // timestamp atual
                $agora = Carbon::now();

                //não pode enviar email antes de expirar
                $emails = Email::where('recebidoPorUsuario', '=', $user->id)
                    ->where('id_tipo_email', '=', 3) //confirmacao
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

                //Gera um link criptografado temporário e será armazenado no BD para o controle das informações
                $link = URL::temporarySignedRoute('confirmacao_email', now()->addMinutes(20), [Crypt::encrypt($user->id)]);

                //corpo da mensagem no e-mail
                $details = [
                    'assunto' => 'Confirmação de email',
                    'body' => 'Segue abaixo o link',
                    'cliente' => $user->pessoa->nomeCompleto,
                    'link' => $link,
                ];

                //Configurações de e-mail
                EmailService::configuracaoEmail();

                EmailService::linkEncaminhadoEmail($user, $link);

                Mail::to($user->email)->send(new ConfirmacaoEmail($details));

                $user->envio_email_confirmacao++;
                $user->save();

                return redirect()->route('login')->with('success', 'Confirmação de email enviado. Por favor! Cheque sua caixa de spam.');
            }
            else{
                return redirect()->route('login')->with('erro', 'CPF e/ou E-mail não localizado no sistema.')->withInput();
            }

        } catch (ValidationException $e ) {
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

    public function confirmacaoEmail(Request $request, $id)
    {
        try {
            $currentURL = URL::full();
            $route = Route::current();

            $email = Email::where('link', 'LIKE', $currentURL)
                ->first();

            if ($email){
                if ($email->expirado == 1 || ! $request->hasValidSignature()){
                    return view('mail.expiredMail3');
                }

                $user = User::find(Crypt::decrypt($id));
                $user->confirmacao_email = 1;
                $user->dataHoraConfirmacaoEmail = Carbon::now();
                $user->envio_email_confirmacao = 0;
                $user->envio_email_confirmacaoApi = 0;
                // $user->bloqueadoPorTentativa = 0;
                // $user->envio_email_confirmacaoApi = 0;
                // $user->dataEmailBloqueadoTentativa = null;
                $user->save();

                return view('mail.expiredMail', compact('user'));
            }
            else{
                return view('mail.expiredMail3');
            }
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

    public function linkEncaminhadoAPI(Request $request)
    {
        try {
            //Validação dos campos
            $input = [
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email
            ];
            $rules = [
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email'
            ];
            $messages = [
                'cpf.required' => 'O CPF é obrigatório.',
                'cpf.max' => 'CPF: Máximo 11 caracteres.',

                'email.required' => 'O email é obrigatório.',
                'email.max' => 'Máximo 255 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            //busca id e nome no bd
            $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
                ->where('email', '=', $request->email)
                ->where('ativo', '=', 1)
                ->select('id', 'email', 'id_pessoa', 'envio_email_confirmacaoApi', 'confirmacao_email')
                ->first();

            if($user){

                if($user->confirmacao_email == 1 || $user->confirmacao_email == true){
                    return $this->error('Usuário já foi confirmado por e-mail e as credencias já estão válidas para acessar o sistema.', 403);
                }

                if($user->envio_email_confirmacaoApi == 3){
                    return $this->error('Já foram enviados 3 links de confirmação de cadastro por email. Não é permitido enviar mais links.', 403);
                }

                // timestamp atual
                $agora = Carbon::now();

                //não pode enviar email antes de expirar
                $emails = Email::where('recebidoPorUsuario', '=', $user->id)
                    ->where('id_tipo_email', '=', 3) //confirmacao
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


                //Gera um link criptografado temporário e será armazenado no BD para o controle das informações
                $link = URL::temporarySignedRoute('confirmacao_email', now()->addMinutes(20), [Crypt::encrypt($user->id)]);

                //corpo da mensagem no e-mail
                $details = [
                    'assunto' => 'Confirmação de email',
                    'body' => 'Segue abaixo o link',
                    'cliente' => $user->pessoa->nomeCompleto,
                    'link' => $link,
                ];

                EmailService::configuracaoEmail();

                EmailService::linkEncaminhadoEmail($user, $link);

                Mail::to($user->email)->send(new ConfirmacaoEmail($details));

                $user->envio_email_confirmacaoApi++;
                $user->save();

                return $this->success(['Enviado link de confirmação de e-mail.']);
            }
            else{
                return $this->error('CPF e/ou E-mail não localizado no sistema.', 403);
            }

        } catch (ValidationException $e ) {
            $message = $e->errors();
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
}
