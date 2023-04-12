const {
  ApplyColorVariables,
  Setup,
  GridGap,
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

module.exports = {
  content: ['./_build/**/*.html', './_templates/**/*.blade.php'],
  theme: {
    screens: feConfig.structure.breakpoints,
    mainColWidths: feConfig.structure.container,
    innerGutters: feConfig.structure.gutters.inner,
    outerGutters: feConfig.structure.gutters.outer,
    columnCount: feConfig.structure.columns,
    fontFamily: {
      sans: '"Inter", sans-serif',
      mono: '"JetBrains Mono", monospace'
    },
    fontFamilies: feConfig.typography.families,
    typesets: feConfig.typography.typesets,
    spacing: SpacingTokens(feConfig.spacing.tokens),
    spacingGroups: feConfig.spacing.groups,
    colors: feConfig.color.tokens,
    borderColor: {
      ...feConfig.color.tokens,
      ...ApplyColorVariables(feConfig.color.tokens, feConfig.color.border),
    },
    textColor: {
      ...feConfig.color.tokens,
      ...ApplyColorVariables(feConfig.color.tokens, feConfig.color.text),
    },
    backgroundColor: {
      ...feConfig.color.tokens,
      ...ApplyColorVariables(feConfig.color.tokens, feConfig.color.background),
    },
    extend: {
      spacing: {
        header: '80px'
      },
      minHeight: {
        'screen-minus-header': 'calc(100vh - 5rem)'
      },
      maxWidth: {
        '240': '15rem',
        '740': '46.25rem',
      },
      height: {
        header: '80px',
        'screen-minus-header': 'calc(100vh - 5rem)'
      },
      zIndex: {
        header: '10'
      }
    }
  },
  corePlugins: {
    container: false
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    Setup,
    ApplyColorVariables,
    GridGap,
    Typography,
    Spacing,
    Layout,
    DevTools,
    ColorTokens,
    Container
  ]
}
