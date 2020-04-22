<?php

namespace App\Models;
use App\Conexao;


class ForgotPassModel extends Conexao {

  private $email;
  private $token;
  private $newPass;

  public function __get($atributo) {
    return $this->$atributo;
  }

  public function __set($atributo, $valor) {
    $this->$atributo = $valor;
  }

  public function __construct() {

    parent::__construct();
  }

  public function setToken() {

    $query = "update usuarios set token = :token, tokenExpire=DATE_ADD(NOW(), INTERVAL 10 MINUTE) where email = :email";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':token', $this->__get('token'));
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->execute();
    
  }

  public function updatePass() {
  
    $query = "update usuarios set token = '',senha = :newPass, tokenExpire = NULL 
    where email = :email and token = :token and tokenExpire > NOW()";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':token', $this->__get('token'));
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->bindValue(':newPass', $this->__get('newPass'));
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return true;
    }

    return false;
    
  }
}

?>