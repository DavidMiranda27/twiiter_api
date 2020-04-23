<?php

namespace App\Middlewares;

class CorsMiddleware
{
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {

        /** @var $response \Slim\Http\Response */
        $response = $next($request, $response);

        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');

    }
}
?>