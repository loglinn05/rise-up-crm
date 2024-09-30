<?php

namespace App\Core;

use \R;

class Database
{
  public function __construct(
    private string $host,
    private string $dbname,
    private string $user,
    private string $password
  ) {}

  public function setup(bool $freeze = true): void
  {
    $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
    R::setup($dsn, $this->user, $this->password);

    R::freeze($freeze);
  }
}
