<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Search extends Main_Abstract implements Main_Interface
{
    private $_pageGroups;

    private $_catGroups;

    public function factory() {
        return true;
    }

    public function main()
    {
        $query = $this->getVar('q');

        if ($query == 'Поиск товаров') {
            $query = false;
        }

        if (!$query) {
            $this->addErr('Не задана поисковая фраза');
        } else {
            $query = strip_tags(htmlspecialchars(trim($query)));

            if (strlen($query) < 3) {
                $this->addErr('Поискова фраза не может быть короче 3-х символов');
            }
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        //$query = mb_strtolower($query, 'UTF-8');
        $navbar = $navTop = $navBot = '';

        $currentPage = 1;
        if (isset($this->getParam['page'])) {
            $currentPage = (int) $this->getParam['page'];

            if ($currentPage < 1) {
                return $this->error404();
            }
        }

        $this->tpl->define_dynamic('_search', 'search.tpl');
        $this->tpl->define_dynamic('search', '_search');
        $this->tpl->define_dynamic('search_row', 'search');

        $select = $this->db->select();
        $select->from('page', array('href', 'header', 'preview', 'body', 'level'));
        $select->where("type <> 'link'");
        $select->where("header LIKE ('%$query%')");
        $select->orWhere("preview LIKE ('%$query%')");
        $select->orWhere("body LIKE ('%$query%')");

        $pages = $this->db->fetchAll($select->__toString());

        foreach ($pages as $key => $value) {
            if (!$item = $this->_checkSearchQueryResult($value, $query)) {
                unset($pages[$key]);
                continue;
            }

            $item['table'] = 'page';
            $pages[$key] = $item;
        }


        $select = $this->db->select();
        $select->from('news', array('href', 'header', 'preview', 'body'));
        $select->where("header LIKE ('%$query%')");
        $select->orWhere("preview LIKE ('%$query%')");
        $select->orWhere("body LIKE ('%$query%')");

        $news = $this->db->fetchAll($select->__toString());

        foreach ($news as $key => $value) {
            if (!$item = $this->_checkSearchQueryResult($value, $query)) {
                unset($news[$key]);
                continue;
            }

            $item['table'] = 'news';
            $news[$key] = $item;
        }

        $select = $this->db->select();
        $select->from('catalog', array('href', 'header', 'preview', 'body', 'level'));
        $select->where("header LIKE ('%$query%')");
        $select->orWhere("preview LIKE ('%$query%')");
        $select->orWhere("body LIKE ('%$query%')");

        $catalog = $this->db->fetchAll($select->__toString());

        foreach ($catalog as $key => $value) {
            if (!$item = $this->_checkSearchQueryResult($value, $query)) {
                unset($catalog[$key]);
                continue;
            }

            $item['table'] = 'catalog';
            $catalog[$key] = $item;
        }



        $array = array_merge($pages, $news, $catalog);

        $adapter = new Zend_Paginator_Adapter_Array($array);
        //$adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(50);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, '/search?q=' . $query, '&');

        if ($navbar) {
            $navTop = $navbar;
            $navBot = $navTop;
        }

        if (count($paginator)) {
            foreach ($paginator as $row) {
                $this->tpl->assign(
                    array(
                        'SEARCH_HREF'       => $this->getSearchItemHref($row),
                        'SEARCH_NAME'       => $row['header'],
                        'SEARCH_PREVIEW'    => $row['preview'] == '' ? '&nbsp;' : $row['preview']
                    )
                );

                $this->tpl->parse('SEARCH_ROW', '.search_row');
            }

            $this->tpl->assign(
                array(
                    'SEARCH_QUERY'  => $query,
                    'SEARCH_COUNT'  => count($array),
                    'PAGES_TOP'     => $navTop,
                    'PAGES_BOTTOM'  => $navBot
                )
            );

            $this->tpl->parse('CONTENT', '.search');
        }

        return true;
    }

    private function _checkSearchQueryResult($item, $query)
    {
        $item['header']    = strip_tags($item['header']);
        $item['preview']   = strip_tags($item['preview']);
        $item['body']      = strip_tags($item['body']);

        $item['preview'] = mb_ereg_replace("&nbsp;", "", $item['preview'], 'i');

        $header     = strpos($item['header'], $query);
        $preview    = strpos($item['preview'], $query);
        $body       = strpos($item['body'], $query);

        //var_dump(mb_stripos($item['preview'], $query, 0, 'UTF-8'));

        if (false === $header && false === $preview && false === $body) {
            return false;
        }

        if (false !== $preview) {
            if ($preview > 500) {
                $start = $preview - 255;
                $item['preview'] = mb_strcut($item['preview'], $start, 500, 'UTF-8');
                $item['preview'] = "... " . $item['preview'] . " ...";
            } else {
                $item['preview'] = mb_strcut($item['preview'], 0, 500, 'UTF-8');
                $item['preview'] = $item['preview'] . " ...";
            }

            $item['preview'] = mb_ereg_replace($query, "<span>$query</span>", $item['preview'], 'i');
        } else {
            $item['preview'] = mb_strcut($item['preview'], 0, 500, 'UTF-8');
            $item['preview'] = $item['preview'] . " ...";
        }

        unset($item['body']);

        return $item;
    }

    private function getSearchItemHref($item)
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';

        if ($item['href'] == 'mainpage') {
            return $host;
        }

        if ($item['table'] == 'news') {
            return $host . 'news/' . $item['href'];
        }

        if ($item['table'] == 'page') {
            if ($item['level'] == 0) {
                return $host . $item['href'];
            }

            $groups = $this->_getPageGroups();

            $level = $item['level'];
            $url = $item['href'];

            while ($level > 0) {
                foreach ($groups as $value) {
                    if ($value['id'] == $level) {
                        $url = $value['href'] . '/' . $url;
                        $level = $value['level'];

                        break;
                    }
                }
            }

            return $host . $url;
        }

        if ($item['table'] == 'catalog') {
            $host .= 'catalog/';

            if ($item['level'] == 0) {
                return $host . $item['href'];
            }

            $groups = $this->_getCatGroups();

            $level = $item['level'];
            $url = $item['href'];

            while ($level > 0) {
                foreach ($groups as $value) {
                    if ($value['id'] == $level) {
                        $url = $value['href'] . '/' . $url;
                        $level = $value['level'];

                        break;
                    }
                }
            }

            return $host . $url;
        }

        return $host;
    }

    private function _getPageGroups()
    {
        if (null === $this->_pageGroups) {
            $this->_pageGroups = $this->db->fetchAll("SELECT * FROM `page` WHERE `type` = 'section'");
        }

        return $this->_pageGroups;
    }

    private function _getCatGroups()
    {
        if (null === $this->_catGroups) {
            $this->_catGroups = $this->db->fetchAll("SELECT * FROM `catalog` WHERE `type` = 'section'");
        }

        return $this->_catGroups;
    }
}