<?php

namespace App\Utils\Cache;



class File
{

  /**
   * Método responsável por retornar o caminho até o arquivo de cache
   * @param  string $hash
   * @return string
   */
  private static function getFilePath($hash)
  {
    $dir = getenv('CACHE_DIR');

    if (!file_exists($dir)) {
      mkdir($dir, 0755, true);
    }

    return $dir.'/'.$hash;

  }

  /**
   * Método responsável por guardar informação no cache
   * @param  string $hash
   * @param  mixed $content
   * @return boolean
   */
  private static function storageCache($hash, $content)
  {
    $serialize = serialize($content);

    // Obtém o caminho até o arquivo de cache
    $cacheFile = self::getFilePath($hash);

    return file_put_contents($cacheFile, $serialize);
  }

  /**
   * Método responsável por retornar o conteúdo gravado no cache
   * @param  string $hash
   * @param  integer $expiration
   * @return mixed
   */
  private static function getContentCache($hash, $expiration)
  {
    $cacheFile = self::getFilePath($hash);

    if (!file_exists($cacheFile)) {
      return false;
    }

    $createTime = filectime($cacheFile);
    $diffTime = time() - $createTime;

    if ($diffTime > $expiration) {
      return false;
    }

    $serialize = file_get_contents($cacheFile);

    return unserialize($serialize);
  }

  /**
   * Método responsável por obter uma informação do cache
   * @param  string $hash
   * @param  integer $expiration
   * @param  Closure $function
   * @return mixed
   */
  public static function getCache($hash, $expiration, $function)
  {

    // verifica o conteúdo gravado
    if ($content = self::getContentCache($hash, $expiration)) {
      return $content;
    }

    $content = $function();

    // Grava o retorno no cache
    self::storageCache($hash, $content);

    return $content;
  }
}
