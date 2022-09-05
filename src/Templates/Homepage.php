<?php

namespace Dashifen\Dashifen2023\Templates;

use Dashifen\Dashifen2023\Templates\Framework\AbstractTemplate;

class Homepage extends AbstractTemplate
{
  /**
   * getTemplateTwig
   *
   * Returns the name of the twig file for this template.
   *
   * @return string
   */
  protected function getTemplateTwig(): string
  {
    return '@templates/homepage.twig';
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
  protected function getTemplateContext(array $siteContext): array
  {
    return ['title' => 'Home'];
  }
}
