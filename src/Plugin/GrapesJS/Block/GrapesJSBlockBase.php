<?php

namespace Drupal\dragon\Plugin\GrapesJS\Block;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dragon\Plugin\GrapesJS\Block\GrapesJSBlockInterface;

/**
* Provides the base GrapeJS Block type class.
*/
abstract class GrapesJSBlockBase extends PluginBase implements GrapesJSBlockInterface {

  /**
  * {@inheritdoc}
  */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
  * {@inheritdoc}
  */
  public function getAttributes() {
    return [
      'title' => $this->pluginDefinition('label')
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function getTemplate() {
    return '<div class="base-block"> </div>';
  }

  /**
  * {@inheritdoc}
  */
  public function getCategory() {
    return $this->pluginDefinition('category');
  }
}
