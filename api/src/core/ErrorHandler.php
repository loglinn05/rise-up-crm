<?php

namespace App\Core;

class ErrorHandler
{
  public static function handleError($errno, $errstr, $errfile, $errline)
  {
    if ($_ENV['DEBUG'] == 'true') {
      self::handleErrorWithDebug(
        $errno,
        $errstr,
        $errfile,
        $errline
      );
    } elseif ($_ENV['DEBUG'] == 'false') {
      self::handleErrorWithoutDebug($errno);
    } else {
      echo json_encode([
        'severity' => 'error',
        'message' => "Incorrect environment settings."
      ]);
      exit(1);
    }
  }

  public static function handleErrorWithoutDebug($errno): void
  {
    http_response_code(500);

    $severity = '';

    switch ($errno) {
      case E_ERROR:
        echo 'error';
        $severity = 'error';
        break;
      case E_WARNING:
        $severity = 'warning';
        break;
      case E_NOTICE:
        $severity = 'notice';
        break;
      default:
        $severity = 'unknown';
        break;
    }

    echo json_encode([
      'severity' => $severity,
      'message' => "A server side error occurred."
    ]);
    exit(1);
  }

  public static function handleErrorWithDebug($errno, $errstr, $errfile, $errline): void
  {
    http_response_code(500);

    $severity = '';

    switch ($errno) {
      case E_ERROR:
        echo 'error';
        $severity = 'error';
        break;
      case E_WARNING:
        $severity = 'warning';
        break;
      case E_NOTICE:
        $severity = 'notice';
        break;
      default:
        $severity = 'unknown';
        break;
    }

    echo json_encode([
      'severity' => $severity,
      'message' => htmlspecialchars($errstr),
      'file' => $errfile,
      'line' => $errline
    ]);
    exit(1);
  }
}
