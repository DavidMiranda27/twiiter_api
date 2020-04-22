<?php

namespace App\Models;
use App\Conexao;

class UsuarioModel extends Conexao {

  private $id;
  private $nome;
  private $email;
  private $senha;

  public function __construct() {

    parent::__construct();
  }


  public function __get($atributo) {
    return $this->$atributo;
  }

  public function __set($atributo, $valor) {
    $this->$atributo = $valor;
  }


  public function salvarUsuario() {

    $query = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':nome', $this->__get('nome'));
    $stmt->bindValue(':email', $this->__get('email'));
    $stmt->bindValue(':senha', $this->__get('senha'));
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
      return true;
    }

    return false;
  }

  public function getUsuarioPorId($id) {

    $query = "SELECT nome, email from usuarios WHERE id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    else {
      return null;
    }
  }

  public function getUsuarioPorEmail($email) {

    $query = "SELECT id, nome, email, senha from usuarios WHERE email = :email";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    else {
      return null;
    }
  }

  public function atualizarUsuario($id) {

    $query = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":nome", $this->__get('nome'));
    $stmt->bindValue(":email", $this->__get('email'));
    $stmt->execute();
    if ($stmt->rowCount() == 1) {
      return true;
    }
    else {
      return false;
    }
  }

  public function pesquisarUsuarios() {
    $query = "
      select
        u.id,
        u.nome,
        u.email,
        (
          select
            count(*)
          from
            usuarios_seguidores as us
          where
            us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
        ) as seguindo_sn
      from
        usuarios as u
      where
      u.nome like :nome and u.id != :id_usuario";
  $stmt = $this->pdo->prepare($query);
  $stmt->bindValue(":nome", '%'.$this->__get('nome').'%');
  $stmt->bindValue(":id_usuario", $this->__get('id'));
  $stmt->execute();

  return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  
  }

  public function seguirUsuario($id_usuario_seguindo) {

    $query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo) values(:id_usuario, :id_usuario_seguindo)";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(":id_usuario", $this->__get('id'));
    $stmt->bindValue(":id_usuario_seguindo", $id_usuario_seguindo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return true;
    }

    return false;
  }

  public function deixarSeguirUsuario($id_usuario_seguindo) {
    $query = "delete from usuarios_seguidores where id_usuario = :id_usuario and
      id_usuario_seguindo = :id_usuario_seguindo";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(":id_usuario", $this->__get('id'));
    $stmt->bindValue(":id_usuario_seguindo", $id_usuario_seguindo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return true;
    }

    return false;
  }


}


?>