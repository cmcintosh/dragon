<?php

namespace Drupal\dragon\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the payment type plugin annotation object.
 *
 * Plugin namespace: Plugin\GrapesJS\Block.
 *
 * @see plugin_api
 *
 * @Annotation
 */
 class GrapesJSBlock extends Plugin {

   /**
    * The plugin ID.
    *
    * @var string
    */
   public $id;

   /**
    * The GrapesJS Block label.
    *
    * @ingroup plugin_translatable
    *
    * @var \Drupal\Core\Annotation\Translation
    */
   public $label;

   /**
    * The category for this GrapesJS Block.
    *
    * @ingroup plugin_translatable
    *
    * @var \Drupal\Core\Annotation\Translation
    */
   public $category;
 }
