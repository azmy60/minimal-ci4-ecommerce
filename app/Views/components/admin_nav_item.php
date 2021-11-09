<a <?= $attributes ?? '' ?> x-data="{ myPath: '<?= $path ?>' }" @click="viewPage(myPath)" :class="{ 'text-emerald-400': currentPath === myPath }" href="<?= $path ?>" class="flex items-center gap-4 py-4 pl-4 hover:bg-emerald-900 md:flex-col md:gap-0 md:text-center md:p-0 md:h-20 md:flex md:justify-center lg:flex-row lg:justify-start lg:pl-4 lg:h-auto lg:py-4 lg:gap-4">
  <svg x-show="currentPath !== myPath" class="w-7 h-7">
    <use xlink:href="<?= '#ph_' . $icon ?>" />
  </svg>
  <svg x-cloak x-show="currentPath === myPath" class="w-7 h-7">
    <use xlink:href="<?= '#ph_' . $icon . '-bold' ?>" />
  </svg>
  <span :class="{ 'font-semibold': currentPath === myPath }" class="text-xl md:text-base lg:text-lg"><?= $name ?></span>
</a>