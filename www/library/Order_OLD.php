<?php
require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Order extends Main_Abstract implements Main_Interface {

    public function factory() {
        return true;
    }

    public function main() {

        $goodsSession = new Zend_Session_Namespace('goods');
        if (isset($goodsSession->array['basket_id'])) {
            $this->orderDetail($goodsSession->array['basket_id']);
            unset($goodsSession->array['basket_id']);
            return true;
        }

        return $this->error404();
    }

    public function add() {

        $this->setMetaTags('Оформить заказ');
        $this->setWay('Оформить заказ');

        $fields = array('order_form_surname', 'order_form_name', 'order_form_patronymic', 'order_form_email', 'order_form_city_phone', 'order_form_mobile_phone', 'order_form_captcha');

        $this->tpl->define_dynamic('_order_form', 'order.tpl');
        $this->tpl->define_dynamic('order_form', '_order_form');
        $this->tpl->define_dynamic('order_form_surname', 'order_form');


        $goodsSession = new Zend_Session_Namespace('goods');
        //order_passport_data


        $this->tpl->define_dynamic('delivery_service_list', 'order_form');
        $this->tpl->define_dynamic('order_empty', 'order_form');
        $this->tpl->define_dynamic('order_success', 'order_form');
        $this->tpl->define_dynamic('order_passport_data', 'order_form');
        //$this->tpl->define_dynamic('order_captcha', 'order_form');
        $this->tpl->parse('ORDER_PASSPORT_DATA', 'null');
        $isShowPassportData = false;

        if (isset($goodsSession->array['basket'])) {
            // Ищем в сессии нужно ли показывать инфу с паспортными данными
            foreach ($goodsSession->array['basket'] as $key => $value) {
                if (is_numeric($key)) {
                    if (isset($value['passport_data']) && $value['passport_data'] == '1' && $value['status'] != 'deleted') {

                        $isShowPassportData = true;
                        break;
                    }
                }
            }
        }



        if ($isShowPassportData) {

            $this->tpl->parse('ORDER_PASSPORT_DATA', '.order_passport_data');
        }


        //if (isset($this->auth->id)) {
            $this->tpl->parse('ORDER_CAPTCHA', 'null');
        //}

        $this->tpl->parse('ORDER_EMPTY', 'null');
        $this->tpl->parse('ORDER_SUCCESS', 'null');

        $this->tpl->parse('DELIVERY_SERVICE_LIST', 'null');


        $isError = false;
        $numsErrorMessage = '';
        $nums = true;
        $data = array();

        if (!($surname = $this->getVar('surname', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->surname)) {
                $surname = $this->auth->surname;
            }
        } else {
            $data['surname'] = $surname;
        }

        if (!($name = $this->getVar('name', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->name)) {
                $name = $this->auth->name;
            }
        } else {
            $data['name'] = $name;
        }

        if (!($patronymic = $this->getVar('patronymic', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->patronymic)) {
                $patronymic = $this->auth->patronymic;
            }
        } else {
            $data['patronymic'] = $patronymic;
        }

        if (!($email = $this->getVar('email', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->email)) {
                $email = $this->auth->email;
            }
        } else {
            $data['email'] = $email;
        }

        if (!($cityPhone = $this->getVar('city_phone', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->city_phone)) {
                $cityPhone = $this->auth->city_phone;
            }
        } else {
            $data['city_phone'] = $cityPhone;
        }

        if (!($mobilePhone = $this->getVar('mobile_phone', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->mobile_phone)) {
                $mobilePhone = $this->auth->mobile_phone;
            }
        } else {
            $data['mobile_phone'] = $mobilePhone;
        }
        if (!($city = $this->getVar('city', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } else {
                $city = '';
            }
        } else {
            $data['city'] = $city;
        }

        if (!($street = $this->getVar('street', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['street'] = $street;
        }

        if (!($houseNum = $this->getVar('house_num', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['house_num'] = $houseNum;
        }

        if (!($apNum = $this->getVar('ap_num', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['ap_num'] = $apNum;
        }

        if (!($dopInfo = $this->getVar('dop_info', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['dop_info'] = $dopInfo;
        }

        if (!($passport = $this->getVar('passport', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->passport)) {
                $passport = $this->auth->passport;
            }
        } else {
            $data['passport'] = $passport;
        }

        if (!($passportNumber = $this->getVar('passport_number', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->passport_number)) {
                $passportNumber = $this->auth->passport_number;
            }
        } else {
            $data['passport_number'] = $passportNumber;
        }

        if (!($whenThePassportIsGiven = $this->getVar('when_the_passport_is_given', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->when_the_passport_is_given)) {
                $whenThePassportIsGiven = $this->auth->when_the_passport_is_given;
            }
        } else {
            $data['when_the_passport_is_given'] = $whenThePassportIsGiven;
        }

        if (!($issuedPassport = $this->getVar('issued_passport', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->issued_passport)) {
                $issuedPassport = $this->auth->issued_passport;
            }
        } else {
            $data['issued_passport'] = $issuedPassport;
        }

        if (!($registrationAddress = $this->getVar('registration_address', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->registration_address)) {
                $registrationAddress = $this->auth->registration_address;
            }
        } else {
            $data['registration_address'] = $registrationAddress;
        }

        if (!($inn = $this->getVar('inn', false))) {
            if (!empty($_POST)) {
                $isError = true;
            } elseif (isset($this->auth->inn)) {
                $inn = $this->auth->inn;
            }
        } else {
            $data['inn'] = $inn;
        }


        if (!($warehouseNumber = $this->getVar('warehouse_number', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['warehouse_number'] = $warehouseNumber;
        }

        if (!($insurance = $this->getVar('insurance', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['insurance'] = $insurance;
        }

        if (!($paymentMethod = $this->getVar('payment_method', false))) {
            if (!empty($_POST)) {
                $isError = true;
            }
        } else {
            $data['payment_method'] = $paymentMethod;
        }


        $deliveryServiceErrorMessage = '';
        if (!($deliveryService = $this->getVar('delivery_service', false))) {

            if (!empty($_POST)) {
                $isError = true;
            }
        } else {

            $isError = false;

            $this->tpl->assign(
                    array('ORDER_FORM_DELIVERY_SERVICE_SELECTED2' => ($deliveryService == 'К дому, офису' ? 'selected' : ''),
                        'ORDER_FORM_DELIVERY_SERVICE_SELECTED1' => ($deliveryService == 'Самовывоз' ? 'selected' : ''),
                    )
            );



            if ($deliveryService == '#') {
                $deliveryServiceErrorMessage = 'Вы не выбрали способ доставки';
                $deliveryService = false;
                $isError = true;
            } elseif ($deliveryService != 'Самовывоз') {
                if (!$city) {
                    $city = false;
                }

                if (!$street) {
                    $street = false;
                }

                if (!$houseNum) {
                    $houseNum = false;
                    $isError = true;
                    $numsErrorMessage = 'Это поле не должно быть пустым';
                    $nums = false;
                }

                if (!$apNum) {
                    $apNum = false;
                    $isError = true;
                    $numsErrorMessage = 'Это поле не должно быть пустым';
                    $nums = false;
                }

                if (!$houseNum && !$apNum) {
                    $isError = true;
                    $numsErrorMessage = 'Эти поля не должны быть пустыми';
                }
            }
        }



        //$captchaErrorMessage = '';
        //$isCaptchaError = false;

        if ($deliveryService !== false) {
            $data['delivery_service'] = $deliveryService;
        }

        if (($row = $this->db->fetchAll("SELECT `name` FROM `delivery` WHERE `language`='$this->lang'"))) {

            foreach ($row as $res) {


                if (($deliverySelected = $this->getVar('delivery_service', false)) && $deliverySelected == $res['name']) {
                    $deliverySelected = 'selected';
                } else {
                    $deliverySelected = '';
                }

                $this->tpl->assign(
                        array('ORDER_FORM_DELIVERY_SERVICE_TEXT' => $res['name'],
                            'ORDER_FORM_DELIVERY_SERVICE_SELECTED' => $deliverySelected
                        )
                );
                $this->tpl->parse('DELIVERY_SERVICE_LIST', '.delivery_service_list');
            }
        }



        /*$captcha = new Zend_Captcha_Png(array(
                    'name' => 'cptch',
                    'wordLen' => 6,
                    'timeout' => 1800,
                ));
        $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
        $captcha->setStartImage('./img/captcha.png');
        $id = $captcha->generate();*/



        $this->tpl->assign(
                array(
                    'ORDER_FORM_SURNAME' => (!$surname ? '' : $surname),
                    'ORDER_FORM_NAME' => (!$name ? '' : $name),
                    'ORDER_FORM_PATRONYMIC' => (!$patronymic ? '' : $patronymic),
                    'ORDER_FORM_EMAIL' => (!$email ? '' : $email),
                    'ORDER_FORM_CITY_PHONE' => (!$cityPhone ? '' : $cityPhone),
                    'ORDER_FORM_MOBILE_PHONE' => (!$mobilePhone ? '' : $mobilePhone),
                    'ORDER_FORM_CITY' => (!$city ? '' : $city),
                    'ORDER_FORM_STREET' => (!$street ? '' : $street),
                    'ORDER_FORM_HOUSE_NUM' => (!$houseNum ? '' : $houseNum),
                    'ORDER_FORM_AP_NUM' => (!$apNum ? '' : $apNum),
                    'ORDER_FORM_DOP_INFO' => (!$dopInfo ? '' : $dopInfo),
                    'ORDER_FORM_PASSPORT' => (!$passport ? '' : $passport),
                    'ORDER_FORM_PASSPORT_NUMBER' => (!$passportNumber ? '' : $passportNumber),
                    'ORDER_FORM_WHEN_THE_PASSPORT_IS_GIVEN' => (!$whenThePassportIsGiven ? '' : $whenThePassportIsGiven),
                    'ORDER_FORM_ISSUED_PASSPORT' => (!$issuedPassport ? '' : $issuedPassport),
                    'ORDER_FORM_REGISTRATION_ADDRESS' => (!$registrationAddress ? '' : $registrationAddress),
                    'ORDER_FORM_INN' => (!$inn ? '' : $inn),
                    //'ORDER_FORM_CAPTCHA_INPUT' => (!$captchaInput ? '' : $captchaInput),
                    'ORDER_FORM_WAREHOUSE_NUMBER' => (!$warehouseNumber ? '' : $warehouseNumber),
                    //'CAPTCHA_ID' => $id,
                    //'CAPTCHA_ERROR_CLASS_NAME' => ($isCaptchaError ? 'error' : ''),
                    //'CAPTCHA_ERROR' => $captchaErrorMessage
        ));






        if (!empty($_POST) && !$isError) {

            $this->tpl->parse('ORDER_FORM', 'null');

            if (isset($goodsSession->array['basket']['totalCount']) && $goodsSession->array['basket']['totalCount'] > 0) {
                $data['total_summ'] = (string) $goodsSession->array['basket']['totalSumm'];
                $data['total_count'] = (string) $goodsSession->array['basket']['totalCount'];
                $data['date'] = date('Y-m-d H:i:s');
                $userId = '-1';
                if (isset($this->auth->id)) {
                    $userId = $this->auth->id;
                }
                $data['user_id'] = $userId;
                $this->db->insert('orders', $data);

                $orderId = $this->db->lastInsertId();

                foreach ($goodsSession->array['basket'] as $key => $value) {

                    if (is_numeric($key)) {
                         if ($value['status'] != 'deleted') {
                        $data2 = array(
                            'name' => $value['name'],
                            'brand' => $value['brand'],
                            'order_id' => "$orderId",
                            'goods_artikul' => $value['artikul'],
                            'count' => "$value[count]",
                            'cost' => "$value[cost]",
                            'total_summ' => "$value[totalSumm]",
                            'url' => $value['url']
                        );

                        $this->db->insert('orders_goods', $data2);
                         }
                    }
                }
                //   unset($goodsSession->array['basket']);



                $body = $this->orderDetail($orderId);



                $host = $_SERVER['HTTP_HOST'];
                $subject = 'Заказ № ' . $orderId . ' с сайта: ' . $host;

                $mail = new Zend_Mail('utf-8');
                $mail->setFrom('webmaster@' . $host, $host);
                $mail->setSubject($subject);
                $mail->setBodyHtml($body);

                $validate = new Zend_Validate_EmailAddress();
                if ($validate->isValid($email)) {
                    $mail->addTo($email, '');
                    $mail->send();
                }

                $mail->clearRecipients();
                $mail->addTo($this->settings['admin_email'], '');
                $mail->send();

                $goodsSession->array = array();

                $this->tpl->define_dynamic('_order_success', 'order.tpl');
                $this->tpl->define_dynamic('order_success', '_order_success');
                $this->tpl->parse('CONTENT', '.order_success');
            }

            $goodsSession->array['basket_id'] = $orderId;
            $content = "<meta http-equiv='refresh' content='0;URL=/order' />";
            $this->tpl->parse('ORDER_FORM', 'null');
            $this->viewMessage($content);
        } else {

            if (!isset($goodsSession->array['basket']['totalCount'])) {
                $this->tpl->parse('CONTENT', '.order_empty');
                //order_empty
            } else {

                $this->tpl->parse('CONTENT', '.order_form');
            }
        }

        return true;
    }

    private function orderDetail($id) {

        $this->setMetaTags('Заказ оформлен');
        $this->setWay('Заказ оформлен');

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $sql = "SELECT * FROM `orders` WHERE `id` = '$id'";

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

        $this->tpl->define_dynamic('_order_detail', 'orders.tpl');
        $this->tpl->define_dynamic('order_detail', '_order_detail');
        $this->tpl->define_dynamic('deliver_service_fields', 'order_detail');
        $this->tpl->define_dynamic('passport_data', 'order_detail');
        $this->tpl->define_dynamic('deliver_service', 'order_detail');
        $this->tpl->define_dynamic('deliver_service', 'passport_data');
        $this->tpl->define_dynamic('order_detail_list', 'order_detail');
        $this->tpl->define_dynamic('order_detail_list_empty', 'order_detail');
        $this->tpl->parse('ORDER_DETAIL_LIST_EMPTY', 'null');
        $this->tpl->parse('ORDER_DETAIL_LIST', 'null');

        // Будет доработано. Должно читаться по специальному полю таблицы.
        $isShowPassportData = false;

        if (!$isShowPassportData) {
            $this->tpl->parse('PASSPORT_DATA', 'null');
        }

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
            'ORDER_DETAIL_TOTAL_SUMM' => number_format($orderDetail['total_summ'], 0, '', ' '),
            'ORDER_DETAIL_TOTAL_COUNT' => $orderDetail['total_count'],
        ));

        if (($row = $this->db->fetchAll("SELECT * FROM `orders_goods` WHERE `order_id` = '$orderDetail[id]'"))) {
            $orderCounter = 1;
            foreach ($row as $res) {
                $this->tpl->assign(array(
                    'ORDER_DETAIL_LIST_COUNTER' => $orderCounter,
                    'ORDER_DETAIL_LIST_URL' => 'http://' . $_SERVER['HTTP_HOST'] . $res['url'],
                    'ORDER_DETAIL_LIST_NAME' => $res['name'],
                    'ORDER_DETAIL_LIST_ARTIKUL' => $res['goods_artikul'],
                    'ORDER_DETAIL_LIST_COST' => number_format($res['cost'], 0, '', ' '),
                    'ORDER_DETAIL_LIST_COUNT' => $res['count'],
                    'ORDER_DETAIL_LIST_SUMM' => number_format($res['total_summ'], 0, '', ' '),
                ));
                $orderCounter++;
                $this->tpl->parse('ORDER_DETAIL_LIST', '.order_detail_list');
            }
        }

        //  var_dump($orderDetail);
        // die;
        $this->tpl->parse('CONTENT', '.order_detail');



        return $this->tpl->prnt_to_var();
    }

    private function isSetOrders() {
        $retVal = false;
        $goodsSession = new Zend_Session_Namespace('goods');

        if (isset($goodsSession->array['basket']['totalSumm']) && $goodsSession->array['basket']['totalSumm'] > 0) {
            $retVal = true;
        }

        if (!$retVal) {
            $this->tpl->define_dynamic('_order_empty', 'order.tpl');
            $this->tpl->define_dynamic('order_empty', '_order_empty');
            $this->tpl->parse('CONTENT', '.order_empty');
        }
        return $retVal;
    }

}
