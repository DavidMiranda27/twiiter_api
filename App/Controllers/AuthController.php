<?php

namespace App\Controllers;

use App\Models\TokenModel;
use App\Models\UsuarioModel;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;


final class AuthController {

  public function login(Request $request, Response $response, array $args): Response {

    $data = $request->getParsedBody();

    $email = $data['email'];
    $senha = md5($data['senha']);

    $usuarioModel = new UsuarioModel();
    $usuario = $usuarioModel->getUsuarioPorEmail($email);

    if(is_null($usuario))
      return $response->withStatus(401);
    
    if ($senha != $usuario['senha']) {
      return $response->withStatus(401);
    }
    
    $expiredAt = (new \DateTime())->modify('+2 days')->format('Y-m-d H:i:s');

    $toknePayload = [
      'sub' => $usuario['id'],
      'name' => $usuario['nome'],
      'email' => $usuario['email'],
      'expired_at' => $expiredAt
    ];

    $token = JWT::encode($toknePayload, getenv('JWT_SECRET_KEY'));
    
    $refreshTokenPayload = [
      'email' => $usuario['email'],
      'random' => uniqid()
    ];

    $refreshToken = JWT::encode($refreshTokenPayload, getenv('JWT_SECRET_KEY'));

    $tokenModel = new TokenModel();
    $tokenModel->__set('expired_at',$expiredAt);
    $tokenModel->__set('refresh_token',$refreshToken);
    $tokenModel->__set('token',$token);
    $tokenModel->__set('usuarios_id',$usuario['id']);

    $tokenModel->createToken();

    $response = $response->withJson([
      "token" => $token,
      "refresh_token" => $refreshToken
    ]);

    return $response;
  }

  public function refreshToken(Request $request, Response $response, array $args): Response {
    
    $data = $request->getParsedBody();
    $refreshToken = $data['refresh_token'];
    $expiredAt = (new \DateTime())->modify('+2 days')->format('Y-m-d H:i:s');

    $refreshTokenDecoded = JWT::decode(
      $refreshToken,
      getenv('JWT_SECRET_KEY'),
      ['HS256']
    );
    
    $tokenModel = new TokenModel();
    $refreshTokenExists = $tokenModel->verifyRefreshToken($refreshToken);
    if(!$refreshTokenExists)
      return $response->withStatus(401);

    $usuarioModel = new UsuarioModel();
    $usuario = $usuarioModel->getUsuarioPorEmail($refreshTokenDecoded->email);
    if(is_null($usuario))
      return $response->withStatus(401);
    
    $toknePayload = [
      'sub' => $usuario['id'],
      'name' => $usuario['nome'],
      'email' => $usuario['email'],
      'expired_at' => $expiredAt
    ];
  
    $token = JWT::encode($toknePayload, getenv('JWT_SECRET_KEY'));
    $refreshTokenPayload = [
      'email' => $usuario['email'],
      'random' => uniqid()
    ];
  
    $refreshToken = JWT::encode($refreshTokenPayload, getenv('JWT_SECRET_KEY'));
  
    $tokenModel = new TokenModel();
    $tokenModel->__set('expired_at', $expiredAt);
    $tokenModel->__set('refresh_token', $refreshToken);
    $tokenModel->__set('token',$token);
    $tokenModel->__set('id', $usuario['id']);
  
    $tokenModel->createToken();
  
    $response = $response->withJson([
      "token" => $token,
      "refresh_token" => $refreshToken
    ]);
  
    return $response;
  }
}

?>