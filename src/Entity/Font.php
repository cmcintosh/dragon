<?php

namespace Drupal\dragon\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
* Defines a Font configuration entity class.
*
* @ConfigEntityType(
*  id = "font",
*  label = @Translation("Font"),
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
*    "type",
*    "file",
*    "url",
*    "eot",
*    "svg",
*    "ttf",
*    "woff",
*    "woff2",
*    "json"
*  }
* )
*/
class Font extends ConfigEntityBase {

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
  protected $type;

  /**
  * The filename for the font.
  */
  protected $file;

  /**
  * The public url for the web font.
  */
  protected $url;
}
