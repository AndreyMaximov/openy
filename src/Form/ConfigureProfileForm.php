<?php

namespace Drupal\openy\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form for selecting features to install.
 */
class ConfigureProfileForm extends FormBase {

  const DEFAULT_PRESET = 'standard';
  const OPENY_CATEGORY_BLOCK = 'block';
  const OPENY_CATEGORY_CONTENT_TYPE = 'content_type';
  const OPENY_CATEGORY_FEATURE = 'feature';
  const OPENY_CATEGORY_HELPER = 'helper';
  const OPENY_CATEGORY_MEDIA = 'media';
  const OPENY_CATEGORY_MENU = 'menu';
  const OPENY_CATEGORY_MODULE = 'module';
  const OPENY_CATEGORY_PARAGRAPH = 'paragraph';
  const OPENY_CATEGORY_TAXONOMY = 'taxonomy';
  const OPENY_CATEGORY_THEME = 'theme';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openy_configure_profile';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, array &$install_state = NULL) {
    $form['#title'] = $this->t('Content');

    $presets = [
      'standard' => $this->t('Standard'),
      'extended' => $this->t('Extended'),
      'custom' => $this->t('Custom'),
    ];

    $default_preset = $this->getDefaultPreset();
    $form['preset'] = [
      '#type' => 'select',
      '#title' => $this->t('Choose preset to install'),
      '#options' => $presets,
      '#default_value' => $default_preset,
    ];

