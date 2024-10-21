<?php

namespace Drupal\map_provider\Element;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Render\Element\RenderElement;

/**
 * Class Map.
 *
 * @package Drupal\map_provider\Element
 *
 * @RenderElement("map")
 */
class Map extends RenderElement {

  /**
   * Returns the element properties for this element.
   *
   * @return array
   *   An array of element properties. See
   *   \Drupal\Core\Render\ElementInfoManagerInterface::getInfo() for
   *   documentation of the standard properties of all elements, and the
   *   return value format.
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#tile_url' => '',
      '#attribution' => '',
      '#center' => [],
      '#zoom' => 13,
      '#attributes' => [],
      '#pre_render' => [
        [$class, 'preRenderMap'],
      ],
      '#attached' => [],
    ];
  }

  /**
   * Pre-render callback: builds a renderable array.
   *
   * @param array $element
   *   A renderable array.
   *
   * @return array
   *   A renderable array representing a map.
   */
  public static function preRenderMap(array $element) {
    $element['#attached'] = NestedArray::mergeDeep([
      'library' => [
        'map_provider/map',
      ],
    ], $element['#attached']);
    $element['#attributes'] = NestedArray::mergeDeep([
      'class' => ['leaflet-map'],
      'data-map' => [
        'center' => $element['#center'],
        'zoom' => $element['#zoom'],
      ],
      'data-tile' => $element['#tile_url'],
      'data-layer' => [
        'attribution' => $element['#attribution'],
      ],
    ], $element['#attributes']);

    $element['#attributes']['data-map'] = Json::encode($element['#attributes']['data-map']);
    $element['#attributes']['data-layer'] = Json::encode($element['#attributes']['data-layer']);

    $element['map'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => $element['#attributes'],
    ];
    return $element;
  }

}
