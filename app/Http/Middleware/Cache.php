<?php

namespace App\Http\Middleware;

use \App\Utils\Cache\File as CacheFile;

class Cache
{

    /**
     * Método responsável por verificar se a request atual pode ser cacheada
     * @param  Request $request
     * @return boolean
     */
    private function isCacheable($request)
    {
      // Vadida o tempo de cache
      if (getenv('CACHE_TIME') <= 0) {
        return false;
      }

      // Valida o método da requisição
      if ($request->getHttpMethod() != 'GET') {
        return false;
      }

      // Valida o header de cache
      $headers = $request->getHeaders();
      if (isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache') {
        return false;
      }

      return true;
    }

    /**
     * Método responsável por retornar a hash do cache
     * @param  Request $request
     * @return string
     */
    private function getHash($request)
    {
      // URI da rota
      $uri = $request->getRouter()->getUri();

      // Query params
      $queryParams = $request->getQueryParams();
      $uri .= !empty($queryParams) ? '?'.http_build_query($queryParams) : '';

      // Remove as barras e retorna a hash
      return rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');
    }

    /**
     * Método responsável por executar o middleware
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {

      // Verifica se a request é cacheavel
      if (!$this->isCacheable($request)) return $next($request);

      $hash = $this->getHash($request);

      return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use ($request, $next){
        return $next($request);
      });
    }
}
