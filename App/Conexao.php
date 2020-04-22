<?php 

namespace App;

abstract class Conexao {

	/**
   	* @var \PDO
   	*/
  protected $pdo;

  public function __construct() {

  	$host = getenv('HOST');
    $dbname = getenv('DBNAME');
    $user = getenv('USER');
    $pass = getenv('PASSWORD');
    $port = getenv('PORT');

    $dsn = "mysql:host={$host};dbname={$dbname};port={$port}";
    $this->pdo = new \PDO($dsn, $user, $pass);
    $this->pdo->setAttribute(
      \PDO::ATTR_ERRMODE,
      \PDO::ERRMODE_EXCEPTION
    );
  }

}

?>