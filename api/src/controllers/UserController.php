<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Validator;

class UserController
{
  public function index()
  {
    $user = new User();
    echo json_encode($user->all());
  }

  public function create()
  {
    $data = $_POST;

    $rules = [
      'name' => ['required', 'string'],
      'email' => ['required', 'string', 'email', 'unique:users'],
      'tel' => ['required', 'string', 'tel'],
      'password' => ['required', 'string', 'confirmed', 'min:8', 'max:255'],
      'address' => ['nullable', 'string']
    ];

    $validator = new Validator($data, $rules);
    if ($validator->validate()) {
      $user = new User();
      $insertedId = $user->create($data);
      if ($insertedId) {
        http_response_code(201);
        echo json_encode([
          'message' => 'The user was successfully added to the database.',
          'record_id' => $insertedId
        ]);
      }
    }
  }

  public function show($id)
  {
    $this->checkIfUserExists($id);

    $user = new User();
    $userData = $user->find($id);
    if ($userData) {
      echo json_encode($userData);
    }
  }

  public function update($id)
  {
    $this->checkIfUserExists($id);

    $data = $_POST;

    $rules = [
      'name' => ['required', 'string'],
      'email' => ['required', 'string', 'email', "unique:users,$id"],
      'tel' => ['required', 'string', 'tel'],
      'address' => ['nullable', 'string']
    ];

    $validator = new Validator($data, $rules);
    if ($validator->validate()) {
      $user = new User();
      $updatedId = $user->update($id, $data);
      if ($updatedId) {
        echo json_encode([
          'message' => 'The user was successfully updated.',
          'record_id' => $updatedId
        ]);
      }
    }
  }

  public function updatePassword($id)
  {
    $this->checkIfUserExists($id);

    $data = $_POST;

    $rules = [
      'new_password' => ['required', 'string', 'confirmed', 'min:8', 'max:255'],
    ];

    $validator = new Validator($data, $rules);
    if ($validator->validate()) {
      $user = new User();
      $updatedId = $user->updatePassword($id, $data);
      if ($updatedId) {
        echo json_encode([
          'message' => 'The user\'s password was successfully updated.',
          'record_id' => $updatedId
        ]);
      }
    }
  }

  public function delete($id)
  {
    $this->checkIfUserExists($id);

    $user = new User();
    if ($user->delete($id)) {
      echo json_encode([
        'message' => 'The user was successfully deleted.',
        'record_id' => (int) $id
      ]);
    }
  }

  private function checkIfUserExists($id)
  {
    $user = new User();
    $userFound = $user->find($id);
    if (!$userFound) {
      http_response_code(404);
      echo json_encode([
        'status' => 404,
        'message' => "No user found with id $id."
      ]);
      exit(1);
    }
  }
}
