<?php

class Comments
{

    protected $db = '';
    protected $tpl = '';
    protected $artikul = '';
    protected $header = '';
    protected $goodsId = '';
    protected $url = '';
    protected $isAdmin = '';
    protected $commentsLengthInPage = '';// = $this->settings['num_news'];

    public function __construct($options = array())
    {
        $this->_setOptions($options);
        $this->_init();
    }

    protected function setFormValues($item)
    {

        $this->tpl->assign(array(
            'COMMENTS_FIO' => (isset($item['fio']) && !empty($item['fio']) ? $item['fio'] : ''),
            'COMMENTS_PERIOD_OF_OPERATION_INDEX_1' => (isset($item['period_of_operation']) && $item['period_of_operation'] == "Менее месяца" ? 'SELECTED' : ''),
            'COMMENTS_PERIOD_OF_OPERATION_INDEX_2' => (isset($item['period_of_operation']) && $item['period_of_operation'] == "от 1 до 3 мес." ? 'SELECTED' : ''),
            'COMMENTS_PERIOD_OF_OPERATION_INDEX_3' => (isset($item['period_of_operation']) && $item['period_of_operation'] == "от 3 до 12 мес." ? 'SELECTED' : ''),
            'COMMENTS_PERIOD_OF_OPERATION_INDEX_4' => (isset($item['period_of_operation']) && $item['period_of_operation'] == "от 1 до 3 лет" ? 'SELECTED' : ''),
            'COMMENTS_PERIOD_OF_OPERATION_INDEX_5' => (isset($item['period_of_operation']) && $item['period_of_operation'] == "свыше 3-х лет" ? 'SELECTED' : ''),
            'COMMENTS_DIGNITY' => (isset($item['dignity']) && !empty($item['dignity']) ? $item['dignity'] : ''),
            'COMMENTS_SHORTCOMINGS' => (isset($item['shortcomings']) && !empty($item['shortcomings']) ? $item['shortcomings'] : ''),
            'COMMENTS_RECOMMENDATIONS' => (isset($item['recommendations']) && !empty($item['recommendations']) ? $item['recommendations'] : ''),
            'COMMENTS_CONCLUSION' => (isset($item['conclusion']) && !empty($item['conclusion']) ? $item['conclusion'] : ''),
            'COMMENTS_RATE_CHECKED_1' => (isset($item['points']) && $item['points'] == '1' ? 'CHECKED' : ''),
            'COMMENTS_RATE_CHECKED_2' => (isset($item['points']) && $item['points'] == '2' ? 'CHECKED' : ''),
            'COMMENTS_RATE_CHECKED_3' => (isset($item['points']) && $item['points'] == '3' ? 'CHECKED' : ''),
            'COMMENTS_RATE_CHECKED_4' => (isset($item['points']) && $item['points'] == '4' ? 'CHECKED' : ''),
            'COMMENTS_RATE_CHECKED_5' => (isset($item['points']) && $item['points'] == '5' ? 'CHECKED' : ''),
        ));
        // var_dump($item['Conclusion']); die;
    }

