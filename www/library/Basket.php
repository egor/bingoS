<?php

//$_SESSION = array();

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Basket extends Main_Abstract implements Main_Interface
{

    public function factory()
    {
        return true;
    }

    public function main()
    {

        return $this->showBasket();
    }

    public function delete()
    {

        $errText = '';
        $goodsSession = new Zend_Session_Namespace('goods');

        $id = end($this->url);
        if (!is_numeric($id) || !isset($goodsSession->array['basket'][$id])) {
            $errText = 'Товар № ' . $id . ' не найден';
            $this->addErr($errText);
        } else {
            $goodsSession->array['basket'][$id]['status'] = 'deleted';
            $cost = $goodsSession->array['basket'][$id]['totalSumm'];
            $count = $goodsSession->array['basket'][$id]['count'];
            $goodsSession->array['basket']['totalSumm'] -= $cost;
            $goodsSession->array['basket']['totalCount'] -= $count;
        }
        //var_dump($goodsSession->array['basket']); die;
        //  var_dump($goodsSession->array['basket']);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if ($this->_err) {
                die(json_encode(array('error' => $errText)));
            }

            if (isset($goodsSession->array['basket'])) {
                die(json_encode($goodsSession->array['basket']));
            } else {
                die(json_encode(array('error' => 'empty')));
            }
        }
        return false;
        //return $this->showBasket('deleted');
    }

    protected function testDeletedRecord($id, $goodsSession)
    {
        if (isset($goodsSession->array['basket']) && is_array($goodsSession->array['basket']) && !empty($goodsSession->array['basket'])) {
            foreach($goodsSession->array['basket'] as $val) {
                if (isset($goodsSession->array['basket'][$id])) {
                    $goodsSession->array['basket'][$id]['status'] = 'active';
                }
            }
        }
    }

    public function add()
    {

        $goodsSession = new Zend_Session_Namespace('goods');


        if (isset($_POST['item_id']) && is_numeric($_POST['item_id']) && isset($_POST['item_count']) && is_numeric($_POST['item_count'])) {

            $id = intval($_POST['item_id']);
            $count = intval($_POST['item_count']);

            $this->testDeletedRecord($id, $goodsSession);


            if (($item = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id` = '$id' LIMIT 1"))) {



                $cost = (int) $item['cost'];
                //$cost = (int) number_format($item['cost'], 0, ', ', " ") ;

                if (!isset($goodsSession->array['basket']) && !isset($goodsSession->array['totalCount'])) {
                    $goodsSession->array['basket'] = array(
                        'totalCount' => 0,
                        'totalSumm' => 0
                    );
                }

                $status = 'active';

                if ($count == 0) {
                    $count = 1;
                }

                if ($count < 0) {
                    $count = -1;
                }

                if ($count == -1) {
                    $status = 'deleted';
                    $count = 1;
                }

                if (!isset($goodsSession->array['basket'][$id])) {
                    $id = $item['id'];
                }

                if (!isset($goodsSession->array['basket'][$id]['totalSumm'])) {
                    $goodsSession->array['basket'][$id] = array();
                }

                $brand = '';

                if (isset($item['brand'])) {
                    $brand = $item['brand'];
                } elseif (isset($item['proizvoditel'])) {
                    $brand = $item['proizvoditel'];
                }

                $url = "/catalog/";

                if (($dataTree = $this->dataTreeManager($id))) {
                    $url .= $dataTree['links'];
                }

                $passportData = '0';

                if (isset($item['passport_data'])) {
                    $passportData = $item['passport_data'];
                }

                $goodsSession->array['basket'][$id] = array(
                    'id' => $id,
                    'href' => $item['href'],
                    'count' => $count,
                    'artikul' => $item['artikul'],
                    'name' => $item['name'],
                    'cost' => $cost,
                    'totalSumm' => ($count * $cost),
                    'status' => $status,
                    'url' => $url,
                    'passport_data' => $passportData,
                    'brand' => $brand
                );
            }

            $totalSumm = 0;
            $totalCount = 0;
            foreach ($goodsSession->array['basket'] as $key => $value) {

                if (is_numeric($key) && isset($value['id']) && $value['id'] == $key) {
                    if ($value['status'] != 'deleted') {
                        $totalSumm += $value['totalSumm'];
                        $totalCount+= $value['count'];
                    }
                }
            }


            $goodsSession->array['basket']['totalCount'] = $totalCount;
            $goodsSession->array['basket']['totalSumm'] = $totalSumm;
        }
        // $goodsSession->array = array();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

            if (isset($goodsSession->array['basket']) && count($goodsSession->array['basket']) > 0) {
                die(json_encode($goodsSession->array['basket']));
            }
            die(json_encode(array('error' => 'empty')));
        }
        return false;
    }

    public function back()
    {

        $errText = '';
        $goodsSession = new Zend_Session_Namespace('goods');
        $id = end($this->url);
        if (!is_numeric($id) || !isset($goodsSession->array['basket'][$id])) {
            $errText = 'Товар № ' . $id . ' не найден';
            $this->addErr($errText);
        } else {
            $cost = $goodsSession->array['basket'][$id]['totalSumm'];
            $count = $goodsSession->array['basket'][$id]['count'];
            $goodsSession->array['basket'][$id]['status'] = 'active';
            $goodsSession->array['basket']['totalSumm'] += $cost;
            $goodsSession->array['basket']['totalCount'] += $count;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if ($this->_err) {
                die(json_encode(array('error' => $errText)));
            }

            if (isset($goodsSession->array['basket'])) {
                die(json_encode($goodsSession->array['basket']));
            } else {
                die(json_encode(array('error' => 'empty')));
            }
        }

        return false;

        //return $this->showBasket();
    }

    public function update()
    {
        if (isset($_POST['count_id']) && count($_POST['count_id']) > 0) {
            $goodsSession = new Zend_Session_Namespace('goods');
            foreach ($_POST['count_id'] as $id => $value) {

                if (isset($goodsSession->array[$id]['count'])) {
                    if ($value <= 0) {
                        $goodsSession->array[$id]['status'] = 'deleted';
                        $goodsSession->array[$id]['count'] = $goodsSession->array[$id]['count'];
                    } else {
                        $goodsSession->array[$id]['count'] = $value;
                    }
                }
            }
        }
        return $this->showBasket();
    }

    public function clear()
    {
        $this->setMetaTags('Очистка корзины');
        $this->setWay('Очистка корзины');
        $goodsSession = new Zend_Session_Namespace('goods');
        $goodsSession->array = array();
        die;
        //   return $this->showBasket();
    }

    protected function showBasket($action = 'active')
    {

        $this->setMetaTags('Корзина');
        $this->setWay('Корзина');

        $goodsSession = new Zend_Session_Namespace('goods');

        $totalSumm = 0;
        $totalCount = 0;
        $basketLength = 0;


        if (isset($goodsSession->array['basket']) && count($goodsSession->array['basket']) > 0) {
            $basketLength = count($goodsSession->array['basket']);
            foreach ($goodsSession->array['basket'] as $key => $value) {

                if (is_numeric($key) && isset($value['id']) && $value['id'] == $key) {
                    if ($value['status'] != 'deleted') {
                        $totalSumm += $value['totalSumm'];
                        $totalCount+= $value['count'];
                    }
                }
            }

            $goodsSession->array['basket']['totalCount'] = $totalCount;
            $goodsSession->array['basket']['totalSumm'] = $totalSumm;
        }


        //$goodsSession->array = array();
        $this->tpl->define_dynamic('_basket', 'basket.tpl');

        $this->tpl->define_dynamic('basket', '_basket');
        $this->tpl->define_dynamic('basket_items', 'basket');

        $this->tpl->parse('BASKET_BRANDS_LIST', 'null');
        $this->tpl->parse('BASKET_COST_LIST', 'null');

        if ($totalCount > 0) {



            $allSumm = 0;
            $counter = 1;



            if ($this->_err) {
                $this->viewErr();
            }



            foreach ($goodsSession->array['basket'] as $key => $item) {

                if (is_array($item)) {

                    $summ = $item['cost'];


                    $url = "/catalog/";

                    if (($dataTree = $this->dataTreeManager($item['id']))) {
                        $url .= $dataTree['links'];
                    }

                    $itemName = "<a href = '$url' title='$item[name]' > $item[name] </a>";

                    /* $itemName = $item['name'];

                      if ($item['status'] == 'active') {
                      $itemName = "<a href = '$url' title='$item[name]' > $item[name] </a>";
                      } */

                    $this->tpl->assign(array(
                        'BASKET_ITEM_ID' => $item['id'],
                        'BASKET_ITEM_COUNTER' => $counter,
                        'BASKET_ITEM_NAME' => $itemName,
                        'BASKET_ITEM_ARTIKUL' => $item['artikul'],
                        'BASKET_ITEM_COUNT' => $item['count'],
                        'BASKET_ITEM_COST' => number_format($item['cost'], 0, '', " "),
                        'BASKET_ITEM_SUMM' => number_format($item['totalSumm'], 0, '', " "),
                        'BASKET_ITEM_CLASS' => ($item['status'] == 'deleted' ? "class='disabled'" : ''),
                        'BASKET_ITEM_INPUT_DISABLED' => ($item['status'] == 'deleted' ? "disabled" : ''),
                        'BASKET_ITEM_INPUT_DISABLED_STYLE' => ($item['status'] == 'deleted' ? "background-color:#fff;" : ''),
                        'BASKET_ITEM_BUTTON_NAME' => ($item['status'] == 'deleted' ? '<img width="16" height="12" alt="" src="/img/back.png">Вернуть' : '<img width="14" height="16" alt="" src="/img/delete.gif">'),
                        'BASKET_ITEM_BUTTON_TEXT' => ($item['status'] == 'deleted' ? 'Вернуть' : '&nbsp;'),
                        'BASKET_ITEM_ACTION' => $item['status'] == 'active' ? 'delete' : 'return',
                        'BASKET_ITEM_ACTION_TEXT' => $item['status'] == 'active' ? 'Удалить' : 'Вернуть',
                    ));
                    $counter++;
                    $this->tpl->parse('BASKET_ITEMS', '.basket_items');
                }
            }


            $this->tpl->assign(
                    array(
                        'BASKET_ITEM_ALL_SUMM' => number_format($goodsSession->array['basket']['totalSumm'], 0, '', " "),
                        'BASKET_ITEM_ALL_COUNT' => number_format($goodsSession->array['basket']['totalCount'], 0, '', " ")
            ));
            $this->tpl->parse('CONTENT', '.basket');
        } else {
            $this->tpl->define_dynamic('basket_empty', '_basket');
            $this->tpl->parse('CONTENT', '.basket_empty');
        }

        return true;
    }

}