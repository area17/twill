/*

Tooltip
Information showing up when hovering some UI/texts. (see tooltip.scss for styling the template)

Highly inspired by popper.js's tooltip (https://popper.js.org/tooltip-documentation.html) but way simpler

*/

const DEFAULT_OPTIONS = {
  container: false,
  delay: 0, // could be : delay: { show: 0, hide: 0 }
  html: false,
  budge: 15,
  placement: 'top',
  theme: 'default',
  title: '',
  template: '<div class="tooltip" role="tooltip"><div class="tooltip__arrow"></div><div class="tooltip__inner"></div></div>',
  trigger: 'hover focus',
  offset: 0
}

export default class Tooltip {
  constructor (ref, options) {
    options = {...DEFAULT_OPTIONS, ...options}

    // save reference and options
    this.reference = ref
    this.options = options

    // get events list
    const events = typeof options.trigger === 'string'
      ? options.trigger.split(' ').filter(trigger => ['click', 'hover', 'focus'].indexOf(trigger) !== -1)
      : []

    // set initial state
    this._isOpen = false

    // set event listeners
    this._setEventListeners(ref, events, options)
  }

  //
  // Public methods
  //

  /**
   * Reveals an element's tooltip. This is considered a "manual" triggering of the tooltip.
   * Tooltips with zero-length titles are never displayed.
   * @method Tooltip#show
   * @memberof Tooltip
   */
  show = () => this._show(this.reference, this.options)

  /**
   * Hides an element’s tooltip. This is considered a “manual” triggering of the tooltip.
   * @method Tooltip#hide
   * @memberof Tooltip
   */
  hide = () => this._hide()

  /**
   * Hides and destroys an element’s tooltip.
   * @method Tooltip#dispose
   * @memberof Tooltip
   */
  dispose = () => this._dispose()

  /**
   * Toggles an element’s tooltip. This is considered a “manual” triggering of the tooltip.
   * @method Tooltip#toggle
   * @memberof Tooltip
   */
  toggle = () => {
    if (this._isOpen) {
      return this.hide()
    } else {
      return this.show()
    }
  }

  //
  // Defaults
  //
  innerSelector = '.tooltip__inner'

  //
  // Private methods
  //

  _events = []

  /**
   * Creates a new tooltip node
   * @memberof Tooltip
   * @private
   * @param {HTMLElement} reference
   * @param {String} template
   * @param {String|HTMLElement|TitleFunction} title
   * @param {Boolean} allowHtml
   * @return {HTMLelement} tooltipNode
   */
  _create (reference, template, theme, title, allowHtml) {
    if (this._tooltipNode) {
      return this
    }

    // create tooltip element
    const tooltipTemp = window.document.createElement('div')
    tooltipTemp.innerHTML = template.trim()
    const tooltipNode = tooltipTemp.childNodes[0]

    // add unique ID to our tooltip (needed for accessibility reasons)
    tooltipNode.id = `tooltip--${Math.random().toString(36).substr(2, 10)}`

    // set initial `aria-hidden` state to `false` (it's visible!)
    tooltipNode.setAttribute('aria-hidden', 'false')

    // add theme class
    tooltipNode.classList.add('tooltip--' + theme)

    // add title to tooltip
    const titleNode = tooltipTemp.querySelector(this.innerSelector)
    if (title.nodeType === 1) {
      // if title is a node, append it only if allowHtml is true
      allowHtml && titleNode.appendChild(title)
    } else {
      // if it's just a simple text, set innerText or innerHtml depending by `allowHtml` value
      allowHtml ? (titleNode.innerHTML = title) : (titleNode.innerText = title)
    }

    // return the generated tooltip node
    return tooltipNode
  }

  _position (reference, placement, budge) {
    let left = 0
    let top = 0
    let newPosition = placement
    let refBoundingRect = reference.getBoundingClientRect()

    // reset class
    this._tooltipNode.classList.remove('tooltip--' + newPosition)

    // Calculates : top and left coordinates relative to the refBoundingRect
    // top, right, bottom, left
    let topCoord = Math.round(refBoundingRect.top - this._tooltipNode.offsetHeight - budge)
    let topCenteredCoord = Math.round(refBoundingRect.top + (refBoundingRect.height / 2) - (this._tooltipNode.offsetHeight / 2))

    let rightCoord = Math.round(refBoundingRect.left + refBoundingRect.width + budge)

    let bottomCoord = Math.round(refBoundingRect.top + refBoundingRect.height + budge)
    let leftCoord = Math.round(refBoundingRect.left - this._tooltipNode.offsetWidth - budge)
    let leftCenteredCoord = Math.round(refBoundingRect.left + (refBoundingRect.width / 2) - (this._tooltipNode.offsetWidth / 2))

    if (placement === 'top') {
      left = leftCenteredCoord // horizontally centered
      top = topCoord
      newPosition = 'top'

      if (left < 10) left = 10

      if (top < 0) {
        top = bottomCoord
        newPosition = 'bottom'
      }
    }

    if (placement === 'top-right') {
      left = rightCoord
      top = topCoord
      newPosition = 'top'

      if (top < 0) {
        top = bottomCoord
        newPosition = 'bottom'
      }
    }

    if (placement === 'bottom') {
      left = leftCenteredCoord // horizontally centered
      top = bottomCoord
      newPosition = 'bottom'

      if (left < 10) left = 10

      if (top > 0) {
        top = topCoord
        newPosition = 'top'
      }
    }

    if (placement === 'right') {
      left = rightCoord
      top = topCenteredCoord // vertically centered
      newPosition = 'right'
    }

    if (placement === 'left') {
      left = leftCoord
      top = topCenteredCoord // vertically centered
      newPosition = 'left'

      if (left < 0) {
        left = rightCoord
        newPosition = 'right'
      }
    }

    this._tooltipNode.style.left = left + 'px'
    this._tooltipNode.style.top = top + 'px'
    this._tooltipNode.classList.add('tooltip--' + newPosition)
  }

