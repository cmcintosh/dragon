<?php
namespace Drupal\dragon\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Template add and edit forms.
 */
class TemplateForm extends EntityForm {

  /**
   * Constructs an ExampleForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);
    $form['#attached']['library'][] = 'dragon/dragon_backend_layout';

    $template = $this->entity;
    $entity_types = $this->getEntityTypes();
    $bundles = [];
    $input = $form_state->getUserInput();
    if (isset($input['entity_type'])) {
      $bundles = $this->getEntityBundles($input['entity_type']);
    }

    $form['#theme'] = 'dragon_backend_template';

    $form['details'] = [
      '#type' => 'details',
      '#title' => t('Details'),
      '#open' => TRUE,
    ];

    $form['details']['id'] = [
      '#type' => 'textfield',
      '#title' => t('Id'),
      '#required' => TRUE,
      '#default_value' => $template->get('id'),
      '#disabled' => !empty($template->get('id'))
    ];

    $form['details']['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#required' => TRUE,
      '#default_value' => $template->get('title')
    ];

    $form['details']['status'] = [
      '#type' => 'checkbox',
      '#title' => t('Enabled'),
      '#default_value' => $template->get('status')
    ];

    $form['details']['entity_type'] = [
      '#type' => 'select',
      '#title' => t('Entity Type'),
      '#default_value' => $template->get('entity_type'),
      '#options' => $entity_types,
      '#ajax' => [
        'event' => 'change',
        'method' => 'replace',
        'callback' => [$this, 'ajaxEntityTypeChanged'],
        'wrapper' => 'entity-bundle-wrapper'
      ]
    ];

    $form['details']['bundle'] = [
      '#type' => 'select',
      '#title' => t('Bundle'),
      '#default_value' => $template->get('bundle'),
      '#options' => $bundles,
      '#prefix' => '<div id="entity-bundle-wrapper">',
      '#suffix' => '</div>'
    ];

    $form['details']['default'] = [
      '#type' => 'checkbox',
      '#title' => t('Default'),
      '#default_value' => $template->get('default')
    ];

    $form['details']['base_template'] = [
      '#type' => 'select',
      '#title' => t('Base Template'),
      '#default_value' => $template->get('base_template'),
      '#options' => $this->getTemplates(),
    ];

    $form['conditions'] = [
      '#type' => 'details',
      '#title' => t('Conditions')
    ];

    $form['author'] = [
      '#type' => 'details',
      '#title' => t('Author Information'),
      '#open' => FALSE
    ];

    $form['author']['author'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => $template->get('author')
    ];

    $form['author']['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => $template->get('author_email')
    ];

    $form['author']['website'] = [
      '#type' => 'textfield',
      '#title' => t('Website'),
      '#default_value' => $template->get('author_website')
    ];

    $form['layout'] = [
      '#type' => 'hidden',
      '#default_value' => $template->get('layout')
    ];

    // Load our available Breakpoints, Fonts, and Colors
    $this->addBreakPoints($form, $form_state);
    $this->addFonts($form, $form_state);
    $this->addColors($form, $form_state);
    $this->addBuilder($form, $form_state);
    // You will need additional form elements for your custom properties.
    return $form;
  }

  /**
  * Returns form after changing the entity.
  */
  public function ajaxEntityTypeChanged(array &$form, FormStateInterface $form_state) {
    return $form['details']['bundle'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $template = $this->entity;

    $template->set('id', $form_state->getValue('id'));
    $template->set('status', $form_state->getValue('status'));
    $template->set('title', $form_state->getValue('title'));
    $template->set('entity_type', $form_state->getValue('entity_type'));
    $template->set('base_template', $form_state->getValue('base_template'));
    $template->set('layout', $form_state->getValue('layout'));
    $template->set('conditions', $form_state->getValue('conditions'));
    $template->set('author', $form_state->getValue('author'));
    $template->set('author_email', $form_state->getValue('author_email'));
    $template->set('author_website', $form_state->getValue('author_website'));

    $status = $template->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Example.', array(
        '%label' => $template->get('title'),
      )));
    }
    else {
      drupal_set_message($this->t('The %label Example was not saved.', array(
        '%label' => $template->get('title'),
      )));
    }

    $form_state->setRedirect('entity.template.collection');
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityQuery->get('template')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  /**
  * Helper function to return all installed entity types.
  */
  private function getEntityTypes() {
    $data = \Drupal::entityManager()->getDefinitions();
    $types = ['' => t('Select a Entity')];
    foreach($data as $type => $row) {
      $types[$type] = $row->getLabel();
    }
    return $types;
  }

  /**
  * Helper function to return all bundles for given entity.
  */
  private function getEntityBundles($entity_type) {
    $bundles = ['' => t('Select a Bundle')];
    $data = \Drupal::entityManager()->getBundleInfo($entity_type);

    foreach($data as $id => $bundle) {
      $bundles[$id] = $id;
    }
    return $bundles;
  }

  /**
  * Load all current templates.
  */
  private function getTemplates() {
    $entities = entity_load_multiple('template');

    if (count($entities) > 0) {
      $templates =['' => t('Select a Template')];
      foreach($entities as $delta => $entity) {
        $id = $entity->get('id');
        $templates[$id] = $entity->get('name');
      }
    }
    else {
      return [
        '' => t('No Available Templates')
      ];
    }
  }

  /**
  * Add all defined breakpoints for use in the form.
  */
  private function addBreakpoints(&$form, $form_state) {
    $entities = entity_load_multiple('breakpoint');
    $form['#attached']['drupalSettings']['dragon']['breakpoints'] = [];
    foreach($entities as $id => $breakpoint) {
      $form['#attached']['drupalSettings']['dragon']['breakpoints'][$id] = [
        'width' => $breakpoint->get('width'),
        'type' => $breakpoint->get('type'),
        'inner_gutters' => $breakpoint->get('inner_gutters'),
        'outer_gutters' => $breakpoint->get('outer_gutters'),
        'columns' => $breakpoint->get('columns'),
        'media_query' => $breakpoint->get('media_query')
      ];
    }
  }

  /**
  * Add all defined fonts for use in the form.
  */
  private function addFonts(&$form, $form_state) {
    $entities = entity_load_multiple('font');
    $form['#attached']['drupalSettings']['dragon']['fonts'] = [];
    foreach($entities as $id => $font) {
      $form['#attached']['drupalSettings']['dragon']['fonts'][$id] = [
        'id' => $font->id(),
      ];
    }
  }

  /**
  * Add all defined fonts for use in the form.
  */
  private function addColors(&$form, $form_state) {
    $entities = entity_load_multiple('color');
    $form['#attached']['drupalSettings']['dragon']['colors'] = [];
    foreach($entities as $id => $color) {
      $form['#attached']['drupalSettings']['dragon']['colors'][$id] = [
        'id' => $color->id(),
        'name' => $color->get('name'),
        'color' => $color->get('color'),
      ];
    }
  }

  /**
  * Add the various builder elements.
  * @TODO: Implement a plugin api system here to load components.
  * that can be used in the builder.
  */
  private function addBuilder(&$form, $form_state) {
    $form['#attached']['drupalSettings']['dragon']['builder']['fields'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['replaceFields'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['actionButtons'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['componentTemplates'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['typeUserDisabledAttrs'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['typeUserAttrs'] = [];
    $form['#attached']['drupalSettings']['dragon']['builder']['disabledAttrs'] = [];
  }

}
