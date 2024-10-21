<?php

namespace Drupal\geomap_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'geomap' field type.
 *
 * @FieldType(
 *   id = "geomap",
 *   label = @Translation("Geomap"),
 *   description = @Translation("Create and store geomap values."),
 *   default_widget = "geomap_default",
 *   default_formatter = "geomap_default"
 * )
 */
class GeomapItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    $default = [
      //      'max_length' => 255,
      //      'is_ascii' => FALSE,
      //      'case_sensitive' => FALSE,
    ];
    return $default + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['address_name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Name'))
      ->setRequired(TRUE);
    $properties['street'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Street'))
      ->setRequired(TRUE);
    $properties['zipcode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Zip code'))
      ->setRequired(TRUE);
    $properties['city'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('City'))
      ->setRequired(TRUE);
    $properties['country'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Country'))
      ->setRequired(TRUE);
    $properties['additional'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Additional'))
      ->setRequired(TRUE);
    $properties['lat'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Latitude'))
      ->setRequired(TRUE);
    $properties['lon'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Longitude'))
      ->setRequired(TRUE);
    $properties['feature'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Feature'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'address_name' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'street' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'zipcode' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'city' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'country' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'additional' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'lat' => [
          'type' => 'numeric',
          'precision' => 18,
          'scale' => 12,
        ],
        'lon' => [
          'type' => 'numeric',
          'precision' => 18,
          'scale' => 12,
        ],
        'feature' => [
          'type' => 'text',
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['street'] = '42 lorem ipsum, 424242, Dolor';
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->street) && empty($this->zipcode) && empty($this->city) && empty($this->country) && empty($this->lat) && empty($this->lon);
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    parent::setValue($values, $notify);
    $properties = array_keys($this->getProperties());
    // Flatten values tree.
    array_walk_recursive($values, function ($value, $key) use ($properties) {
      if (in_array($key, $properties)) {
        $this->{$key} = $value;
      }
    });
  }

}
