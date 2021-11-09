const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
  mode: 'jit',
  purge: [
    './app/Views/**/*.php',
    './src/**/*.js',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      spacing: {
        88: '22rem',
        112: '28rem',
      },
      fontFamily: {
        sans: ['"Source Sans Pro"', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        emerald: colors.emerald,
        trueGray: colors.trueGray,
        cyan: colors.cyan,
        yellow: colors.yellow,
        red: colors.red,
        white: colors.white,
        black: colors.black,
      },
    },
  },
  variants: {},
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
