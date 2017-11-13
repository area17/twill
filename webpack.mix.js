let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application.
 |
 */

mix.setPublicPath('public');

mix.options({
  processCssUrls: false,
  purifyCss: false, // Remove unused CSS selectors.
})

mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve('frontend/js'),
      'styles': path.resolve('frontend/scss')
    }
  },
  module: {
    rules: [
      {
        test: /\.(js|vue)$/,
        exclude: /node_modules/,
        loader: 'eslint-loader',
        enforce: 'pre',
        include: [path.resolve('frontend/js')],
        options: {
          formatter: require('eslint-friendly-formatter')
        }
      }
    ]
  }
});

mix.disableNotifications();

mix.copyDirectory('frontend/fonts', 'public/assets/admin/fonts');
mix.copyDirectory('assets/vendor', 'public/assets/vendor');

mix.js(
  'frontend/js/main-listing.js',
  'public/assets/admin/js'
).js(
  'frontend/js/main-form.js',
  'public/assets/admin/js'
).js(
  'frontend/js/main-buckets.js',
  'public/assets/admin/js'
).js(
  'frontend/js/main-dashboard.js',
  'public/assets/admin/js'
).extract([
  'vue', 'vuex', 'axios',
  'quill', 'vuedraggable', 'cropperjs',
  'flatpickr', 'vue-select', 'vue-timeago',
  'date-fns', 'lodash/debounce'
]).sass(
  'frontend/scss/app.scss',
  'public/assets/admin/css'
).sourceMaps();
