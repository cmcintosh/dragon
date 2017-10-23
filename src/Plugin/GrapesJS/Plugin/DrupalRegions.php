<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* @GrapesJSPlugin(
*   id = "drupal-regions",
*   library = "dragon/drupalRegions"
* )
*/
class DrupalRegions extends GrapesJSPluginBase implements GrapesJSPluginInterface {

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

      // We need to get this theme's defined regions.
      $regions = [];
      $theme = \Drupal::config('system.theme')->get('default');
      $uri = drupal_get_path('theme', $theme) .  "/{$theme}.info.yml";
      $theme_info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($uri));

      return [
        'regions' => $theme_info['regions']
      ];
    }

}
