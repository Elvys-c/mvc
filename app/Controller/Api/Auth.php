<?php

namespace App\Controller\Api;

use \App\Model\Entity\User;
use Firebase\JWT\JWT;

class Auth extends Api
{
  /**
   * Método responsável por gerar um token JWT
   * @param  Request $request
   * @return array
   */
  public static function generateToken($request)
  {
    $postVars = $request->getPostVars($request);

    if (!isset($postVars['email']) || !isset($postVars['senha'])) {
      throw new \Exception("Os campos 'email' e 'senha' são obrigatórios", 400);
    }

    $obUser = User::getUserByEmail($postVars['email']);

    if (!$obUser instanceof User) {
      throw new \Exception("O email ou senha são inválidos", 400);
    }

    if (!password_verify($postVars['senha'], $obUser->senha)) {
      throw new \Exception("O email ou senha são inválidos", 400);
    }

    $payload = ['email' => $obUser->email];
    $key = getenv('JWT_KEY');

    // echo "<pre>";
    // print_r($key);
    // echo "</pre>";
    // exit();

    return ['token' => JWT::encode($payload, $key, 'HS256')];
  }
}
