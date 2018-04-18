// http://eslint.org/docs/user-guide/configuring

module.exports = {
  root: true,
  parserOptions: {
    parser: 'babel-eslint',
    sourceType: 'module'
  },
  env: {
    browser: true,
  },
  // Standard JS : https://github.com/standard/standard
  // VueJs - Priority A: Essential https://github.com/vuejs/eslint-plugin-vue#bulb-rules
  extends: [
    'standard',
    'plugin:vue/essential'
  ],
  // required to lint *.vue files
  plugins: [
    'vue'
  ],
  // add your custom rules here
  'rules': {
    // no need of v-key in vue v-for loop
    'vue/require-v-for-key': 'off',
    // indent
    'indent': ['error', 2, {'SwitchCase' : 1}],
    'vue/script-indent': ['error', 2, {
      'baseIndent': 1,
      'switchCase': 1
    }],
    'no-useless-escape': 0,
    // allow paren-less arrow functions
    'arrow-parens': 0,
    // allow async-await
    'generator-star-spacing': 0,
    // allow debugger during development
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0
  },
  overrides: [
    {
      files: ["*.vue"],
      rules: {
        "indent": "off",
        "vue/script-indent": ['error', 2, {
          'baseIndent': 1,
          'switchCase': 1
        }]
      }
    }
  ]
}
