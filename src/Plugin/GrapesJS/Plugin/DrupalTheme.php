<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* Used to generate the theme yaml elements that will be utilized.
* @GrapesJSPlugin(
*   id = "drupal-theme",
*   library = "",
*   weight = 100
* )
*/
class DrupalTheme extends GrapesJSPluginBase implements GrapesJSPluginInterface {

  /**
  * {@inheritdoc}
  */
  public function generate(&$templates) {

  }

}
