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
    $twitterAccountHolderUid = $config->get('twitter_account_holder');
    $twitterAccountHolderUser = NULL;
    if ($twitterAccountHolderUid) {
      $twitterAccountHolderUser = \Drupal\user\Entity\User::load($twitterAccountHolderUid);
    }

    $form['twitter_account_holder'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('Site-wided twitter account holder'),
      '#description' => $this->t('Select a user who is configured to tweet on the site-wide Twitter account.'),
      '#default_value' => $twitterAccountHolderUser,
    ];
    $form['default_tweet_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default tweet body'),
      '#description' => $this->t('Default value of the &quot;Tweet body&quot; field.'),
      '#default_value' => $config->get('default_tweet_body'),
    ];

    // Only upon initial load of the form (not on submit), warn if configured twitter user
    // does not actually have a twitter account configured.
    if (empty($form_state->getUserInput()) && !$form_state->isRebuilding() && $twitterAccountHolderUid) {
      // Get all the Twitter accounts associated with the user account.
      $user_manager = \Drupal::service('social_post.user_manager');
      $accounts = $user_manager->getAccounts('social_post_twitter', $twitterAccountHolderUid);

      // If no twitter account linked, warn.
      if (empty($accounts)) {
        \Drupal::messenger()->addWarning(t('No Twitter account is configured for the specified Twitter Account Holder (%username). Please <a href="/user/@uid/edit">edit that user</a> to configure Twitter account access.',
          [
            '%username' => $twitterAccountHolderUser->getAccountName(),
            '@uid' => $twitterAccountHolderUid
          ]
        ));
      }
    }

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
