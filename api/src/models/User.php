<?php

namespace App\Models;

use \R;

class User extends Model
{
  protected $table_name = 'users';
  protected $hidden = ['password'];

  public function __construct()
  {
    parent::__construct();
  }

  public function all()
  {
    $showFields = implode(', ', $this->show);
    $table = $this->table_name;

    $users = R::getAll("SELECT $showFields FROM $table");
    return $users;
  }

  public function create($data)
  {
    $user = R::dispense($this->table_name);
    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->tel = $data['tel'];
    $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
    if (isset($data['address'])) {
      $user->address = $data['address'];
    }
    $lastInsertId = R::store($user);
    return $lastInsertId;
  }

  public function find($id)
  {
    $showFields = implode(', ', $this->show);
    $table = $this->table_name;

    $sql = "SELECT $showFields FROM $table WHERE `id` = ? LIMIT 1";

    $user = R::getRow($sql, [$id]);
    if (!empty($user)) {
      return $user;
    } else {
      return false;
    }
  }

  public function update($id, $data)
  {
    $user = R::load($this->table_name, $id);
    // If no user with this ID found
    if ($user->id == 0) {
      return false;
    }
    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->tel = $data['tel'];
    if (isset($data['address'])) {
      $user->address = $data['address'];
    }
    $updateId = R::store($user);
    return $updateId;
  }

  public function updatePassword($id, $data)
  {
    $user = R::load($this->table_name, $id);
    // If no user with this ID found
    if ($user->id == 0) {
      return false;
    }
    $user->password = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $updateId = R::store($user);
    return $updateId;
  }

  public function delete($id)
  {
    $user = R::load($this->table_name, $id);
    $deletedId = R::trash($user);
    return $deletedId;
  }
}
