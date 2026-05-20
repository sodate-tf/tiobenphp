import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',

    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts,jsx,tsx,vue}',
  ],

  theme: {
    extend: {
      fontFamily: {
        // se você usa Inter no site, alinhe aqui também
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [forms],
}