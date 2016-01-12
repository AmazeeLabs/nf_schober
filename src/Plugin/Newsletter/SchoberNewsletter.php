<?php

/**
 * @file
 *  Contains Drupal\nf_schober\Plugin\Newsletter\SchoberNewsletter
 */

namespace Drupal\nf_schober\Plugin\Newsletter;


use Drupal\Core\Config\Config;
use Drupal\newsletter_field\Newsletter\NewsletterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public function __construct(Config $schober_config) {
    parent::__construct();
    $this->schober_config = $schober_config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')->get('schober.settings')
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

    // @todo: we need to check how can we inject this here.
    $additional_data['Key_Account_Manager'] = 'PSP';

    // The e-mail has to also be a field in the post.
    $additional_data['Email'] = $mail;

    // Everything is ready, we can post the data.
    $url = $this->schober_config->get('post_url');

    ob_start();
    print_r($additional_data);
    $output = ob_get_clean();
    drupal_set_message($mail . ' was subscribed to the newsletter list ' . $list_id . '. Additional data: ' . $output);

  }
}
