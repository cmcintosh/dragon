<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* @GrapesJSPlugin(
*   id = "drupal-fields",
*   library = "dragon/drupalFields"
* )
*/
class DrupalFields extends GrapesJSPluginBase implements GrapesJSPluginInterface {

    /**
    * Return the DOM Element for this component.
    */
    public function getLibrary() {
      return $this->pluginDefinition['library'];
    }

    /**
    * Return preconfigured options for the plugin.
    */
    public function getOptions() {
      return [ ];
    }

    /**
    * Return all of the defined drupal fields.
    */
    public function drupalSettings() {
      $settings = [];
      $entities = [];
      try {
        foreach (\Drupal::routeMatch()->getParameters() as $param) {

          if ($param instanceof \Drupal\Core\Entity\EntityInterface) {
            $entity = $param;
            $entity_type = $entity->getEntityTypeId();
            $bundle = $entity->bundle();
            $bundle_fields = \Drupal::entityManager()->getFieldDefinitions($entity_type, $bundle);
            $fields = [];

            $settings[$entity_type] = [
              'label' => ucwords(str_replace('_', ' ', $entity_type))
            ];

            foreach($bundle_fields as $id => $field) {
              $view = $entity->get($id)->view();
              // ksm($view);
              $view['attributes']['data-field'] = $entity_type . '.' . $id;
              $fields[$id] = [
                'id' => $id,
                'label' => $field->getLabel(),
                'value' => render($view),
                'entity_type' => $entity_type
              ];
            }

            $settings[$entity_type][$bundle] = $fields;
          }
        }
      }
      catch(\Exception $e) {
        watchdog_exception('dragon_drupal_fields', $e);
      }
  
      return [
        'drupalFields' => $settings
      ];
    }

}