    protected function _init()
    {
        if (!empty($this->tpl)) {
            $this->tpl->define_dynamic('_comments', 'comments.tpl');
            $this->tpl->define_dynamic('comments', '_comments');
            $this->tpl->define_dynamic('comments_captcha', 'comments');
            $this->tpl->define_dynamic('comments_items', 'comments');
            $this->tpl->define_dynamic('comment_admin_block', 'comments_items');
            $this->tpl->define_dynamic('comment_admin_block_admin_buttons', 'comment_admin_block');
            $this->tpl->define_dynamic('comment_vote_link', 'comments_items');
            $this->tpl->define_dynamic('comment_vote_no_link', 'comments_items');

            // Есил находим в сесси ошибки полей в комментах для этого товара оставляем открытой форму и вываливаем сообщения об ощибках

            $errorFioClassName = '';
            $errorConclusionClassName = '';
            $errorCaptchaClassName = '';
            $errorFioMessage = '';
            $errorConclusionMessage = '';
            $errorCaptchaMessage = '';

            if ($this->isAdmin) {
                $this->tpl->parse('COMMENTS_CAPTCHA', 'null');                    
            } else {
                $this->tpl->parse('COMMENT_ADMIN_BLOCK_ADMIN_BUTTONS', 'null');    
            }

            if (isset($_SESSION['comments']['error'][$this->goodsId]['name_1'])) {
                $errorFioClassName = 'error';
                $errorFioMessage = '<span id="name_1" class="f_error">' . $_SESSION['comments']['error'][$this->goodsId]['name_1'] . '</span>';
            }

            if (isset($_SESSION['comments']['error'][$this->goodsId]['name_5'])) {
                $errorConclusionClassName = 'error';
                $errorConclusionMessage = '<span id="name_5"  class="f_error">' . $_SESSION['comments']['error'][$this->goodsId]['name_5'] . '</span>';
            }

            if (isset($_SESSION['comments']['error'][$this->goodsId]['captcha'])) {
                $errorCaptchaClassName = 'error';
                $errorCaptchaMessage = '<span  id="captcha"  class="f_error f_protect_error">' . $_SESSION['comments']['error'][$this->goodsId]['captcha'] . '</span>';
            }
            if (isset($_SESSION['comments']['error'])) {
                unset($_SESSION['comments']['error']);
            }

            if (isset($_SESSION['comments']['isSave'])) {
                if (!$_SESSION['comments']['isSave']) {
                    $this->tpl->assign(array(
                        'FORM_STYLE' => 'block',
                        'MESSAGE_STYLE' => 'none',
                    ));
                } else {
                    $this->tpl->assign(array(
                        'FORM_STYLE' => 'none',
                        'MESSAGE_STYLE' => 'block',
                    ));
                }
                unset($_SESSION['comments']['isSave']);
            } else {
                $this->tpl->assign(array(
                    'FORM_STYLE' => 'none',
                    'MESSAGE_STYLE' => 'none',
                ));
            }

            if (!isset($_SESSION['comments']['data'])) {
                $_SESSION['comments']['data'] = array();
            }

            $this->setFormValues($_SESSION['comments']['data']);

            unset($_SESSION['comments']['data']);

            $this->tpl->assign(array(
                // Сообщения об ошибках                
                'ERROR_FIO_CLASS_NAME' => $errorFioClassName,
                'ERROR_FIO_MESSAGE' => $errorFioMessage,
                'ERROR_CONCLUSION_CLASS_NAME' => $errorConclusionClassName,
                'ERROR_CONCLUSION_MESSAGE' => $errorConclusionMessage,
                'ERROR_CAPTCHA_CLASS_NAME' => $errorCaptchaClassName,
                'ERROR_CAPTCHA_MESSAGE' => $errorCaptchaMessage,
            ));

            
            $action = '/commentsactions/add';
            
            if (isset($_SESSION['comments']['action'])) {
                $action = $_SESSION['comments']['action'];
                unset($_SESSION['comments']['action']);
            }

            $this->tpl->assign(array(
                'COMMENTS_GOODS_ID' => $this->goodsId,
                'COMMENTS_GOODS_HEADER' => $this->header,
                'COMMENTS_FORM_ACTION' => $action,
                'CAPTCHA_ID' => $this->getCapchaId()
            ));

           

            $this->tpl->parse('COMMENTS_ITEMS', 'null');
            $this->tpl->parse('COMMENT_VOTE_LINK', 'null');
            $this->tpl->parse('COMMENT_VOTE_NO_LINK', 'null');
        }
    }

    protected function _setOptions($options)
    {
        if (is_array($options) && !empty($options)) {
            foreach ($options as $key => $val) {
                if (isset($this->{$key})) {
                    $this->{$key} = $val;
                } elseif (isset($this->{"_$key"})) {
                    $this->{"_$key"} = $val;
                } else {
                    throw new Exception("Класс " . __CLASS__ . ". Не могу найти опцию $key;");
                }
            }
        }
    }

