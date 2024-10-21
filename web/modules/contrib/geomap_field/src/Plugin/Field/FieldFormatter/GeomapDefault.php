<?php

namespace Drupal\geomap_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geomap_default' formatter.
 *
 * @FieldFormatter(
 *   id = "geomap_default",
 *   label = @Translation("Geomap"),
 *   field_types = {
 *     "geomap"
 *   }
 * )
 */
class GeomapDefault extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $default = [
      'map_provider' => 'yaml_map_provider:osm',
      'height' => '350px',
      'width' => '350px',
    ];
    return $default + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    /** @var \Drupal\map_provider\Plugin\MapProviderManager $map_manager */
    $map_manager = \Drupal::service('plugin.manager.map_provider');
    $map_options = [];
    foreach ($map_manager->getDefinitions() as $id => $definition) {
      $map_options[$id] = $definition['label'];
    }
    $elements['map_provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Map Provider'),
      '#options' => $map_options,
      '#default_value' => $this->settings['map_provider'],
    ];

    $elements['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#description' => $this->t('The map height is required by Leaflet, make sure to set the height using this field or a custom css.'),
      '#default_value' => $this->settings['height'],
    ];

    $elements['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $this->settings['width'],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    /** @var \Drupal\map_provider\Plugin\MapProviderManager $map_manager */
    $map_manager = \Drupal::service('plugin.manager.map_provider');
    $map_plugin_label = $map_manager->getDefinition($this->getSetting('map_provider'))['label'] ?? '';
    $summary[] = $this->t('Map provider: @provider', ['@provider' => $map_plugin_label]);

    $summary[] = $this->t('Map height: @height', ['@height' => $this->getSetting('height')]);
    $summary[] = $this->t('Map width: @width', ['@width' => $this->getSetting('width')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    /** @var \Drupal\map_provider\Plugin\MapProviderManager $map_manager */
    $map_manager = \Drupal::service('plugin.manager.map_provider');

    foreach ($items as $delta => $item) {
      /** @var \Drupal\map_provider\Plugin\MapProviderInterface $map_provider */
      $plugin = $this->getSetting('map_provider');
      if (empty($plugin)) {
        $plugin = 'yaml_map_provider:osm';
      }
      $map_provider = $map_manager->createInstance($plugin);
      $elements[$delta] = [
        '#type' => 'map',
        '#tile_url' => $map_provider->getUrl(),
        '#attribution' => $map_provider->getAttribution(),
        '#center' => [
          $item->lat ?? 51.505,
          $item->lon ?? -0.09,
        ],
        '#zoom' => 13,
        '#attributes' => [
          'class' => ['geomap-formatter-map'],
        ],
        '#attached' => [
          'library' => ['geomap_field/geomap_default_formatter'],
        ],
      ];
      if ($height = $this->getSetting('height')) {
        $style = "height:$height;";
        if ($width = $this->getSetting('width')) {
          $style .= "width:$width";
        }
        $elements[$delta]['#attributes']['style'] = $style;
      }
    }

    return $elements;
  }

}
