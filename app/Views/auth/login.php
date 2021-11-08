<?= $this->extend('wrappers/base') ?>
<?= $this->section('head') ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('body') ?>

<body class="w-screen h-screen px-4 pb-4 sm:grid sm:place-content-center lg:gap-32 xl:gap-72 lg:grid-flow-col">
    <section class="self-center hidden space-y-2 lg:block w-88">
        <h1 class="text-4xl font-bold">Halo!<svg class="inline-block w-12 ml-2" viewBox="0 0 24 25">
                <path fill="#EF9645" d="M3.24 6.348c.627-.438 1.572-.354 2.135.11l-.646-.937c-.519-.741-.333-1.542.408-2.062.742-.518 2.842.874 2.842.874-.524-.748-.426-1.696.322-2.22a1.655 1.655 0 0 1 2.304.407l6.947 9.813-.885 8.584-7.389-2.695-6.445-9.555a1.663 1.663 0 0 1 .408-2.319Z" />
                <path fill="#FFDC5D" d="M1.797 11.807s-.755-1.1.346-1.854c1.1-.754 1.853.346 1.853.346l3.5 5.105c.121-.201.253-.4.4-.596L3.039 7.723s-.754-1.099.346-1.853 1.853.345 1.853.345l4.57 6.665c.17-.139.345-.278.524-.415L5.033 4.738s-.754-1.1.346-1.853c1.1-.754 1.853.345 1.853.345l5.298 7.726c.195-.12.388-.223.58-.332L8.16 3.403s-.754-1.1.345-1.854c1.1-.754 1.854.346 1.854.346l5.236 7.636.796 1.161c-3.3 2.263-3.613 6.52-1.729 9.268.377.55.927.173.927.173-2.262-3.299-1.571-7.006 1.728-9.268l-.973-4.868s-.363-1.283.92-1.647c1.282-.363 1.646.92 1.646.92l1.123 3.335c.445 1.323.92 2.641 1.547 3.888a8.003 8.003 0 0 1-2.627 10.186A8.002 8.002 0 0 1 7.83 20.605l-6.032-8.798Z" />
                <path fill="#5DADEC" d="M8 21.611c-2.667 0-5.361-2.694-5.361-5.361 0-.369-.27-.667-.64-.667-.368 0-.694.298-.694.667 0 4 2.695 6.695 6.695 6.695.369 0 .667-.326.667-.695 0-.369-.298-.639-.667-.639Z" />
                <path fill="#5DADEC" d="M4.667 22.917c-2 0-3.334-1.334-3.334-3.334a.667.667 0 1 0-1.333 0c0 2.667 2 4.667 4.667 4.667a.666.666 0 1 0 0-1.333ZM16 1.583a.667.667 0 1 0 0 1.334c2.667 0 5.333 2.392 5.333 5.333a.667.667 0 0 0 1.334 0C22.667 4.574 20 1.583 16 1.583Z" />
                <path fill="#5DADEC" d="M19.333.278c-.368 0-.666.27-.666.639 0 .368.298.694.666.694 2 0 3.306 1.484 3.306 3.306 0 .368.326.666.694.666.369 0 .639-.298.639-.666 0-2.558-1.972-4.639-4.639-4.639Z" />
            </svg></h1>
        <p class="text-lg">Ini adalah halaman khusus penjual. Untuk melihat halaman pembeli, <a href="/" class="underline text-emerald-500">klik disini</a>.</p>
    </section>
    <form action="<?= route_to('login') ?>" method="POST" class="grid items-end w-full h-full login-form sm:w-112 sm:border sm:border-trueGray-300 sm:rounded-lg sm:grid-rows-none sm:gap-6 sm:p-16">
        <?= csrf_field() ?>
        <div class="space-y-2">
            <div class="text-4xl font-bold">Nama Toko</div>
            <p class="lg:hidden">Ini adalah halaman khusus penjual. Untuk melihat halaman pembeli, <a href="/" class="underline text-emerald-500">klik disini</a>.</p>
        </div>
        <?php if (session()->has('error') || session()->has('message')) : ?>
            <div class="<?= session()->has('error') ? 'text-red-500 bg-red-100 border-red-500' : 'text-emerald-700 bg-emerald-50 border-emerald-500' ?> px-4 py-2 border rounded-lg">
                <?= session('error') ?? session('message') ?>
            </div>
        <?php endif ?>

        <div class="space-y-4">
            <?= view('components/input', [
                'label' => 'Email', 'name' => 'login',
                'type' => 'email', 'size' => 'large',
                'error' => session('errors.login'),
            ]) ?>
            <?= view('components/input', [
                'label' => 'Password', 'name' => 'password',
                'type' => 'password', 'size' => 'large',
                'error' => session('errors.password'),
            ]) ?>
        </div>
        <div class="space-y-2 sm:self-start sm:mt-0">
            <button type="submit" class="w-full py-2.5 px-4 text-lg rounded-lg font-semibold bg-emerald-500 text-white">Masuk</button>
            <a href="<?= route_to('forgot') ?>" class="block text-lg text-trueGray-800 lg:text-base">Lupa password?</a>
        </div>
    </form>
</body>

<?= $this->endSection() ?>