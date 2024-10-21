<?php

namespace Drupal\map_provider\Plugin\MapProvider;

use Drupal\map_provider\Plugin\MapProviderBase;

/**
 * Class YamlMapProvider.
 *
 * @package Drupal\map_provider\Plugin\MapProvider
 *
 * @MapProvider(
 *   id = "yaml_map_provider",
 *   label = @Translation("Yaml map provider"),
 *   deriver = "Drupal\map_provider\Plugin\Derivative\YamlMapProvider"
 * )
 */
class YamlMapProvider extends MapProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return $this->getPluginDefinition()['url'];
  }

  /**
   * Get the attribution to display on the map.
   *
   * @return string|array
   *   A string or array representing the link to the map provider.
   */
  public function getAttribution() {
    $definition = $this->getPluginDefinition();
    return $definition['attribution'] ?? '';
  }

}
