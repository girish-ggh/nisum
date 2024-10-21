/**
 * @file
 * Map Provider js behavior
 */


(function ($, Drupal) {

  Drupal.behaviors.mapProviderLeafletMap = {

    excludedData: [
      'drupalSelector',
      'jqueryOnceMapProviderLeafletMap'
    ],

    attach: function (context) {
      once('mapProviderLeafletMap', '.leaflet-map', context).forEach(function (elt) {
        this._createMap(elt);
      }.bind(this));
    },

    /**
     * Create a Leaflet map.
     *
     * @event map:beforeInit
     *   This event is triggered before the call to L.map().
     *   Allow other js file to alter the settings.
     *
     * @param elt
     *   The map target element.
     * @private
     */
    _createMap: function (elt) {
      var $elt = $(elt);
      var mapSettings = $elt.data('map');
      // Allow other js file to alter the settings before map init.
      $elt.trigger('map:beforeInit', mapSettings);
      // Init Leaflet.
      try {
        $elt.leafletMap = L.map($elt[0], mapSettings);
      }
      catch (e) {
        Drupal.throwError(e);
      }
      if ($elt.leafletMap) {
        var event = $.Event('map:afterInit');
        var layerSettings = $elt.data('layer');
        $elt.trigger(event, [$elt.leafletMap, layerSettings]);
        if (!event.isDefaultPrevented()) {
          L.tileLayer($elt.data('tile'), layerSettings).addTo($elt.leafletMap);
        }
      }
    },

  };

})(jQuery, Drupal);
