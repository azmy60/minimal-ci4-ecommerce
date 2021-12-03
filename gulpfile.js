const { series, src, dest, watch } = require('gulp')
const { spawn } = require('child_process')
const { nodeResolve } = require('@rollup/plugin-node-resolve');
const commonjs = require('@rollup/plugin-commonjs');
const rollup = require('rollup')
const gulpIf = require('gulp-if')
const postcss = require('gulp-postcss')
const tailwindcss = require('tailwindcss')
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const browserSync = require('browser-sync')
const atImport = require('postcss-import')
const svgSprite = require('gulp-svg-sprite')
const through2 = require('through2')
const { terser } = require('rollup-plugin-terser')
const reload = browserSync.reload

// Remove files in public/css
function clean() {
  const del = require('del')
  return del([
    'public/css/*',
    'public/js/*',
    'app/Views/dest/*',
  ])
}

// Create iconset
function svg() {
  return src('src/svg/iconset/*.svg')
    .pipe(svgSprite({
      xmlDeclaration: false,
      doctypeDeclaration: false,
      dimensionAttributes: false,
      mode: { 
        symbol: {
          inline: true,
          sprite: 'iconset.svg',
        },
      },
    }))
    .pipe(through2.obj((file, _, cb) => {
      if (file.isBuffer()) {
        // const reg_xmlns_and_fill = /xmlns="http:\/\/www.w3.org\/2000\/svg"|fill="(?!none).*?"/g
        const reg_xmlns_and_fill = /xmlns="http:\/\/www.w3.org\/2000\/svg"|fill=".*?"/g        
        const code = file.contents.toString().replace(reg_xmlns_and_fill, '')
        file.contents = Buffer.from(code)
      }
      cb(null, file)
    }))
    .pipe(dest('app/Views/dest'))
}

// Build css
function css() {
  return src('src/css/main.css')
    .pipe(
      postcss([
        atImport(),
        tailwindcss(),
        autoprefixer(),
        ...process.env.NODE_ENV === 'production' 
          ? [cssnano()] 
          : []
      ])
    )
    .pipe(dest('public/css'))
    .pipe(reload({ stream: true }))
}

async function js() {
  const addProductBundle = await rollup.rollup({
    input: 'src/js/addProductHelper.js',
    plugins: [
      nodeResolve(),
      commonjs(),
    ]
  })

  await addProductBundle.write({
    dir: 'public/js',
    name: 'uploadZoneData',
    format: 'iife',
    sourcemap: true,
    plugins: [
      terser(),
    ]
  })
}

// Run php server + live reload
function serve() {
  setTimeout(() => {
    browserSync({ proxy: '[::1]:8080', notify: false })
  }, 300)

  // Watch css
  watch(['src/css/**/*.css', 'tailwind.config.js'], series(css))

  // Watch views
  watch('app/Views/**/*.twig').on('change', series(css, browserSync.reload))

  // Watch js
  watch('src/js/**/*.js', series(js))
  
  return phpServer = spawn('php', 'spark serve'.split(' '), {stdio: 'inherit'})
}

exports.iconset = series(svg)
exports.build = series(clean, svg, css, js)
exports.default = series(clean, svg, css, js, serve)