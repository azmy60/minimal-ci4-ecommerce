<div class="<?= $class ?? '' ?> grid gap-1">
  <?php if (!empty($label)) : ?>
    <label for="<?= $name ?>" class="<?= !empty($error) ? 'text-red-500' : 'text-trueGray-800' ?> font-semibold"><?= $label ?></label>
  <?php endif ?>

  <div <?= ($type === 'password') ? 'x-data="{ show: false }"' : '' ?> class="relative">
    <?php if ($type === 'password') :  ?>
      <div @click="$refs.passwordInput.focus(); show = !show" class="absolute w-6 h-6 transform -translate-y-1/2 cursor-pointer right-4 top-1/2 text-trueGray-500">
        <svg x-show="!show" class="w-full h-full">
          <use xlink:href="#ph_eye" />
        </svg>
        <svg x-show="show" class="w-full h-full">
          <use xlink:href="#ph_eye-slash" />
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