/**
 * @file
 * Geomap default widget custom behavior
 */


(function ($, Drupal) {

  Drupal.behaviors.geomapDefaultFormatter = {

    attach: function (context) {
      once('geomapDefaultFormatter', '.geomap-formatter-map', context)
        .on('map:afterInit', function (event, map) {
          var mapSettings = $(event.target).data('map');
          if (mapSettings.center) {
            var marker = new L.marker([
              mapSettings.center[0],
              mapSettings.center[1]
            ], {draggable: 'false'});
            map.addLayer(marker);
          }
        }.bind(this));
    }

  };

})(jQuery, Drupal);