    $options = $this->getOptions();
    $default_options = $this->getDefaultOptions($default_preset);
    $form['pick'] = [
      '#tree' => TRUE,
    ];
    $form['pick']['paragraphs'] = [
      '#type' => 'details',
      '#title' => $this->t('Paragraphs'),
      '#open' => TRUE,
      'paragraphs' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_PARAGRAPH],
        '#default_value' => $default_options[self::OPENY_CATEGORY_PARAGRAPH],
      ],
    ];
    $form['pick']['features'] = [
      '#title' => $this->t('Features'),
      '#type' => 'details',
      '#open' => TRUE,
      'features' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_FEATURE],
        '#default_value' => $default_options[self::OPENY_CATEGORY_FEATURE],
      ],
    ];
    $form['pick']['content_types'] = [
      '#title' => $this->t('Content types'),
      '#type' => 'details',
      '#open' => TRUE,
      'content_types' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_CONTENT_TYPE],
        '#default_value' => $default_options[self::OPENY_CATEGORY_CONTENT_TYPE],
      ],
    ];
    $form['pick']['taxonomy'] = [
      '#title' => $this->t('Taxonomy'),
      '#type' => 'details',
      '#open' => TRUE,
      'taxonomy' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_TAXONOMY],
        '#default_value' => $default_options[self::OPENY_CATEGORY_TAXONOMY],
      ],
    ];
    $form['pick']['menu'] = [
      '#title' => $this->t('Menu'),
      '#type' => 'details',
      '#open' => TRUE,
      'menu' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_MENU],
        '#default_value' => $default_options[self::OPENY_CATEGORY_MENU],
      ],
    ];
    $form['pick']['media'] = [
      '#title' => $this->t('Media'),
      '#type' => 'details',
      '#open' => TRUE,
      'media' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_MEDIA],
        '#default_value' => $default_options[self::OPENY_CATEGORY_MEDIA],
      ],
    ];
    $form['pick']['blocks'] = [
      '#title' => $this->t('Blocks'),
      '#type' => 'details',
      '#open' => TRUE,
      'blocks' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_BLOCK],
        '#default_value' => $default_options[self::OPENY_CATEGORY_BLOCK],
      ],
    ];
    $form['pick']['themes'] = [
      '#title' => $this->t('Themes'),
      '#type' => 'details',
      '#open' => TRUE,
      'themes' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_THEME],
        '#default_value' => $default_options[self::OPENY_CATEGORY_THEME],
      ],
    ];
    $form['pick']['helper_modules'] = [
      '#title' => $this->t('Helper modules'),
      '#type' => 'details',
      '#open' => TRUE,
      'helper_modules' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_HELPER],
        '#default_value' => $default_options[self::OPENY_CATEGORY_HELPER],
      ],
    ];
    $form['pick']['modules'] = [
      '#title' => $this->t('Modules'),
      '#type' => 'details',
      '#open' => TRUE,
      'modules' => [
        '#type' => 'checkboxes',
        '#options' => $options[self::OPENY_CATEGORY_MODULE],
        '#default_value' => $default_options[self::OPENY_CATEGORY_MODULE],
      ],
    ];

    $form['#attached']['library'] = ['openy/profile-preset'];
    $form['#attached']['drupalSettings']['presets'] = $this->getPresetsInfo();

    $form['actions'] = [
      'continue' => [
        '#type' => 'submit',
        '#value' => $this->t('Continue'),
      ],
      '#type' => 'actions',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $GLOBALS['install_state']['openy']['preset'] = $form_state->getValue('preset');
    $GLOBALS['install_state']['openy']['pick'] = $form_state->getValue('pick');
  }

  /**
   * Returns default preset machine name.
   *
   * @return string
   *   Default preset machine name.
   */
  private function getDefaultPreset() {
    if (!empty($GLOBALS['install_state']['forms'][$this->getFormId()]['preset'])) {
      return $GLOBALS['install_state']['forms'][$this->getFormId()]['preset'];
    };

    return self::DEFAULT_PRESET;
  }

  /**
   * Returns categories template.
   *
   * @return array
   *   An associative category-keyed array.
   */
  private function getCategories() {
    return [
      self::OPENY_CATEGORY_BLOCK => [],
      self::OPENY_CATEGORY_CONTENT_TYPE => [],
      self::OPENY_CATEGORY_FEATURE => [],
      self::OPENY_CATEGORY_HELPER => [],
      self::OPENY_CATEGORY_MEDIA => [],
      self::OPENY_CATEGORY_MENU => [],
      self::OPENY_CATEGORY_MODULE => [],
      self::OPENY_CATEGORY_PARAGRAPH => [],
      self::OPENY_CATEGORY_TAXONOMY => [],
      self::OPENY_CATEGORY_THEME => [],
    ];
  }

  /**
   * Returns default options for a preset.
   *
   * @param string $preset
   *   Preset name.
   *
   * @return array
   *   Groupped default options for the form for the given preset.
   */
  private function getDefaultOptions($preset) {
    $default_options = $this->getCategories();
    $presets_info = $this->getPresetsInfo(FALSE);
    if (empty($presets_info[$preset])) {
      return $default_options;
    }

    return $presets_info[$preset];
  }

  /**
   * Returns meta-information for all profile-related modules.
   */
  private function getOpenYModulesInfo() {
    $_SESSION = [];
    $list = [];
    $path = drupal_get_path('profile', 'openy');
    $files = file_scan_directory($path, '/^' . DRUPAL_PHP_FUNCTION_PATTERN . '\.info.yml$/', [
      'key' => 'name',
      'min_depth' => 0,
    ]);
    foreach ($files as $file) {
      list($name, ) = explode('.', $file->name);
      // Get the .info.yml file for the module or theme this file belongs to.
      $info = \Drupal::service('info_parser')->parse($file->uri);
      switch ($info['type']) {
        case 'theme':
          $info['openy']['category'] = self::OPENY_CATEGORY_THEME;
          $list[$name] = $info;
          break;

        case 'module':
          // Don't take contrib modules or OpenY modules not ready to be used with
          // this wizard into account.
          if (array_key_exists('openy', $info)) {
            $list[$name] = $info;
          }
          break;

        case 'profile':
          foreach ($info['optional'] as $module) {
            $info_file = drupal_get_path('module', $module) . '/' . $module . '.info.yml';
            $module_info = \Drupal::service('info_parser')->parse($info_file);
            $module_info['openy'] = [
              'presets' => [],
              'category' => self::OPENY_CATEGORY_MODULE,
            ];
            $list[$module] = $module_info;
          }
          break;
      }
    }

    return $list;
  }


  /**
   * Prepares the lists of features to be used as form options lists.
   *
   * @return array
   *   Associative array of options keyed by option category.
   */
  private function getOptions() {
    $options = $this->getCategories();
    foreach ($this->getOpenYModulesInfo() as $name => $info) {
      $category = $info['openy']['category'];
      $options[$category][$name] = $info['name'];
    }

    foreach ($options as $category => &$_options) {
      asort($_options);
    }

    return $options;
  }

  /**
   * Returns the lists of features groupped by preset.
   *
   * @param boolean $plain
   *   Indicates if the modules must not be groupped by category.
   *
   * @return array
   *   Presets info.
   */
  private function getPresetsInfo($plain = TRUE) {
    $presets_info = [];
    $modules_info = $this->getOpenYModulesInfo();
    foreach ($modules_info as $module => $info) {
      if (empty($info['openy']['presets'])) {
        continue;
      }
      foreach ($info['openy']['presets'] as $preset) {
        if ($plain) {
          $presets_info[$preset][] = $module;
        }
        else {
          if (!isset($presets_info[$preset])) {
            $presets_info[$preset] = $this->getCategories();
          }
          $category = $info['openy']['category'];
          $presets_info[$preset][$category][] = $module;
        }
      }
    }

    return $presets_info;
  }

}
