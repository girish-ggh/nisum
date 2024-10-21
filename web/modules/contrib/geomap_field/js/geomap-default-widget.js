/**
 * @file
 * Geomap default widget custom behavior
 */


(function ($, Drupal) {

  Drupal.behaviors.geomapDefaultWidget = {

    attach: function (context) {
      once('geomapDefaultWidget', '.geomap-widget-map', context)
        .on('map:afterInit', function (event, map) {
          var $map = $(event.target);
          var geolocationPlugin = $map.data('geolocation-plugin');
          var reverseUrl = '/geolocation_provider/reverse/' + geolocationPlugin;
          var geolocationUrl = '/geolocation_provider/geolocation/' + geolocationPlugin;
          var $widget = $map.parents('.geomap-widget-wrapper');
          var $latlon = $widget.find('.latlon-wrapper');
          var $latField = $latlon.find('.geomap-field-lat');
          var $lonField = $latlon.find('.geomap-field-lon');

          var marker = new L.marker([
            $latField.val() || 51.505,
            $lonField.val() || -0.09
          ], {draggable: 'true'});
          marker.on('dragend', function (event) {
            var position = marker.getLatLng();
            $latField.val(position.lat);
            $lonField.val(position.lng);
            this.updateLoader($widget, true);
            $.get(reverseUrl + '/' + position.lat + '/' + position.lng, function (data) {
              this.updateSuggestions($widget, data);
              this.updateLoader($widget, false);
              if (data.features && data.features.length > 0) {
                this.updateFeatureInput($widget, data.features[0]);
              }
            }.bind(this));
          }.bind(this));
          map.addLayer(marker);
          $widget.marker = marker;

          // Listen Lat/Lon fields changes.
          $latlon.find('input').on('input', Drupal.debounce(function (event) {
            this.updateMarker($widget, map);
          }.bind(this), 200));

          // Geolocation behavior.
          $widget.find('.geomap-geolocation-btn').on('click', function (event) {
            var street = $widget.find('input[name$="][street]"]').val();
            var zipcode = $widget.find('input[name$="][zipcode]"]').val();
            var city = $widget.find('input[name$="][city]"]').val();
            var country = $widget.find('input[name$="][country]"]').val();
            var address = street + ', ' + zipcode + ' ' + city + ', ' + country;
            this.updateLoader($widget, true);
            $.get(geolocationUrl + '/' + address, function (data) {
              this.updateLoader($widget, false);
              if (data.features && data.features.length > 0) {
                var feature = data.features[0];
                $latField.val(feature.geometry.coordinates[1]);
                $lonField.val(feature.geometry.coordinates[0]);
                this.updateMarker($widget, map);
                this.updateFeatureInput($widget, feature);
              }
            }.bind(this));
            event.preventDefault();
          }.bind(this));

        }.bind(this));
    },

    updateMarker: function ($widget, map) {
      var lat = $widget.find('.geomap-field-lat').val();
      var lon = $widget.find('.geomap-field-lon').val();
      if (lat && lon) {
        var latlon = new L.LatLng(lat, lon);
        if (latlon !== $widget.marker.getLatLng()) {
          $widget.marker.setLatLng(latlon);
          map.setView(latlon);
        }
      }
    },

    updateLoader: function ($widget, status) {
      $widget.find('.geomap-loader').attr('aria-hidden', status ? 'false' : 'true');
    },

    updateSuggestions: function ($widget, featureCollection) {
      var $list = $widget.find('.geolocation-suggestions-list');
      $list.empty();
      var features = featureCollection.features || [];
      features.forEach(function (feature) {
        var $item = $('<li>', {'class': 'geolocation-suggestions-list-item'});
        var $btn = $('<button>');
        $btn.data('feature', feature);
        $btn.on('click', this.onSuggestionClick.bind(this));
        $btn.text(feature.properties.label)
        $list.append($item.append($btn));
      }.bind(this));
    },

    onSuggestionClick: function (event) {
      var $suggestion = $(event.target);
      var feature = $suggestion.data('feature');
      // Update address input fields.
      var $widget = $suggestion.parents('.geomap-widget-wrapper');
      $widget.find('input[name$="][street]"]').val(feature.properties.name);
      $widget.find('input[name$="][zipcode]"]').val(feature.properties.postcode);
      $widget.find('input[name$="][city]"]').val(feature.properties.city);
      this.updateFeatureInput($widget, feature);
      event.preventDefault();
    },

    updateFeatureInput: function ($widget, feature) {
      $widget.find('input[name$="][feature]"]').val(JSON.stringify(feature));
    }

  };

})(jQuery, Drupal);
