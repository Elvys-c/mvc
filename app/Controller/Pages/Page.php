<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page{

    /**
    * Método responsável por retornar o topo da página
    * @return string
    */
    private static function getHeader(){
        return View::render('pages/header');
    }

    /**
    * Método responsável por retornar o rodapé da página
    * @return string
    */
    private static function getFooter(){
        return View::render('pages/footer');
    }

    /**
     * Método responsável por retornar um link da paginação
     * 
     * @param array $queryParams
     * @param array $page
     * @param string $url
     * @return array
     */
    private static function getPaginationLink($queryParams, $page, $url, $label = null)
    {
        $queryParams['page'] = $page['page'];

        $link = $url.'?'.http_build_query($queryParams);

        return View::render('pages/pagination/link', [
                         'page' => $label ?? $page['page'],
                         'link' => $link,
                         'active' => $page['current'] ? 'active' : ''
        ]);
    } 

    /**
     * Método responsável por renderizar o layout da paginação
     * @param  Request $request
     * @param  Pagination $obPagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        $pages = $obPagination->getPages();

        if (count($pages) <= 1){
            return '';
        }

        $links = '';

        $url = $request->getRouter()->getCurrentUrl();
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $limit = getenv('PAGINATION_LIMIT');
        $middle = ceil($limit / 2);
        $start = $middle > $currentPage ? 0 : $currentPage - $middle;
        $limit = $limit + $start;

        if ($limit > count($pages)) {
            $diff = $limit - count($pages);
            $start = $start - $diff;
        }

        if ($start > 0) {
            $links .= self::getPaginationLink($queryParams, reset($pages), $url, '<<');
        }


        foreach ($pages as $page) {
            if ($page['page'] <= $start) continue;

            if ($page['page'] > $limit) {
                $links .= self::getPaginationLink($queryParams, end($pages), $url, '>>');
                break;
            }

            $links .= self::getPaginationLink($queryParams, $page, $url);
        }

        return View::render('pages/pagination/box', ['links' => $links]);
    }

    /**
    * Método responsável por retornar o conteúdo (view) da nossa pagina genérica
    * @return string
    */
    public static function getPage($title, $content){
        return View::render('pages/page',
            ['title' => $title,
             'header' => self::getHeader(),
             'content' => $content,
             'footer' => self::getFooter()]);
    }

}
