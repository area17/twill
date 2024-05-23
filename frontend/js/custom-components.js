import BlockMixin from '@/mixins/block'

// Blocks
const registerBlockComponent = (app, name, component) => {
  return !app.component(name)
    ? app.component(name, component)
    : false
}

const registerCustomComponents = (app) => {
  if (typeof window[process.env.VUE_APP_NAME].TWILL_BLOCKS_COMPONENTS !== 'undefined') {
    window[process.env.VUE_APP_NAME].TWILL_BLOCKS_COMPONENTS.map(componentName => {
      return registerBlockComponent(app, componentName, {
        template: '#' + componentName,
        mixins: [BlockMixin]
      })
    })
  }

  // Custom components
  const extractComponentNameFromContextKey = (contextKey) => `a17-${contextKey.match(/\w+/)[0].replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase()}`

  const importedCustomBlocks = require.context('@/components/blocks/customs/', false, /\.(js|vue)$/i)
  importedCustomBlocks.keys().map(block => {
    const componentName = extractComponentNameFromContextKey(block.replace(/customs\//, ''))
    return registerBlockComponent(app, componentName, importedCustomBlocks(block).default)
  })

  const importedTwillBlocks = require.context('@/components/blocks/', false, /\.(js|vue)$/i)
  importedTwillBlocks.keys().map(block => {
    const componentName = extractComponentNameFromContextKey(block)
    return registerBlockComponent(app, componentName, importedTwillBlocks(block).default)
  })

  // Custom form components
  const importedComponents = require.context(process.env.VUE_APP_CUSTOM_COMPONENTS_PATH, true, /\.(js|vue)$/i)
  importedComponents.keys().map(block => {
    // eslint-disable-next-line
    const componentName = extractComponentNameFromContextKey(block)
    return app.component(componentName, importedComponents(block).default)
  })

  // Vendor form components
  const importedVendorComponents = require.context('@/components/customs-vendor/', true, /\.(js|vue)$/i)
  importedVendorComponents.keys().map(block => {
    const componentName = extractComponentNameFromContextKey(block)
    return app.component(componentName, importedVendorComponents(block).default)
  })
}

export default registerCustomComponents

