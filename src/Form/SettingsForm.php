<?php

namespace Drupal\drupal_insight_backstage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\Core\State\StateInterface;

/**
 * Configure drupal_insight_backstage settings for this site.
 */
class SettingsForm extends FormBase {

  /**
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'drupal_insight_backstage_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret Key'),
      '#description' => $this->t('Secret Key from backstage.io'),
      '#required' => TRUE,
      '#default_value' => $this->state->get('drupal_insight_backstage.secret_key', ''),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state
      ->set('drupal_insight_backstage.secret_key', $form_state->getValue('api_key'));
      $this->messenger()->addStatus($this->t('Settings saved successfully.'));
  }

}
