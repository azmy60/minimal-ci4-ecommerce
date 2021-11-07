<div class="<?= $class ?? '' ?> grid gap-1">
  <?php if (!empty($label)) : ?>
    <label for="<?= $name ?>" class="<?= !empty($error) ? 'text-red-500' : 'text-trueGray-800' ?> font-semibold"><?= $label ?></label>
  <?php endif ?>

  <div <?= ($type === 'password') ? 'x-data="{ show: false }"' : '' ?> class="relative">
    <?php if ($type === 'password') :  ?>
      <div @click="$refs.passwordInput.focus()" class="absolute w-6 h-6 transform -translate-y-1/2 cursor-pointer right-4 top-1/2 text-trueGray-500">
        <svg viewBox="0 0 24 24" @click="show = !show" :class="{'hidden': show, 'block': !show}">
          <path d="M23.185 11.695c-.033-.074-.826-1.835-2.592-3.6C18.24 5.741 15.27 4.498 12 4.498c-3.27 0-6.241 1.243-8.593 3.595C1.641 9.86.847 11.621.815 11.695a.75.75 0 000 .61c.033.074.826 1.835 2.592 3.6C5.76 18.256 8.73 19.499 12 19.499c3.27 0 6.241-1.243 8.593-3.594 1.765-1.765 2.56-3.526 2.592-3.6a.75.75 0 000-.61zM12 18c-2.886 0-5.407-1.048-7.494-3.116A12.511 12.511 0 012.34 12c.58-1.06 1.31-2.032 2.167-2.883C6.593 7.048 9.114 5.999 12 5.999s5.407 1.05 7.494 3.118A12.511 12.511 0 0121.66 12c-.676 1.26-3.62 6-9.661 6zM12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 7.5a3 3 0 110-6 3 3 0 010 6z" />
        </svg>
        <svg viewBox="0 0 24 24" @click="show = !show" :class="{'hidden': !show, 'block': show}">
          <path d="M5.055 3.246a.75.75 0 10-1.11 1.009l1.803 1.983C2.344 8.328.88 11.55.815 11.696a.75.75 0 000 .609c.033.074.826 1.835 2.592 3.6C5.76 18.257 8.73 19.5 12 19.5c1.68.01 3.344-.337 4.88-1.016l2.065 2.27a.75.75 0 001.11-1.008l-15-16.5zm4.437 7.11l3.907 4.298a3 3 0 01-3.907-4.298zM12 18c-2.886 0-5.407-1.048-7.494-3.116A12.512 12.512 0 012.339 12c.444-.826 1.856-3.137 4.438-4.63l1.684 1.852a4.499 4.499 0 005.968 6.564l1.38 1.518c-1.216.466-2.507.701-3.81.694zm11.185-5.694c-.04.088-.988 2.191-3.128 4.107a.75.75 0 01-1-1.117A12.527 12.527 0 0021.66 12a12.51 12.51 0 00-2.167-2.883C17.407 7.048 14.886 5.999 12 5.999c-.608 0-1.216.049-1.816.148a.75.75 0 11-.247-1.48A12.564 12.564 0 0112 4.5c3.27 0 6.241 1.244 8.593 3.595 1.765 1.766 2.56 3.527 2.592 3.602a.75.75 0 010 .609zm-10.62-3.252a.75.75 0 01.28-1.473 4.516 4.516 0 013.635 3.998.75.75 0 11-1.493.139 3.01 3.01 0 00-2.422-2.664z" />
        </svg>
      </div>
    <?php endif ?>
    <input id="<?= $name ?>" name="<?= $name ?>" <?= ($type === 'password') ? ":type=\"show ? 'text' : 'password'\" x-ref=\"passwordInput\"" : "type=\"$type\"" ?> class="<?= ($type == 'password') ? 'pl-4 pr-12' : 'px-4' ?> <?= !empty($error) ? 'border-red-500' : 'border-trueGray-400' ?> w-full py-2.5 bg-white border rounded-lg  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent placeholder-trueGray-500">
  </div>

  <?php if (!empty($helper)) :  ?>
    <p class="text-trueGray-500"><?= $helper ?></p>
  <?php elseif (!empty($error)) :  ?>
    <p class="text-red-500"><?= $error ?></p>
  <?php endif ?>
</div>