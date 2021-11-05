## Requirements
- php 7.3+, 8.0+
- A SQLite driver `sudo apt-get install php-sqlite3`
- [A local email server](https://gist.github.com/raelgc/6031274) for testing email verification and forgot password

## Setup
1. Install php dependencies `composer install`
2. Edit **app/Config/Email.php** and verify that a **fromName** and **fromEmail** are set as that is used when sending emails for password reset, etc. Hint:
    ```php
    $fromName = '<your-user>';
    $fromEmail = '<your-user>@localhost'`;
    ```
3. Apply database migration `php spark migrate --all`. This includes migrations from Myth:Auth

## Dependencies
- CodeIgniter 4 (see [official documentation](https://codeigniter.com/user_guide/intro/index.html) / [github](https://github.com/codeigniter4/CodeIgniter4))
- Myth:Auth (see [github](https://github.com/lonnieezell/myth-auth))