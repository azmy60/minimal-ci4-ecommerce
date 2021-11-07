const { series, src, dest, watch } = require('gulp')
const { spawn } = require('child_process')
const gulpIf = require('gulp-if')
const postcss = require('gulp-postcss')
const tailwindcss = require('tailwindcss')
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const browserSync = require('browser-sync')
const reload = browserSync.reload

// Remove files in public/css
function clean() {
  const del = require('del')
  return del([
    'public/css/*'
  ])
}

// Build css
function css() {
  return src('src/css/main.css')
    .pipe(
      postcss([
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
  
  browserSync({ proxy: '[::1]:8080' })

  // Watch css
  watch(['src/css/*.css', 'tailwind.config.js'], series(css))

  // Watch views
  watch('app/Views/**/*.php').on('change', browserSync.reload)

  return phpServer
}

exports.build = series(clean, css)
exports.default = series(clean, css, serve)
