const path = require('path')
const srcDirectory = 'frontend'
const pages = {
  'main-buckets': `${srcDirectory}/js/main-buckets.js`,
  'main-dashboard': `${srcDirectory}/js/main-dashboard.js`,
  'main-form': `${srcDirectory}/js/main-form.js`,
  'main-listing': `${srcDirectory}/js/main-listing.js`
}

module.exports = {
  assetsDir: 'twill/assets',
  pages,
  // disable hashes in filenames
  filenameHashing: false,
  // delete HTML related webpack plugins
  chainWebpack: config => {
    config.resolve.alias
      .set('@', path.resolve(`${srcDirectory}/js`))
    config.resolve.alias
      .set('styles', path.resolve(`${srcDirectory}/scss`))

    // Remove generated html by page
    Object.keys(pages).forEach(page => {
      config.plugins.delete(`html-${page}`)
      config.plugins.delete(`preload-${page}`)
      config.plugins.delete(`prefetch-${page}`)
    })
  }
}
