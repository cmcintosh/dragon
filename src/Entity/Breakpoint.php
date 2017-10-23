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
*  id = "breakpoint",
*  label = @Translation("Breakpoint"),
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
*    "width",
*    "type",
*    "inner_gutters",
*    "outer_gutters",
*    "columns",
*    "media_query"
*  }
* )
*/
class Breakpoint extends ConfigEntityBase {

  protected $id;
  protected $width;
  protected $type;
  protected $inner_gutters;
  protected $outer_gutters;
  protected $columns;
  protected $media_query;

}
