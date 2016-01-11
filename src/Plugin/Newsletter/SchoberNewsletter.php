<?php

/**
 * @file
 *  Contains Drupal\nf_schober\Plugin\Newsletter\SchoberNewsletter
 */

namespace Drupal\nf_schober\Plugin\Newsletter;


use Drupal\newsletter_field\Newsletter\NewsletterBase;

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
   * {@inheritdoc}
   */
  public function subscribe($mail, array $additional_data = []) {
    ob_start();
    print_r($additional_data);
    $output = ob_get_clean();
    drupal_set_message($mail . ' was subscribed to the newsletter. Additional data: ' . $output);

  }
}
