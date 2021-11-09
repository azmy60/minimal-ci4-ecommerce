<a x-data="{ isCurrentPath: location.pathname === '<?= $path ?>' }" :class="{ 'text-emerald-400': isCurrentPath }" href="<?= $path ?>" class="flex items-center gap-4 py-4 pl-4 hover:bg-emerald-900 md:flex-col md:gap-0 md:text-center md:p-0 md:h-20 md:flex md:justify-center lg:flex-row lg:justify-start lg:pl-4 lg:h-auto lg:py-4 lg:gap-4">
  <svg x-show="!isCurrentPath" class="w-7 h-7">
    <use xlink:href="<?= '#ph_' . $icon ?>" />
  </svg>
  <svg x-show="isCurrentPath" class="w-7 h-7">
    <use xlink:href="<?= '#ph_' . $icon . '-bold' ?>" />
  </svg>
  <span :class="{ 'font-semibold': isCurrentPath }" class="text-xl md:text-base lg:text-lg"><?= $name ?></span>
</a>