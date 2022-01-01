const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
  purge: [
    './app/Views/**/*.twig',
    './app/Views/**/*.html',
    './src/**/*.js',
  ],
  theme: {
    extend: {
      spacing: {
        88: '22rem',
        112: '28rem',
      },
      fontFamily: {
        sans: ['"Source Sans Pro"', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    // require('@tailwindcss/forms'),
  ],
}
