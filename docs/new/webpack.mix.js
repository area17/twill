const mix = require('laravel-mix')
require('laravel-mix-jigsaw')

mix.disableNotifications()
mix.setPublicPath('source/assets/build')

mix.jigsaw({
    watch: {
        files: [
            'config.php',
            'bootstrap.php',
            'blade.php',
            'listeners/**/*.php',
            'source/*.md',
            'source/*.php',
            'source/*.html',
        ],
        dirs: ['source/*/', 'phplight', 'php-generator'],
        notDirs: ['source/_assets/', 'source/assets/'],
    },
})
    .js('source/_assets/js/main.js', 'js')
    .css('source/_assets/css/main.css', 'css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .options({
        processCssUrls: false,
    })
    .version()
