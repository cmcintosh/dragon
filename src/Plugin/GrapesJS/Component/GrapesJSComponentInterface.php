<?php

namespace Drupal\dragon\Plugin\GrapesJS\Component;

/**
* Defines the interface for the GrapeJS Block.
* @see https://github.com/artf/grapesjs/wiki/Components
*/
interface GrapesJSComponentInterface {

  /**
  * Return the Dom Element.
  */
  public function getDomElement();

  /**
  * Returns the model for this component.
  */
  public function getModel();

  /**
  * Returns the view for this component.
  */
  public function getView();

}
