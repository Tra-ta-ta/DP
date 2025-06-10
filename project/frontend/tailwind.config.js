/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  darkMode: 'class', // важно!
  theme: {
    extend: {
      colors: {
        wood: {
          light: '#e6d3b3',
          dark: '#3b2f2f',
        },
      },
    },
  },
  plugins: [],
}