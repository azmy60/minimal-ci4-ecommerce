<p>Someone requested a password reset at this email address for <?= site_url() ?>.</p>

<p>To reset the password click this URL.</p>

<a href="<?= site_url('reset-password') . '?token=' . $hash . '&email=' . $email ?>">
  <?= site_url('reset-password') . '?token=' . $hash . '&email=' . $email ?>
</a>

<br>

<p>If you did not request a password reset, you can safely ignore this email.</p>