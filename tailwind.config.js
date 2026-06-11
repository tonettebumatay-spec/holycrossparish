export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      // THIS IS THE SECTION YOU ARE LOOKING FOR
      backgroundImage: {
        'church': "url('/images/frontchurch.png')",
      }
    },
  },
  plugins: [require('@tailwindcss/forms')],
};