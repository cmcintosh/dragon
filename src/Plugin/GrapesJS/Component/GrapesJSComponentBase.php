<?php

namespace Drupal\dragon\Plugin\GrapesJS\Component;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dragon\Plugin\GrapesJS\Component\GrapesJSComponentInterface;

/**
* Provides the base GrapeJS Block type class.
*/
abstract class GrapesJSComponentBase extends PluginBase implements GrapesJSComponentInterface {

    /**
    * Return the DOM Element for this component.
    */
    public function getDomElement() {
      return 'picture';
    }

    /**
    * Return the Model for this component.
    *
    * In This Example:
    * <picture>
    *  <source srcset="smaller.jpg" media="(max-width: 768px)">
    *  <source srcset="default.jpg">
    *  <img srcset="default.jpg" alt="My default image">
    * </picture>
    */
    public function getModel() {
      return '';
    }

    /**
    * Return the view for this component.
    */
    public function getView() {
      return '';
    }

}
