<?php

/**
 * @file
 *  Contains Drupal\nf_schober\Plugin\Newsletter\SchoberNewsletter
 */

namespace Drupal\nf_schober\Plugin\Newsletter;


use Drupal\Core\Config\Config;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\newsletter_field\Newsletter\NewsletterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The Schober newsletter plugin.
 *
 * @Newsletter(
 *   id = "shober_newsletter",
 *   label = @Translation("Schober newsletter"),
 * )
 */
class SchoberNewsletter extends NewsletterBase {

  /**
   * @var Config
   */
  protected $schober_config;

  /**
   * @var LanguageManagerInterface
   */
  protected $language_manager;

  /**
   * @var Request
   */
  protected $request;

  /**
   * Constructs a Schober newsletter plugin.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Config $schober_config, LanguageManagerInterface $language_manager, Request $request) {
    $this->schober_config = $schober_config;
    $this->language_manager = $language_manager;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('schober.settings'),
      $container->get('language_manager'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }


  /**
   * {@inheritdoc}
   */
  public function subscribe($mail, $list_id = '', array $additional_data = []) {
    // We will add the newsletter settings to the additional data to be
    // submitted.
    $additional_data['clientCode'] = $this->schober_config->get('client_code');
    $additional_data['ac'] = $this->schober_config->get('ac');
    $additional_data['doubleOptin'] = $this->schober_config->get('double_optin');

    // Add also some fixed parameters.
    $additional_data['xp_sendBackParams'] = 0;
    $additional_data['xp_redirectLP'] = 0;

    $additional_data['Sprache'] = strtoupper($this->language_manager->getCurrentLanguage()->getId());

    // @todo: we need to check how can we inject this here. For the moment, we
    // will just take the one from the 'account' variable in the URL query, but
    // we should find somehow a way to do this cleaner. If there is no 'account'
    // in the query, we will use the default_key_account_manager setting.
    $additional_data['Key_Account_Manager'] = $this->request->get('account', $this->schober_config->get('default_key_account_manager'));

    // The e-mail has to also be a field in the post.
    $additional_data['Email'] = $mail;

    // Everything is ready, we can post the data.
    $url = $this->schober_config->get('post_url');

    return $this->doPostRequest($url, $additional_data);
  }

  /**
   * Posts data to an url.
   *
   * @param string $url
   *  The URL to post to.
   *
   * @param array $post_data
   *  The data to post.
   */
  protected function doPostRequest($url, array $post_data) {
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

    curl_exec($curl);

    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($http_code == 200) {
      return TRUE;
    }
    return FALSE;
  }
}
