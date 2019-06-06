<?php

namespace Drupal\qed_42\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;

/**
 * Provides a block with a qed42 test.
 * 
 * @Block(
 *   id = "qed_42_block",
 *   admin_label = @Translation("Qed42 Block"),
 * )
 */
class QedBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $node_id = \Drupal::routeMatch()->getParameter('node')->id();
        if (!empty($node_id) && is_numeric($node_id)) {
            $node_data = \Drupal::entityTypeManager()->getStorage('node')->load($node_id);
            $author_id = $node_data->get('uid')->getValue()['0']['target_id'];
            if (!empty($node_data->get('field_qed42_category')->getValue())) {
                $category = $node_data->get('field_qed42_category')->getValue()['0']['target_id'];
            }
        }
        $connection = \Drupal::database();
          // Display same category by same author first
        if (!empty($author_id) && !empty($category)) {
            $query = $connection->select('node_field_data', 'n');
            $query->leftjoin('node__field_qed42_category', 'fqc', 'fqc.entity_id = n.nid');
            $query->fields('n', ['nid', 'title', 'uid']);
            $query->condition('n.type', 'article');
            $query->condition('n.status', 1);
            //Not show current node
            if (!empty($node_id) && is_numeric($node_id)) {
                $query->condition('n.nid', $node_id, '!=');
            }
            $query->condition('n.uid', $author_id, "=");
            $query->condition('fqc.field_qed42_category_target_id', $category, "=");
            $query->orderBy('n.title', 'ASC');
            $query->orderBy('n.created', 'DESC');
            $result1 = $query->execute()->fetchAll();
        }
        // Display same category by different author
        if (!empty($author_id) && !empty($category)) {
            $query = $connection->select('node_field_data', 'n');
            $query->leftjoin('node__field_qed42_category', 'fqc', 'fqc.entity_id = n.nid');
            $query->fields('n', ['nid', 'title', 'uid']);
            $query->condition('n.type', 'article');
            $query->condition('n.status', 1);
            //Not show current node
            if (!empty($node_id) && is_numeric($node_id)) {
                $query->condition('n.nid', $node_id, '!=');
            }
            $query->condition('n.uid', $author_id, "!=");
            $query->condition('fqc.field_qed42_category_target_id', $category, "=");
            $query->orderBy('n.title', 'ASC');
            $query->orderBy('n.created', 'DESC');
            $result2 = $query->execute()->fetchAll();
        }

        // Display different category by same author
        if (!empty($author_id) && !empty($category)) {
            $query = $connection->select('node_field_data', 'n');
            $query->leftjoin('node__field_qed42_category', 'fqc', 'fqc.entity_id = n.nid');
            $query->fields('n', ['nid', 'title', 'uid']);
            $query->condition('n.type', 'article');
            $query->condition('n.status', 1);
            //Not show current node
            if (!empty($node_id) && is_numeric($node_id)) {
                $query->condition('n.nid', $node_id, '!=');
            }
            $query->condition('n.uid', $author_id, "=");
            $query->condition('fqc.field_qed42_category_target_id', $category, "!=");
            $query->orderBy('n.title', 'ASC');
            $query->orderBy('n.created', 'DESC');
            $result3 = $query->execute()->fetchAll();
        }


        //Display different category by different author 
        if (!empty($author_id) && !empty($category)) {
            $query = $connection->select('node_field_data', 'n');
            $query->leftjoin('node__field_qed42_category', 'fqc', 'fqc.entity_id = n.nid');
            $query->fields('n', ['nid', 'title', 'uid']);
            $query->condition('n.type', 'article');
            $query->condition('n.status', 1);
            //Not show current node
            if (!empty($node_id) && is_numeric($node_id)) {
                $query->condition('n.nid', $node_id, '!=');
            }
            $query->condition('n.uid', $author_id, "!=");
            $query->condition('fqc.field_qed42_category_target_id', $category, "!=");
            $query->orderBy('n.title', 'ASC');
            $query->orderBy('n.created', 'DESC');
            $result4 = $query->execute()->fetchAll();
        }

        $result = array_merge($result1, $result2, $result3, $result4);
        $qed_data = [];
        foreach ($result as $key => $val) {
            if ($key == 5) {
                break;
            }
            $qed_data[] = ['nid' => $val->nid, 'title' => $val->title];
        }
        return [
            '#theme' => 'qed-42-template',
            '#qed42_data' => $qed_data,
        ];
    }

}
