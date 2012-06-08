<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

require_once PATH . 'library/Pictures.php';

require_once PATH . 'library/FormManager.php';

class User extends Main_Abstract implements Main_Interface {

    public function factory() {

        if (!$this->_isUser()) {
            return false;
        }
        $this->tpl->define_dynamic('edit', 'adm/edit.tpl');

        return true;
    }

    public function main() {

        return $this->error404();
    }

    public function profile() {
        $this->setMetaTags('Мои данные');
        $this->setWay('Мои данные');
        $this->tpl->define_dynamic('_profile', 'profile.tpl');
        $this->tpl->define_dynamic('profile', '_profile');
        $this->saveProfile();

        $row = $this->db->fetchRow("SELECT * FROM `users` WHERE `id`='" . $this->auth->id . "'");

        $this->tpl->assign(array(
            'USER_PROFILE_ID' => (isset($row['id']) && !empty($row['id']) ? $row['id'] : ''),
            'USER_PROFILE_LOGIN' => (isset($row['login']) && !empty($row['login']) ? $row['login'] : ''),
            'USER_PROFILE_EMAIL' => (isset($row['email']) && !empty($row['email']) ? $row['email'] : ''),
            'USER_PROFILE_PRIVILEGE' => (isset($row['privilege']) && !empty($row['privilege']) ? $row['privilege'] : ''),
            'USER_PROFILE_IS_ACTIVE' => (isset($row['is_active']) && !empty($row['is_active']) ? $row['is_active'] : ''),
            'USER_PROFILE_NAME' => (isset($row['name']) && !empty($row['name']) ? $row['name'] : ''),
            'USER_PROFILE_SURNAME' => (isset($row['surname']) && !empty($row['surname']) ? $row['surname'] : ''),
            'USER_PROFILE_PATRONYMIC' => (isset($row['patronymic']) && !empty($row['patronymic']) ? $row['patronymic'] : ''),
            'USER_PROFILE_CITY_PHONE' => (isset($row['city_phone']) && !empty($row['city_phone']) ? $row['city_phone'] : ''),
            'USER_PROFILE_MOBILE_PHONE' => (isset($row['mobile_phone']) && !empty($row['mobile_phone']) ? $row['mobile_phone'] : ''),
            'USER_PROFILE_PASSPORT' => (isset($row['passport']) && !empty($row['passport']) ? $row['passport'] : ''),
            'USER_PROFILE_PASSPORT_NUMBER' => (isset($row['passport_number']) && !empty($row['passport_number']) ? $row['passport_number'] : ''),
            'USER_PROFILE_WHEN_THE_PASSPORT_IS_GIVEN' => (isset($row['when_the_passport_is_given']) && !empty($row['when_the_passport_is_given']) ? $row['when_the_passport_is_given'] : ''),
            'USER_PROFILE_ISSUED_PASSPORT' => (isset($row['issued_passport']) && !empty($row['issued_passport']) ? $row['issued_passport'] : ''),
            'USER_PROFILE_REGISTRATION_ADDRESS' => (isset($row['registration_address']) && !empty($row['registration_address']) ? $row['registration_address'] : ''),
            'USER_PROFILE_INN' => (isset($row['inn']) && !empty($row['inn']) ? $row['inn'] : ''),
        ));


        $this->tpl->parse('CONTENT', '.profile');
        return true;
    }

