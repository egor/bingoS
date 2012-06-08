<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Order extends Main_Abstract implements Main_Interface
{

    public function factory()
    {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $value = strip_tags($value);
                $value = preg_replace('/\{+|\}+/', 'kot', $value);
                $_POST[$key] = $value;
            }
        }

        return true;
    }

    public function main()
    {

        $goodsSession = new Zend_Session_Namespace('goods');
        if (isset($goodsSession->array['basket_id'])) {
            $this->orderDetail($goodsSession->array['basket_id']);
            unset($goodsSession->array['basket_id']);

            return true;
        }

        return $this->error404();
    }

    public function add()
    {
        $this->setMetaTags('Оформить заказ');
        $this->setWay('Оформить заказ');

        $goodsSession = new Zend_Session_Namespace('goods');

        $this->tpl->define_dynamic('_order_form', 'order.tpl');
        $this->tpl->define_dynamic('order_form', '_order_form');
        $this->tpl->define_dynamic('order_form_surname', 'order_form');
        $this->tpl->define_dynamic('delivery_service_list', 'order_form');
        $this->tpl->define_dynamic('order_empty', '_order_form');
        $this->tpl->define_dynamic('order_success', '_order_form');
        $this->tpl->define_dynamic('order_passport_data', 'order_form');

        $this->tpl->parse('DELIVERY_SERVICE_LIST', 'null');



        if (!isset($goodsSession->array['basket']['totalCount']) || $goodsSession->array['basket']['totalCount'] <= 0) {
            $this->tpl->parse('CONTENT', '.order_empty');
            return true;
        }

        $this->tpl->parse('ORDER_PASSPORT_DATA', 'null');
        $isShowPassportData = false;
        // Ищем в сессии нужно ли показывать инфу с паспортными данными
        if (isset($goodsSession->array['basket'])) {
            foreach ($goodsSession->array['basket'] as $key => $value) {
                if (is_numeric($key) && isset($value['passport_data'])) {
                    if ($value['passport_data'] == '1' && $value['status'] != 'deleted') {
                        $isShowPassportData = true;
                        $this->tpl->parse('ORDER_PASSPORT_DATA', '.order_passport_data');
                        break;
                    }
                }
            }
        }

        $userId = '-1';
        $surname = '';
        $name = '';
        $patronymic = '';
        $email = '';
        $cityPhone = '';
        $mobilePhone = '';
        $passport = '';
        $passportNumber = '';
        $whenThePassportIsGiven = '';
        $issuedPassport = '';
        $registrationAddress = '';
        $inn = '';

        $city = '';
        $street = '';
        $houseNum = '';
        $apNum = '';
        $dopInfo = '';
        $warehouseNumber = '';
        $insurance = '';
        $paymentMethod = '';
        $deliveryService = '';

        if (null !== $this->auth) {
            $userId = $this->auth->id;
            $surname = $this->auth->surname;
            $name = $this->auth->name;
            $patronymic = $this->auth->patronymic;
            $email = $this->auth->email;
            $cityPhone = $this->auth->city_phone;
            $mobilePhone = $this->auth->mobile_phone;
            $passport = $this->auth->passport;
            $passportNumber = $this->auth->passport_number;
            $whenThePassportIsGiven = $this->auth->when_the_passport_is_given;
            $issuedPassport = $this->auth->issued_passport;
            $registrationAddress = $this->auth->registration_address;
            $inn = $this->auth->inn;
        }

        if (!empty($_POST)) {
            $surname = $this->getVar('surname', '');
            $name = $this->getVar('name', '');
            $patronymic = $this->getVar('patronymic', '');
            $email = $this->getVar('email', '');
            $cityPhone = $this->getVar('city_phone', '');
            $mobilePhone = $this->getVar('mobile_phone', '');
            $city = $this->getVar('city', '');
            $street = $this->getVar('street', '');
            $houseNum = $this->getVar('house', '');
            $apNum = $this->getVar('ap_num', '');
            $dopInfo = $this->getVar('dop_info', '');
            $passport = $this->getVar('passport', '');
            $passportNumber = $this->getVar('passport_number', '');
            $whenThePassportIsGiven = $this->getVar('when_the_passport_is_given', '');
            $issuedPassport = $this->getVar('issued_passport', '');
            $registrationAddress = $this->getVar('registration_address', '');
            $inn = $this->getVar('inn', '');
            $warehouseNumber = $this->getVar('warehouse_number', '');
            $insurance = $this->getVar('insurance', '');
            $paymentMethod = $this->getVar('payment_method', '');
            $deliveryService = $this->getVar('delivery_service', '');
        }

        $isError = false;

        // Переменный для сообщений об ошибках

        $defaultErrorMessage = 'Это поле необходимо заполнить!';
        $defaultErrorClassName = 'error';

        $surnameErrorMessage = '';
        $surnameErrorClassName = '';

        $nameErrorMessage = '';
        $nameErrorClassName = '';

        $patronymicErrorMessage = '';
        $patronymicErrorClassName = '';

        $emailErrorMessage = '';
        $emailErrorClassName = '';

        $mobilePhoneErrorMessage = '';
        $mobilePhoneErrorClassName = '';

        $cityErrorMessage = '';
        $cityErrorClassName = '';

        $streetErrorMessage = '';
        $streetErrorClassName = '';

        $houseErrorMessage = '';
        $houseErrorClassName = '';

        $apNumErrorMessage = '';
        $apNumErrorClassName = '';


        $warehouseNumberErrorMessage = '';
        $warehouseNumberErrorClassName = '';

        $validate = new Zend_Validate_EmailAddress();

        $deliveryServiceErrorMessage = '';
        $deliveryServiceErrorClassName = '';
        
        $slideFormVisibleCssParam = 'none';
        $slideFormDeliveryServiceVisibleCssParam = 'none';



        if (!empty($_POST) && isset($_POST['surname'])) {
            if (!$surname) {
                $surnameErrorMessage = $defaultErrorMessage;
                $surnameErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (empty($name)) {
                $nameErrorMessage = $defaultErrorMessage;
                $nameErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (empty($patronymic)) {
                $patronymicErrorMessage = $defaultErrorMessage;
                $patronymicErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (empty($email)) {
                $emailErrorMessage = $defaultErrorMessage;
                $emailErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (!$validate->isValid($email)) {
                $emailErrorMessage = '{WRONG_EMAIL}';
                $emailErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (empty($mobilePhone)) {
                $mobilePhoneErrorMessage = $defaultErrorMessage;
                $mobilePhoneErrorClassName = $defaultErrorClassName;
                $isError = true;
            } elseif (empty($deliveryService)) {
                $deliveryServiceErrorMessage = 'Укажите способ доставки';
                $deliveryServiceErrorClassName = $defaultErrorClassName;
                $isError = true;
            } else {
                if ($deliveryService == 'К дому, офису') {
                    $slideFormVisibleCssParam = 'block';
                    $slideFormDeliveryServiceVisibleCssParam = 'none';

                    if (empty($city)) {
                        $cityErrorMessage = $defaultErrorMessage;
                        $cityErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($street)) {
                        $streetErrorMessage = $defaultErrorMessage;
                        $streetErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($houseNum)) {
                        $houseErrorMessage = $defaultErrorMessage;
                        $houseErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($apNum)) {
                        $apNumErrorMessage = $defaultErrorMessage;
                        $apNumErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    }
                } elseif ($deliveryService != 'Самовывоз') {

                    if (empty($warehouseNumber)) {

                       

                        $warehouseNumberErrorMessage = $defaultErrorMessage;
                        $warehouseNumberErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($city)) {
                        $cityErrorMessage = $defaultErrorMessage;
                        $cityErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($street)) {
                        $streetErrorMessage = $defaultErrorMessage;
                        $streetErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($houseNum)) {
                        $houseErrorMessage = $defaultErrorMessage;
                        $houseErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    } elseif (empty($apNum)) {
                        $apNumErrorMessage = $defaultErrorMessage;
                        $apNumErrorClassName = $defaultErrorClassName;
                        $isError = true;
                    }
                    
                    if ($isError) {
                         $slideFormVisibleCssParam = 'block';
                        $slideFormDeliveryServiceVisibleCssParam = 'block';
                    }
                    
                }
            }
            /*
              if ($isShowPassportData) {
              if (!$passport) {
              $isError = true;
              }

              if (!$passportNumber) {
              $isError = true;
              }

              if (!$whenThePassportIsGiven) {
              $isError = true;
              }

              if (!$issuedPassport) {
              $isError = true;
              }

              if (!$registrationAddress) {
              $isError = true;
              }

              if (!$inn) {
              $isError = true;
              }
              } */
        }

        
        $this->tpl->assign(array(
            'ORDER_FORM_SURNAME_ERROR_MESSAGE' => $surnameErrorMessage,
            'ORDER_FORM_SURNAME_ERROR_CLASS_NAME' => $surnameErrorClassName,
            'ORDER_FORM_NAME_ERROR_MESSAGE' => $nameErrorMessage,
            'ORDER_FORM_NAME_ERROR_CLASS_NAME' => $nameErrorClassName,
            'ORDER_FORM_PATRONYMIC_ERROR_MESSAGE' => $patronymicErrorMessage,
            'ORDER_FORM_PATRONYMIC_ERROR_CLASS_NAME' => $patronymicErrorClassName,
            'ORDER_FORM_EMAIL_ERROR_MESSAGE' => $emailErrorMessage,
            'ORDER_FORM_EMAIL_ERROR_CLASS_NAME' => $emailErrorClassName,
            'ORDER_FORM_MOBILE_PHONE_ERROR_MESSAGE' => $mobilePhoneErrorMessage,
            'ORDER_FORM_MOBILE_PHONE_CLASS_NAME' => $mobilePhoneErrorClassName,
            'ORDER_FORM_CITY_ERROR_MESSAGE' => $cityErrorMessage,
            'ORDER_FORM_CITY_ERROR_CLASS_NAME' => $cityErrorClassName,
            'ORDER_FORM_STREET_ERROR_MESSAGE' => $streetErrorMessage,
            'ORDER_FORM_STREET_ERROR_CLASS_NAME' => $streetErrorClassName,
            'ORDER_FORM_HOUSE_ERROR_MESSAGE' => $houseErrorMessage,
            'ORDER_FORM_HOUSE_ERROR_CLASS_NAME' => $houseErrorClassName,
            'ORDER_FORM_AP_NUM_ERROR_MESSAGE' => $apNumErrorMessage,
            'ORDER_FORM_AP_NUM_ERROR_CLASS_NAME' => $apNumErrorClassName,
            'ORDER_FORM_WAREHOUSE_NUMBER_ERROR_MESSAGE' => $warehouseNumberErrorMessage,
            'ORDER_FORM_WAREHOUSE_NUMBER_ERROR_CLASS_NAME' => $warehouseNumberErrorClassName,
        
            'ORDER_FORM_DELIVERY_SERVICE_ERROR_MESSAGE' => $deliveryServiceErrorMessage,
            'ORDER_FORM_DELIVERY_SERVICE_ERROR_CLASS_NAME' => $deliveryServiceErrorClassName,
                          
            
            'ORDER_DISPLAY_BLOCK' => $slideFormDeliveryServiceVisibleCssParam,
            'ORDER_DISPLAY_BLOCK1' => $slideFormVisibleCssParam,
            'ORDER_FORM_INSURANCE_SELECTED_INDEX_1' => ($insurance == 'на полную стоимость' ? 'SELECTED' : ''),
            'ORDER_FORM_INSURANCE_SELECTED_INDEX_2' => ($insurance == 'на минимальную стоимость' ? 'SELECTED ' : ''),
            'ORDER_FORM_PAYMENT_METHOD_SELECTED_INDEX_1' => ($paymentMethod == 'наличным платежом' ? 'SELECTED' : ''),
            'ORDER_FORM_PAYMENT_METHOD_SELECTED_INDEX_2' => ($paymentMethod == 'предоплата' ? 'SELECTED' : ''),
        ));


        if (!empty($_POST) && isset($_POST['surname']) && !$isError) {
            $data = array(
                'user_id' => $userId,
                'surname' => $surname,
                'name' => $name,
                'patronymic' => $patronymic,
                'email' => $email,
                'city_phone' => $cityPhone,
                'mobile_phone' => $mobilePhone,
                'city' => $city,
                'street' => $street,
                'house_num' => $houseNum,
                'ap_num' => $apNum,
                'dop_info' => $dopInfo,
                'passport' => $passport,
                'passport_number' => $passportNumber,
                'when_the_passport_is_given' => $whenThePassportIsGiven,
                'issued_passport' => $issuedPassport,
                'registration_address' => $registrationAddress,
                'inn' => $inn,
                'warehouse_number' => $warehouseNumber,
                'insurance' => $insurance,
                'payment_method' => $paymentMethod,
                'delivery_service' => $deliveryService,
                'total_summ' => (string) $goodsSession->array['basket']['totalSumm'],
                'total_count' => (string) $goodsSession->array['basket']['totalCount'],
                'date' => date('Y-m-d H:i:s')
            );

            $this->db->insert('orders', $data);
            $orderId = $this->db->lastInsertId();

            foreach ($goodsSession->array['basket'] as $key => $value) {
                if (is_numeric($key)) {
                    if ($value['status'] != 'deleted') {
                        $data = array(
                            'name' => $value['name'],
                            'brand' => $value['brand'],
                            'order_id' => "$orderId",
                            'goods_artikul' => $value['artikul'],
                            'count' => "$value[count]",
                            'cost' => "$value[cost]",
                            'total_summ' => "$value[totalSumm]",
                            'url' => $value['url']
                        );

                        $this->db->insert('orders_goods', $data);
                    }
                }
            }

            $body = $this->orderDetail($orderId, true);

            $host = $_SERVER['HTTP_HOST'];
            $subject = 'Заказ № ' . $orderId . ' с сайта: ' . $host;

            $mail = new Zend_Mail('utf-8');
            $mail->setFrom('webmaster@' . $host, $host);
            $mail->setSubject($subject);
            $mail->setBodyHtml($body);

            $validate = new Zend_Validate_EmailAddress();
            if ($validate->isValid($email)) {
                $mail->clearFrom();
                $mail->setFrom($email, $subject);
                $mail->addTo($email, '');
                $mail->send();
                $mail->clearRecipients();
            }

            $admin_emails = explode(',', $this->settings['admin_email']);

            foreach ($admin_emails as $email) {
                $mail->addTo(trim($email), 'Администратор');
                $mail->send();
                $mail->clearRecipients();
            }

            $goodsSession->array = array();

            $goodsSession->array['basket_id'] = $orderId;
            $content = "<meta http-equiv='refresh' content='0;URL=/order' />";
            $this->viewMessage($content);

            return true;
        }

        $this->tpl->assign(
                array(
                    'ORDER_FORM_DELIVERY_SERVICE_SELECTED2' => ($deliveryService == 'К дому, офису' ? 'selected' : ''),
                    'ORDER_FORM_DELIVERY_SERVICE_SELECTED1' => ($deliveryService == 'Самовывоз' ? 'selected' : '')
                )
        );

        $delivery = $this->db->fetchAll("SELECT `name` FROM `delivery` WHERE `language`='$this->lang'");

        if ($delivery) {
            foreach ($delivery as $res) {
                if (($deliverySelected = $this->getVar('delivery_service', false)) && $deliverySelected == $res['name']) {
                    $deliverySelected = 'selected';
                } else {
                    $deliverySelected = '';
                }

                $this->tpl->assign(
                        array(
                            'ORDER_FORM_DELIVERY_SERVICE_TEXT' => $res['name'],
                            'ORDER_FORM_DELIVERY_SERVICE_SELECTED' => $deliverySelected
                        )
                );

                $this->tpl->parse('DELIVERY_SERVICE_LIST', '.delivery_service_list');
            }
        }

        $this->tpl->assign(
                array(
                    'ORDER_FORM_SURNAME' => $surname,
                    'ORDER_FORM_NAME' => $name,
                    'ORDER_FORM_PATRONYMIC' => $patronymic,
                    'ORDER_FORM_EMAIL' => $email,
                    'ORDER_FORM_CITY_PHONE' => $cityPhone,
                    'ORDER_FORM_MOBILE_PHONE' => $mobilePhone,
                    'ORDER_FORM_CITY' => $city,
                    'ORDER_FORM_STREET' => $street,
                    'ORDER_FORM_HOUSE_NUM' => $houseNum,
                    'ORDER_FORM_AP_NUM' => $apNum,
                    'ORDER_FORM_DOP_INFO' => $dopInfo,
                    'ORDER_FORM_PASSPORT' => $passport,
                    'ORDER_FORM_PASSPORT_NUMBER' => $passportNumber,
                    'ORDER_FORM_WHEN_THE_PASSPORT_IS_GIVEN' => $whenThePassportIsGiven,
                  
                    'ORDER_FORM_ISSUED_PASSPORT' => $issuedPassport,
                    'ORDER_FORM_REGISTRATION_ADDRESS' => $registrationAddress,
                    'ORDER_FORM_INN' => $inn,
                    'ORDER_FORM_WAREHOUSE_NUMBER' => $warehouseNumber
                )
        );

        $this->tpl->parse('CONTENT', '.order_form');

        return true;
    }

    private function orderDetail($id, $isSendEmail = false)
    {
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



        $this->tpl->assign(
                array(
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
                    'ORDER_DETAIL_DOP_INFO' => (!empty($orderDetail['dop_info']) ? " <tr><td colspan=\"6\" align=\"left\"><b>Дополнительно:</b></td></tr> <tr><td colspan=\"6\" align=\"left\">$orderDetail[dop_info] </td></tr>" : ''),
                )
        );
        
        if ($isSendEmail) {
            $this->tpl->assign('ORDER_TEXT', '');
        }
   
           
            
            
        if (($row = $this->db->fetchAll("SELECT * FROM `orders_goods` WHERE `order_id` = '$orderDetail[id]'"))) {
            $orderCounter = 1;

            foreach ($row as $res) {
                $this->tpl->assign(
                        array(
                            'ORDER_DETAIL_LIST_COUNTER' => $orderCounter,
                            'ORDER_DETAIL_LIST_URL' => 'http://' . $_SERVER['HTTP_HOST'] . $res['url'],
                            'ORDER_DETAIL_LIST_NAME' => $res['name'],
                            'ORDER_DETAIL_LIST_ARTIKUL' => $res['goods_artikul'],
                            'ORDER_DETAIL_LIST_COST' => number_format($res['cost'], 0, '', ' '),
                            'ORDER_DETAIL_LIST_COUNT' => $res['count'],
                            'ORDER_DETAIL_LIST_SUMM' => number_format($res['total_summ'], 0, '', ' ')
                        )
                );

                $orderCounter++;

                $this->tpl->parse('ORDER_DETAIL_LIST', '.order_detail_list');
            }
        }

        $this->tpl->parse('CONTENT', '.order_detail');

        return $this->tpl->prnt_to_var();
    }

}