<?php

declare(strict_types=1);

require "vendor/autoload.php";

set_exception_handler('App\\Core\\ExceptionHandler::handleException');
set_error_handler('App\\Core\\ErrorHandler::handleError');

header('Content-type: application/json');

require "bootstrap.php";

require "router.php";
