<?php

namespace App\Controllers;

use App\Models\ForgotPassModel;
use App\Models\UsuarioModel;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require './libs/PHPMailer/SMTP.php';
require './libs/PHPMailer/PHPMailer.php';
require './libs/PHPMailer/Exception.php';


final class ForgotController {

  public function forgotPass(Request $request, Response $response, array $args): Response {

    $data = $request->getParsedBody();
    $email = $data['email'];
    $token = $this::generateToken();

    $usuarioModel = new UsuarioModel();
    if ($usuarioModel->getUsuarioPorEmail($email) != null) { //Achei o email 



      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;

      $mail->Username = 'contasendemail@gmail.com';
      $mail->Password = '!#@*4321';
      $mail->SMTPSecure = 'tls';
      $mail->port = 587;

      $mail->setFrom('contasendemail@gmail.com');
      $mail->addAddress($email);
      $mail->isHTML(true);
      $mail->Subject = 'Reset Password';

      $mail->Body = '
      <!DOCTYPE html>
      <html style="box-sizing: border-box;font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;-webkit-tap-highlight-color: transparent;">
      <head style="box-sizing: border-box;">
        <title style="box-sizing: border-box;">Recuperação de Senha</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" style="box-sizing: border-box;">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous" style="box-sizing: border-box;">
      </head>
      <body style="box-sizing: border-box;margin: 0;font-family: -apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,Roboto,&quot;Helvetica Neue&quot;,Arial,&quot;Noto Sans&quot;,sans-serif,&quot;Apple Color Emoji&quot;,&quot;Segoe UI Emoji&quot;,&quot;Segoe UI Symbol&quot;,&quot;Noto Color Emoji&quot;;font-size: 1rem;font-weight: 400;line-height: 1.5;color: #212529;text-align: left;background-color: #fff;min-width: 992px!important;">
        <div class="card text-center" style="box-sizing: border-box;position: relative;display: flex;-ms-flex-direction: column;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 1px solid rgba(0,0,0,.125);border-radius: .25rem;text-align: center!important;">
          <div class="card-body" style="box-sizing: border-box;-ms-flex: 1 1 auto;flex: 1 1 auto;min-height: 1px;padding: 1.25rem;">
            <h5 class="card-title" style="box-sizing: border-box;margin-top: 0;margin-bottom: .75rem;font-weight: 500;line-height: 1.2;font-size: 1.25rem;">Solicitação de recuperação de senha</h5>
            <p class="card-text" style="box-sizing: border-box;margin-top: 0;margin-bottom: 1rem;orphans: 3;widows: 3;">Um pedido de recuperação de senha foi feito, por favor click no botão abaixo:</p>
            <form method="post" action="localhost/twiiter_vendor_less/public/reset_password" style="box-sizing: border-box;">
              <input type="hidden" name="email" value="'.$email.'" style="box-sizing: border-box;margin: 0;font-family: inherit;font-size: inherit;line-height: inherit;overflow: visible;">
              <input type="hidden" name="token" value="'.$token.'" style="box-sizing: border-box;margin: 0;font-family: inherit;font-size: inherit;line-height: inherit;overflow: visible;">
              <input type="submit" class="btn btn-primary" value="Recuperar" style="box-sizing: border-box;margin: 0;font-family: inherit;font-size: 1rem;line-height: 1.5;overflow: visible;-webkit-appearance: button;display: inline-block;font-weight: 400;color: #fff;text-align: center;vertical-align: middle;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-color: #007bff;border: 1px solid transparent;padding: .375rem .75rem;border-radius: .25rem;transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;border-color: #007bff;">
            </form>
          </div>
        </div>
      </body>
      </html>
          ';
      
      if ($mail->send()) {
        $forgotModel = new ForgotPassModel();
        $forgotModel->__set('email', $email);
        $forgotModel->__set('token', $token);
        $forgotModel->setToken();

        $response = $response->withJson([
          "message" => "Por favor verifique sua caixa de e-mail"
        ]);

        return $response;
      }
      else {
        $response = $response->withJson([
          "message" => "Erro ao enviar e-mail"
        ]);

        return $response;
      }
    }

    $response = $response->withJson([
      "message" => "E-mail nao encontrado" 
    ]);

    return $response;
  }


  public function setNewPass(Request $request, Response $response, array $args): Response {

    $data = $request->getParsedBody();

    $email = $data['email'];
    $token = $data['token'];
    $newPass = $data['novaSenha'];

    $forgotModel = new ForgotPassModel();
    $forgotModel->__set('email', $email);
    $forgotModel->__set('token', $token);
    $forgotModel->__set('newPass', md5($newPass));
    
    if ($forgotModel->updatePass()) {
      $response = $response->withJson([
        "message" => "Senha auterada com sucesso"
      ]);

      return $response;
    }

    $response = $response->withJson([
      "message" => "Erro ao alterar a senha"
    ]);

    return $response;

  }

  public static function generateToken($len = 10) {

    $token = "poiuztrewqasdfghjklmnbvcxy1234567890";
		$token = str_shuffle($token);
		$token = substr($token, 0, $len);

		return $token;
  }

}

?>