<?php

class CatalogListItem
{
    private $_parentClass;

    private $_showEmptyPic = true;

    private $_showEmptyPrice = true;

    private $_isAdmin = false;

    private $_isStore = false;

    private $_items = array();

    private $_htmlParts = array();

    private $_goodsSession = null;

    public function __construct(Main_Abstract_Base $parentClass, $options = array())
    {
        $this->_parentClass = $parentClass;

        $this->setOptions($options);
        $this->_goodsSession = new Zend_Session_Namespace('goods');
    }

    public function setOptions($options = array())
    {
        if (!empty($options)) {
            if (isset($options['showEmptyPic']) && !empty($options['showEmptyPic'])) {
                $this->_showEmptyPic = (bool) $options['showEmptyPic'];
            }

            if (isset($options['showEmptyPrice']) && !empty($options['showEmptyPrice'])) {
                $this->_showEmptyPrice = (bool) $options['showEmptyPrice'];
            }

            if (isset($options['isAdmin']) && !empty($options['isAdmin'])) {
                $this->_isAdmin = (bool) $options['isAdmin'];
            }

            if (isset($options['isStore']) && !empty($options['isStore'])) {
                $this->_isStore = (bool) $options['isStore'];
            }
        }
    }

    public function setItems(array $items = array())
    {
        $this->_items = $items;
    }

    public function generateListHTML()
    {
        for ($i = 0; $i < count($this->_items); $i++) {
            $item = $this->_items[$i];

            $lastClass = ($i > 0 && $i%3 == 0 ? ' last' : '');

            $parentInfo = $this->_parentClass->dataTreeManager($item['id']);
            $itemUrl    = $parentInfo['links'];

            $this->loadImages($item, $itemUrl, $lastClass);
            $this->loadNames($item, $itemUrl, $lastClass);
            $this->loadPriceOld($item, $lastClass);
            $this->loadEconomy($item, $lastClass);
            $this->loadPriceNew($item, $lastClass);
            $this->loadBasket($item, $lastClass);
        }
    }

    public function loadImages($item, $itemUrl, $lastClass)
    {
        $imgSrc = '/img/no-foto/no-foto-200x180.gif';
        $imgAlt = 'Нет фото';
        $imgTitle = $imgAlt;

        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $item['pic'])) {
            $imgSrc = '/img/catalog/small_1/' . $item['pic'];
            $imgAlt = $item['pic_alt'];
            $imgTitle = $item['pic_title'];

            if (empty($imgAlt)) {
                $imgAlt = $item['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $item['name'];
            }
        } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $item['artikul'] . '.jpg')) {
            $imgSrc = '/img/catalog/small_1/' . $item['artikul'] . '.jpg';
            $imgAlt = $item['pic_alt'];
            $imgTitle = $item['pic_title'];

            if (empty($imgAlt)) {
                $imgAlt = $item['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $item['name'];
            }
        }

        $overlayImage = '';
        if ($this->_globalNovelty || $item['status'] == 'new') {
            $overlayImage = '<span class="p_new"></span>';
        } elseif ($this->_globalHit || $item['status'] == 'hit') {
            $overlayImage = '<span class="p_hit">';
        } elseif ($this->_globalAction || $item['status'] == 'action') {
            $overlayImage = '<span class="p_action">';
        }

        $this->_htmlParts['images'][] = '<li class="p_item'.$lastClass.'"><a href="/catalog/'.$itemUrl.'" title="'.$imgTitle.'">'.$overlayImage.'<img src="'.$imgSrc.'" class="p_img" alt="'.$imgAlt.'" title="'.$imgTitle.'" /></a></li>';
    }

    public function loadNames($item, $itemUrl, $lastClass)
    {
        $this->_htmlParts['names'][] = '<li class="p_item'.$lastClass.'"><a href="/catalog/'.$itemUrl.'" class="p_name" title="'.$item['name'].'">'.$item['name'].'</a></li>';
    }

    public function loadPriceOld($item, $lastClass)
    {
        if ($this->_isStore) {
            if (isset($item['cost_old'])) {
                if ($items['cost_old'] > $items['cost']) {
                    
                }
            }
        }
    }

    public function loadEconomy($item, $lastClass)
    {

    }

    public function loadPriceNew($item, $lastClass)
    {

    }

    public function loadBasket($item, $lastClass)
    {

    }
}