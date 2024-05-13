<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{

    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request)
    {
        $headers = $request->getHeaders();
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
          $decode = (array)JWT::decode($jwt, new Key(getenv('JWT_KEY'), 'HS256'));
        } catch (\Exception $e) {
          throw new \Exception("Token inválido.", 400);
        }


        $email = $decode['email'] ?? '';

        $obUser = User::getUserByEmail($email);

        return $obUser instanceof User ? $obUser : false;
    }

    /**
     * Método rsponsável por validar o acesso via JWT
     * @param  Request $request
     */
    private function auth($request)
    {
        if ($obUser = $this->getJWTAuthUser($request)) {
          $request->user = $obUser;
          return true;
        }

        throw new \Exception("Acesso negado.", 403);
    }

    /**
     * Método responsável por executar o middleware
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Realiza a validação do acesso via JWT
        $this->auth($request);

        // Executa o próximo nível do middleware
        return $next($request);
    }
}
