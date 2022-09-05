<?php

namespace Dashifen\Dashifen2023;

use Twig\Error\LoaderError;
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
   * addTimberNamespaces
   *
   * Creates a Timber namespace for each folder within the twigs
   *
   * @param FilesystemLoader $loader
   *
   * @return FilesystemLoader
   * @throws LoaderError
   */
  protected function addTimberNamespaces(FilesystemLoader $loader): FilesystemLoader
  {
    $dir = $this->getStylesheetDir() . '/assets/twigs';
    $folders = array_filter(glob($dir . '/*'), 'is_dir');
    foreach ($folders as $folder) {
      
      // each folder within $folders runs from the root of the filesystem all
      // the way to the folders within the /assets/twigs folder.  all we want
      // are those folder names.  we explode them all, pop off the last bit,
      // and then use them as a namespace.  so, for example, the @templates
      // namespace will map to the /assets/twigs/templates folder.
      
      $namespaces = explode('/', $folder);
      $namespace = array_pop($namespaces);
      $loader->addPath($folder, $namespace);
    }
    
    return $loader;
  }
  
  /**
   * addAssets
   *
   * Adds the scripts and styles for this theme into the WordPress assets
   * queue.
   *
   * @return void
   */
  protected function addAssets(): void
  {
    $font1 = $this->enqueue('//fonts.googleapis.com/css2?family=El+Messiri&display=swap');
    $font2 = $this->enqueue('//fonts.googleapis.com/css2?family=Roboto&display=swap');
    $this->enqueue('assets/dashifen.css', [$font1, $font2]);
  }
  
  /**
   * prepareTheme
   *
   * Prepares the theme by specifying what it supports and adding menus.
   *
   * @return void
   */
  protected function prepareTheme(): void
  {
    register_nav_menus([
      'main'   => 'Main Menu',
      'footer' => 'Footer Menu',
    ]);
    
    add_theme_support('post-thumbnails', get_post_types(['public' => true]));
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
