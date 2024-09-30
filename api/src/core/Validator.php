<?php

namespace App\Core;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use \R;

class Validator
{
  private $data;
  private $rules;
  private $errors = [];

  public function __construct(array $data, array $rules)
  {
    $this->data = $data;
    $this->rules = $rules;
  }

  private function string($field)
  {
    if (!is_string($this->data[$field])) {
      $this->errors[$field][] = "$field field should be a string.";
    }
  }

  private function min($field, $min)
  {
    if (strlen($this->data[$field]) < (int) $min) {
      $this->errors[$field][] = "$field field's length should be minimum $min characters.";
    }
  }

  private function max($field, $max)
  {
    if (strlen($this->data[$field]) > (int) $max) {
      $this->errors[$field][] = "$field field's length should be maximum $max characters.";
    }
  }

  private function email($field)
  {
    if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
      $this->errors[$field][] = "$field field should be a valid email.";
    }
  }

  private function tel($field)
  {
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
      $telProto = $phoneUtil->parse($this->data[$field], "UA");
      $isValid = $phoneUtil->isValidNumber($telProto);

      if (!$isValid) {
        $this->errors[$field][] = "$field field should be a valid phone number.";
      }
    } catch (NumberParseException $e) {
      $this->errors[$field][] = "$field field should be a valid phone number.";
    }
  }

  private function confirmed($field)
  {
    $confirmationField = $field . '_confirmation';

    if (
      !isset($this->data[$confirmationField]) ||
      $this->data[$confirmationField] !== $this->data[$field]
    ) {
      $this->errors[$field][] = "$field and $confirmationField do not match.";
    }
  }

  private function unique($field, $table, $ignore_id = 0)
  {
    $rec = R::findOne($table, "`$field` = ?", [$this->data[$field]]);

    if (isset($rec)) {
      if ($rec->id != (int) $ignore_id) {
        $this->errors[$field][] = "$field is already taken.";
      }
    }
  }

  public function validate()
  {
    foreach ($this->rules as $key => $propRules) {
      foreach ($propRules as $propRule) {
        $ruleDesc = $this->extractRule($propRule);
        $rule = $ruleDesc['rule'];
        if ($rule === 'nullable') {
          // If the field is not set, skip validation
          if (!isset($this->data[$key])) {
            continue 2; // Skip to the next field
          }
        } elseif ($rule === 'required') {
          // If the field is required but not set, handle the error
          if (!isset($this->data[$key])) {
            $this->errors[$key][] = "$key field is required.";
            continue 2; // Skip to the next field
          }
        } else {
          // Perform validation for other rules
          if (isset($ruleDesc['rule_args'])) {
            $this->$rule($key, ...$ruleDesc['rule_args']);
          } else {
            $this->$rule($key);
          }
        }
      }
    }

    if (count($this->errors) > 0) {
      $this->validationFailedResponse();
    } else {
      return true;
    }
  }

  public function extractRule($ruleString)
  {
    preg_match('/(\w+)(:(\w+,?)+)?/', $ruleString, $matches);

    $ruleDescription['rule'] = $matches[1];

    if (isset($matches[2])) {
      $args = explode(',', ltrim($matches[2], ':'));
      for ($i = 0; $i < count($args); $i++) {
        $ruleDescription['rule_args'][] = $args[$i];
      }
    }
    return $ruleDescription;
  }

  private function validationFailedResponse(): void
  {
    http_response_code(422);
    echo json_encode([
      'code' => 422,
      'message' => "Validation failed.",
      "errors" => $this->errors
    ]);
    exit(1);
  }
}
