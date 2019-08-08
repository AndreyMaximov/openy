<?php

namespace Drupal\openy_home_branch\Plugin\HomeBranchLibrary;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\openy_home_branch\HomeBranchLibraryBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the home branch library plugin for user menu block.
 *
 * @HomeBranchLibrary(
 *   id="hb_menu_selector",
 *   label = @Translation("Home Branch Menu Selector"),
 *   entity="block"
 * )
 */
class HBMenuSelector extends HomeBranchLibraryBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  const BLOCK_ID = 'system_menu_block:account';

  /**
   * The Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrary() {
    return 'openy_home_branch/menu_selector';
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedForAttaching($variables) {
    return ($variables['plugin_id'] == self::BLOCK_ID);
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrarySettings() {
    // Get locations list.
    $query = $this->database->select('node_field_data', 'n');
    $query->fields('n', ['nid', 'title']);
    $query->condition('n.status', NodeInterface::PUBLISHED);
    $query->condition('n.type', 'branch');
    $query->orderBy('n.title');
    $query->addTag('openy_home_branch_get_locations');
    $query->addTag('node_access');
    $result = $query->execute()->fetchAllKeyed();

    return [
      'menuSelector' => '.nav-global .page-head__top-menu ul.navbar-nav',
      'defaultTitle' => $this->t('My home branch'),
      'locations' => $result,
    ];
  }

}
