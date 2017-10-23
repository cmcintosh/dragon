<?php

namespace Drupal\dragon\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dragon\Entity\Color;

class ColorAdminManageForm extends FormBase {

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
      return 'color_admin_manage';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'dragon/dragon_color';

    $form['detail'] = [
      '#type' => 'fieldset',
      '#title' => t('Colors')
    ];

    $form['detail']['colors'] = [
      '#type' => 'table',
      '#rows' => [],
      '#attributes' => [
        'id' => 'color-list',
      ],
    ];

    $entities = entity_load_multiple('color');
    foreach($entities as $color) {
      $form['detail']['colors'][] = [
        'weight' => [
          '#attributes' => ['class' => 'weight'],
          '#default_value' => $color->get('weight'),
        ],
        'original_id' => [
          '#type' => 'value',
          '#value' => $color->get('id'),
        ],
        'name' => [
          '#type' => 'textfield',
          '#placeholder' => t('Name'),
          '#size' => 15,
          '#default_value' => $color->get('name'),
        ],
        'id' => [
          '#type' => 'textfield',
          '#title' => '$wem-color-',
          '#placeholder' => t('Variable'),
          '#size' => 15,
          '#default_value' => $color->get('id'),
        ],
        'color' => [
          '#type' => 'color',
          '#placeholder' => t('Color code'),
          '#size' => 8,
          '#attributes' => [ 'class' => [ 'spectrum' ] ],
          '#default_value' => $color->get('color'),
        ],
        'delete' => [
          '#type' => 'button',
          '#value' => t('Delete')
        ]
      ];

    }

    $form['detail']['colors'][] = [
      'weight' => [
        '#attributes' => ['class' => 'weight'],
        '#default_value' => 0,
      ],
      'original_id' => [
        '#type' => 'value',
        '#value' => 0,
      ],
      'name' => [
        '#type' => 'textfield',
        '#placeholder' => t('Name'),
        '#size' => 15,
      ],
      'id' => [
        '#type' => 'textfield',
        '#title' => '$wem-color-',
        '#placeholder' => t('Variable'),
        '#size' => 15,
      ],
      'color' => [
        '#type' => 'color',
        '#placeholder' => t('Color code'),
        '#size' => 8,
        '#attributes' => [ 'class' => [ 'spectrum' ] ]
      ],
      'delete' => [
        '#type' => 'button',
        '#value' => t('Delete')
      ]
    ];

    $settings = array(
      'action' => 'order',
      'relationship' => 'sibling',
      'group' => 'weight',
    );

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

    foreach($values['colors'] as $color) {

      if ($entity = Color::load($color['original_id'])) {
          $entity->delete();
          $entity = Color::create([
            'id'    => $color['id'],
            'name'  => $color['name'],
            'color' => $color['color'],
            'weight' => $color['weight']
          ]);
          $entity->save();
      }
      else if (!empty($color['id'])) {
        $entity = Color::create([
          'id'    => $color['id'],
          'name'  => $color['name'],
          'color' => $color['color'],
          'weight' => $color['weight']
        ]);
        $entity->save();
      }
    }
  }

}
