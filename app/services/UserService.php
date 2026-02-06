<?php 
namespace app\services;
use app\repositories\UserRepository;
class UserService {
  private $repo;
  public function __construct(UserRepository $repo) { $this->repo = $repo; }

  public function register( $values) {
    // $hash = password_hash((string)$plainPassword, algo: PASSWORD_DEFAULT);
    return $this->repo->create(
      $values['nom']
    );
  }
}
