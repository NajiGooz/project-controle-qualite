/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './src/**/*.{js,jsx,ts,tsx}',
    'node_modules/flowbite-react/lib/esm/**/*.js'
  ],
  theme: {
    extend: {
      fontFamily: {
        primaryRegular: ['Regular'],
        primaryMedium: ['Medium'],
        primaryBold: ['Bold'],
        primarySemiBold: ['SemiBold'],
      }
    },

  },
  plugins: [
    require('flowbite/plugin')
  ],
}

