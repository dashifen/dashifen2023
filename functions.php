<?php

namespace Dashifen;

use Dashifen\Dashifen2023\Theme;
use Dashifen\WPHandler\Handlers\HandlerException;

if (!class_exists(Theme::class)) {
  
  // if we don't already know our Theme object, then we must not have included
  // an autoloader.  therefore, we'll include the one that's adjacent to this
  // file which will make our theme's objects available to its other files.
  
  require 'vendor/autoload.php';
}

(function () {
  
  // initializing our theme in this anonymous function means that nothing that
  // we declare in this scope is available outside of it.  this should make it
  // so that our objects remain inaccessible except within the context of our
  // theme.
  
  try {
    (new Theme())->initialize();
  } catch (HandlerException $e) {
    Theme::catcher($e);
  }
})();
