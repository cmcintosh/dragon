<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* @GrapesJSPlugin(
*   id = "gjs-plugin-export",
*   library = "dragon/grapesjs-export"
* )
*/
class Export extends GrapesJSPluginBase implements GrapesJSPluginInterface {

    /**
    * {@inheritdoc}
    */
    public function getLibrary() {
      return $this->pluginDefinition['library'];
    }

    /**
    * {@inheritdoc}
    */
    public function getOptions() {
      return [];
    }

}
