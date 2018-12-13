let mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application.
 |
 */

mix.setPublicPath('public')

mix.options({
  processCssUrls: false,
  purifyCss: false // Remove unused CSS selectors.
})

mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve('frontend/js'),
      'styles': path.resolve('frontend/scss')
    }
  },
  optimization: {
    runtimeChunk: {
      name: 'assets/admin/js/manifest'
    },
    splitChunks: {
      cacheGroups: {
        vendor: {
          test (module, chunks) {
            // This prevents stylesheet resources with these extensions
            // from being moved from their original chunk to the vendor chunk
            if (module.resource && (/^.*\.(css|scss|less)$/).test(module.resource)) {
              return false
            }
            return module.context && module.context.indexOf('node_modules') !== -1
          },
          chunks: "initial",
          name: "assets/admin/js/vendor",
          enforce: true
        }
      }
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
})

mix.copyDirectory('frontend/fonts', 'public/assets/admin/fonts')

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
).sass(
  'frontend/scss/app.scss',
  'public/assets/admin/css',
  { implementation: require('node-sass') }
)

if (mix.inProduction()) {
  mix.version()
} else {
  mix.sourceMaps()
}
