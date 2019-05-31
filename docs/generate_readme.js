/**
 * For previewing changes to the documentation.
 * 
 * - Open a terminal window in the /docs directory
 * - run `npm run dev`
 * - you will then be able to preview the documentation website at `http://localhost:8080`
 * 
 */

const fs = require('fs');

const sections = [
    'preface',
    'getting-started/architecture_concepts',
    'getting-started/environment-requirements',
    'getting-started/installation',
    'getting-started/configuration',
    'getting-started/artisan_commands',
    'crud-modules',
    'media-library',
    'block-editor',
    'other-cms-features',
    'resources'
];

const settings = `---
sidebar: auto
pageClass: twill-doc
title: Documentation
---`;

const content = settings + sections.map((section) => {
    return '\n\n' + fs.readFileSync('./.sections/' + section + '.md', 'utf8') + '\n\n';
}).join('');

fs.writeFileSync('README.md', content);