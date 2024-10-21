<?php

namespace Drupal\map_provider\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Map provider plugins.
 */
interface MapProviderInterface extends PluginInspectionInterface {

  /**
   * Get the url to used as tile provider.
   *
   * @return string
   *   The url to use as a tile provider.
   */
  public function getUrl();

  /**
   * Get the attribution to display on the map.
   *
   * @return string|array
   *   A string or array representing the link to the map provider.
   */
  public function getAttribution();

}
