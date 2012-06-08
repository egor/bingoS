<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{TITLE}</title>
    <meta name="keywords" content="{KEYWORDS}" />
    <meta name="description" content="{DESCRIPTION}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="all" />
    <link href="/css/style.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="/css/fixed.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="/js/jquery-plugins/ui/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" rel="stylesheet" />

    <!--[if IE]>
    <link href="/css/ie.css" rel="stylesheet" type="text/css" media="screen" />
    <![endif]-->
    <script type="text/javascript" src="/js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="/js/jq-validate.js"></script>
    <script type="text/javascript" src="/js/jq-scripts.js"></script>
    <script type="text/javascript" src="/js/jq-basket.js?v=1.2"></script>

    
     <link  rel="stylesheet" type="text/css" href="/css/jquery.lightbox-0.5.css" />
        <SCRIPT type="text/javascript" src='/js/jquery.lightbox-0.5.min.js'></SCRIPT>
        <script type="text/javascript" src="/js/jq-light-box-init.js"></script>

    <script type="text/javascript" src="/js/jquery-plugins/ui/js/jquery-ui-1.8.14.custom.min.js"></script>
         <link  rel="stylesheet" type="text/css" href="/js/star-rating/jquery.rating.css" />
    <script type="text/javascript" src="/js/star-rating/jquery-rating-pack.js"></script>
    
    
    <!-- BDP: adminjslib -->

<link href="/css/admin-buttons.css" rel="stylesheet" type="text/css" media="screen" />

<script type="text/javascript" src="/js/jq-adm-scripts.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery-plugins/jq-tree/themes/default/style.css" />
<script type="text/javascript" src="/js/jquery-plugins/jq-tree/_lib/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery-plugins/jq-tree/_lib/jquery.hotkeys.js"></script>
<script type="text/javascript" src="/js/jquery-plugins/jq-tree/jquery.jstree.js"></script>
<script type="text/javascript" src="/js/jq-check-all.js"></script>
<!-- EDP: adminjslib -->

    
    <!--<script type="text/javascript" src="/js/jquery.js"></script>-->
    <script type="text/javascript" src="/js/main.js"></script>
    <!--api vkontakte btn-->
    <script type="text/javascript" src="http://vk.com/js/api/share.js?11" charset="utf-8"></script>
