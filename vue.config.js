const fs = require("fs")
const path = require('path')
const isProd = process.env.NODE_ENV === 'production'

// Define global vue variables
process.env.VUE_APP_NAME = process.env.VUE_APP_NAME || 'TWILL'

// eslint-disable-next-line no-console
console.log('\x1b[32m', `\n🔥 Building Twill assets in ${isProd ? 'production' : 'dev'} mode.`)

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
/**
 * For configuration
 * @see: https://github.com/Turbo87/webpack-notifier
 */
const WebpackNotifierPlugin = require('webpack-notifier')

const srcDirectory = 'frontend'
const partialsDirectory = '../views/partials'
const outputDir = isProd ? 'dist' : (process.env.TWILL_DEV_ASSETS_PATH || 'dist')
const assetsDir = process.env.TWILL_ASSETS_DIR || 'assets/admin'
// Only works with laravel valet.
const useHttps = process.env.TWILL_DEV_MODE_SSH ? process.env.TWILL_DEV_MODE_SSH === 'true' : false

const pages = {
  'main-buckets': `${srcDirectory}/js/main-buckets.js`,
  'main-dashboard': `${srcDirectory}/js/main-dashboard.js`,
  'main-form': `${srcDirectory}/js/main-form.js`,
  'main-listing': `${srcDirectory}/js/main-listing.js`,
  'main-free': `${srcDirectory}/js/main-free.js`
}

const svgConfig = (suffix = null) => {
  suffix = suffix !== null ? `-${suffix}` : ''

  return {
    output: {
      filename: `${partialsDirectory}/icons/icons${suffix}-svg.blade.php`,
      chunk: {
        name: `icons${suffix}`
      }
    },
    sprite: {
      prefix: 'icon--'
    },
    styles: {
      filename: `~svg-sprite-icons${suffix}.scss`,
      variables: {
        sprites: `icons${suffix}-sprites`,
        sizes: `icons${suffix}-sizes`,
        variables: `icons${suffix}-variables`,
        mixin: `icons${suffix}-sprites-mixin`
      }
    }
  }
}

const plugins = [ new CleanWebpackPlugin() ]

// Default icons and optionnal custom admin icons
// Warning : user need to make sure each SVG files are named uniquely
const iconDirectories = [`${srcDirectory}/icons/**/*.svg`];
if (fs.existsSync(`${srcDirectory}/icons-custom`) && fs.readdirSync(`${srcDirectory}/icons-custom`).length !== 0) {
  iconDirectories.push(`${srcDirectory}/icons-custom/**/*.svg`);
}
plugins.push(new SVGSpritemapPlugin(iconDirectories, svgConfig()))
// File format icons
plugins.push(new SVGSpritemapPlugin(`${srcDirectory}/icons-files/**/*.svg`, svgConfig('files')))
// Wysiwyg icons
plugins.push(new SVGSpritemapPlugin(`${srcDirectory}/icons-wysiwyg/**/*.svg`, svgConfig('wysiwyg')))

plugins.push(new WebpackAssetsManifest({
  output: `${assetsDir}/twill-manifest.json`,
  publicPath: true,
  customize (entry, original, manifest, asset) {
    const search = new RegExp(`${assetsDir.replace(/\//gm, '\/')}\/(css|fonts|js|icons)\/`, 'gm')
    return {
      key: entry.key.replace(search, '')
    }
  }
}))

if (!isProd) {
  plugins.push(new WebpackNotifierPlugin({
    title: 'Twill',
    contentImage: path.join(__dirname, 'docs/.vuepress/public/favicon-180.png')
  }))
}

// Define npm module resolve order: 1. local (Twill), 2. root (App)
const appModuleFolder = path.resolve(__dirname, '../../../node_modules') // vendor/area17/twill/
const resolveModules = ['node_modules']
if (fs.existsSync(appModuleFolder)) {
  resolveModules.push(appModuleFolder)
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
        prependData: `@import "~styles/setup/_settings.scss";`
      }
    }
  },
  // Define entries points
  pages,
  devServer: {
    hot: true,
    https: useHttps,
    disableHostCheck: true,
    headers: {
      "Access-Control-Allow-Origin": "*"
    }
  },
  runtimeCompiler: true,
  configureWebpack: {
    resolve: {
      alias: {
        'prosemirror-tables': path.join(__dirname, 'node_modules/prosemirror-tables/src/index.js'),
        'prosemirror-state' : path.join(__dirname, 'node_modules/prosemirror-state/src/index.js'),
        'prosemirror-view' : path.join(__dirname, 'node_modules/prosemirror-view/src/index.js'),
        'prosemirror-transform' : path.join(__dirname, 'node_modules/prosemirror-transform/src/index.js')
      },
      modules: resolveModules
    },
    plugins,
    performance: {
      hints: false
    }
  },
  chainWebpack: config => {
    // Update default vue-cli aliases
    config.resolve.alias.set('fonts', path.resolve(`${srcDirectory}/fonts`))
    config.resolve.alias.set('@', path.resolve(`${srcDirectory}/js`))
    config.resolve.alias.set('styles', path.resolve(`${srcDirectory}/scss`))

    // Delete HTML related webpack plugins by page
    Object.keys(pages).forEach(page => {
      config.plugins.delete(`html-${page}`)
      config.plugins.delete(`preload-${page}`)
      config.plugins.delete(`prefetch-${page}`)
    })
  }
}

if (useHttps) {
  const homeDir = process.env.HOME;
  const host = process.env.APP_URL.split('//')[1] ?? process.env.APP_URL;

  // This takes the ssh certificates from your `valet secure` domain so that browsers (Looking at safari) stop
  // complaining about it.
  config.devServer.host = host;
  config.devServer.https = {
    key: fs.readFileSync(path.resolve(homeDir, `.config/valet/Certificates/${host}.key`)),
    cert: fs.readFileSync(path.resolve(homeDir, `.config/valet/Certificates/${host}.crt`)),
  }
}

module.exports = config
