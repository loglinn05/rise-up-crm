<?php

namespace App\Models;

use \R;

class Model
{
  protected $table_name = '';
  protected $hidden = [];
  protected $show = [];

  public function __construct()
  {
    $sql = '';
    if (count($this->hidden) > 0) {
      $sql = "SHOW COLUMNS FROM `" . $this->table_name .
        "` WHERE `Field` NOT IN (" . R::genSlots($this->hidden) . ")";
    } else {
      $sql = "SHOW COLUMNS FROM `" . $this->table_name . "`";
    }

    $showFields = R::getAll($sql, $this->hidden);

    foreach ($showFields as $field) {
      $this->show[] = $field['Field'];
    }
  }
}