    public function items()
    {
        if (!empty($this->db)) {

            $page = 1;
            if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                $page = (int) $_GET['page'];

                if ($page < 1) {
                    $page = 1;
                }
            }


            $selectItem = $this->db->select();
            $selectItem->from('comments');
            $selectItem->where('goods_artikul = ?', $this->artikul);
            $selectItem->where('parent_id = ?', 0);
            $selectItem->order(array('date DESC'));




            $selectCount = $this->db->select();
            $selectCount->from('comments', array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));
            $selectCount->where('parent_id = ?', 0);
            $selectCount->where('goods_artikul = ?', $this->artikul);

            $this->tpl->define_dynamic('admin_buttons', 'comments_items');


            if (!$this->isAdmin) {
                 $selectItem->where('visible = "1"');
                  $selectCount->where('visible = "1"');
                  $this->tpl->parse('ADMIN_BUTTONS', 'null');
            }
            
            
            
            if (!is_numeric($this->commentsLengthInPage) || intval($this->commentsLengthInPage) <= 0) {
                $this->commentsLengthInPage = 5;
            }

            $adapter = new Zend_Paginator_Adapter_DbSelect($selectItem);
            $adapter->setRowCount($selectCount);

            $paginator = new Zend_Paginator($adapter);

            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage(2);
            $paginator->setPageRange(10);


            $navbar = $this->_loadPaginator($paginator, $this->url);

            $this->tpl->assign(
                    array(
                        'PAGINATION' => $navbar
                    )
            );

