<?php

namespace Drupal\dragon\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
* Defines a FontPack configuration entity class.
*
* @ConfigEntityType(
*  id = "font_pack",
*  label = @Translation("Font Pack"),
*  handers = {
*    "access" = "Drupal\dragon\Access\FontAccessControlHandler",
*  },
*  admin_permission = "administer dragon_fonts",
*  entity_keys = {
*    "id" = "id",
*    "status" = "status",
*  },
*  config_export = {
*    "id",
*    "name",
*    "families",
*    "ff_smoothing",
*    "webkit_smoothing"
*  }
* )
*/
class FontPack extends ConfigEntityBase {

  /**
  * The id of the font
  */
  protected $id;

  /**
  * The Font Family name.
  */
  protected $name;

  /**
  * The Type of font this is, either upload or webfont.
  */
  protected $families;

  /**
  * The filename for the font.
  */
  protected $ff_smoothing;

  /**
  * The public url for the web font.
  */
  protected $webkit_smoothing;
}
