<?php

namespace Dashifen\Dashifen2023;

use Twig\Loader\FilesystemLoader;
use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WPHandler\Handlers\Themes\AbstractThemeHandler;

class Theme extends AbstractThemeHandler
{
  public const SLUG = 'dashifen2023';
  
  /**
   * initialize
   *
   * Uses addAction and/or addFilter to attach protected methods of this object
   * to the ecosystem of WordPress action and filter hooks.
   *
   * @return void
   * @throws HandlerException
   */
  public function initialize(): void
  {
    if (!$this->isInitialized()) {
      $this->addFilter('timber/loader/loader', 'addTimberNamespaces');
      $this->addAction('wp_enqueue_scripts', 'addAssets');
      $this->addAction('after_setup_theme', 'prepareTheme');
    }
  }
  
  /**
   * @param FilesystemLoader $loader
   *
   * @return FilesystemLoader
   */
  protected function addTimberNamespaces(FilesystemLoader $loader): FilesystemLoader
  {
    $dir = $this->getStylesheetDir() . '/assets/twigs';
    $folders = array_filter(glob($dir . '/*', 'is_dir'));
    
    self::debug($folders, true);
    
    return $loader;
  }
  
  
  
  protected function addAssets(): void
  {
  
  }
  
  protected function prepareTheme(): void
  {
  
  }
  
  
  /**
   * getPrefix
   *
   * Uses our SLUG constant to build a prefix for option names that we can
   * use throughout this theme.
   *
   * @return string
   */
  public static function getPrefix(): string
  {
    return self::SLUG . '-';
  }
}