            if ($paginator) {

                foreach ($paginator as $res) {
                    
                    $getPartLink = (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0 ? "/?page=$_GET[page]" : '');
                    
                    $activeLinkText = 'Опубликовать';
                    $activeLinkUrl = "/commentsactions/activate/$res[id]$getPartLink";                    
                    if ($res['visible'] == '1') {
                        $activeLinkText = 'Опубликовано. Скрыть';
                        $activeLinkUrl = "/commentsactions/deactivate/$res[id]$getPartLink";
                    }
                    
                    $commentItemStyle = 'vote_bad';
                    $points = intval($res['points']);
                    if ($points == 3) {
                        $commentItemStyle = 'vote_normall';
                    } elseif ($points > 3) {
                        $commentItemStyle = 'vote_good';
                    }
                    
                    $this->tpl->assign(array(
                        'COMMENTS_ACTIVE_LINK_TEXT' => $activeLinkText,
                        'COMMENTS_ACTIVE_STYLE' => $commentItemStyle,
                        'COMMENTS_ACTIVE_LINK_URL' => $activeLinkUrl,
                        'COMMENTS_ITEM_ID' => $res['id'],
                        'COMMENTS_ITEM_GET_VALUE' => $getPartLink,
                        'COMMENTS_ITEM_DAY' => $this->getDate($res['date']),
                        'COMMENTS_ITEM_FIO' => $res['fio'],
                        'COMMENTS_ITEM_PERIOD_OF_OPERATION' => $res['period_of_operation'],
                        'COMMENTS_ITEM_DIGNITY' => $res['dignity'],
                        'COMMENTS_ITEM_CONCLUSION' => $res['conclusion'],
                        'COMMENTS_ITEM_RECOMMENDATIONS' => $res['recommendations'],
                        'COMMENTS_ITEM_SHORTCOMINGS' => $res['shortcomings'],
                        'COMMENTS_ITEM_POINTS' => $this->getPoints($res['points']),
                        'TIP_HELPFUL_YES' => (!empty($res['tip_helpful_yes']) ? $res['tip_helpful_yes'] : '0'),
                        'TIP_HELPFUL_NO' => (!empty($res['tip_helpful_no']) ? $res['tip_helpful_no'] : '0')
                    ));

                    if (isset($_SESSION['comment']['tip_helpful'][$res['id']]) && $_SESSION['comment']['tip_helpful'][$res['id']] == $res['id']) {
                        $this->tpl->parse('COMMENT_VOTE_NO_LINK', 'comment_vote_no_link');
                        $this->tpl->parse('COMMENT_VOTE_LINK', 'null');
                    } else {
                        $this->tpl->parse('COMMENT_VOTE_NO_LINK', 'null');
                        $this->tpl->parse('COMMENT_VOTE_LINK', 'comment_vote_link');
                    }

                    $this->adminMessage($res['id']);

                    $this->tpl->parse('COMMENTS_ITEMS', '.comments_items');
                }
            }
            $this->tpl->parse('COMMENTS', 'comments');
        }
    }

    protected function adminMessage($messageId)
    {

        $this->tpl->parse('COMMENT_ADMIN_BLOCK', 'null');

        if (($row = $this->db->fetchAll("SELECT `id`, `conclusion`, `date` FROM `comments` WHERE `parent_id` = '$messageId'"))) {

            foreach ($row as $res) {

                $this->tpl->assign(array(
                    'COMMENT_ADMIN_BLOCK_DATE' => $this->getDate($res['date']),
                    'COMMENT_ADMIN_BLOCK_CONCLUSION' => $res['conclusion'],
                    'COMMENTS_ITEM_ADMIN_ID' => $res['id']
                ));
                $this->tpl->parse('COMMENT_ADMIN_BLOCK', '.comment_admin_block');
            }
        }
    }

    protected function getDate($date)
    {
        $ret = '';
        if (!empty($date)) {
            list($year, $month, $day) = explode('-', $date);
            $monthArray = array(
                '00' => 'января',
                '01' => 'января',
                '02' => 'февраля',
                '03' => 'марта',
                '04' => 'апреля',
                '05' => 'мая',
                '06' => 'июня',
                '07' => 'июля',
                '08' => 'августа',
                '09' => 'сентября',
                '10' => 'октября',
                '11' => 'ноября',
                '12' => 'декабря'
            );
            $ret = $day . ' ' . $monthArray[$month] . ' ' . $year;
        }
        return $ret;
    }

    protected function getPoints($value = '')
    {
        if (is_numeric($value)) {
            return ' <span class="stars s_' . $value . '"></span>';
        }
    }

    protected function getCapchaId()
    {
        $captcha = new Zend_Captcha_Png(array(
                    'name' => 'cptch',
                    'wordLen' => 6,
                    'timeout' => 1800,
                ));
        $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
        $captcha->setStartImage('./img/captcha.png');
        return $captcha->generate();
    }

    protected function _loadPaginator(Zend_Paginator $paginator, $baseUrl = '', $delimiter = '?')
    {
        $pages = get_object_vars($paginator->getPages('Sliding'));

        if (!$pages['pageCount'] || $pages['pageCount'] <= 1) {
            return '';
        }

        $baseUrl = rtrim($baseUrl, '/');

        $navbar = '<div class="paginator">';

        if (isset($pages['previous'])) {
            $navbar .= '<a href="' . $baseUrl . '#comments">«</a>';
            $navbar .= '<a href="' . $baseUrl . ($pages['first'] == $pages['previous'] ? '' : $delimiter . 'page=' . $pages['previous']) . '#comments" class="an">&lsaquo;</a>';
        }

        foreach ($pages['pagesInRange'] as $page) {
            if ((int) $page == $pages['current']) {
                $navbar .= '<b>' . $page . '</b>';
            } else {
                $url = $baseUrl;
                if ($page > 1) {
                    $url .= $delimiter . 'page=' . $page;
                }

                $navbar .= '<a href="' . $url . '#comments">' . $page . '</a>';
            }
        }

        if (isset($pages['next'])) {
            $navbar .= '<a href="' . $baseUrl . $delimiter . "page=" . $pages['next'] . '#comments" class="an">&rsaquo;</a>';
            $navbar .= '<a href="' . $baseUrl . $delimiter . "page=" . $pages['last'] . '#comments">»</a>';
        }

        $navbar .= "<span class=\"count\">" . $pages['firstItemNumber'] . " - " . $pages['lastItemNumber'] . " из " . $pages['totalItemCount'] . "</span>";

        $navbar .= "</div>";

        return $navbar;
    }

}

?>
