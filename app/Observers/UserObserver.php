<?php

namespace App\Observers;

use App\Mail\ConfirmacaoEmail;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $estaLogado = Auth::check();

        if (!$estaLogado) {
            
            //gera um link temporário e criptogrado
            $link = URL::temporarySignedRoute('confirmacao_email', now()->addMinutes(20), [Crypt::encrypt($user->id)]);

            $details = [
                'assunto' => 'Confirmação de email',
                'body' => 'Segue abaixo o link',
                'cliente' => $user->pessoa->nome,
                'link' => $link,
            ];

            EmailService::configuracaoEmail();

            EmailService::novoEmail($user, $link);

            Mail::to($user->email)->send(new ConfirmacaoEmail($details));
        }

    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
