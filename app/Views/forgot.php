<?= $this->extend('wrappers/base') ?>
<?= $this->section('body') ?>

<body class="w-screen h-screen px-4 pb-4 sm:grid sm:place-content-center">
    <form action="<?= route_to('forgot') ?>" method="post" class="flex flex-col items-stretch w-full h-full pt-6 flex-nowrap sm:w-112 sm:border sm:border-trueGray-300 sm:rounded-lg sm:gap-6 sm:p-16">
        <?= csrf_field() ?>

        <div class="space-y-2">
            <div class="text-4xl font-bold">Reset password</div>
            <p>Don't worry, we will email you a link to reset your password.</p>
        </div>

        <?php if (session()->has('error') || session()->has('message')) : ?>
            <div class="<?= session()->has('error') ? 'text-red-500 bg-red-100 border-red-500' : 'text-emerald-700 bg-emerald-50 border-emerald-500' ?> px-4 py-2 border rounded-lg">
                <?= session('error') ?? session('message') ?>
            </div>
        <?php endif ?>

        <?= view('components/input', [
            'label' => 'Email', 'name' => 'email',
            'type' => 'email', 'size' => 'large',
            'error' => session('errors.email'),
            'class' => 'mt-6 sm:mt-0'
        ]) ?>

        <div class="flex flex-col justify-end flex-grow sm:mt-6">
            <button type="submit" class="w-full py-2.5 px-4 text-lg rounded-lg font-semibold bg-emerald-500 text-white">Send me a link</button>
        </div>
    </form>
</body>

<?= $this->endSection() ?>