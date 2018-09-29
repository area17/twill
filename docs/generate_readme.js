const fs = require('fs');

const sections = [
	'preface',
 	'getting-started/architecture_concepts',
 	'getting-started/environment-requirements',
 	'getting-started/installation',
 	'getting-started/configuration',
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