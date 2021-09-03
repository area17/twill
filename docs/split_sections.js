const fs = require('fs');

class Section {
  constructor(titleLine, lines=[]) {
    this.titleLine = titleLine;
    this.lines = lines;

    this.headerLevel = titleLine.replace(/[^#]/g, '');

    this.title = titleLine.replace(/^#+ +/, '');

    this.slug = this.title
      .toLowerCase()
      .replace(/[^a-z0-9]/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-/g, '')
      .replace(/-$/g, '');
  }

  parser() {
    if (!this._parser) {
      this._parser = new SectionParser(this.lines, this.headerLevel + '#');
    }
    return this._parser;
  }

  intro() {
    return this.parser().intro;
  }

  children() {
    return this.parser().sections;
  }
}

class SectionParser {
  constructor(lines, nextHeaderLevel) {
    this.lines = lines;
    this.nextHeaderLevel = nextHeaderLevel;
    this.intro = [];
    this.sections = [];

    this.parseSections();
  }

  parseSections() {
    let currentSection = null;

    this.lines.forEach(line => {
      const re = new RegExp(`^${this.nextHeaderLevel} `);

      if (re.test(line)) {
        currentSection && this.sections.push(currentSection);
        currentSection = new Section(line);
        return;
      }

      if (!currentSection) {
        this.intro.push(line);
      } else {
        currentSection.lines.push(line);
      }
    });

    currentSection && this.sections.push(currentSection);
  }
}

function writePage(title, lines, fileName) {
  let header = [
    '---',
    'pageClass: twill-doc',
    `title: ${title}`,
    '---',
    '',
    `# ${title}`,
    '',
  ];

  fs.writeFileSync(fileName, header.concat(lines).join("\n"));
}

function exportToPages(section, parentDirectory) {
  writePage(section.title, section.intro(), `${parentDirectory}/README.md`, );

  section.children().forEach(child => {
    writePage(child.title, child.lines, `${parentDirectory}/${child.slug}.md`);
  });
}

function exportToDirectories(documentation, parentDirectory) {
  documentation.children().forEach(child => {
    const sectionDirectory = `${parentDirectory}/${child.slug}`;

    fs.mkdirSync(sectionDirectory);

    exportToPages(child, sectionDirectory);
  });
}

function exportNavigation(documentation, parentDirectory) {
  const folders = documentation.children().map(folder => {
    const pages = folder.children().map(page => {
      return {
        title: page.title,
        path: `/${folder.slug}/${page.slug}.html`,
        collapsable: true,
      }
    });

    return {
      title: folder.title,
      path: `/${folder.slug}/`,
      children: pages,
      collapsable: true,
    }
  });

  const navigation = [{
    title: documentation.title,
    children: folders,
    collapsable: false,
  }];

  const json = JSON.stringify(navigation, false, '  ');

  const template = `module.exports = ${json};`

  fs.writeFileSync(`${parentDirectory}/sidebar.js`, template);
}

function main() {
  const documentation = new Section(
    '# Documentation',
    fs.readFileSync('./README.md', 'utf8').split('\n')
  );

  const parentDirectory = './src'

  if (!fs.existsSync(parentDirectory)){
      fs.mkdirSync(parentDirectory);
  } else {
    console.error(`Error: directory '${parentDirectory}' exists!`);
    process.exit(1);
  }

  exportToDirectories(documentation, parentDirectory);

  exportNavigation(documentation, parentDirectory);
}

main();
