<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

/**
* Defines the interface for the GrapeJS Block.
* @see https://github.com/artf/grapesjs/wiki/Components
*/
interface GrapesJSPluginInterface {

  /**
  * Returns the library that contains the plugin.
  */
  public function getLibrary();

  /**
  * Returns any drupalSettings that need to be added for the plugin to work.
  */
  public function drupalSettings();

}
