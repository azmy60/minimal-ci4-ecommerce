# etalase.id

## Requirements
- php 7.3+, 8.0+
- php gd library `sudo apt-get install php-gd`
- A SQLite driver `sudo apt-get install php-sqlite3`
- [A local email server](https://gist.github.com/raelgc/6031274) for testing email verification and forgot password

## Setup
1. Install the php dependencies `composer install`
2. Copy env file to .env, and update the database configurations
3. Edit **app/Config/Email.php** and verify that **fromName** and **fromEmail** are set as that is used when sending emails for password reset, etc. Hint:
    ```php
    $fromName = '<your-user>';
    $fromEmail = '<your-user>@localhost'`;
    ```
3. Apply database migration `php spark migrate --all`. This includes migrations from Myth:Auth
4. Run these npm commands
```bash
npm install
npm run prod
```
5. Set the php config's `post_max_size` to `21M` at minimum. https://stackoverflow.com/a/6135485/10012118
6. Disable `ONLY_FULL_GROUP_BY` sql mode. https://stackoverflow.com/a/36033983/10012118

## Creating a new account
### Using CLI
```bash
php spark auth:create_user
php spark auth:set_password # Identity: <your-email>
php spark auth:activate_user # Identity: <your-email>
```

### On browser
1. Go to http://localhost/register
2. Use \<your-user\>@localhost.com (from your local email server)
3. Check your email and click the verification link
    
## Running a local server
Before you launch a local server, it's important to run `npm run prod` everytime there are changes in the ***src*** directory.
```bash
npm run prod # build the css files
php spark serve
```

## Running Live Reload
This watches files only in **app/Views** and **public/css**. Use this when working on those files and wanting to see real-time updates in a browser.
```bash
npm run dev
```

## Dependencies
- CodeIgniter 4 (see [official documentation](https://codeigniter.com/user_guide/intro/index.html) / [github](https://github.com/codeigniter4/CodeIgniter4))
- Myth:Auth (see [github](https://github.com/lonnieezell/myth-auth))
- Twig (see [official website](https://twig.symfony.com/))
- kenjis/codeigniter-ss-twig (see [github](https://github.com/kenjis/codeigniter-ss-twig))
- TailwindCSS (see [official website](https://tailwindcss.com/))
- Alpine.js (see [official website](https://alpinejs.dev/))
- clipboard.js (see [official website](https://clipboardjs.com/))
- fuse.js (see [official website](https://fusejs.io/))
- Lodash (see [official website](https://lodash.com/))

## UI Resources
- Phosphor icons (see [official website](https://phosphoricons.com/))
- Twemoji (see [official website](https://twemoji.twitter.com/))

## Troubleshooting
```
PHP Warning:  PHP Startup: ^(text/|application/xhtml\+xml) (offset=0): unrecognised compile-time option bit(s) in Unknown on line 0
```
To solve the above issue, run this command:
```bash
sudo apt-get install --only-upgrade libpcre2-16-0 libpcre2-32-0 libpcre2-8-0 libpcre2-dev libpcre2-posix2

```
source:
[https://github.com/oerdnj/deb.sury.org/issues/1674#issuecomment-964284447](https://github.com/oerdnj/deb.sury.org/issues/1674#issuecomment-964284447)
