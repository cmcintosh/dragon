<?php

namespace Drupal\dragon\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dragon\Entity\Breakpoint;

class BreakpointAdminManageForm extends FormBase {

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
      return 'breakpoint_admin_manage';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'dragon/font-awesome';
    $form['#attached']['library'][] = 'dragon/ruler';
    $form['#attached']['library'][] = 'dragon/dragon_breakpoints';

    $form['breakpoints'] = [
      '#type' => 'detail',
      '#title' => t('Breakpoints'),
      '#tree' => TRUE,
      '#open' => TRUE,
    ];

    $entities = entity_load_multiple('breakpoint');
    foreach($entities as $breakpoint) {
      $form['breakpoints'][] = [
        '#theme' => 'dragon_breakpoint_form',
        'original_id' => [
          '#type' => 'value',
          '#value' => $breakpoint->get('id')
        ],
        'id' => [
          '#type' => 'textfield',
          '#title' => t('Variable: $wem-bp-'),
          '#default_value' => $breakpoint->get('id')
        ],
        'width' => [
          '#type' => 'textfield',
          '#title' => t('Width'),
          '#attributes' => [ 'class' => [ 'container-width' ] ],
          '#default_value' => $breakpoint->get('width'),
          '#size' => 10
        ],
        'type' => [
          '#type' => 'select',
          '#title' => t('Type'),
          '#options' => [
            'fixed' => t('Fixed'),
            'fluid' => t('Fluid')
          ],
          '#attributes' => [ 'class' => [ 'container-type' ] ],
          '#default_value' => $breakpoint->get('type')
        ],
        'columns' => [
          '#type' => 'number',
          '#title' => t('Columns'),
          '#attributes' => [ 'class' => [ 'breakpoint-columns' ] ],
          '#default_value' => $breakpoint->get('columns'),
        ],
        'inner_gutters' => [
          '#type' => 'textfield',
          '#title' => t('Inner Gutters'),
          '#attributes' => [ 'class' => [ 'breakpoint-inner_gutters' ] ],
          '#default_value' => $breakpoint->get('inner_gutters'),
          '#size' => 10
        ],
        'outer_gutters' => [
          '#type' => 'textfield',
          '#title' => t('Outer Gutters'),
          '#attributes' => [ 'class' => [ 'breakpoint-outer_gutters' ] ],
          '#default_value' => $breakpoint->get('outer_gutters'),
          '#size' => 10
        ],
        'media_query' => [
          '#type' => 'textarea',
          '#title' => t('Advanced Media Query'),
          '#default_value' => $breakpoint->get('media_query')
        ]
      ];
    }

    $form['breakpoints'][] = [
      '#theme' => 'dragon_breakpoint_form',
      'original_id' => [
        '#type' => 'value',
        '#value' => 0
      ],
      'id' => [
        '#type' => 'textfield',
        '#title' => t('Variable: $wem-bp-'),
      ],
      'width' => [
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#attributes' => [ 'class' => [ 'container-width' ] ],
        '#default_value' => 1024,
        '#size' => 10
      ],
      'type' => [
        '#type' => 'select',
        '#title' => t('Type'),
        '#options' => [
          'fixed' => t('Fixed'),
          'fluid' => t('Fluid')
        ],
        '#attributes' => [ 'class' => [ 'container-type' ] ],
        '#default_value' => 'fixed'
      ],
      'columns' => [
        '#type' => 'number',
        '#title' => t('Columns'),
        '#attributes' => [ 'class' => [ 'breakpoint-columns' ] ],
        '#default_value' => 12,
      ],
      'inner_gutters' => [
        '#type' => 'textfield',
        '#title' => t('Inner Gutters'),
        '#attributes' => [ 'class' => [ 'breakpoint-inner_gutters' ] ],
        '#default_value' => 5,
        '#size' => 10
      ],
      'outer_gutters' => [
        '#type' => 'textfield',
        '#title' => t('Outer Gutters'),
        '#attributes' => [ 'class' => [ 'breakpoint-outer_gutters' ] ],
        '#default_value' => 10,
        '#size' => 10
      ],
      'media_query' => [
        '#type' => 'textarea',
        '#title' => t('Advanced Media Query'),
      ]
    ];

    $form['submit'] = [
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

    foreach($values['breakpoints'] as $breakpoint) {
      if ($entity = Breakpoint::load($breakpoint['original_id'])) {
        $entity->delete();
        $entity = Breakpoint::create([
          'id' => $breakpoint['id'],
          'width' => $breakpoint['width'],
          'type' => $breakpoint['type'],
          'columns' => $breakpoint['columns'],
          'inner_gutters' => $breakpoint['inner_gutters'],
          'outer_gutters' => $breakpoint['outer_gutters'],
          'media_query' => $breakpoint['media_query'],
        ]);
        $entity->save();
      }
      else if ( !empty($breakpoint['id']) ) {
        $entity = Breakpoint::create([
          'id' => $breakpoint['id'],
          'width' => $breakpoint['width'],
          'type' => $breakpoint['type'],
          'columns' => $breakpoint['columns'],
          'inner_gutters' => $breakpoint['inner_gutters'],
          'outer_gutters' => $breakpoint['outer_gutters'],
          'media_query' => $breakpoint['media_query'],
        ]);
        $entity->save();
      }
    }

  }

}
