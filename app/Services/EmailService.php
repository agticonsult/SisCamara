<?php

namespace App\Services;

use App\Models\Email;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class EmailService
{
    //configuração de email remetente
    public static function configuracaoEmail()
    {
        Config::set('mail.mailers.smtp.username', 'no-reply@agile.inf.br');
        Config::set('mail.mailers.smtp.password', 'sup2011@2019');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', '587');
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.from.address', 'no-reply@agile.inf.br');
        Config::set('mail.from.name', 'SisCamara');
    }

    //cria um novo e-email para um novo usuário assim que a função é chamada
    public static function novoEmail(User $novoUsuario, $link)
    {
        $email = new Email();
        $email->enviadoEm = Carbon::now();
        $email->expiradoEm = Carbon::now()->addMinutes(20);
        $email->expirarMin = 20;
        $email->expirarHora = 0;
        $email->link = $link;
        $email->emailRecebido = $novoUsuario->email;
        $email->emailEnviado = 'no-reply@agile.inf.br';
        $email->recebidoPorUsuario = $novoUsuario->id;
        $email->id_tipo_email = 3;
        $email->ativo = 1;
        $email->expirado = 0;
        $email->save();
    }

    public static function linkEncaminhadoEmail(User $user, $link)
    {
        $email = new Email();
        $email->enviadoEm = Carbon::now();
        $email->expiradoEm = Carbon::now()->addMinutes(20);
        $email->expirarMin = 20;
        $email->expirarHora = 0;
        $email->link = $link;
        $email->emailRecebido = $user->email;
        $email->emailEnviado = 'no-reply@agile.inf.br';
        $email->recebidoPorUsuario = $user->id;
        $email->id_tipo_email = 3;
        $email->ativo = 1;
        $email->expirado = 0;
        $email->save();
    }
}
