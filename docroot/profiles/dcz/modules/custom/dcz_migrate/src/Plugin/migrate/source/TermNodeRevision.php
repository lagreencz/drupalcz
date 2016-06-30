<?php

/**
 * @file
 * Contains \Drupal\dcz_migrate\Plugin\migrate\source\TermNodeRevision.
 */

namespace Drupal\dcz_migrate\Plugin\migrate\source;

/**
 * Source returning tids from the term_node table for the non-current revision.
 *
 * @MigrateSource(
 *   id = "dcz_d6_term_node_revision"
 * )
 */
class TermNodeRevision extends TermNode {

  /**
   * {@inheritdoc}
   */
  const JOIN = 'tn.nid = n.nid AND tn.vid != n.vid';

}
