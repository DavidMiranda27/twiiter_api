<?php

use App\Controllers\AuthController;
use App\Controllers\ForgotController;
use App\Controllers\TweetController;
use App\Controllers\UsuarioController;
use App\Middlewares\JwtDateTimeMiddleware;

use function src\jwtAuth;
use function src\slimConfiguration;

$app = new \Slim\App(slimConfiguration());


$app->get('/teste', function($request, $response) { echo 'teste'; })
    ->add(new JwtDateTimeMiddleware())
    ->add(jwtAuth());


$app->post('/usuario', UsuarioController::class . ':insertUsuario'); //Criar uma conta

$app->group('', function() use ($app) {
    
    //Usuario rotas
    $app->get('/usuario/{id}', UsuarioController::class . ':getUsuario');
    $app->put('/usuario/{id}', UsuarioController::class . ':updateUsuario');
    $app->delete('/usuario/{id}', UsuarioController::class . ':deleteUsuario');
    $app->post('/seguir-usuario/{id_usuario_seguindo}', UsuarioController::class . ':seguirUser');
    $app->post('/deixar-seguir-usuario/{id_usuario_seguindo}', UsuarioController::class . ':deixarSeguirUser');
    $app->get('/buscar-usuario', UsuarioController::class . ':findUser');

    //Tweet rotas
    $app->post('/tweet', TweetController::class . ':insertTweet');
    $app->get('/tweet/{id_usuario}', TweetController::class . ':getAllTweets');
    $app->delete('/tweet/{id}', TweetController::class . ':deleteTweets');

})->add(new JwtDateTimeMiddleware())->add(jwtAuth());



//Forgot Pass
$app->post('/forgot-password', ForgotController::class . ':forgotPass');
$app->put('/update-password', ForgotController::class . ':setNewPass');

//Refresh token
$app->post('/refresh_token',AuthController::class . ':refreshToken');

//Login
$app->post('/login',AuthController::class . ':login');

$app->run();

?>