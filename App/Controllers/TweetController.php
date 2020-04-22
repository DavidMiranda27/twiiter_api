<?php

namespace App\Controllers;

use App\Models\TweetModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


final class TweetController {

  public function insertTweet(Request $request, Response $response, array $args): Response {

    $data = $request->getParsedBody();

    $tweetModel = new TweetModel();
    $tweetModel->__set('id_usuario', $data['id_usuario']);
    $tweetModel->__set('tweet', $data['tweet']);
    

    if($tweetModel->salvarTweet()) {

      $response = $response->withJson([
        "message" => "Tweet criado com sucesso"
      ]);

      return $response;
    }

    $response = $response->withJson([
      "message" => "Erro ao criar tweet"
    ]);

    return $response;
  }

  public function getAllTweets(Request $request, Response $response, array $args): Response {

    $userId = $args['id_usuario'];


    $tweetModel = new TweetModel();

    $tweetModel->__set('id_usuario', $userId);

    $tweets = $tweetModel->getAll();

    if ($tweets != null) {

      $response = $response->withJson($tweets);
      return $response;

    }

    $response = $response->withJson([
      "message" => "Erro: usuário não encontrado"
    ]);

    return $response;

  }

  public function deleteTweets(Request $request, Response $response, array $args): Response {

    $tweetId = $args['id'];

    $tweetModel = new TweetModel();

    $tweetModel->__set('id', $tweetId);
    
    if ($tweetModel->deletar()) {

      $response = $response->withJson([
        "message" => "Tweet removido com sucesso"
      ]);

      return $response;
    }

    $response = $response->withJson([
      "message" => "Erro ao remover tweet"
    ]);

    return $response;

  }

}

?>