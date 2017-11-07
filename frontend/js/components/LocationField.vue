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

      <input type="hidden" :name="`${name}__lat`" :value="lat" />
      <input type="hidden" :name="`${name}__lng`" :value="lng" />
    </div>
  </a17-inputframe>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

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
        focused: false
      }
    },
    computed: {
      latLng: {
        get () {
          return this.lat + '|' + this.lng
        },
        set (value) {
          var coord = value.split('|')
          this.lat = parseFloat(coord[0])
          this.lng = parseFloat(coord[coord.length - 1])
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
        for (var i = 0; i < this.markers.length; i++) {
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
      }
    },
    mounted: function () {
      let self = this

      const preset = this.lat + this.lng

      const mapOptions = {
        zoom: preset ? this.zoom : 1,
        center: new google.maps.LatLng(this.lat, this.lng),
        mapTypeControl: false,
        panControl: false,
        zoomControl: false,
        streetViewControl: false
      }

      /* global google */
      if (google) {
        if (this.showMap) {
          // Init google API here
          const mapCanvas = document.createElement('div')
          mapCanvas.className = 'form__map'

          this.$el.appendChild(mapCanvas)

          this.map = new google.maps.Map(mapCanvas, mapOptions)
        }

        // Create the autocomplete object and associate it with the UI input control.
        this.autocompletePlace = new google.maps.places.Autocomplete(self.$el.querySelector('input[type="search"]'))

        // When a place is selected
        google.maps.event.addListener(this.autocompletePlace, 'place_changed', this.onPlaceChanged)

        if (preset) {
          if (this.showMap) this.addMarker(new google.maps.LatLng(self.lat, self.lng))

          // reverse geocoding
          if (this.address === '') {
            const geocoder = new google.maps.Geocoder()
            const location = { lat: self.lat, lng: self.lng }

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
        }
      }
    },
    beforeDestroy: function () {
      if (google) google.maps.event.clearListeners(this.autocompletePlace, 'place_changed', this.onPlaceChanged)
    }
  }
</script>
