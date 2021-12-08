<?php

namespace App\Authentication\Resetters;

use Config\Email;
use Myth\Auth\Authentication\Resetters\BaseResetter;
use Myth\Auth\Authentication\Resetters\ResetterInterface;
use Myth\Auth\Entities\User;

/**
 * Class EmailResetter
 *
 * Sends a reset password email to user.
 *
 * @package Myth\Auth\Authentication\Resetters
 */
class EmailResetter extends BaseResetter implements ResetterInterface
{
    /**
     * Sends a reset email
     *
     * @param User $user
     *
     * @return bool
     */
    public function send(User $user = null): bool
    {
        $twig = new \Kenjis\CI4Twig\Twig();

        $email = service('email');
        $config = new Email();

        $sent = $email->setFrom($config->fromEmail, $config->fromName)
              ->setTo($user->email)
              ->setSubject(lang('Auth.forgotSubject'))
              ->setMessage($twig->render($this->config->views['emailForgot'], [
                  'email' => $user->email,
                  'site' => parse_url(base_url())['host'],
                  'link' => base_url('reset-password') . '?token=' . $user->reset_hash . '&email=' . rawurlencode($user->email),
                ]))
              ->setMailType('html')
              ->send();

        if (! $sent)
        {
            $this->error = lang('Auth.errorEmailSent', [$user->email]);
            return false;
        }

        return true;
    }
}
