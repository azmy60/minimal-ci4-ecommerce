<?= $this->extend('wrappers/base') ?>
<?= $this->section('head') ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('body') ?>

<body class="w-screen h-screen px-4 pb-4 sm:grid sm:place-content-center">
    <?= view('components/iconset') ?>
    <form action="<?= route_to('reset-password') ?>" method="post" class="grid items-end w-full h-full sm:w-112 sm:border sm:border-trueGray-300 sm:rounded-lg sm:grid-rows-none sm:gap-6 sm:p-16">
        <?= csrf_field() ?>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold">Reset your password</h1>
            <p>TODO: put a text here</p>
        </div>

        <?php if (session()->has('error') || session()->has('message')) : ?>
            <div class="<?= session()->has('error') ? 'text-red-500 bg-red-100 border-red-500' : 'text-emerald-700 bg-emerald-50 border-emerald-500' ?> px-4 py-2 border rounded-lg">
                <?= session('error') ?? session('message') ?>
            </div>
        <?php endif ?>

        <input type="hidden" name="token" value="<?= old('token', $token ?? '') ?>">
        <input type="hidden" name="email" value="<?= old('email', $email ?? '') ?>">

        <br>
        <div class="space-y-4">
            <?= view('components/input', [
                'label' => 'New password', 'name' => 'password',
                'type' => 'password', 'size' => 'large',
                'error' => session('errors.password'),
            ]) ?>

            <?= view('components/input', [
                'label' => 'Confirm new password', 'name' => 'pass_confirm',
                'type' => 'password', 'size' => 'large',
                'error' => session('errors.pass_confirm'),
            ]) ?>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 text-lg rounded-lg font-semibold bg-emerald-500 text-white">Reset my password</button>
    </form>
</body>
<?= $this->endSection() ?>