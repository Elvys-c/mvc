<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{

  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  public static function getTestimonyItems($request, &$obPagination)
  {
      $items = '';

      $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

      $results = EntityTestimony::getTestimonies(null, 'id ASC', $obPagination->getLimit());

      while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {

          $items .= View::render('admin/modules/testimonies/item', [
              'id' => $obTestimony->id,
              'nome' => $obTestimony->nome,
              'mensagem' => $obTestimony->mensagem,
              'data' => date('d/m/Y - H:i:s', strtotime($obTestimony->data))
          ]);

      }

      return $items;
  }

  /**
   * Método responsável por renderizar a view de listagem de depoimentos
   * @param  Request $request
   * @return string
   */
    public static function getTestimonies($request)
    {
      //Conteúdo da Home
      $content = View::render('admin/modules/testimonies/index', [
        'itens' => self::getTestimonyItems($request, $obPagination),
        'pagination' => parent::getPagination($request, $obPagination)
      ]);

      // Retorna a página completa
      return parent::getPanel('Depoimentos > WDEV', $content, 'testimonies');
    }

}
