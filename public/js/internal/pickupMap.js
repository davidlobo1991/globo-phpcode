var mapClass = {}

$(function () {

  mapClass = {
    startPoint: 'Globobalear',
    geocoder: new google.maps.Geocoder(),
    mapDiv: document.getElementById('map'),
    map: {},
    marker: {},

    geoCodePosition: function (pos) {
      this.geocoder.geocode({
        latLng: pos
      }, function (responses) {
        if (responses && responses.length > 0) {
          mapClass.updateSearchInput(responses[0].formatted_address)
        }
      })
    },
    updateInputsPosition: function (latLng) {
      $('[name=longitude]').val(latLng.lng())
      $('[name=latitude]').val(latLng.lat())
    },
    updateSearchInput: function (str) {
      $('[name^="mapaddress"]').val(str)
    },
    codeAddress: function (address) {
      mapClass.geocoder.geocode({'address': address}, function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
          mapClass.updateInputsPosition(results[0].geometry.location)
          mapClass.marker.setPosition(results[0].geometry.location)
          mapClass.map.setCenter(results[0].geometry.location)
        } else {
          alert('ERROR : ' + status)
        }
      })
    },
    updateMarker: function (location) {
      mapClass.marker.setPosition(location)
      mapClass.updateInputsPosition(location)
      mapClass.geoCodePosition(location)
    },
    init: function () {
      this.map = new google.maps.Map(this.mapDiv, {
        zoom: 11,
      })
      this.marker = new google.maps.Marker({
        map: this.map,
        animation: google.maps.Animation.DROP
      })

      if ($('[name=longitude]').val() === '' && $('[name=latitude]').val() === '') {
        mapClass.updateSearchInput(this.startPoint)
        mapClass.codeAddress(this.startPoint)
      } else {
        var location = new google.maps.LatLng($('[name=latitude]').val(), $('[name=longitude]').val())
        mapClass.updateMarker(location)
        this.map.setCenter(this.marker.getPosition())
      }

      if ($('form').length) {
        this.marker.setDraggable(true)
        mapClass.initEvents()
      }

    },
    initEvents: function () {
      google.maps.event.addListener(this.marker, 'dragstart', function () {
        mapClass.updateSearchInput('Arrastrando...')
      })

      google.maps.event.addListener(this.marker, 'dragend', function () {
        mapClass.updateMarker(this.getPosition())
      })

      google.maps.event.addListener(this.map, 'click', function (event) {
        mapClass.updateMarker(event.latLng)
      })
    }
  }

  mapClass.init()
})