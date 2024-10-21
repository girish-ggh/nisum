<?php

namespace Drupal\map_provider\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\map_provider\YamlMapProviderManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class YamlMapProvider.
 *
 * @package Drupal\map_provider\Plugin\Derivative
 */
class YamlMapProvider extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The yaml map provider plugin manager.
   *
   * @var \Drupal\map_provider\YamlMapProviderManager
   */
  private $yamlMapProviderManager;

  /**
   * YamlMapProvider deriver constructor.
   *
   * @param \Drupal\map_provider\YamlMapProviderManager $yaml_map_provider_manager
   *   The yaml map provider plugin manager.
   */
  public function __construct(YamlMapProviderManager $yaml_map_provider_manager) {
    $this->yamlMapProviderManager = $yaml_map_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('plugin.manager.yaml_map_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $definitions = $this->yamlMapProviderManager->getDefinitions();
    foreach ($definitions as $definition) {
      $this->derivatives[$definition['id']] = array_merge($base_plugin_definition, $definition);
    }
    return $this->derivatives;
  }

}
