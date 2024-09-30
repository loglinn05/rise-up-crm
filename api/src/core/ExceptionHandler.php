<?php

namespace App\Core;

class ExceptionHandler
{
  public static function handleException(\Throwable $exception)
  {
    if ($_ENV['DEBUG'] == 'true') {
      self::handleExceptionWithDebug($exception);
    } elseif ($_ENV['DEBUG'] == 'false') {
      self::handleExceptionWithoutDebug($exception);
    } else {
      echo json_encode([
        'severity' => 'error',
        'message' => "Incorrect environment settings."
      ]);
      exit(1);
    }
  }

  public static function handleExceptionWithoutDebug(\Throwable $exception): void
  {
    http_response_code(500);

    echo json_encode([
      'code' => $exception->getCode(),
      'message' => $exception->getMessage()
    ]);
  }

  public static function handleExceptionWithDebug(\Throwable $exception): void
  {
    http_response_code(500);

    echo json_encode([
      'code' => $exception->getCode(),
      'message' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine()
    ]);
  }
}
