/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./_build/**/*.html",
    ],
    theme: {
        extend: {}
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
