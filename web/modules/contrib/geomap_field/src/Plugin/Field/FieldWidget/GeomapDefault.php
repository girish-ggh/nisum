<?php

namespace Drupal\geomap_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geomap_default' widget.
 *
 * @FieldWidget(
 *   id = "geomap_default",
 *   label = @Translation("Geomap"),
 *   field_types = {
 *     "geomap"
 *   }
 * )
 */
class GeomapDefault extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $default = [
      'geolocation_provider' => 'bano_geolocation_provider',
      'map_provider' => 'yaml_map_provider:osm',
    ];
    return $default + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    /** @var \Drupal\geolocation_provider\Plugin\GeolocationProviderPluginManager $geolocation_manager */
    $geolocation_manager = \Drupal::service('plugin.manager.geolocation_provider_plugin');
    $geo_options = [];
    foreach ($geolocation_manager->getDefinitions() as $id => $definition) {
      $geo_options[$id] = $definition['label'];
    }
    $elements['geolocation_provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Geolocation Provider'),
      '#options' => $geo_options,
      '#default_value' => $this->settings['geolocation_provider'],
    ];

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

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    /** @var \Drupal\geolocation_provider\Plugin\GeolocationProviderPluginManager $geolocation_manager */
    $geolocation_manager = \Drupal::service('plugin.manager.geolocation_provider_plugin');
    $geolocation_plugin_label = $geolocation_manager->getDefinition($this->getSetting('geolocation_provider'))['label'] ?? '';
    $summary[] = t('Geolocation provider: @provider', ['@provider' => $geolocation_plugin_label]);

    /** @var \Drupal\map_provider\Plugin\MapProviderManager $map_manager */
    $map_manager = \Drupal::service('plugin.manager.map_provider');
    $map_plugin_label = $map_manager->getDefinition($this->getSetting('map_provider'))['label'] ?? '';
    $summary[] = t('Map provider: @provider', ['@provider' => $map_plugin_label]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\geomap_field\Plugin\Field\FieldType\GeomapItem $item */
    $item = $items[$delta];
    /** @var \Drupal\map_provider\Plugin\MapProviderManager $map_manager */
    $map_manager = \Drupal::service('plugin.manager.map_provider');
    /** @var \Drupal\map_provider\Plugin\MapProviderInterface $map_provider */
    $map_provider = $map_manager->createInstance($this->getSetting('map_provider'));
    $geomap_form = [
      '#type' => 'details',
      '#open' => TRUE,
      'fields_wrapper' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['geomap-widget-wrapper']],
        'fields' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['geomap-widget-fields-wrapper']],
          'address_name' => [
            '#type' => 'textfield',
            '#title' => $this->t('Address name'),
            '#default_value' => $item->address_name ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'street' => [
            '#type' => 'textfield',
            '#title' => $this->t('Street'),
            '#default_value' => $item->street ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'zipcode' => [
            '#type' => 'textfield',
            '#title' => $this->t('Zip code'),
            '#default_value' => $item->zipcode ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'city' => [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#default_value' => $item->city ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'country' => [
            '#type' => 'textfield',
            '#title' => $this->t('Country'),
            '#default_value' => $item->country ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'additional' => [
            '#type' => 'textfield',
            '#title' => $this->t('Additional address information'),
            '#default_value' => $item->additional ?? NULL,
            '#required' => $element['#required'],
            '#size' => 25,
          ],
          'geolocation' => [
            '#type' => 'button',
            '#value' => $this->t('Try to geolocate the address'),
            '#attributes' => [
              'class' => ['geomap-geolocation-btn'],
            ],
          ],
          'latlon' => [
            '#type' => 'details',
            '#title' => $this->t('Latitude & longitude'),
            '#tree' => TRUE,
            '#open' => TRUE,
            '#attributes' => ['class' => ['latlon-wrapper']],
            'lat' => [
              '#type' => 'textfield',
              '#title' => $this->t('Latitude'),
              '#default_value' => $item->lat ?? NULL,
              '#required' => $element['#required'],
              '#size' => 15,
              '#attributes' => ['class' => ['geomap-field-lat']],
            ],
            'lon' => [
              '#type' => 'textfield',
              '#title' => $this->t('Longitude'),
              '#default_value' => $item->lon ?? NULL,
              '#required' => $element['#required'],
              '#size' => 15,
              '#attributes' => ['class' => ['geomap-field-lon']],
            ],
          ],
          'feature' => [
            '#type' => 'hidden',
            '#title' => $this->t('Feature'),
            '#default_value' => $item->feature ?? NULL,
          ],
        ],
        'map-wrapper' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['geomap-widget-map-wrapper']],
          'map' => [
            '#type' => 'map',
            '#tile_url' => $map_provider->getUrl(),
            '#attribution' => $map_provider->getAttribution(),
            '#center' => [
              $item->lat ?? 51.505,
              $item->lon ?? -0.09,
            ],
            '#zoom' => 13,
            '#attributes' => [
              'class' => ['geomap-widget-map'],
              'data-geolocation-plugin' => $this->getSetting('geolocation_provider'),
            ],
          ],
          'suggestions' => [
            '#type' => 'inline_template',
            '#template' => '<ul class="geolocation-suggestions-list"></ul>',
          ],
          'loader' => [
            '#type' => 'inline_template',
            '#template' => '<div class="geomap-loader" aria-hidden="true"></div>',
          ],
        ],
      ],
      '#attached' => [
        'library' => [
          'geomap_field/geomap_default_widget',
        ],
      ],
    ];
    return $element + $geomap_form;
  }

}
