<?php

namespace App\Controllers;
use App\Models\UsuarioModel;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


final class UsuarioController {

	public function insertUsuario(Request $request, Response $response, array $args): Response {

			$data = $request->getParsedBody();
			
    	$usuario = new UsuarioModel();
    	$usuario->__set('nome', $data['nome']);
    	$usuario->__set('email', $data['email']);
			$usuario->__set('senha', md5($data['senha']));
			
			if ($usuario->salvarUsuario()) {
				$response = $response->withJson([
					"message" => "Cadastrado com sucesso"
				]);
			}
			else {
				$response = $response->withJson([
					"message" => "Erro ao cadastrar usuário"
				]);
			}
			return $response;

		}
		
		public function getUsuario(Request $request, Response $response, array $args): Response {

			$userId = $args['id'];

			$usuario = new UsuarioModel();
			$user = $usuario->getUsuarioPorId($userId);

			if ($user != null) {
				$response = $response->withJson($user);
			}
			else {
				$response = $response->withJson([
					"message" => "Erro usuário não encontrado"
				]);
			}

			return $response;

		}

		public function updateUsuario(Request $request, Response $response, array $args): Response {

			$userId = $args['id'];
			$data = $request->getParsedBody(); //dados do formulário

			$usuario = new UsuarioModel();

			$userDados = $usuario->getUsuarioPorId($userId); //Dados do BD

			if ($userDados != null) {
				$arrayMerge = array_merge($userDados,$data); //Merge dos dados

				$usuario->__set('nome', $arrayMerge['nome']);
				$usuario->__set('email', $arrayMerge['email']);

				if ($usuario->atualizarUsuario($userId)) {
					$response = $response->withJson($arrayMerge);
				}
				else {
					$response = $response->withJson([
						"message" => "Erro ao atualizar cadastro"
					]);
				}
			}
			
			else {
				$response = $response->withJson([
					"message" => "Erro usuário não encontrado"
				]);
			}

			return $response;

		}

		public function findUser(Request $request, Response $response, array $args): Response {

			$data = $request->getParsedBody();
			$token = $data['token'];
			$nome = $data['nome'];

			$tokenDecoded = JWT::decode(
				$token,
				getenv('JWT_SECRET_KEY'),
				['HS256']
			);

			$userId = $tokenDecoded->sub;

			$userModel = new UsuarioModel();
			$userModel->__set('id', $userId);
			$userModel->__set('nome', $nome);

			$resultadoBusca = $userModel->pesquisarUsuarios();

			$response = $response->withJson($resultadoBusca);

			return $response;
		}

		public function seguirUser(Request $request, Response $response, array $args): Response {

			$id_usuario_seguindo = $args['id_usuario_seguindo'];
			$data = $request->getParsedBody();
			$token = $data['token'];

			$tokenDecoded = JWT::decode(
				$token,
				getenv('JWT_SECRET_KEY'),
				['HS256']
			);

			$userId = $tokenDecoded->sub;

			$userModel = new UsuarioModel();
			$userModel->__set('id', $userId);

			if ($userModel->seguirUsuario($id_usuario_seguindo)) {
				$response = $response->withJson([
					"message" => "você está segundo esse usuario(a) agora"
				]);
				
				return $response;
			}

			$response = $response->withJson([
				"message" => "Erro ao tentar seguir usuario(a)"
			]);

			return $response;
		}

		public function deixarSeguirUser(Request $request, Response $response, array $args): Response {

			$id_usuario_seguindo = $args['id_usuario_seguindo'];
			$data = $request->getParsedBody();
			$token = $data['token'];

			$tokenDecoded = JWT::decode(
				$token,
				getenv('JWT_SECRET_KEY'),
				['HS256']
			);

			$userId = $tokenDecoded->sub;

			$userModel = new UsuarioModel();
			$userModel->__set('id', $userId);

			if ($userModel->deixarSeguirUsuario($id_usuario_seguindo)) {
				$response = $response->withJson([
					"message" => "você deixou de seguir esse usuario(a) agora"
				]);
				
				return $response;
			}

			$response = $response->withJson([
				"message" => "Erro ao tentar deixar de seguir usuario(a)"
			]);

			return $response;
		}

}

?>