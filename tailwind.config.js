/** @type {import('tailwindcss').Config} */
const colors = ['teal','cyan','emerald','rose','indigo','sky','purple'];
module.exports = {
  content: ['./*.php', './includes/**/*.php', './assets/js/**/*.js'],
  safelist: colors.flatMap(c => [`bg-${c}-600`, `hover:bg-${c}-700`, `text-${c}-600`, `text-${c}-400`, `shadow-${c}-600/30`, `bg-${c}-100`]),
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
      },
    },
  },
};
