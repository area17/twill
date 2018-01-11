export default function (parents, spacer) {
  const options = []

  function setSpacing (level) {
    return Array(level + 1).join(spacer) + ' '
  }

  function getOptionsFromArray (parents, level) {
    parents.forEach(function (parent) {
      const option = {}
      option.value = parent.id
      if (parent.edit) option.edit = parent.edit
      option.label = setSpacing(level) + parent.name
      options.push(option)

      if (parent.children && parent.children.length) {
        const newLevel = level + 1
        getOptionsFromArray(parent.children, newLevel)
      }
    })
  }

  getOptionsFromArray(parents, 0)

  return options
}
