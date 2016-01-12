<?php

/**
 * @file
 *  Contains Drupal\nf_schober\Form\SchoberSettingsForm
 */

namespace Drupal\nf_schober\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings class for the Schober newsletter.
 */
class SchoberSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'schober_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return array('schober.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('schober.settings');

    $form['post_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Post URL'),
      '#description' => t('Please input the url where to post the data, for example: <em>https://www.xcampaign.ch/dispatcher/service</em>'),
      '#default_value' => $config->get('post_url'),
      '#required' => TRUE,
    );
    $form['client_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Client code'),
      '#description' => t('Please input the client code to be used for subscribing to the newsletter.'),
      '#default_value' => $config->get('client_code'),
      '#required' => TRUE,
    );
    $form['ac'] = array(
      '#type' => 'textfield',
      '#title' => t('AC'),
      '#description' => t('Please input the value for the hidden <em>ac</em> field.'),
      '#default_value' => $config->get('ac'),
    );
    $form['double_optin'] = array(
      '#type' => 'checkbox',
      '#title' => t('Double optin'),
      '#default_value' => $config->get('double_optin'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // The current landing page is state, not configuration!
    $this->config('schober.settings')
      ->set('post_url', $form_state->getValue('post_url'))
      ->set('client_code', $form_state->getValue('client_code'))
      ->set('ac', $form_state->getValue('ac'))
      ->set('double_optin', $form_state->getValue('double_optin'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
