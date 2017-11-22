<template>
  <a17-inputframe :error="error" :note="note" :locale="locale" @localize="updateLocale" :label="label">
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
  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  const MAPMESSAGE = {
    show: 'Show&nbsp;map',
    hide: 'Hide&nbsp;map'
  }
  const GOOGLEMAPURL = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='
  const APIKEY = window.hasOwnProperty('APIKEYS') && window.APIKEYS.hasOwnProperty('googleMapApi') ? window.APIKEYS.googleMapApi : null

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
      initialLat: {
        type: Number,
        default: 0
      },
      initialLng: {
        type: Number,
        default: 0
      }
    },
    data: function () {
      return {
        map: null,
        autocompletePlace: null,
        markers: [],
        address: '',
        lat: parseFloat(this.initialLat),
        lng: parseFloat(this.initialLng),
        focused: false,
        isMapOpen: this.openMap,
        mapMessage: this.openMap ? MAPMESSAGE.hide : MAPMESSAGE.show,
        googleScript: null
      }
    },
    computed: {
      value: {
        get () {
          return {
            latlng: this.lat + '|' + this.lng,
            address: this.address
          }
        },
        set (value) {
          const coord = value.latlng.split('|')
          this.lat = parseFloat(coord[0])
          this.lng = parseFloat(coord[coord.length - 1])
          this.address = value.address
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
      onFocus: function (event) {
        this.focused = true

        this.$emit('focus')
      },
      onBlur: function (event) {
        this.focused = false

        // see formStore mixin
        this.saveIntoStore()

        this.$emit('blur')
      },
      onInput: function (event) {
        const newValue = event.target.value

        this.address = newValue
        this.$emit('change', newValue)
      },
      onPlaceChanged: function () {
        const place = this.autocompletePlace.getPlace()

        this.clearMarkers()
        this.clearLatLng()

        if (place.geometry) {
          const location = place.geometry.location

          this.address = place.formatted_address
          this.setLatLng(location)

          if (this.map) {
            this.addMarker(location)
            this.map.panTo(location)
            this.map.setZoom(this.zoom)
          }
        }
      },
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
        /* global google */
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
      },
      initGeocoder: function () {
        const self = this
        // Create the autocomplete object and associate it with the UI input control.
        this.autocompletePlace = new google.maps.places.Autocomplete(this.$el.querySelector('input[type="search"]'))
        // When a place is selected
        google.maps.event.addListener(this.autocompletePlace, 'place_changed', this.onPlaceChanged)

        if (this.address === '') {
          const geocoder = new google.maps.Geocoder()
          const location = {lat: this.lat, lng: this.lng}

          // reverse geocoding
          geocoder.geocode({
            'location': location
          }, function (results, status) {
            if (status === 'OK') {
              if (results[1]) {
                self.address = results[1].formatted_address
              } else {
                console.info('Geocoding - No results found')
              }
            } else {
              console.warn('Geocoding - Geocoder failed due to: ' + status)
            }
          })
        }
      },
      initGoogleApi: function () {
        this.initGeocoder()
        if (this.showMap && this.isMapOpen) {
          this.initMap()
        }
        if (this.googleScript) this.googleScript.removeEventListener('load', this.initGoogleApi)
      },
      loadScript: function (src) {
        const id = 'google-map-api-script'
        let script = document.getElementById(id)
        if (script) {
          this.googleScript = script
          script.addEventListener('load', this.initGoogleApi)
        } else {
          script = document.createElement('script')
          this.googleScript = script
          script.setAttribute('id', id)
          script.type = 'text/javascript'
          script.onload = this.initGoogleApi
          document.getElementsByTagName('head')[0].appendChild(script)
          script.src = src
        }
      }
    },
    mounted: function () {
      /* global google */
      if (typeof google !== 'undefined') {
        this.initGoogleApi()
      } else {
        const src = GOOGLEMAPURL + APIKEY
        this.loadScript(src, this.initGoogleApi)
      }
    },
    beforeDestroy: function () {
      /* global google */
      if (typeof google !== 'undefined') google.maps.event.clearListeners(this.autocompletePlace, 'place_changed', this.onPlaceChanged)
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
