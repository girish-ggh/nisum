<?php

namespace Drupal\map_provider\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Map provider item annotation object.
 *
 * @see \Drupal\map_provider\Plugin\MapProviderManager
 * @see plugin_api
 *
 * @Annotation
 */
class MapProvider extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
