<?php

namespace App\Authentication\Activators;

use Config\Email;
use Myth\Auth\Authentication\Activators\ActivatorInterface;
use Myth\Auth\Authentication\Activators\BaseActivator;
use Myth\Auth\Entities\User;

/**
 * Class EmailActivator
 *
 * Sends an activation email to user.
 *
 * @package Myth\Auth\Authentication\Activators
 */
class EmailActivator extends BaseActivator implements ActivatorInterface
{
  /**
   * Sends an activation email
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
      ->setSubject(lang('Auth.activationSubject'))
      ->setMessage($twig->render($this->config->views['emailActivation'], [
        'email' => $user->email,
        'site' => parse_url(base_url())['host'],
        'link' => base_url('activate-account') . '?token=' . $user->activate_hash,
      ]))
      ->setMailType('html')
      ->send();

    if (!$sent) {
      $this->error = lang('Auth.errorSendingActivation', [$user->email]);
      return false;
    }

    return true;
  }
}
