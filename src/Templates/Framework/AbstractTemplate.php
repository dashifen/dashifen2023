<?php

namespace Dashifen\Dashifen2023\Templates\Framework;

use RegexIterator;
use Timber\Timber;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Dashifen\Dashifen2023\Theme;
use Dashifen\Transformer\TransformerException;
use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WPHandler\Traits\OptionsManagementTrait;
use Dashifen\WPTemplates\AbstractTemplate as AbstractTimberTemplate;
use Dashifen\WPTemplates\TemplateException as BaselineTemplateException;

abstract class AbstractTemplate extends AbstractTimberTemplate
{
  use OptionsManagementTrait;
  
  protected int $postId;
  
  /**
   * AbstractTemplate constructor.
   *
   * @throws HandlerException
   * @throws TemplateException
   * @throws TransformerException
   */
  public function __construct()
  {
    $this->postId = get_the_ID();
    
    try {
      $twig = $this->getTwig();
      $context = $this->getContext();
      parent::__construct($twig, $context);
    } catch (BaselineTemplateException $e) {
      throw new TemplateException(
        $e->getMessage(),
        $e->getCode(),
        $e
      );
    }
  }
  
  /**
   * getTwig
   *
   * Returns the twig file for this template after confirming that it exists
   * within this theme.
   *
   * @return string
   * @throws HandlerException
   * @throws TemplateException
   * @throws TransformerException
   */
  private function getTwig(): string
  {
    $twig = $this->getTemplateTwig();
    if (!isset($this->findTwigs()[$twig])) {
      throw new TemplateException('Unknown template: ' . $twig,
        TemplateException::UNKNOWN_TWIG);
    }
    
    return $twig;
  }
  
  /**
   * getTemplateTwig
   *
   * Returns the name of the twig file for this template.
   *
   * @return string
   */
  abstract protected function getTemplateTwig(): string;
  
  /**
   * findTwigs
   *
   * Returns an array of twig filenames located within this theme.
   *
   * @return array
   * @throws HandlerException
   * @throws TransformerException
   */
  private function findTwigs(): array
  {
    // in a production environment, we want to avoid a filesystem search as
    // much as possible.  so, if we're not debugging and it's not a new version
    // of this theme, then we'll assume that the list of twigs is the same as
    // last time we searched for them.
    
    if (!self::isDebug() && !$this->isNewThemeVersion()) {
      return $this->getOption('twigs', []);
    }
    
    $directory = new RecursiveDirectoryIterator(     // get all files
      get_stylesheet_directory() . '/assets/twigs/', // in or under this folder
      FilesystemIterator::SKIP_DOTS                  // skipping . and ..
    );
    
    $files = new RegexIterator(                      // limit results
      new RecursiveIteratorIterator($directory),     // within this iterator
      '/.twig$/',                                    // to .twig files
      RegexIterator::MATCH,                          // keeping only matches
      RegexIterator::USE_KEY                         // based on keys
    );
    
    // now, we convert our iterator to an array and get it's keys.  these are
    // the paths to each twig file (the values are the SplFileInfo objects; we
    // don't need those).  and, if we're on Windows, we do a quick change to
    // the directory separator
    
    $twigs = array_keys(iterator_to_array($files));
    if (substr(PHP_OS, 0, 3) === 'WIN') {
      array_walk($twigs, fn(&$twig) => $twig = str_replace('\\', '/', $twig));
    }
    
    // our map here splits the full path names based on the folder in which
    // our twigs are located.  then, everything after that folder with the
    // Timber namespace prefix should match the twig files that our templates
    // want to use.  finally, we flip the array to do an O(1) lookup for files
    // instead of O(N) searches.
    
    $twigs = array_flip(
      array_map(
        fn($twig) => '@' . explode('assets/twigs/', $twig)[1],
        $twigs
      )
    );
    
    $this->updateOption('twigs', $twigs);
    return $twigs;
  }
  
  /**
   * isNewThemeVersion
   *
   * Returns true if the theme version in the database isn't the same as the
   * one in the style.css file.
   *
   * @return bool
   * @throws TransformerException
   * @throws HandlerException
   */
  protected function isNewThemeVersion(): bool
  {
    $knownVersion = $this->getOption('version');
    $currentVersion = wp_get_theme()->get('Version');
    if (($isNewVersion = $knownVersion !== $currentVersion)) {
      $this->updateOption('version', $currentVersion);
    }
    
    return $isNewVersion;
  }
  
  /**
   * getContext
   *
   * Returns an array of information that we pass to Timber so that it can use
   * it while compiling our templates into valid HTML.
   *
   * @return array
   * @throws HandlerException
   * @throws TemplateException
   * @throws TransformerException
   */
  private function getContext(): array
  {
    return array_merge(
      ($siteContext = $this->getSiteContext()),
      ['page' => $this->getTemplateContext($siteContext)]
    );
  }
  
  /**
   * getSiteContext
   *
   * Returns information that's global, i.e. it's the same throughout the site.
   *
   * @return array
   * @throws HandlerException
   * @throws TemplateException
   * @throws TransformerException
   */
  private function getSiteContext(): array
  {
    return [
      'year' => date('Y'),
      'twig' => basename($this->getTwig(), '.twig'),
      'site' => [
        'url'    => home_url(),
        'title'  => 'Dashifen.com',
        'images' => get_stylesheet_directory_uri() . '/assets/images/',
        'logo'   => [
          'alt' => 'a witch\'s hat with a purple band and a gold buckle',
          'src' => 'witch-hat.png',
        ],
      ],
    ];
  }
  
  /**
   * getTemplateContext
   *
   * Returns an array of information necessary for the compilation of a
   * specific twig template.
   *
   * @param array $siteContext
   *
   * @return array
   */
  abstract protected function getTemplateContext(array $siteContext): array;
  
  /**
   * compile
   *
   * Compiles either a previously set template file and context or can use
   * the optional parameters here to specify the file and context at the time
   * of the call and returns it to the calling scope.     *
   *
   * @param bool        $debug
   * @param string|null $file
   * @param array|null  $context
   *
   * @return string
   * @throws TemplateException
   */
  public function compile(bool $debug = false, ?string $file = null, ?array $context = null): string
  {
    if (($file ??= $this->file) === null) {
      throw new TemplateException('Cannot compile without a twig file.',
        TemplateException::UNKNOWN_TWIG);
    }
    
    if (($context ??= $this->context) === null) {
      throw new TemplateException('Cannot compile without a template\'s context.',
        TemplateException::UNKNOWN_CONTEXT);
    }
   
    if ($debug || self::isDebug()) {
      $context['page']['context'] = print_r($context, true);
    }
    
    return Timber::fetch($file, $context);
  }
  
  /**
   * getOptionNamePrefix
   *
   * Returns the prefix that is used to differentiate the options for this
   * handler's sphere of influence from others.
   *
   * @return string
   */
  public function getOptionNamePrefix(): string
  {
    return Theme::getPrefix();
  }
  
  /**
   * getOptionNames
   *
   * Returns an array of valid option names for use within the isOptionValid
   * method.
   *
   * @return array
   */
  protected function getOptionNames(): array
  {
    return ['twigs', 'version'];
  }
}