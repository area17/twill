set -e

rm -rf src docs .vuepress/docs README.md

node generate_readme.js

node split_sections.js

cp -r .vuepress src/

mv src/sidebar.js src/.vuepress/

echo '---
pageClass: twill-doc
---

# This is the documentation index

' >> src/index.md

npm run build