  _show (reference, options) {
    // don't show if it's already visible
    if (this._isOpen && !this._isOpening) {
      return this
    }
    this._isOpen = true

    // get budge
    const budge = reference.getAttribute('data-tooltip-budge') || options.budge

    // get theme
    const theme = reference.getAttribute('data-tooltip-theme') || options.theme

    // get placement
    const placement = reference.getAttribute('data-tooltip-placement') || options.placement

    // if the tooltipNode already exists, just show it
    if (this._tooltipNode) {
      this._tooltipNode.style.opacity = ''
      this._tooltipNode.style.visibility = ''
      this._tooltipNode.style.transition = 'opacity 0.3s'
      this._tooltipNode.setAttribute('aria-hidden', 'false')

      // refresh position
      this._position(reference, placement, budge)

      return this
    }

    // get title
    const title = reference.getAttribute('data-tooltip-title') || options.title

    // create tooltip node
    const tooltipNode = this._create(
      reference,
      options.template,
      theme,
      title,
      options.html
    )

    // Add `aria-describedby` to our reference element for accessibility reasons
    reference.setAttribute('aria-describedby', tooltipNode.id)

    // append tooltip to container
    const container = this._findContainer(options.container, reference)

    this._append(tooltipNode, container)

    this._tooltipNode = tooltipNode

    // refresh position
    this._position(reference, placement, budge)

    return this
  }

  _hide () {
    // don't hide if it's already hidden
    if (!this._isOpen) {
      return this
    }

    this._isOpen = false

    // hide tooltipNode
    this._tooltipNode.style.opacity = '0'
    this._tooltipNode.style.visibility = 'hidden'
    this._tooltipNode.style.transition = ''
    this._tooltipNode.setAttribute('aria-hidden', 'true')

    return this
  }

  _dispose () {
    // remove event listeners
    if (this._events.length) {
      this._events.forEach(({func, event}) => {
        this.reference.removeEventListener(event, func)
      })
      this._events = []
    }

    if (this._tooltipNode) {
      this._hide()

      // destroy tooltipNode
      this._tooltipNode.parentNode.removeChild(this._tooltipNode)
      this._tooltipNode = null
    }

    return this
  }

  _findContainer (container, reference) {
    if (typeof container === 'string') {
      container = window.document.querySelector(container)
    } else if (container === false) {
      // if container is `false`, set it to reference parent
      container = reference.parentNode
    }
    return container
  }

  /**
   * Append tooltip to container
   * @memberof Tooltip
   * @private
   * @param {HTMLElement} tooltip
   * @param {HTMLElement|String|false} container
   */
  _append (tooltipNode, container) {
    container.appendChild(tooltipNode)
  }

  _setEventListeners (reference, events, options) {
    const directEvents = []
    const oppositeEvents = []

    events.forEach(event => {
      switch (event) {
        case 'hover':
          directEvents.push('mouseenter')
          oppositeEvents.push('mouseleave')
          break
        case 'focus':
          directEvents.push('focus')
          oppositeEvents.push('blur')
          break
        case 'click':
          directEvents.push('click')
          oppositeEvents.push('click')
          break
      }
    })

    // schedule show tooltip
    directEvents.forEach(event => {
      const func = evt => {
        if (this._isOpening === true) {
          return
        }
        evt.usedByTooltip = true
        this._scheduleShow(reference, options.delay, options, evt)
      }
      this._events.push({event, func})
      reference.addEventListener(event, func)
    })

    // schedule hide tooltip
    oppositeEvents.forEach(event => {
      const func = evt => {
        if (evt.usedByTooltip === true) {
          return
        }
        this._scheduleHide(reference, options.delay, options, evt)
      }
      this._events.push({event, func})
      reference.addEventListener(event, func)
    })
  }

  _scheduleShow (reference, delay, options /*, evt */) {
    this._isOpening = true
    // defaults to 0
    const computedDelay = (delay && delay.show) || delay || 0
    if (computedDelay > 0) window.setTimeout(() => this._show(reference, options), computedDelay)
    else this._show(reference, options)
  }

  _scheduleHide (reference, delay, options, evt) {
    this._isOpening = false
    // defaults to 0
    const computedDelay = (delay && delay.hide) || delay || 0
    window.setTimeout(() => {
      if (this._isOpen === false) {
        return
      }
      if (!document.body.contains(this._tooltipNode)) {
        return
      }

      // if we are hiding because of a mouseleave, we must check that the new
      // reference isn't the tooltip, because in this case we don't want to hide it
      if (evt.type === 'mouseleave') {
        const isSet = this._setTooltipNodeEvent(evt, reference, delay, options)

        // if we set the new event, don't hide the tooltip yet
        // the new event will take care to hide it if necessary
        if (isSet) {
          return
        }
      }

      this._hide()
    }, computedDelay)
  }

  _setTooltipNodeEvent = (evt, reference, delay, options) => {
    const relatedreference = evt.relatedreference || evt.toElement

    const callback = evt2 => {
      const relatedreference2 = evt2.relatedreference || evt2.toElement

      // Remove event listener after call
      this._tooltipNode.removeEventListener(evt.type, callback)

      // If the new reference is not the reference element
      if (!reference.contains(relatedreference2)) {
        // Schedule to hide tooltip
        this._scheduleHide(reference, options.delay, options, evt2)
      }
    }

    if (this._tooltipNode.contains(relatedreference)) {
      // listen to mouseleave on the tooltip element to be able to hide the tooltip
      this._tooltipNode.addEventListener(evt.type, callback)
      return true
    }

    return false
  }
}
