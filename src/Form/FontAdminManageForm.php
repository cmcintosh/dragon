<?php

namespace Drupal\dragon\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dragon\Entity\Font;
use Drupal\dragon\Entity\FontPack;
use Drupal\Core\File\FileSystem;
use Drupal\file\Entity\File;

class FontAdminManageForm extends FormBase {

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'font_admin_manage';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('dragon.settings');

    $form['google_font_api'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#description' => t('Enter Google Font API Key.'),
      '#default_value' => $config->get('google_font_api')
    ];

    $form['detail']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save')
    ];

    return $form;
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm (array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = \Drupal::service('config.factory')
      ->getEditable('dragon.settings');
    $config->set('google_font_api', $values['google_font_api']);
    $config->save();
  }

}
