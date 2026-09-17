/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.{php,js}","./api/*.{php,js}","./templates/*.{php,js}"],
  theme: {
    extend: {},
    animatedSettings: {
        animatedSpeed: 1000,
        heartBeatSpeed: 500,
        hingeSpeed: 2000,
        bounceInSpeed: 750,
        bounceOutSpeed: 750,
        animationDelaySpeed: 500,
        classes: ['bounceIn']
    }
  },
  plugins: [
  	require('@tailwindcss/line-clamp'),
  	require('@tailwindcss/forms'),
  	require("tailwindcss-animatecss")
  ],
}

