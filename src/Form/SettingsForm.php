<?php

namespace Drupal\tweet_node\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'tweet_node.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('tweet_node.settings');
    $form['twitter_account_holder'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('Site-wided twitter account holder'),
      '#description' => $this->t('Select a user who is configured to tweet on the site-wide Twitter account.'),
      '#default_value' => \Drupal\user\Entity\User::load($config->get('twitter_account_holder')),
    ];
    $form['default_tweet_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default tweet body'),
      '#description' => $this->t('Default value of the &quot;Tweet body&quot; field.'),
      '#default_value' => $config->get('default_tweet_body'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('tweet_node.settings')
      ->set('default_tweet_body', $form_state->getValue('default_tweet_body'))
      ->set('twitter_account_holder', $form_state->getValue('twitter_account_holder'))
      ->save();
  }

}
