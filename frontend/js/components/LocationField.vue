<template>
  <a17-inputframe :error="error" :note="note" :locale="locale" @localize="updateLocale" :label="label" :name="name" :required="required">
    <div class="form__field" :class="textfieldClasses">
      <input
        type="search"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="address"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <div v-if="showMap" class="form__field--showMap">
        <a href="#" type="button" @click.prevent="toggleMap"><span v-svg symbol="location"></span><span v-html="mapMessage"></span></a>
      </div>

      <input type="hidden" :name="`${name}__lat`" :value="lat"/>
      <input type="hidden" :name="`${name}__lng`" :value="lng"/>
    </div>
    <div class="form__mapContainer" v-if="showMap" v-show="isMapOpen"></div>
  </a17-inputframe>
</template>

<script>
  import debounce from 'lodash/debounce'
  import isEqual from 'lodash/isEqual'

  import FormStoreMixin from '@/mixins/formStore'
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'
  import { loadScript } from '@/utils/loader'

  const MAPMESSAGE = {
    show: window.$trans('fields.map.show'),
    hide: window.$trans('fields.map.hide')
  }
  const GOOGLEMAPURL = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='
  const APIKEY = window[process.env.VUE_APP_NAME].hasOwnProperty('APIKEYS') && window[process.env.VUE_APP_NAME].APIKEYS.hasOwnProperty('googleMapApi') ? window[process.env.VUE_APP_NAME].APIKEYS.googleMapApi : null

  /* global google */

  export default {
    name: 'A17Locationfield',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
      type: {
        type: String,
        default: 'text'
      },
      zoom: {
        type: Number,
        default: 15
      },
      showMap: {
        type: Boolean,
        default: true
      },
      openMap: {
        type: Boolean,
        default: false
      },
      saveExtendedData: {
        type: Boolean,
        default: false
      },
      autoDetectLatLngValue: {
        type: Boolean,
        default: false
      },
      initialLat: {
        type: Number,
        default: null
      },
      initialLng: {
        type: Number,
        default: null
      }
    },
    data: function () {
      return {
        map: null,
        autocompletePlace: null,
        markers: [],
        address: '',
        boundingBox: [],
        types: [],
        beforeFocusAddress: '',
        lat: this.initialLat,
        lng: this.initialLng,
        focused: false,
        isMapOpen: this.openMap,
        mapMessage: this.openMap ? MAPMESSAGE.hide : MAPMESSAGE.show
      }
    },
    computed: {
      value: {
        get () {
          const resp = {
            latlng: this.lat + '|' + this.lng,
            address: this.address
          }

          if (this.saveExtendedData) {
            resp.boundingBox = this.boundingBox
            resp.types = this.types
          }

          return resp
        },
        set (value) {
          const coord = value.latlng.split('|')
          this.lat = parseFloat(coord[0])
          this.lng = parseFloat(coord[coord.length - 1])
          this.address = value.address

          if (this.saveExtendedData) {
            this.boundingBox = value.boundingBox
            this.types = value.types
          }
        }
      },
      textfieldClasses: function () {
        return {
          's--focus': this.focused,
          's--disabled': this.disabled
        }
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (!isEqual(newValue, this.value)) {
          this.value = newValue

          this.clearMarkers()

          if (this.address === '') {
            this.lat = this.initialLat
            this.lng = this.initialLng
          }

          if (this.lat && this.lng && this.map) {
            const location = { lat: this.lat, lng: this.lng }
            this.addMarker(location)
            this.map.panTo(location)
          }
        }
      },
      onFocus: function (event) {
        this.focused = true
        this.beforeFocusAddress = this.address

        this.$emit('focus')
      },
      onBlur: function (event) {
        this.focused = false

        if (this.address === '') {
          this.clearMarkers()
          this.lat = this.initialLat
          this.lng = this.initialLng
        }

        // Only save into the store if something changed from the moment you focused the field
        // see formStore mixin
        if (this.beforeFocusAddress !== this.address) this.saveIntoStore()

        this.$emit('blur')
      },
      onInput: function (event) {
        const newValue = event.target.value

        this.address = newValue
        this.$emit('change', newValue)

        if (this.autoDetectLatLngValue) {
          const latlng = newValue.match(/^(-?\d+(?:\.\d+)?),+ *(-?\d+(?:\.\d+)?)$/)

          if (latlng) {
            this.onLatLngEntered(latlng[1], latlng[2])
          }
        }
      },
      onPlaceChanged: function () {
        const place = this.autocompletePlace.getPlace()

        this.clearMarkers()
        this.clearLatLng()

        if (place.geometry) {
          const location = place.geometry.location

          this.address = place.formatted_address
          this.setLatLng(location)

          if (this.saveExtendedData) {
            this.boundingBox = place.geometry.viewport
            this.types = place.types
          }

          if (this.map) {
            this.addMarker(location)
            this.map.panTo(location)
            this.map.setZoom(this.zoom)
          }
        }

        this.beforeFocusAddress = this.address

        // see formStore mixin
        this.saveIntoStore()
      },
      onClick: function (event) {
        const latlng = event.latLng

        this.clearMarkers()
        this.clearLatLng()

        this.address = [latlng.lat(), latlng.lng()].join(',')
        this.setLatLng(latlng)

        if (this.map) {
          this.addMarker(latlng)
        }

        // see formStore mixin
        this.saveIntoStore()
      },
      onLatLngEntered: debounce(function (lat, lng) {
        const latlng = new google.maps.LatLng(lat, lng)

        this.clearMarkers()
        this.clearLatLng()

        this.address = [latlng.lat(), latlng.lng()].join(',')
        this.setLatLng(latlng)

        if (this.map) {
          this.addMarker(latlng)
          this.map.setCenter(latlng)
        }

        // see formStore mixin
        this.saveIntoStore()
      }, 600),
      clearMarkers: function () {
        for (let i = 0; i < this.markers.length; i++) {
          if (this.markers[i]) {
            this.markers[i].setMap(null)
          }
        }
        this.markers = []
      },
      clearLatLng: function () {
        this.lat = 0
        this.lng = 0
      },
      addMarker: function (location) {
        const marker = new google.maps.Marker({
          position: location,
          map: this.map
        })

        this.markers.push(marker)
      },
      setLatLng: function (latlng) {
        this.lat = latlng.lat()
        this.lng = latlng.lng()
      },
      toggleMap: function () {
        this.isMapOpen = !this.isMapOpen
        this.mapMessage = this.isMapOpen ? MAPMESSAGE.hide : MAPMESSAGE.show

        if (!this.map && typeof google !== 'undefined') {
          this.$nextTick(function () {
            this.initMap()
          })
        }
      },
      initMap: function () {
        const preset = this.lat + this.lng

        const mapOptions = {
          zoom: preset ? this.zoom : 1,
          center: new google.maps.LatLng(this.lat, this.lng),
          mapTypeControl: false,
          panControl: false,
          zoomControl: false,
          streetViewControl: false
        }

        // Init google API here
        const mapCanvas = document.createElement('div')
        mapCanvas.className = 'form__map'

        this.$el.querySelector('.form__mapContainer').appendChild(mapCanvas)
        this.map = new google.maps.Map(mapCanvas, mapOptions)

        if (preset) {
          this.addMarker(new google.maps.LatLng(this.lat, this.lng))
        }

        this.map.addListener('click', this.onClick)
      },
      initGeocoder: function () {
        const self = this
        // Create the autocomplete object and associate it with the UI input control.
        this.autocompletePlace = new google.maps.places.Autocomplete(this.$el.querySelector('input[type="search"]'))
        // When a place is selected
        google.maps.event.addListener(this.autocompletePlace, 'place_changed', this.onPlaceChanged)

        if (this.address === '' && this.lat && this.lng) {
          const geocoder = new google.maps.Geocoder()
          const location = { lat: this.lat, lng: this.lng }

          // reverse geocoding
          geocoder.geocode({
            location
          }, function (results, status) {
            if (status === 'OK') {
              if (results[1]) {
                self.address = results[1].formatted_address
              } else {
                console.error('Geocoding - No results found')
              }
            } else {
              console.error('Geocoding - Geocoder failed due to: ' + status)
            }
          })
        }
      },
      initGoogleApi: function () {
        this.initGeocoder()
        if (this.showMap && this.isMapOpen) {
          this.initMap()
        }
      }
    },
    mounted: function () {
      if (typeof google !== 'undefined') {
        this.initGoogleApi()
      } else {
        const id = 'google-map-api-script'
        const src = GOOGLEMAPURL + APIKEY
        loadScript(id, src, 'text/javascript')
          .then(() => {
            this.initGoogleApi()
          })
      }
    },
    beforeDestroy: function () {
      if (typeof google !== 'undefined') google.maps.event.clearListeners(this.autocompletePlace, 'place_changed', this.onPlaceChanged)
    }
  }
</script>

<style lang="scss" scoped>

  .form__field {
    display: flex;
    align-items: center;
    padding: 0 15px;

    input {
      padding: 0;
    }

    .form__field--showMap {

      a {
        @include font-tiny();
        display: flex;
        align-items: center;
        text-decoration: none;
        color: $color__text--light;
        transition: color 250ms;

        &:hover {
          color: $color__text--forms;
        }

        span {
          margin-right: 5px;
        }
      }
    }
  }

</style>
