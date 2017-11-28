<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* Used to generate the page template files for the new theme.
* @GrapesJSPlugin(
*   id = "drupal-pages",
*   library = "",
*   weight = 100
* )
*/
class DrupalPages extends GrapesJSPluginBase implements GrapesJSPluginInterface {

  /**
  * {@inheritdoc}
  */
  public function generate(&$templates) {

  }

}
