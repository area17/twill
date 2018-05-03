const DEFAULT_OPTIONS = {
  target: 'data-sticky-target',
  toptarget: 'data-sticky-top',
  classContainer: 'sticky',
  classFixed: 'sticky__fixed',
  classAbs: 'sticky__abs',
  classEnd: 'sticky__scrolled',
  topOffset: 0,
  offset: 20
}

// import getCurrentMediaQuery from '../../frontend/js/helpers/getCurrentMediaQuery'

export default class Sticky {
  constructor (ref, options) {
    options = { ...DEFAULT_OPTIONS, ...options }

    this.target = null
    this.toptarget = null

    // save reference and options
    this.container = ref
    this.containerID = this.container.getAttribute('data-sticky-id')

    this.options = options

    // Target is the thing we want to get sticky
    // get required target
    if (this.options.target) this.target = this.container.querySelector('[' + this.options.target + '="' + this.containerID + '"]')

    // Toptarget is the thing that will limit the sticky to certain boundaries
    // get required toptarget
    if (this.options.toptarget) this.toptarget = this.container.querySelector('[' + this.options.toptarget + '="' + this.containerID + '"]')
    this.topMargin = this.container.hasAttribute('data-sticky-offset') ? parseInt(this.container.getAttribute('data-sticky-offset')) : this.options.offset

    // TopOffset
    this.topOffset = this.container.hasAttribute('data-sticky-topoffset') ? parseInt(this.container.getAttribute('data-sticky-topoffset')) : this.options.topOffset

    ref.classList.add(this.options.classContainer)

    // set event listeners
    this._setEventListeners()
    this._refresh()
  }

  //
  // Public methods
  //

  /**
   * Refresh sticky position
   * @method Sticky#refresh
   * @memberof Sticky
   */
  refresh = () => this._refresh()

  /**
   * Remove event and destroy
   * @method Sticky#dispose
   * @memberof Sticky
   */
  dispose = () => this._dispose()

  //
  // Defaults
  //
  status = 'top'
  ticking = false
  anchors = [ 'Top', 'Bottom' ]
  lastScrollPos = 0
  prevScrollPos = -1

  //
  // Private methods
  //

  _refresh () {
    // console.log(getCurrentMediaQuery)
    // if (getCurrentMediaQuery.indexOf("small") !== -1) return false
    if (!this.target) return false

    const scrollPos = this.lastScrollPos
    const targetHeight = this.target.offsetHeight
    const asideHeight = this.container.offsetHeight
    const anchor = targetHeight + this.topMargin < window.innerHeight ? 0 : 1

    let containerTop = (this.toptarget) ? this.toptarget.getBoundingClientRect().top + this.topOffset : this.container.getBoundingClientRect().top + this.topOffset
    let containerBottom = containerTop + asideHeight - targetHeight

    // if the sticky target is smaller than the window height, adjust the containerTop position
    containerTop = (containerTop - this.topMargin) + Math.max(0, targetHeight + this.topMargin - window.innerHeight) + scrollPos

    if (this.toptarget) containerBottom = containerTop + asideHeight - targetHeight - Math.max(0, this.toptarget.getBoundingClientRect().top - this.container.getBoundingClientRect().top)

    if (this.target.offsetHeight < asideHeight) {
      // Top : we scrolled up
      if (this.status !== 'top' && scrollPos < containerTop) {
        this._removePositionClass()
        this.status = 'top'
      }

      // Scrolling : we are scrolling in the middle of the zone
      if (this.status !== 'scrolling' && scrollPos >= containerTop && scrollPos < containerBottom) {
        this._removePositionClass()
        this.target.classList.add(this.options.classFixed + this.anchors[anchor])

        this.status = 'scrolling'
      }

      // Bottom : we scrolled down
      if (this.status !== 'bottom' && scrollPos >= containerBottom) {
        this._removePositionClass()
        this.target.classList.add(this.options.classAbs)

        this.status = 'bottom'
      }

      // Bonus : The content after the container start to appear
      if ((scrollPos + window.innerHeight) >= (this.container.getBoundingClientRect().top + scrollPos + asideHeight)) {
        this.target.classList.add(this.options.classEnd)
      } else {
        this.target.classList.remove(this.options.classEnd)
      }
    }

    return this
  }

  _removePositionClass () {
    for (let i = 0; i < this.anchors.length; i++) {
      this.target.classList.remove(this.options.classFixed + this.anchors[i])
    }

    this.target.classList.remove(this.options.classAbs)
  }

  _scroll () {
    let self = this

    self.lastScrollPos = window.pageYOffset

    if (!self.ticking) {
      window.requestAnimationFrame(function () {
        self._refresh()

        self.prevScrollPos = self.lastScrollPos
        self.ticking = false
      })
    }

    self.ticking = true

    return this
  }

  _resize () {
    this.lastScrollPos = window.pageYOffset
    this.status = ''

    this._refresh()

    return this
  }

  _dispose () {
    window.removeEventListener('scroll', () => this._scroll())
    window.removeEventListener('resize', () => this._resize())

    return this
  }

  _setEventListeners () {
    window.addEventListener('scroll', () => this._scroll())
    window.addEventListener('resize', () => this._resize())
    this._resize()
  }
}
