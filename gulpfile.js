const { series, src, dest, watch } = require('gulp')
const { spawn } = require('child_process')
const gulpIf = require('gulp-if')
const postcss = require('gulp-postcss')
const tailwindcss = require('tailwindcss')
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const browserSync = require('browser-sync')
const atImport = require('postcss-import')
const svgSprite = require('gulp-svg-sprite')
const through2 = require('through2')
const reload = browserSync.reload

// Remove files in public/css
function clean() {
  const del = require('del')
  return del([
    'public/css/*',
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

// Run php server + live reload
function serve() {
  const phpServer = spawn('php', 'spark serve'.split(' '), {stdio: 'inherit'})
  
  setTimeout(() => {
    browserSync({ proxy: '[::1]:8080', notify: false })
  }, 300)

  // Watch css
  watch(['src/css/**/*.css', 'tailwind.config.js'], series(css))

  // Watch views
  watch('app/Views/**/*.twig').on('change', series(css, browserSync.reload))

  return phpServer
}

exports.build = series(clean, svg, css)
exports.default = series(clean, svg, css, serve)