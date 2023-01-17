const fs = require("fs")
const path = require('path')
const isProd = process.env.NODE_ENV === 'production'

// Define global vue variables
process.env.VUE_APP_NAME = process.env.VUE_APP_NAME || 'TWILL'

process.env.VUE_APP_CUSTOM_COMPONENTS_PATH = process.env.VUE_APP_CUSTOM_COMPONENTS_PATH ?? '@/components/customs/'

if (isProd) {
  // eslint-disable-next-line no-console
  console.log('\x1b[32m', `\n🔥 Building Twill assets in ${isProd ? 'production' : 'dev'} mode.`)
  console.log('\nLoading components from: ' + process.env.VUE_APP_CUSTOM_COMPONENTS_PATH)
}

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
const partialsDirectory = '../views/partials'
const outputDir = isProd ? 'dist' : (process.env.TWILL_DEV_ASSETS_PATH || 'dist')
const assetsDir = process.env.TWILL_ASSETS_DIR || 'assets/twill'
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

const plugins = []

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

// Define npm module resolve order: 1. local (Twill), 2. root (App)
const appModuleFolder = path.resolve(__dirname, '../../../node_modules') // vendor/area17/twill/
const resolveModules = ['node_modules']
if (fs.existsSync(appModuleFolder)) {
  resolveModules.push(appModuleFolder)
}

const config = {
  // Define base outputDir of build
  outputDir,
  // Define root asset directory
  assetsDir,
  // Remove sourcemaps for production
  productionSourceMap: false,
  css: {
    extract: process.env.NODE_ENV === 'production' ? { ignoreOrder: true } : false,
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
    allowedHosts: 'all',
    headers: {
      "Access-Control-Allow-Origin": "*"
    }
  },
  runtimeCompiler: true,
  configureWebpack: {
    performance: {
      hints: false
    },
    devtool: false,
    resolve: {
      alias: {
        'fonts':path.resolve(`${srcDirectory}/fonts`),
        '@':path.resolve(`${srcDirectory}/js`),
        'styles':path.resolve(`${srcDirectory}/scss`),
      },
      modules: resolveModules
    },
    plugins
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