</head>
    <body >

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <div class="wrap_main">
    	<div id="header">
            <!-- BDP: main_logo_text -->
            <span class="logo"><img src="/img/bg_header_logo.png" alt="{LOGOALT}" title="{LOGOALT}" /></span>
            <!-- EDP: main_logo_text -->

            <!-- BDP: main_logo_url -->
            <a class="logo" href="/" title="{LOGOALT}"><img src="/img/bg_header_logo.png" alt="{LOGOALT}" title="{LOGOALT}" /></a>
            <!-- EDP: main_logo_url -->

            <div class="sub_nav">
                <a href="/" title="{TITLEMAIN}" class="main"></a>
                <a href="/sitemap" title="{TITLEMAP}" class="sitemap"></a>
                <a href="javascript:void(0);" onclick="return bookmark(this);" title="{TITLEFEED}" class="add"></a>
            </div>
            <table class="nav">
            	<tr>
                    <!-- BDP: horisontal -->
                    <td {H_MENU_CLASS_NAME}><a href="{MENU_HREF}" title="{MENU_NAME}">{MENU_NAME}</a></td>
                    <!-- EDP: horisontal -->
            	</tr>
            </table>
            <span class="slogan">{LOGOTITLE}</span>
            <span class="phone">{SLG2_PHONES}</span>
            <span class="work_time">{SLG2_OTHER}</span>
            <p class="text">{SLG2}</p>

            <div class="wrap_basket_info{BASKET_STATUS_CLASS}" id="basket_block">
                {BASKET_BLOCK_CONTENT}
            </div>
        </div>

        <div class="wrap_top_banners">
            <!-- BDP: top_banner_block -->
            <div class="baners">
                <!-- BDP: top_banner_item -->
                <div class="baner{BANNER_MIDDLE}">{BANNER_CODE}</div>
                <!-- EDP: top_banner_item -->
            </div>
            <!-- EDP: top_banner_block -->
        </div>

    <div id="wrapper">
    	<div class="wrap_conteiner">
            <!-- BDP: breadcrumbs -->
            <div class="breadcrumbs">
                {WAY}
            </div>
            <!-- EDP: breadcrumbs -->

            <div id="conteiner">
                <div id="left_colum">
                    <div class="wrap_nav">
                        <!-- BDP: administration -->
                        <b class="title">Администрирование</b>
                        <ul class="nav">
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


                        <b class="title">Каталог</b>
                        <div class="wrap_search">
                            <form action="/search" class="f_serach">
                                <input type="text" name="q" class="s_text" onblur="if(this.value=='') this.value='Поиск по каталогу'" onfocus="if(this.value=='Поиск по каталогу') this.value=''" value="Поиск по каталогу" />
                                <input type="submit" name="s" class="s_btn" value="" />
                            </form>
                        </div>
                        <ul class="nav">
                        <!-- BDP: catalog_menu -->
                            <!-- BDP: catalog_menu_complex -->
                            <li class="{CATALOG_MENU_ACTIVE}"><a href="/catalog{CATALOG_MENU_HREF}" title="{CATALOG_MENU_NAME}">{CATALOG_MENU_NAME}</a>
                                <ul>
                                    <!-- BDP: catalog_menu_complex_sub -->
                                    {CATALOG_MENU_COMPLEX_ELEMENT}
                                    <!-- EDP: catalog_menu_complex_sub -->
                                </ul>
                            </li>
                            <!-- EDP: catalog_menu_complex -->

                            <!-- BDP: catalog_menu_single -->
                            <li class="{CATALOG_MENU_ACTIVE}"><a href="/catalog{CATALOG_MENU_HREF}" title="{CATALOG_MENU_NAME}">{CATALOG_MENU_NAME}</a></li>
                            <!-- EDP: catalog_menu_single -->
                        <!-- EDP: catalog_menu -->
                        </ul>
                    </div>
                    <div class="l_baner">
                        {LEFT_BANNER}
                    </div>
                </div>
                <div id="right_colum">
                    <div class="wrap_text">

                        <div id="dialog" style="display:none;">
                            <form action="/enter" method="post">
                                <p id="colse"> <a href="#" ><img src="/img/auth-modal-close-button.gif" width="15" height="15"></a></p>
                                <p class="content" id="head"><strong>Авторизация</strong></p>
                                <p class="content" id="email">e-mail: <input type="text" name="login"> </p>
                                <p class="content" id="password">Пароль: <input type="password" name="password"> </p>
                                <p id="forgot-password"><!--Забыли пароль? --><input type="checkbox" id="forgot-password"> Запомнить меня</p>
                                <p id="send"> <!--<a href="">Регистрация</a>--> <input class="button" type="submit" value="Войти"> </p>
                            </form>
                        </div>

                        <!-- BDP: p_header -->
                        <h1 class="title">{HEADER}</h1>
                        <!-- EDP: p_header -->

                        {CONTENT}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="wrap_footer">
	<div id="footer">
		<div class="left">
            <img src="/img/bg_footer_logo.gif" class="logo" />
            
            {LOGO_TITLE_BOTTOM}
        </div>
        <div class="right">{SLG3} </div>
    </div>
    <div class="wrap_footer_banners">
        <div class="footer_banners">
            <div class="left">
                <b>Наши партнеры</b>
                <div class="wrap">
                   {PARTNERS}
                </div>
            </div>
            <div class="right">
                <a href="#" title="" class="bingo" target="_blank">
                   <img src="/img/bingo.png" alt="" class="img" />
                   <br />создание сайтов
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>