module.exports = {
  title: 'Twill',
  description: 'Twill — An open source CMS toolkit for Laravel',
  base: `/${process.env.VUEPRESS_BASE}/`,
  dest: "docs",
  head: [
    ['link', { rel: 'shortcut icon', href: '/favicon.ico' }],
    ['link', { rel: 'apple-touch-icon', href: '/favicon-192.png' }],
    ['meta', { name: 'theme-color', content: '#000000' }],
    ['meta', { property: 'og:url', content: 'https://twill.io/' }],
    ['meta', { name: 'twitter:url', content: 'https://twill.io/' }],
    ['meta', { property: 'og:image', content: '/social_share.png' }],
    ['meta', { property: 'og:image:width', content: '1200' }],
    ['meta', { property: 'og:image:height', content: '630' }],
    ['meta', { name: 'twitter:image', content: '/social_share.png' }],
    ['meta', { itemprop: 'image', content: '/social_share.png' }],
    ['meta', { property: 'og:site_name', content: 'Twill' }],
    ['meta', { property: 'og:author', content: 'https://www.facebook.com/twillcms/' }],
    ['meta', { name: 'twitter:card', content: 'summary_large_image' }],
    ['meta', { name: 'twitter:site', content: '@twillcms' }],
    ['meta', { name: 'twitter:domain', content: 'twill.io' }],
    ['meta', { name: 'twitter:creator', content: '@twillcms' }]
  ],
  themeConfig: {
    docsRepo: 'area17/twill',
    docsDir: 'docs/src',
    docsBranch: '2.x',
    editLinks: true,
    editLinkText: 'Edit this page on GitHub',
    algolia: {
      apiKey: '9360cb12e45076d95f77b63549021a6d',
      indexName: 'twill',
      appId: '89HNJPXALF',
    },
    nav: [
      { text: 'GitHub', link: 'https://github.com/area17/twill' },
    ],
    sidebar: require('./sidebar.js'),
  },
  plugins: [
    ['@vuepress/google-analytics', {
      ga: 'UA-117094786-1'
    }]
  ]
}
