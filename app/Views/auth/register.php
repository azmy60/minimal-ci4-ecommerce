<?= $this->extend('wrappers/base') ?>
<?= $this->section('head') ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('body') ?>

<body class="w-screen h-screen px-4 pb-4 sm:grid sm:place-content-center">
    <form action="<?= route_to('register') ?>" method="post" class="grid items-end w-full h-full sm:w-112 sm:border sm:border-trueGray-300 sm:rounded-lg sm:grid-rows-none sm:gap-6 sm:p-16">
        <?= csrf_field() ?>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold">Create a new account</h1>
            <p>TODO: We won't deliver a register page to a client. A user account should be created under the hood. maybe...</p>
        </div>

        <div class="space-y-4">
            <?= view('components/input', [
                'label' => 'Email address', 'name' => 'email',
                'type' => 'email', 'size' => 'large',
                'error' => session('errors.email'),
            ]) ?>
    
            <?= view('components/input', [
                'label' => 'New password', 'name' => 'password',
                'type' => 'password', 'size' => 'large',
                'error' => session('errors.password'),
            ]) ?>
        </div>
    
        <button type="submit" class="w-full py-2.5 px-4 text-lg rounded-lg font-semibold bg-emerald-500 text-white">Register</button>
    </form>
</body>


<?= $this->endSection() ?>