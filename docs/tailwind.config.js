/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./_build/**/*.html",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('/opt/homebrew/lib/node_modules/@tailwindcss/typography'),
    ],
}
