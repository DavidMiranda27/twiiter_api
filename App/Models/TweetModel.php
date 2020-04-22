<?php

namespace App\Models;

use App\Conexao;

class TweetModel extends Conexao {

  private $id;
  private $id_usuario;
  private $tweet;
  private $data;

  public function __construct() {

    parent::__construct();
  }

  public function __get($atributo) {
    return $this->$atributo;
  }

  public function __set($atributo, $valor) {
    $this->$atributo = $valor;
  }

  public function  salvarTweet() {

    $query = "INSERT INTO tweets(id_usuario, tweet) values(:id_usuario, :tweet)";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
    $stmt->bindValue(':tweet', $this->__get('tweet'));
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
      return true;
    }

    return false;

  }

  public function getAll() {
    $query = "
        select
          t.id ,t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
        from 
          tweets as t
          left join usuarios as u on (t.id_usuario = u.id)  
        where
          t.id_usuario = :id_usuario
          or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
        order by
          t.data desc";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    return null;

    
  }


  public function deletar() {

    $query = "delete from tweets where id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return true;
    }

    return false;
  }
}

?>