const path = require('path')
const fs = require('fs')

// Define global vue variables
process.env.VUE_APP_VERSION = fs.readFileSync(path.resolve('VERSION'))
process.env.VUE_APP_NAME = require('./package').name.toUpperCase()

// TODO: fix extract font paths in generated css
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
const isProd = process.env.NODE_ENV === 'production'
const pages = {
  'main-buckets': `${srcDirectory}/js/main-buckets.js`,
  'main-dashboard': `${srcDirectory}/js/main-dashboard.js`,
  'main-form': `${srcDirectory}/js/main-form.js`,
  'main-listing': `${srcDirectory}/js/main-listing.js`
}

module.exports = {
  // Define default publicPath
  publicPath: publicPath,
  // Define base outputDir of build
  outputDir: outputDir,
  // Define root asset directory
  assetsDir: assetsDir,
  // Remove sourcemaps for production
  productionSourceMap: false,
  css: {
    loaderOptions: {
      // define global settings pass in each components
      sass: {
        data: `@import "~styles/setup/_settings.scss";`
      }
    }
  },
  // Define entries points
  pages,
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
      // Change default manifest name to work with default "mix" Laravel helper
      new WebpackAssetsManifest({
        output: `${publicPath}/mix-manifest.json`
      })
    ]
  },
  chainWebpack: config => {
    // Update default vue-cli aliases
    config.resolve.alias
      .set('fe', path.resolve(`${srcDirectory}`))
    config.resolve.alias
      .set('@', path.resolve(`${srcDirectory}/js`))
    config.resolve.alias
      .set('styles', path.resolve(`${srcDirectory}/scss`))

    // delete HTML related webpack plugins by page
    Object.keys(pages).forEach(page => {
      config.plugins.delete(`html-${page}`)
      config.plugins.delete(`preload-${page}`)
      config.plugins.delete(`prefetch-${page}`)
    })
  }
}
