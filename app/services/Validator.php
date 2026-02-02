<?php
namespace app\Services;
use app\repositories\UserRepository;
class Validator {

  public static function validateRegister(array $input, UserRepository $repo = null) {
    $errors = [
      'nom' => '', 'prenom' => '',
    ];

    $values = [
      'nom' => trim((string)($input['nom'] ?? '')),
      'prenom' => trim((string)($input['prenom'] ?? '')),
    ];

    if (mb_strlen($values['nom']) < 2) $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
    if (mb_strlen($values['prenom']) < 2) $errors['prenom'] = "Le prénom doit contenir au moins 2 caractères.";

    $ok = true;
    foreach ($errors as $m) { if ($m !== '') { $ok = false; break; } }

    return ['ok' => $ok, 'errors' => $errors, 'values' => $values];
  }
}
