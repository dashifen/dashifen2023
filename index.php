<?php

namespace Dashifen;

use Dashifen\Dashifen2023\Theme;
use Dashifen\Dashifen2023\Router;
use Dashifen\Dashifen2023\Templates\Framework\TemplateFactory;
use Dashifen\Dashifen2023\Templates\Framework\TemplateException;

try {
  $templateName = (new Router())->getTemplateObjectName();
  $templateObject = TemplateFactory::produceTemplate($templateName);
  $templateObject->render();
} catch (TemplateException $e) {
  Theme::catcher($e);
}
