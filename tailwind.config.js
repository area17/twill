module.exports = {
    mode: 'jit',
    content: [
        "./views/**/*.blade.php",
        './vendor/usernotnull/tall-toasts/config/**/*.php',
        './vendor/wire-elements/modal/resources/views/*.blade.php',
        './vendor/usernotnull/tall-toasts/resources/views/**/*.blade.php',
        // @todo: Proper whitelist for this
        // https://github.com/wire-elements/modal/issues/65
        '../storage/framework/views/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
