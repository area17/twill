const {
  Setup,
  ColorTokens,
  SpacingTokens,
  Spacing,
  Typography,
  Layout,
  DevTools,
  Container
} = require('@area17/a17-tailwind-plugins')

// config
const feConfig = require('./frontend.config.json')

console.log(feConfig.color.border)
console.log(feConfig.color.tokens)

module.exports = {
  content: ['./_build/**/*.html'],
  theme: {
    screens: feConfig.structure.breakpoints,
    mainColWidths: feConfig.structure.container,
    innerGutters: feConfig.structure.gutters.inner,
    outerGutters: feConfig.structure.gutters.outer,
    columnCount: feConfig.structure.columns,
    fontFamilies: feConfig.typography.families,
    typesets: feConfig.typography.typesets,
    spacing: SpacingTokens(feConfig.spacing.tokens),
    spacingGroups: feConfig.spacing.groups,
    colors: feConfig.color.tokens,
    borderColor: feConfig.color.tokens
  },

  corePlugins: {
    container: false
  },
  plugins: [
    require('@tailwindcss/forms'),
    Setup,
    Typography,
    Spacing,
    Layout,
    DevTools,
    ColorTokens,
    Container
  ]
}
