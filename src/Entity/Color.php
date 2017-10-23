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
*  id = "color",
*  label = @Translation("Color"),
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
*    "color",
*    "weight",
*  }
* )
*/
class Color extends ConfigEntityBase {

  /**
  * The id of the font
  */
  protected $id;

  /**
  * The Font Family name.
  */
  protected $name;

  /**
  * The color code.
  */
  protected $color;

  /**
  * The weight.
  */
  protected $weight;
}
