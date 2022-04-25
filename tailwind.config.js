module.exports = {
    mode: 'jit',
    content: ["./views/**/*.blade.php"],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
