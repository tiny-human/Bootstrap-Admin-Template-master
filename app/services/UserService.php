<?php 
namespace app\Services;
use app\repositories\UserRepository;
class UserService {
  private $repo;
  public function __construct(UserRepository $repo) { $this->repo = $repo; }

  public function register(array $values, $plainPassword) {
    // $hash = password_hash((string)$plainPassword, algo: PASSWORD_DEFAULT);
    return $this->repo->create(
      $values['nom'], $values['prenom'], $values['email']
    );
  }
}
