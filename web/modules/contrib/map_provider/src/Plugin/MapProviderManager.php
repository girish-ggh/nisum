<?php

namespace Drupal\map_provider\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Map provider plugin manager.
 */
class MapProviderManager extends DefaultPluginManager {

  /**
   * Constructs a new MapProviderManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/MapProvider', $namespaces, $module_handler, 'Drupal\map_provider\Plugin\MapProviderInterface', 'Drupal\map_provider\Annotation\MapProvider');
    $this->alterInfo('map_provider_map_provider_info');
    $this->setCacheBackend($cache_backend, 'map_provider_map_provider_plugins');
  }

}
