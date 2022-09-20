<?php

namespace Dashifen\Dashifen2023;

use Dashifen\WPDebugging\WPDebuggingTrait;

class Router
{
  use WPDebuggingTrait;
  
  public static function getTemplateObjectName(): string
  {
    if (is_front_page()) {
      return 'HomepageTemplate';
    }
    
    return 'DefaultTemplate';
  }
}