    protected function saveProfile() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                // print "\$$key = \$this->getVar('$key', '');<br>";
                // print "if ((\$$key = \$this->getVar('$key', false))) {<br>   \$data['$key'] = \$$key;<br>}<br>";
            }

            $oldPassword = $this->getVar('old_password', '');
            $password = $this->getVar('password', '');
            $passwordConfirm = $this->getVar('password_confirm', '');

            if (!empty($oldPassword) && !empty($password) && !empty($passwordConfirm) && isset($this->auth->id)) {
                if ($password == $passwordConfirm) {
                    $this->db->update('users', array(
                        'pass' => crypt($password, $this->cryptKey)
                            ), "id=" . $this->auth->id);
                }
            }

            $auth = Zend_Auth::getInstance();
            $user = $auth->getIdentity();

            //var_dump($user);

            $data = array();

            if (($email = $this->getVar('email', false))) {
                $user->email = $email;
                $data['email'] = $email;
            }
            if (($name = $this->getVar('name', false))) {
                $user->name = $name;
                $data['name'] = $name;
            }

            if (($surname = $this->getVar('surname', false))) {
                $user->surname = $surname;
                $data['surname'] = $surname;
            }

            if (($patronymic = $this->getVar('patronymic', false))) {
                $user->patronymic = $patronymic;
                $data['patronymic'] = $patronymic;
            }

            if (($city_phone = $this->getVar('city_phone', false))) {
                $user->city_phone = $city_phone;
                $data['city_phone'] = $city_phone;
            }

            if (($mobile_phone = $this->getVar('mobile_phone', false))) {
                $user->mobile_phone = $mobile_phone;
                $data['mobile_phone'] = $mobile_phone;
            }

            if (!empty($data) && isset($this->auth->id)) {
                $this->db->update('users', $data, "id=" . $this->auth->id);
            }

            $data = array();
            if (($passport = $this->getVar('passport', false))) {
                $user->passport = $passport;
                $data['passport'] = $passport;
            }
            if (($passport_number = $this->getVar('passport_number', false))) {
                $user->passport_number = $passport_number;
                $data['passport_number'] = $passport_number;
            }
            if (($when_the_passport_is_given = $this->getVar('when_the_passport_is_given', false))) {
                $user->when_the_passport_is_given = $when_the_passport_is_given;
                $data['when_the_passport_is_given'] = $when_the_passport_is_given;
            }
            if (($issued_passport = $this->getVar('issued_passport', false))) {
                $user->issued_passport = $issued_passport;
                $data['issued_passport'] = $issued_passport;
            }
            if (($registration_address = $this->getVar('registration_address', false))) {
                $user->registration_address = $registration_address;
                $data['registration_address'] = $registration_address;
            }
            if (($inn = $this->getVar('inn', false))) {
                $user->inn = $inn;
                $data['inn'] = $inn;
            }

            if (!empty($data) && isset($this->auth->id)) {
                $this->db->update('users', $data, "id=" . $this->auth->id);
            }
            //var_dump($user);
            //exit();
            $auth->getStorage()->write($user);

            /*
              ALTER TABLE `users` ADD `patronymic` VARCHAR( 255 ) NOT NULL ,
              ADD `city_phone` VARCHAR( 255 ) NOT NULL ,
              ADD `mobile_phone` VARCHAR( 255 ) NOT NULL ,
              ADD `passport` VARCHAR( 255 ) NOT NULL ,
              ADD `passport_number` VARCHAR( 255 ) NOT NULL ,
              ADD `when_the_passport_is_given` VARCHAR( 255 ) NOT NULL ,
              ADD `issued_passport` VARCHAR( 255 ) NOT NULL ,
              ADD `registration_address` VARCHAR( 255 ) NOT NULL ,
              ADD `inn` VARCHAR( 255 ) NOT NULL
             */
        }
        return true;
    }

    public function orders() {
        $this->setMetaTags('Мои заказы');
        $this->setWay('Мои заказы');

        $this->_orders($this->auth->id);

        return true;
    }

    // Заказы
    protected function _orders($userId=null)
    {

        if (!$this->getCatOption('isStore')) {
            return false;
        }


        $fields = $this->db->fetchAll('DESC `orders`');
        $orderBy = '`date`';
        $filterLink = '';
        $dateFilterLinkParams = '';
        $date1 = $this->getVar('date1');
        $date2 = $this->getVar('date2');
        $sqlDate = "";
        $userFilterQuery = '';

        if ($userId !== null) {
            $userFilterQuery = " WHERE `user_id`=\"$userId\" AND `status` != 'deleted-user' ";
        }

        if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $date1)) {
            $sqlDate .= " `date` >= '$date1 00:00:00'";
        }

        if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $date2)) {
            $sqlDate .= (!empty($sqlDate) ? " AND " : '') . " `date` <= '$date2 23:59:59'";
        }

        if (!empty($sqlDate)) {
            if (empty($userFilterQuery)) {
                $sqlDate = " WHERE " . $sqlDate;
            } else {
                $sqlDate = " AND " . $sqlDate;
            }
        }


        $userFilterQuery .= $sqlDate;

        $metaTexDays = "";




        $orderTypes = array(
            'retail' => 'Розница',
            'wholesale' => 'Опт'
        );

        $orderStatus = array(
            'no-paid' => 'Не оплачен',
            'paid' => 'Оплачен',
            'completed' => 'Выполнен',
            'booked' => 'Бронь',
            'advance' => 'Аванс',
            'deleted-user' => 'Удален пользователем'
        );

        $orderClass = array(
            'no-paid' => 'neoplachen',
            'paid' => 'oplachen',
            'completed' => 'done',
            'booked' => 'bron',
            'advance' => 'avans',
            'deleted-user' => 'Удален пользователем'
        );


        $sql = "SELECT *, date_format(`date`, '%d/%m/%Y  %H:%i' ) as dete_format FROM `orders` $userFilterQuery ORDER BY `id` DESC";

        $ordersList = $this->db->fetchAll($sql);

        if ($ordersList) {

            if ($userId === null) {
                $this->tpl->define_dynamic('_orders', 'adm/orders.tpl');
            } else {
                $this->tpl->define_dynamic('_orders', 'user_orders.tpl');
            }


            $this->tpl->define_dynamic('orders', '_orders');
            $this->tpl->define_dynamic('orders_list', 'orders');

            foreach ($ordersList as $orderItem) {
                $id = $orderItem['id'];
                $idItem = "00001";

                if ($id < 10) {
                    $idItem = "0000$id";
                }

                if ($id >= 10 && $id < 100) {
                    $idItem = "000$id";
                }

                if ($id >= 100 && $id < 1000) {
                    $idItem = "00$id";
                }

                if ($id >= 1000 && $id < 10000) {
                    $idItem = "0$id";
                }

                if ($id >= 10000) {
                    $idItem = $id;
                }

                if ($userId === null) {
                    $this->tpl->assign(array(
                        'ADM_ORDER_ID' => $id,
                        'ADM_ORDER_ID1' => $idItem,
                        'ADM_ORDER_FIO' => $orderItem['surname'] . ' ' . $orderItem['name'] . ' ' . $orderItem['patronymic'],
                        'ADM_ORDER_SUMM' => number_format($orderItem['total_summ'], 0, '', ' '),
                        'ADM_ORDER_CITY' => $orderItem['city'],
                        'ADM_ORDER_DATE' => $orderItem['dete_format'],
                        'ADM_ORDER_STATUS' => $orderStatus[$orderItem['status']],
                        'ADM_ORDER_CLASS' => $orderClass[$orderItem['status']]
                    ));
                } else {

                    $this->tpl->assign(array(
                        'ADM_ORDER_ID' => $id,
                        'ADM_ORDER_ID1' => $idItem,
                        'ADM_ORDER_SUMM' => number_format($orderItem['total_summ'], 0, '', ' '),
                        'ADM_ORDER_DATE' => $orderItem['dete_format'],
                        'ADM_ORDER_STATUS' => $orderStatus[$orderItem['status']],
                        'ADM_ORDER_CLASS' => $orderClass[$orderItem['status']]
                    ));
                }

                $this->tpl->parse('ORDERS_LIST', '.orders_list');
            }

            $this->tpl->parse('CONTENT', '.orders');
        } else {
            $this->tpl->define_dynamic('_orders_empty', 'adm/orders.tpl');
            $this->tpl->define_dynamic('orders_empty', '_orders_empty');
            $this->tpl->parse('CONTENT', '.orders_empty');
        }
        return true;
    }

    public function order_detail() {

        $this->_orderDetail($this->auth->id);
        return true;
    }

    // Заказ подробнее
    protected function _orderDetail($userId=null) {

        $this->setMetaTags('Просмотр заказа');
        $this->setWay('Просмотр заказа');

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $userFilterQuery = '';

        if ($userId !== null) {
            $userFilterQuery = " AND `user_id`='$userId'";
            $this->tpl->define_dynamic('_order_detail', 'user_orders.tpl');
        } else {
            $this->tpl->define_dynamic('_order_detail', 'adm/orders.tpl');
        }

        $sql = "SELECT * FROM `orders` WHERE `id` = '$id' $userFilterQuery";
        $orderDetail = $this->db->fetchRow($sql);

        //  foreach ($orderDetail as $key => $value) {
        //print "<tr>\n<td class=\"width\">:</td>\n<td> {ORDER_DETAIL_".strtoupper($key)."} </td> \n </tr> \n";
        // print "'ORDER_DETAIL_" . strtoupper($key) . "'=>\$orderDetail['$key'],\n";
        // }


        if (!$orderDetail) {
            $this->addErr("Описание заказа с номером $id не найдено");
            $this->viewErr();
            return true;
        }


        $this->tpl->define_dynamic('order_detail', '_order_detail');
        $this->tpl->define_dynamic('deliver_service_fields', 'order_detail');
        $this->tpl->define_dynamic('deliver_service', 'order_detail');
        $this->tpl->define_dynamic('order_detail_list', 'order_detail');
        $this->tpl->define_dynamic('order_detail_list_empty', 'order_detail');
        $this->tpl->parse('ORDER_DETAIL_LIST_EMPTY', 'null');
        $this->tpl->parse('ORDER_DETAIL_LIST', 'null');

        $id = $orderDetail['id'];
        $idItem = "00001";

        if ($id < 10) {
            $idItem = "0000$id";
        }

        if ($id >= 10 && $id < 100) {
            $idItem = "000$id";
        }

        if ($id >= 100 && $id < 1000) {
            $idItem = "00$id";
        }

        if ($id >= 1000 && $id < 10000) {
            $idItem = "0$id";
        }

        if ($id >= 10000) {
            $idItem = $id;
        }

        if (empty($orderDetail['delivery_service']) || $orderDetail['delivery_service'] == 'Самовывоз') {
            $this->tpl->parse('DELIVER_SERVICE_FIELDS', 'null');
            $this->tpl->parse('DELIVER_SERVICE', 'null');
        } elseif ($orderDetail['delivery_service'] == 'К дому, офису') {
            $this->tpl->parse('DELIVER_SERVICE_FIELDS', 'null');
        }



        $this->tpl->assign(array(
            'ORDER_DETAIL_ID' => $idItem,
            'ORDER_DETAIL_SURNAME' => $orderDetail['surname'],
            'ORDER_DETAIL_NAME' => $orderDetail['name'],
            'ORDER_DETAIL_PATRONYMIC' => $orderDetail['patronymic'],
            'ORDER_DETAIL_EMAIL' => $orderDetail['email'],
            'ORDER_DETAIL_CITY_PHONE' => $orderDetail['city_phone'],
            'ORDER_DETAIL_MOBILE_PHONE' => $orderDetail['mobile_phone'],
            'ORDER_DETAIL_DELIVERY_SERVICE' => $orderDetail['delivery_service'],
            'ORDER_DETAIL_WAREHOUSE_NUMBER' => $orderDetail['warehouse_number'],
            'ORDER_DETAIL_INSURANCE' => $orderDetail['insurance'],
            'ORDER_DETAIL_PAYMENT_METHOD' => $orderDetail['payment_method'],
            'ORDER_DETAIL_CITY' => $orderDetail['city'],
            'ORDER_DETAIL_STREET' => $orderDetail['street'],
            'ORDER_DETAIL_HOUSE_NUM' => $orderDetail['house_num'],
            'ORDER_DETAIL_AP_NUM' => $orderDetail['ap_num'],
            'ORDER_DETAIL_DOP_INFO' => $orderDetail['dop_info'],
            'ORDER_DETAIL_PASSPORT' => $orderDetail['passport'],
            'ORDER_DETAIL_PASSPORT_NUMBER' => $orderDetail['passport_number'],
            'ORDER_DETAIL_WHEN_THE_PASSPORT_IS_GIVEN' => $orderDetail['when_the_passport_is_given'],
            'ORDER_DETAIL_ISSUED_PASSPORT' => $orderDetail['issued_passport'],
            'ORDER_DETAIL_REGISTRATION_ADDRESS' => $orderDetail['registration_address'],
            'ORDER_DETAIL_INN' => $orderDetail['inn'],
            'ORDER_DETAIL_TOTAL_SUMM' => $orderDetail['total_summ'],
            'ORDER_DETAIL_TOTAL_COUNT' => $orderDetail['total_count'],
        ));

        if (($row = $this->db->fetchAll("SELECT * FROM `orders_goods` WHERE `order_id` = '$orderDetail[id]'"))) {
            $orderCounter = 1;
            foreach ($row as $res) {
                $this->tpl->assign(array(
                    'ORDER_DETAIL_LIST_URL' => $res['url'],
                    'ORDER_DETAIL_LIST_COUNTER' => $orderCounter,
                    'ORDER_DETAIL_LIST_NAME' => $res['name'],
                    'ORDER_DETAIL_LIST_ARTIKUL' => $res['goods_artikul'],
                    'ORDER_DETAIL_LIST_COST' => $res['cost'],
                    'ORDER_DETAIL_LIST_COUNT' => $res['count'],
                    'ORDER_DETAIL_LIST_SUMM' => $res['total_summ'],
                ));
                $this->tpl->parse('ORDER_DETAIL_LIST', '.order_detail_list');
                $orderCounter++;
            }
        }

        //  var_dump($orderDetail);
        // die;
        $this->tpl->parse('CONTENT', '.order_detail');

        return true;
    }

    public function orderdelete() {
        $this->setMetaTags('Удаление заказа');
        $this->setWay('Удаление заказа');

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $sql = "UPDATE `orders` SET `status`='deleted-user' WHERE `id`='$id' AND `user_id`='" . $this->auth->id . "'";
        $this->db->query($sql);

       $content = "Заказ удален<meta http-equiv='refresh' content='2;URL=/user/orders'>";

        $this->viewMessage($content);
        return true;
    }

}

?>
