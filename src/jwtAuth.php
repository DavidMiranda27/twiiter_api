<?php

namespace src;

use Tuupola\Middleware\JwtAuthentication;


use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

function jwtAuth(): JwtAuthentication {
  return new JwtAuthentication([
    'secret' => getenv('JWT_SECRET_KEY'),
    'attribute' => 'jwt',
    'secure' => true,
    'relaxed' => ['localhost'],
        'error' => function(Response $response,array $args){
            
            return $response->withJson([
                'message' => 'Acesso não autorizado!',
                'status' => 400
            ]);
        }
  ]);
}

?>