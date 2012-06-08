<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><HEAD><TITLE>{TITLE}</TITLE>
        <meta name="keywords" content="{KEYWORDS}">
        <meta name="description" content="{DESCRIPTION}">
        <META http-equiv=Content-Type content="text/html; charset=utf-8">
        <META NAME="Robots" CONTENT="index,follow">
        <META NAME="Copyright" CONTENT="2012 bingo.in.ua">
        <LINK href="/css/style.css" type="text/css" rel=stylesheet>
        <script type="text/javascript" src="http://3dstudio.in.ua/js/swfObject_2.js"></script>
        <LINK href="/js/jquery-plugins/ui/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" rel=stylesheet>

        <script type="text/javascript" src="/js/jquery-1.5.1.min.js"></script>
        <script type="text/javascript" src="/js/jq-validate.js"></script>
        <script type="text/javascript" src="/js/jq-scripts.js"></script>
        <script type="text/javascript" src="/js/jq-basket.js"></script>
        <script type="text/javascript" src="/js/jquery-plugins/ui/js/jquery-ui-1.8.14.custom.min.js"></script>

        <link rel="stylesheet" type="text/css" href="/js/star-rating/jquery.rating.css" />
        <script type="text/javascript" src="/js/star-rating/jquery-rating-pack.js"></script>

        <link  rel="stylesheet" type="text/css" href="/css/jquery.lightbox-0.5.css" />
        <SCRIPT type="text/javascript" src='/js/jquery.lightbox-0.5.min.js'></SCRIPT>
        <script type="text/javascript" src="/js/jq-light-box-init.js"></script>



        <!-- BDP: adminjslib -->
        <script type="text/javascript" src="/js/jq-adm-scripts.js"></script>
        <link rel="stylesheet" type="text/css" href="/js/jquery-plugins/jq-tree/themes/default/style.css" />
        <script type="text/javascript" src="/js/jquery-plugins/jq-tree/_lib/jquery.cookie.js"></script>
        <script type="text/javascript" src="/js/jquery-plugins/jq-tree/_lib/jquery.hotkeys.js"></script>
        <script type="text/javascript" src="/js/jquery-plugins/jq-tree/jquery.jstree.js"></script>

        <script type="text/javascript" src="/js/jq-check-all.js"></script>


        <!-- EDP: adminjslib -->

        <script type="text/javascript" src="http://userapi.com/js/api/openapi.js?48"></script>

        <script type="text/javascript">
        VK.init({apiId: 2835961, onlyWidgets: true});
        </script>

    </HEAD>
    <body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

        <div class="wraper">
            <div class="header">
                <!-- BDP: is_index_page -->
                <img class="logo" src="/img/logo.gif" width="241" height="58" alt="{LOGOALT}" title="{LOGOALT}" />
                <!-- EDP: is_index_page -->
                <!-- BDP: is_no_index_page -->
                <a href="/" class="logo" title="{LOGOALT}"><img src="/img/logo.gif" width="241" height="58" alt="{LOGOALT}" title="{LOGOALT}" /></a>
                <!-- EDP: is_no_index_page -->
                <p class="slogan">{LOGOTITLE}</p>
                <p class="top_text">{SLG2}</p>
                <p class="phone">Наши телефоны:<br />{SLG2_PHONES}</p>
                <p class="phone phone2">Мы работаем:<br />{SLG2_OTHER}</p>
                <!--<div class="enter_exit"><a href="{ENTER_SITE_URL}">{ENTER_SITE_TITLE}</a><span>|</span><a href="{ENTER_SITE_URL2}" {ENTER_SITE_URL2_ID}>{ENTER_SITE_URL2_TITLE}</a></div>-->
                <!-- BDP: basket_block -->

                <div class="busket basket-empty"  id="{EMPTY_BASKET_ID_STATUS}">
                    <p class="goods_c"><strong>Корзина</strong><span class="right_txt"><span>нет товаров</span></span></p>
                    <p class="goods-d">К оплате:<span class="right_txt"><strong class="none">0 грн.</strong></span></p>
                    <p class="nobord"><span>Ваша корзина пуста</span></p>
                </div>
                <div class="busket busket_bg" id="{BASKET_ID_STATUS}">
                    <p class="goods_c"><strong><a href="/basket">Корзина</a></strong><span class="right_txt"><a href="/basket">{BASKET_TOTAL_COUNT} {BASKET_TOTAL_COUNT_TEXT}</a></span></p>
                    <p class="goods-d"><a href="/basket">К оплате:</a><span class="right_txt"><strong><a href="/basket">{BASKET_TOTAL_SUMM} грн.</a></strong></span></p>
                    <p class="order"><a href="/basket">Перейти в корзину</a></p>
                </div>

                <!-- EDP: basket_block -->

                <div class="top_menu_block">
                    <table>
                        <tr>

                            <!-- BDP: horisontal -->
                            <td {H_MENU_CLASS_NAME}><a href="{MENU_HREF}" title="{MENU_NAME}">{MENU_NAME}</a></td>
                            <!-- EDP: horisontal -->

                        </tr>
                    </table>
                </div>
                <ul class="pic_menu">
                    <li class="active"><a class="home" href="/" title="{TITLEMAIN}">&nbsp;</a></li>
                    <li class="pad"><a href="/sitemap" title="{TITLEMAP}" class="sitemap">&nbsp;</a></li>
                    <li><a href="javascript:void(0);" onclick="return bookmark(this);" title="{TITLEFEED}"  class="email">&nbsp;</a></li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
        <!-- BDP: top_banner_block -->
        <div class="top_banner_list">
            <ul>
                <li>{TOP_BANNER_LEFT}</li>
                <li>{TOP_BANNER_RIGHT}</li>
            </ul>
            <div class="clear"></div>
        </div>
        <!-- EDP: top_banner_block -->

        <div class="wraper">
            <div class="pager">
                <ul class="sf-menu">
                    {WAY}
                </ul>
                <div class="clear"></div>
            </div>
            <div class="left_column">

                <div class="left_menu">

                    <!-- BDP: administration -->
                    <div class="user-menu">
                        <span><img src="/img/admin_icons/admin-header.png" width="16" height="16">Администрирование</span>

                        <br>
                    </div>

                    <ul class="ul-user-menu">
                        <li><img src="/img/admin_icons/admin-home.png" width="16" height="16" alt="Главная страница"><a href='{BASE_PATH}admin/editpage/mainpage' title="Главная страница">Главная страница</a></li>
                        <li><img src="/img/admin_icons/admin-menu.png" width="16" height="16" alt="Горизонтальное меню"><a href='{BASE_PATH}admin/menu/horisontal' title="Горизонтальное меню">Горизонтальное меню</a></li>
                        <li><img src="/img/admin_icons/admin-news.png" width="16" height="16" alt="Новости"><a href='{BASE_PATH}news' title="Новости">Новости</a></li>
                        <li><img src="/img/admin_icons/admin-orders.png" width="16" height="16" alt="База заказов"><a href='{BASE_PATH}admin/orders/' title="База заказов">База заказов</a></li>
                        <li><img src="/img/admin_icons/admin-delivery.png" width="16" height="16" alt="Службы доставки"><a href='{BASE_PATH}admin/deliveryservice/' title="Службы доставки">Службы доставки</a></li>
                        <li><img src="/img/admin_icons/admin-catalog.png" width="16" height="16" alt="Каталог"><a href='{BASE_PATH}catalog/' title="Каталог">Каталог</a></li>
                        <li><img src="/img/admin_icons/admin-catalog-options.png" width="16" height="16" alt="Каталог"><a href='{BASE_PATH}admin/catalogoptions/' title="Настройки каталога">Настройки каталога</a></li>
                        <li><img src="/img/admin_icons/admin-import.png" width="16" height="16" alt="Импорт товаров"><a href='{BASE_PATH}admin/import/' title="Импорт товаров">Импорт товаров</a></li>
                        <li><img src="/img/admin_icons/admin-export.png" width="16" height="16" alt="Экспорт товаров"><a href='{BASE_PATH}admin/export/' title="Экспорт товаров">Экспорт товаров</a></li>
                        <li><img src="/img/admin_icons/admin-catalog.png" width="16" height="16" alt="Каталог"><a href='{BASE_PATH}admin/comments/' title="Комментарии">Комментарии</a></li>

                        <li><img src="/img/admin_icons/admin-upload.png" width="16" height="16" alt="Загрузка картинок каталога"><a href='{BASE_PATH}admin/loadcatpics/' title="Загрузка картинок каталога">Загрузка картинок каталога</a></li>
                        <li><img src="/img/admin_icons/admin-options.png" width="16" height="16" alt="Настройки сайта"><a href='{BASE_PATH}admin/settings' title="Настройки сайта">Настройки сайта</a></li>
                        <li><img src="/img/admin_icons/admin-baners.png" width="16" height="16" alt="Баннеры"><a href='{BASE_PATH}admin/banners/' title="Баннеры">Баннеры</a></li>
                        <li><img src="/img/admin_icons/admin-lookups.png" width="16" height="16" alt="Редактируемые поля"><a href='{BASE_PATH}admin/lookups' title="Редактируемые поля">Редактируемые поля</a></li>
                        <li><img src="/img/admin_icons/admin-meta-tags.png" width="16" height="16" alt="Мета-Теги"><a href='{BASE_PATH}admin/metatags' title="Мета-Теги">Мета-Теги</a></li>
                        <li><img src="/img/admin_icons/exit.png" width="16" height="16" alt="Мета-Теги"><a href='{BASE_PATH}logout' title="Выход">Выход</a></li>
                    </ul>

                    <!-- EDP: administration -->


                    <!-- BDP: user_menu -->
                    <div class="user-menu">
                        <span><img src="/img/user_menu_avatar.gif" width="16" height="16">Мой кабинет</span>
                        <br>
                    </div>

                    <ul class="ul-user-menu">
                        <li><img src="/img/admin_icons/my-data.png" width="16" height="16" alt="Мои данные"><a href='{BASE_PATH}user/profile' title="Мои данные">Мои данные</a></li>
                        <li><img src="/img/admin_icons/tachka.png" width="16" height="16" alt="Мои заказы"><a href='{BASE_PATH}user/orders' title="Мои заказы">Мои заказы</a></li>
                        <li><img src="/img/admin_icons/exit.png" width="16" height="16" alt="Выход"><a href='{BASE_PATH}logout' title="Выход">Выход</a></li>
                    </ul>


                    <!-- EDP: user_menu -->
                    <div class="search_block"><form method="get" action="/search"><input type="text" onblur="if (this.value==''){this.value='введите запрос'}" onfocus="if (this.value=='введите запрос'){this.value=''}" onclick="value=''" name="q" value="Поиск товаров" id="email"></form></div>


                    <ul>




                        <!-- BDP: catalog_menu -->

                        <!-- BDP: catalog_menu_complex -->
                        <li><a class="{CATALOG_MENU_ACTIVE}" href='/catalog{CATALOG_MENU_HREF}' title='{CATALOG_MENU_NAME}'>{CATALOG_MENU_NAME}</a>
                            <ul>
                                <!-- BDP: catalog_menu_complex_sub -->
                                <li><a class="{CATALOG_SUBMENU_ACTIVE}" href="/catalog{CATALOG_MENU_HREF}"  title='{CATALOG_MENU_NAME}'>{CATALOG_MENU_NAME}</a></li>
                                <!-- EDP: catalog_menu_complex_sub -->
                            </ul>
                        </li>
                        <!-- EDP: catalog_menu_complex -->

                        <!-- BDP: catalog_menu_single -->
                        <li><a class="{CATALOG_MENU_ACTIVE}" href='/catalog{CATALOG_MENU_HREF}' title='{CATALOG_MENU_NAME}'>{CATALOG_MENU_NAME}</a></li>
                        <!-- EDP: catalog_menu_single -->
                        <!-- EDP: catalog_menu -->

                    </ul>
                </div>
                <ul class="all_list">
                    <li><img src="/img/top_bg.jpg" width="251" height="25" alt="" /></li>
                    <!-- BDP: catalog_menu_action_goods -->
                    <li><a href="/catalog/actions" title="Акции"><img src="/img/all_link.jpg" width="251" height="61" alt="Акции" /></a></li>
                    <!-- EDP: catalog_menu_action_goods -->

                    <!-- BDP: catalog_menu_hit_goods -->
                    <li><a href="/catalog/hits" title="Хиты">Хиты</a></li>
                    <li><a href="/catalog/hits" title="Хиты"><img src="/img/all_link2.jpg" width="251" height="61" alt="Хиты" /></a></li>
                    <!-- EDP: catalog_menu_hit_goods -->


                    <!-- BDP: catalog_menu_new_goods -->
                    <li><a href="/catalog/novelty" title="Новинки"><img src="/img/all_link3.jpg" width="251" height="61" alt="Новинки" /></a></li>
                    <!-- EDP: catalog_menu_new_goods -->

                    <li><img src="/img/bottom_bg.jpg" width="251" height="10" alt="" /></li>
                </ul>
                <div class="nofont">
                    {LEFT_BANNER}
                </div>
            </div>
            <div class="right_column">

                <div class="type_elem">



                    <div id="dialog" >
                        <form action="/enter" method="post">
                            <p id="colse"> <a href="#" ><img src="/img/auth-modal-close-button.gif" width="15" height="15"></a></p>
                            <p class="content" id="head"><strong>Авторизация</strong></p>
                            <p class="content" id="email">e-mail: <input type="text" name="login"> </p>
                            <p class="content" id="password">Пароль: <input type="password" name="password"> </p>
                            <p id="forgot-password">Забыли пароль? <input type="checkbox" id="forgot-password"> Запомнить меня</p>
                            <p id="send"> <a href="">Регистрация</a> <input class="button" type="submit" value="Войти"> </p>
                        </form>
                    </div>
                    <!-- BDP: p_header -->
                    <div class="div-h1{DIV_H1_ADMIN}">
                        <h1>{HEADER}</h1>

                    </div>
                    {H1_ADMIN_MENU}

                    <div class="clear"></div>
                    <!-- EDP: p_header -->

                    {CONTENT}

                </div>
                <noscript>
                    <style type="text/css"> .type_elem {display:none;} </style>
                    <h2>Внимание! Для корректной работы сайта необходима поддержка JavaScript </h2>
                </noscript>
                <div id="no-cookies-text">

                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="footer_wrap">
            <div class="footer">
                <div class="left">
                    <p><img src="/img/logo2.jpg" width="88" height="31" alt="{LOGOALT}" title="{LOGOALT}" /></p>
                    <p>{LOGO_TITLE_BOTTOM}</p>
                    <p>{ADRES}</p>
                </div>
                <div class="bmenu">
                    <ul>
                        <!-- BDP: horisontal_bottom -->
                        <li><a href="{MENU_HREF}" title="{MENU_NAME}">{MENU_NAME}</a></li>
                        <!-- EDP: horisontal_bottom -->

                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="right">
                    <p>{SLG3}</p>
                </div>
                <div class="clear"></div>
                <div class="rules">
                    <p>{SLG3_ROOLES}</p>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="footer_wrap2">
            <div class="footer">
                <div class="bingo">
                    <a href="http://bingo.in.ua/" alt="Bingo! Создание сайтов" title="Bingo! Создание сайтов"  target="_blank"><img src="/img/bingo.gif"  title="Bingo! Создание сайтов" width="86" height="28" alt="" /><br />создание сайтов</a>
                </div>
                <div class="partners">
                    Наши партнеры:
                    <ul>
                        {PARTNERS}

                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </body>
</HTML>