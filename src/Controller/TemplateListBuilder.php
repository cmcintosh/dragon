<?php
namespace Drupal\dragon\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Example.
 */
class TemplateListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('CSS Id');
    $header['title'] = $this->t('Layout');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['title'] = $entity->get('title');
    $row['status'] = $entity->get('status') ? t('Enabled') : t('Disabled');


    // You probably want a few more properties here...

    return $row + parent::buildRow($entity);
  }

}
