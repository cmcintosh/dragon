<?php

namespace Drupal\dragon\Plugin\GrapesJS\Block;

/**
* Defines the interface for the GrapeJS Block.
*/
interface GrapesJSBlockInterface {

  /**
  * Return the lable to display on the interface.
  */
  public function getLabel();

  /**
  * Returns the attributes for this block.
  */
  public function getAttributes();

  /**
  * Returns the content template for this block.
  */
  public function getTemplate();

  /**
  * Returns the category this block should be displayed under.
  */
  public function getCategory();
}
