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
*  id = "template",
*  label = @Translation("Template"),
*  handlers = {
*     "list_builder" = "Drupal\dragon\Controller\TemplateListBuilder",
*     "form" = {
*       "default" = "Drupal\dragon\Form\TemplateForm",
*       "add"     = "Drupal\dragon\Form\TemplateForm",
*       "edit"     = "Drupal\dragon\Form\TemplateForm",
*       "delete" = "Drupal\dragon\Form\TemplateDeleteForm"
*     }
*   },
*  admin_permission = "administer dragon_templates",
*  entity_keys = {
*    "id" = "id",
*    "status" = "status",
*  },
*  config_export = {
*    "id",
*    "template",
*    "theme",
*    "status",
*    "variant",
*    "layout",
*    "conditions",
*    "author",
*    "author_email",
*    "author_website",
*  },
*  links = {
*    "edit-form" = "/admin/dragon/templates/{template}/edit",
*    "delete-form" = "/admin/dragon/templates/{template}/delete"
*  }
* )
*/
class Template extends ConfigEntityBase {
  public $id;
  public $status;
  public $theme;
  public $title;
  public $variant;
  public $variant_status;
  public $entity_type;
  public $bundle;
  public $default;
  public $base_template;
  public $layout;
  public $conditions;
  public $author;
  public $author_email;
  public $author_website;
}
