/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        button: '#298AAB',   
        font_color: '#133A59',   
        font_normal: '#4A5565',   
        button_lite: '#298AAB'       
      },
    },
  },
  plugins: [],
}

