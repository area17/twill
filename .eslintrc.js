module.exports = {
  root: true,
  env: {
    node: true
  },
  extends: ['plugin:vue/essential', '@vue/standard', 'prettier'],
  rules: {
    'no-unmodified-loop-condition': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
    'no-unused-vars': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
    'no-unreachable': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
    // indent
    indent: ['error', 2, { SwitchCase: 1 }],
    'vue/script-indent': [
      process.env.NODE_ENV === 'production' ? 'error' : 'warn',
      2,
      {
        baseIndent: 1,
        switchCase: 1
      }
    ],
    'no-useless-escape': 0,
    // allow paren-less arrow functions
    'arrow-parens': 0,
    // allow async-await
    'generator-star-spacing': 0,
    // allow hasOwnProperty
    'no-prototype-builtins': 0,
    // allow debugger during development
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
    'no-console':
      process.env.NODE_ENV === 'production'
        ? ['error', { allow: ['error'] }]
        : 'warn'
  },
  overrides: [
    {
      files: ['*.vue'],
      rules: {
        indent: 'off',
        'vue/script-indent': [
          process.env.NODE_ENV === 'production' ? 'error' : 'warn',
          2,
          {
            baseIndent: 1,
            switchCase: 1
          }
        ]
      }
    }
  ],
  parserOptions: {
    parser: '@babel/eslint-parser'
  }
}
