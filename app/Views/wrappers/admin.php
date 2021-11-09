<?= $this->extend('wrappers/base') ?>
<?= $this->section('head') ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('body') ?>

<body class="relative flex w-full">
  <?= view('components/iconset') ?>
  <aside x-data="{ showNav: false }" @click.outside="showNav = false" class="fixed top-0 z-50 w-full text-white select-none md:sticky md:h-screen md:w-24 md:flex md:flex-col lg:w-56 h-14 bg-trueGray-900">
    <div @click="showNav = !showNav" class="ml-4 mt-[0.875rem] cursor-pointer fill-current w-7 h-7 md:hidden">
      <svg x-show="!showNav" class="w-full h-full">
        <use xlink:href="#ph_list-bold" />
      </svg>
      <svg x-show="showNav" class="w-full h-full">
        <use xlink:href="#ph_x-bold" />
      </svg>
    </div>
    <div class="absolute text-2xl font-semibold leading-none transform -translate-x-1/2 -translate-y-1/2 md:text-4xl md:w-full md:px-6 md:mt-8 md:text-center lg:text-left md:static md:transform-none left-1/2 top-1/2">
      <span class="md:hidden">
        Nama Toko
      </span>
      <span class="hidden md:inline lg:hidden">
        N
      </span>
      <span class="hidden lg:inline">
        Nama
      </span>
    </div>
    <nav x-show="showNav" x-transition class="absolute w-full pt-1 md:flex-grow md:pt-0 md:static md:mt-8 bg-trueGray-900 admin-nav">
      <?php
      $topItems = [
        [
          'name' => 'Home',
          'path' => route_to('admin'),
          'icon' => 'house',
        ],
        [
          'name' => 'Products',
          'path' => route_to('admin/products'),
          'icon' => 'package',
        ],
        [
          'name' => 'Categories',
          'path' => route_to('admin/categories'),
          'icon' => 'squares-four',
        ],
        [
          'name' => 'Settings',
          'path' => route_to('admin/settings'),
          'icon' => 'gear-six',
        ],
        [
          'name' => 'Help',
          'path' => route_to('admin/help'),
          'icon' => 'question',
        ],
      ];

      $bottomItems = [
        [
          'name' => 'Logout',
          'path' => '/logout',
          'icon' => 'sign-out',
        ],
        [
          'name' => 'Send feedback',
          'path' => '/admin/send-feedback',
          'icon' => 'chat-text',
        ],
      ];
      ?>

      <div>
        <?php foreach ($topItems as $item) {
          echo view('components/admin_nav_item', $item);
        }
        ?>
      </div>

      <div class="border-t border-trueGray-800">
        <?php foreach ($bottomItems as $item) {
          echo view('components/admin_nav_item', $item);
        }
        ?>
      </div>
    </nav>
  </aside>

  <main class="z-0 px-4 mt-14 md:px-7 md:mt-8">
    <?= $this->renderSection('main') ?>
  </main>
</body>

<?= $this->endSection() ?>