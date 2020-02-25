const path = require('path')
const isProd = process.env.NODE_ENV === 'production'

// Define global vue variables
process.env.VUE_APP_NAME = require('./package').name.toUpperCase()

// eslint-disable-next-line no-console
console.log('\x1b[32m', `${process.env.VUE_APP_NAME}`)
console.log('\x1b[32m', `\n🔥 Building frontend application in ${isProd ? 'production' : 'dev'} mode.`)

/**
 * For configuration
 * @see: https://github.com/johnagan/clean-webpack-plugin
 */
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
/**
 * For configuration
 * @see: https://github.com/cascornelissen/svg-spritemap-webpack-plugin
 */
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin')
/**
 * For configuration
 * @see: https://github.com/webdeveric/webpack-assets-manifest
 */
const WebpackAssetsManifest = require('webpack-assets-manifest')

const srcDirectory = 'frontend'
const publicPath = 'twill'
const outputDir = `dist`
const assetsDir = `${publicPath}/assets`
const pages = {
  'main-buckets': `${srcDirectory}/js/main-buckets.js`,
  'main-dashboard': `${srcDirectory}/js/main-dashboard.js`,
  'main-form': `${srcDirectory}/js/main-form.js`,
  'main-listing': `${srcDirectory}/js/main-listing.js`,
  'main-free': `${srcDirectory}/js/main-free.js`
}

const config = {
  // Define base outputDir of build
  outputDir: outputDir,
  // Define root asset directory
  assetsDir: assetsDir,
  // Remove sourcemaps for production
  productionSourceMap: false,
  css: {
    loaderOptions: {
      // define global settings imported in all components
      sass: {
        data: `@import "~styles/setup/_settings.scss";`
      }
    }
  },
  // Define entries points
  pages,
  devServer: {
    sockPort: 8080,
    headers: {
      "Access-Control-Allow-Origin": "*"
    }
  },
  runtimeCompiler: true,
  configureWebpack: {
    plugins: [
      new CleanWebpackPlugin(),
      new SVGSpritemapPlugin(`${srcDirectory}/icons/**/*.svg`, {
        output: {
          filename: isProd
            ? `${assetsDir}/icons/icons.[contenthash].svg`
            : `${assetsDir}/icons/icons.svg`,
          chunk: {
            name: 'icons'
          }
        },
        sprite: {
          prefix: 'icon--'
        },
        styles: {
          filename: '~svg-sprite-icons.scss',
          variables: {
            sprites: 'icons-sprites',
            sizes: 'icons-sizes',
            variables: 'icons-variables',
            mixin: 'icons-sprites-mixin'
          }
        }
      }),
      new SVGSpritemapPlugin(`${srcDirectory}/icons-files/**/*.svg`, {
        output: {
          filename: isProd
            ? `${assetsDir}/icons/icons-files.[contenthash].svg`
            : `${assetsDir}/icons/icons-files.svg`,
          chunk: {
            name: 'icons-files'
          }
        },
        sprite: {
          prefix: 'icon--'
        },
        styles: {
          filename: '~svg-sprite-icons-files.scss',
          variables: {
            sprites: 'icons-files-sprites',
            sizes: 'icons-files-sizes',
            variables: 'icons-files-variables',
            mixin: 'icons-files-sprites-mixin'
          }
        }
      }),
      new WebpackAssetsManifest({
        output: `${publicPath}/twill-manifest.json`,
        publicPath: true,
        customize (entry, original, manifest, asset) {
          const search = new RegExp(`${assetsDir.replace(/\//gm, '\/')}\/(css|fonts|js|icons)\/`, 'gm')
          return {
            key: entry.key.replace(search, '')
          }
        }
      })
    ]
  },
  chainWebpack: config => {
    // Update default vue-cli aliases
    config.resolve.alias
      .set('fonts', path.resolve(`${srcDirectory}/fonts`))
    config.resolve.alias
      .set('@', path.resolve(`${srcDirectory}/js`))
    config.resolve.alias
      .set('styles', path.resolve(`${srcDirectory}/scss`))
    config.resolve.alias
      .set('vue$', path.resolve(`node_modules/vue/dist/vue.esm.js`))
    /* Delete default copy webpack plugin
       Because we are in a custom architecture instead of vue-cli project
       Copying public folder could be confusing with default Laravel architecture
     */
    config.plugins.delete('copy')

    // Delete HTML related webpack plugins by page
    Object.keys(pages).forEach(page => {
      config.plugins.delete(`html-${page}`)
      config.plugins.delete(`preload-${page}`)
      config.plugins.delete(`prefetch-${page}`)
    })
  }
}

module.exports = config
