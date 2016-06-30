<?php

/**
 * @file
 * Contains \Drupal\dcz_migrate\Plugin\migrate\source\TermNode.
 */

namespace Drupal\dcz_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Source returning tids from the term_node table for the current revision.
 *
 * @MigrateSource(
 *   id = "dcz_d6_term_node",
 *   source_provider = "taxonomy"
 * )
 */
class TermNode extends DrupalSqlBase {

  /**
   * The join options between the node and the term node table.
   */
  const JOIN = 'tn.vid = n.vid';

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('term_node', 'tn')
      ->distinct()
      ->fields('tn', array('nid', 'vid'))
      ->fields('n', array('type'));
    // Because this is an inner join it enforces the current revision.
    // Zakomentovano, protoze nemusime spojovat taxonomicky slovnik?
    // $query->innerJoin('term_data', 'td', 'td.tid = tn.tid AND td.vid = :vid', array(':vid' => $this->configuration['vid']));
    $query->innerJoin('node', 'n', static::JOIN);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'nid' => $this->t('The node ID.'),
      'vid' => $this->t('The node revision ID.'),
      'tid' => $this->t('The term ID.'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    print "term_node:nid:" . $row->getSourceProperty('nid'). "\n";

    // Select the terms belonging to the revision selected.
    $query = $this->select('term_node', 'tn')
      ->fields('tn', array('tid'))
      ->condition('n.nid', $row->getSourceProperty('nid'));
    $query->join('node', 'n', static::JOIN);
    // Zakomentovano, protoze nemusime spojovat taxonomicky slovnik?
    // $query->innerJoin('term_data', 'td', 'td.tid = tn.tid AND td.vid = :vid', array(':vid' => $this->configuration['vid']));
    $row->setSourceProperty('tids', $query->execute()->fetchCol());
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['vid']['type'] = 'integer';
    $ids['vid']['alias'] = 'tn';
    return $ids;
  }

}
