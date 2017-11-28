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

  /**
  *  Is used when processing the DOM to generate the theme.
  *  @return array(
  *    // Return files that should be added to the theme.
  *    'files' => [
  *      [
  *          'content' => '',
  *          'uri' => ''
  *       ],
  *     ],
  *     // Return preproces functions that should be included in the .theme file.
  *     'functions' => [
  *       $function_name => [
  *         'content' => ''
  *       ]
  *     ]
  )
  */
  public function generate(&$templates);

}
