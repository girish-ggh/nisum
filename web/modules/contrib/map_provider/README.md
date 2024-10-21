# Map provider

Map provider manager. Allow developers to use and define multiple map providers using the Drupal 8 Plugin API.
Also include a render element to display a map provider using Leaflet.

## Installation

This module require the leaflet js library.

Make sure to install leaflet in the libraries folder. Using composer with a custom repository:

```json
{
    "type": "package",
    "package": {
        "name": "custom/leaflet",
        "version": "v1.3.4",
        "type": "drupal-library",
        "dist": {
            "url": "https://github.com/Leaflet/Leaflet/archive/v1.3.4.zip",
            "type": "zip"
        }
    }
}
```

## Define a map provider

There are 2 ways to define a map provider.

The YAML way:

```yaml
# your_module.map.provider.yml
osm:
  id: osm
  label: OSM
  url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'

```

The annotated way:

```php
<?php

namespace Drupal\your_module\Plugin\MapProvider;

use \Drupal\map_provider\Plugin\MapProviderBase;

/**
 * Class OSM.
 * 
 * @MapProvider(
 *   id = "osm",
 *   label = @Translation("OSM")
 * )
 */
class OSM extends MapProviderBase {

  public function getUrl(){
    return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  }

  public function getAttribution(){
    return '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
  }

}
```

## Use the render element to display a map

The module define a render element to display a map:

```php
<?php
$build['map'] = [
  '#type' => 'map',
  '#tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  '#attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  '#center' => [51.505, -0.09],
  '#zoom' => 13,
];
```

You can customize the map using `#attached` and `#attributes` like:

```php
<?php
$build['map'] = [
  '#type' => 'map',
  '#tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  '#attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  '#center' => [51.505, -0.09],
  '#zoom' => 13,
  '#attributes' => [
    // Make sure to set the map height. But not as below...
    'style' => 'height: 500px; width: 500px;',
    // Add custom class used by the custom js behavior.
    'class' => ['map-override'],
  ],
  '#attached' => [
    // Add your custom js behavior here.
    'library' => ['your_module/map-override'],
  ],
];
```

## How to add custom js behavior

First, make sure to set the weight of your js file:

```yaml
my-library:
  js:
    # Ensure loading your file before the map.js.
    map-override.js: {weight: -1}
```

And listen for events that meet your needs:

```js
//map-override.js

Drupal.behaviors.myBehavior = {
  attach: function() {
    $('.map-override').once()
      .on('map:beforeInit', function(event, settings) {
        // Listen to the beforeInit event to alter the map settings.
        settings.zoomControl = false;
      })
      .on('map:afterInit', function(event, map, settings) {
        // Listen to the afterInit event to alter the layer settings, create markers or anything else.
        settings.maxZoom = 18;
        
        var marker = new L.marker([45.1667, 5.7167], {
          draggable: 'true'
        });
        map.addLayer(marker);
        
        // Use prevent default if you plan to use a leaflet plugin that define a new tileLayer constructor.
        event.preventDefault();
        // Example with the colorFilter plugin
        settings.filter = [];
        L.tileLayer.colorFilter($(this).data('tile'), settings).addTo(map);
      });
  }
};

```
