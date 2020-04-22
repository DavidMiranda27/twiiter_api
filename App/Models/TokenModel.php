<?php

namespace App\Models;

use App\Conexao;

final class TokenModel extends Conexao {

  private $id;
  private $token;
  private $refresh_token;
  private $expired_at;
  private $usuarios_id;


  public function __construct() {

    parent::__construct();
  }


  public function __get($atributo) {
    return $this->$atributo;
  }

  public function __set($atributo, $valor) {
    $this->$atributo = $valor;
  }

  public function createToken() {
  
    $statement = $this->pdo
      ->prepare("INSERT INTO tokens 
                  (
                    token,
                    refresh_token,
                    expired_at,
                    usuarios_id
                  )
                  VALUES
                  (
                    :token,
                    :refresh_token,
                    :expired_at,
                    :usuarios_id
                  )
               ");
    
    $statement->bindValue(':token', $this->__get('token'));
    $statement->bindValue(':refresh_token', $this->__get('refresh_token'));
    $statement->bindValue(':expired_at', $this->__get('expired_at'));
    $statement->bindValue(':usuarios_id', $this->__get('usuarios_id'));

    $statement->execute();

  }

  public function verifyRefreshToken(string $refreshToken): bool {

    $statement = $this->pdo
      ->prepare('SELECT id FROM tokens WHERE refresh_token = :refresh_token');
    $statement->bindValue(':refresh_token',$refreshToken);
    $statement->execute();
    $tokens = $statement->fetchAll(\PDO::FETCH_ASSOC);
    return count($tokens) === 0 ? false : true;
    
  }
  

}
?>