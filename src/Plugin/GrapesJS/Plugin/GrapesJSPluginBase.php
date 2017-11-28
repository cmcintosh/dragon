<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* Provides the base GrapeJS Block type class.
*/
abstract class GrapesJSPluginBase extends PluginBase implements GrapesJSPluginInterface {

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

    /**
    * {@inheritdoc}
    */
    public function drupalSettings() {
      return [];
    }

    /**
    * {@inheritdoc}
    */
    public function generate(&$html) {

    }
}
