import isEmpty from 'lodash/isEmpty'

/*
* Gather selected items in a selected object (currently used for medias and browsers)
* if a block is passed as second argument, we retrieve selected items namespaced by the block id
* and strip it out from the key to clean things up and make it easier for the backend
*/
const gatherSelected = (selected, block = null) => {
  return Object.assign({}, ...Object.keys(selected).map(key => {
    if (block) {
      if (isBlockField(key, block.id)) {
        return {
          [stripOutBlockNamespace(key, block.id)]: selected[key]
        }
      }
    } else if (!key.startsWith('blocks[')) {
      return {
        [key]: selected[key]
      }
    }
    return null
  }).filter(x => x))
}

export const isBlockField = (name, id) => {
  return name.startsWith('blocks[' + id + ']')
}

export const stripOutBlockNamespace = (name, id) => {
  const nameWithoutBlock = name.replace('blocks[' + id + '][', '')
  return nameWithoutBlock.match(/]/gi).length > 1 ? nameWithoutBlock.replace(']', '') : nameWithoutBlock.slice(0, -1)
}

export const buildBlock = (block, rootState) => {
  return {
    id: block.id,
    type: block.type,
    name: block.name,
    // retrieve all fields for this block and clean up field names
    content: rootState.form.fields.filter((field) => {
      return isBlockField(field.name, block.id)
    }).map((field) => {
      return {
        name: stripOutBlockNamespace(field.name, block.id),
        value: field.value
      }
    }).reduce((content, field) => {
      content[field.name] = field.value
      return content
    }, {}),
    medias: gatherSelected(rootState.mediaLibrary.selected, block),
    browsers: gatherSelected(rootState.browser.selected, block),
    // gather repeater blocks from the repeater store module
    blocks: Object.assign({}, ...Object.keys(rootState.repeaters.repeaters).filter(repeaterKey => {
      return repeaterKey.startsWith('blocks-' + block.id)
    }).map(repeaterKey => {
      return {
        [repeaterKey.replace('blocks-' + block.id + '_', '')]: rootState.repeaters.repeaters[repeaterKey].map(repeaterItem => {
          return buildBlock(repeaterItem, rootState)
        })
      }
    }))
  }
}

export const isBlockEmpty = (blockData) => {
  return isEmpty(blockData.content) && isEmpty(blockData.browsers) && isEmpty(blockData.medias) && isEmpty(blockData.blocks)
}

export const gatherRepeaters = (rootState) => {
  return Object.assign({}, ...Object.keys(rootState.repeaters.repeaters).filter(repeaterKey => {
    // we start by filtering out repeater blocks
    return !repeaterKey.startsWith('blocks-')
  }).map(repeater => {
    return {
      [repeater]: rootState.repeaters.repeaters[repeater].map(repeaterItem => {
        // and for each repeater we build a block for each item
        const repeaterBlock = buildBlock(repeaterItem, rootState)

        // we want to inline fields in the repeater object
        // and we don't need the type of component used
        const fields = repeaterBlock.content
        delete repeaterBlock.content
        delete repeaterBlock.type

        // and lastly we want to keep the id to update existing items
        fields.id = repeaterItem.id

        return Object.assign(repeaterBlock, fields)
      })
    }
  }))
}

export const gatherBlocks = (rootState) => {
  const used = { ...rootState.blocks.created }
  return Object.keys(used).map(name => {
    return used[name].map(block => {
      block.name = name
      return buildBlock(block, rootState)
    })
  }).flat()
}

export const getFormFields = (rootState) => {
  const fields = rootState.form.fields.filter((field) => {
    // we start by filtering out blocks related form fields
    return !field.name.startsWith('blocks[') && !field.name.startsWith('mediaMeta[')
  }).reduce((fields, field) => {
    // and we create a new object with field names as keys,
    // to inline fields in the submitted data
    fields[field.name] = field.value
    return fields
  }, {})

  return fields
}

export const getModalFormFields = (rootState) => {
  const fields = rootState.form.modalFields.filter((field) => {
    // we start by filtering out blocks related form fields
    return !field.name.startsWith('blocks[') && !field.name.startsWith('mediaMeta[')
  }).reduce((fields, field) => {
    // and we create a new object with field names as keys,
    // to inline fields in the submitted data
    fields[field.name] = field.value
    return fields
  }, {})

  return fields
}

export const getFormData = (rootState) => {
  const fields = getFormFields(rootState)

  // we can now create our submitted data object out of:
  // - our just created fields object,
  // - publication properties
  // - selected medias and browsers
  // - created blocks and repeaters
  const data = Object.assign(fields, {
    cmsSaveType: rootState.form.type,
    published: rootState.publication.published,
    public: rootState.publication.visibility === 'public',
    publish_start_date: rootState.publication.startDate,
    publish_end_date: rootState.publication.endDate,
    languages: rootState.language.all,
    parent_id: rootState.parents.active,
    medias: gatherSelected(rootState.mediaLibrary.selected),
    browsers: gatherSelected(rootState.browser.selected),
    blocks: gatherBlocks(rootState),
    repeaters: gatherRepeaters(rootState)
  })

  return data
}
