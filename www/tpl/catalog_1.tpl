<!-- BDP: catalog -->

<!-- BDP: catalog_items_body -->
<div class="sort_block">
    <div class="filter">
        <form id="filter-form">
            {FILTER}
        </form>

        {GOODS_LENGTH_RANGE}

    </div>
</div>

<div class="goods_list">
    {TOP_NAV_BAR}
    <!-- BDP: catalog_items -->
    {CATALOG_ITEM}
    <!-- EDP: catalog_items -->
    {BOTT_NAV_BAR}
    <div class="clear"></div>
</div>
<!-- EDP: catalog_items_body -->


<!-- BDP: catalog_items_empty -->
{GOODS_EMPTY}
<!-- EDP: catalog_items_empty -->

<!-- EDP: catalog -->




<!-- BDP: catalog_section_list_body -->




{TOP_NAV_BAR}
<ul class="news_list">
    <!-- BDP: catalog_section_list -->
    <li>{CATALOG_SECTION_LIST_PIC}
        <strong> <a href="{CATALOG_SECTION_LIST_HREF}">{CATALOG_SECTION_LIST_NAME}</a> </strong>
        <p>{CATALOG_SECTION_LIST_PREVIEW}</p>
        <p class="{ADMIM_CLASS_NAME}"><a href="{CATALOG_SECTION_LIST_HREF}" title="{CATALOG_SECTION_LIST_NAME}">Подробнее</a> {ADMIN_BUTTON_PANEL}</p>
    </li>
    <!-- EDP: catalog_section_list -->
</ul>
{BOTT_NAV_BAR}
<!-- BDP: catalog_section_list_empty -->
{GOODS_EMPTY}
<!-- EDP: catalog_section_list_empty -->


<!-- EDP: catalog_section_list_body -->


<!-- BDP: catalog_detail_body -->



<div class="product_block">
    <div class="group">Группа: <span><a href="{CATALOG_DETAIL_SECTION_URL}">{CATALOG_DETAIL_SECTION_TEXT}</a></span></div>

    <div class="img">
        {CATALOG_DETAIL_ING_TYPE}
        {CATALOG_DETAIL_ING_LOOP}

        <a href="{CATALOG_DETAIL_IMG_REAL_PATH}{CATALOG_DETAIL_IMG}" title="{CATALOG_DETAIL_TITLE}" rel="lightbox[photo-item]"  id="href-detial" >
            <img src="{CATALOG_DETAIL_IMG_BIG_PATH}{CATALOG_DETAIL_IMG}" id="img-detial" width="370" height="370" alt="{CATALOG_DETAIL_ALT}" title="{CATALOG_DETAIL_TITLE}" />

        </a>

    </div>
    <div class="desc">

        <div class="rate1">{CATALOG_DETAIL_RATING}</div>
        <div class="clear"></div>

        <div class="clear"></div>
        <!-- BDP: store -->
        <!-- BDP: catalog_detail_plashka -->
        <p class="price">Розничная цена:<span>{CATALOG_DETAIL_COST_IN_SHOP} грн.</span></p>
        <p class="eco">Экономия:<span>{CATALOG_DETAIL_COST_ECONOM} грн.</span></p>
        <!-- EDP: catalog_detail_plashka -->
        <p class="newprice">Наша цена:<span>{CATALOG_DETAIL_COST} грн.</span></p>

        <!-- BDP: pbutton -->
        <div class="buy" id="{CATALOG_DETAIL_ID}"><input type="text" class="goods-input" id="{CATALOG_DETAIL_ID}" value="1"> шт.
            <a href="detail" class="buy-button button" id="{CATALOG_DETAIL_ID}">Купить</a></div>

        <!-- EDP: pbutton -->

        <!-- BDP: npbutton -->
        <div class="order detail" ><img width="14" height="13" src="/img/order.gif" alt=""><a href="/basket">Перейти в корзину</a></div>
        <!-- EDP: npbutton -->

        <!-- BDP: availability_button -->
        <div class="expected"> {EXPECTED_TEXT}</div>
        <!-- EDP: availability_button -->

         <!-- BDP: availability_button1 -->
        <div class="expected"> {EXPECTED_TEXT2}</div>
        <!-- EDP: availability_button1 -->

        <!-- EDP: store -->

       <!--{CATALOG_DETAIL_PREWIEV}-->


        <table>
            <tbody>


                {RIGHT_FIELDS}

            </tbody></table>

        <ul class="social-share">
            <li>
                <div id="vk_like"></div>
                <script type="text/javascript">
                VK.Widgets.Like("vk_like", {type: "button"});
                </script>
            </li>

            <li><div class="fb-like" data-href="{SOCIAL_PAGE_URL}" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"></div></li>

            <li><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>

            <!-- Поместите этот тег туда, где должна отображаться кнопка +1. -->
            <li><g:plusone href="http://temp.dp.ua/catalog/lodochnie-motori-evinrude/e-25-dr-lodochnij-motor-evinrude-e-tec"></g:plusone></li>
        </ul>

        <!-- Поместите этот вызов функции отображения в соответствующее место. -->
        <script type="text/javascript">
        window.___gcfg = {lang: 'ru'};

        (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
        </script>

    </div>
    <div class="clear"></div>
    <div class="list_3d">


        <div style="margin: 15px 0 15px 120px ;"> {3D_OBJECTS}</div>
        <div class="clear"></div>

        <!-- BDP: catalog_foreshortening_list -->
        <div class="details_list">

            <ul>
                <!-- BDP: catalog_foreshortening_items -->
                <li style="font-size: 12px"><a href="/img/catalog/gallery/real/foreshortening/{F_SRC}" title="{G_TITLE}" rel="lbox[photo-item1]"  id="{F_ID}"><img src="/img/catalog/gallery/small_1/foreshortening/{F_SRC}" width="100" height="100" alt="{F_ALT}" title="{F_TITLE}" id="img-{F_ID}" /></a></li>
                <!-- EDP: catalog_foreshortening_items -->
            </ul>
        </div>
        <div class="clear"></div>
        <!-- EDP: catalog_foreshortening_list -->


    </div>



    <!-- BDP: catalog_gallery_list -->
    <div class="clear"></div>
    <div class="akces_block">

    </div>
    <p style="font-size: 16px;">Галерея: </p>
    <div class="list_3d">

        <div >

            <ul>
                <!-- BDP: catalog_gallery_items -->
                <li ><a href="/img/catalog/gallery/real/gallery/{G_SRC}"  rel="lightbox[photo-item]" title="{G_TITLE}" ><img src="/img/catalog/gallery/small_1/gallery/{G_SRC}" width="100" height="100" alt="{G_ALT}" title="{G_TITLE}"/></a></li>
                <!-- EDP: catalog_gallery_items -->
            </ul>
        </div>

        <div class="clear"></div>
    </div>
    <!-- EDP: catalog_gallery_list -->



    <div class="clear"></div>

    {BOTTOM_FIELDS}
    <div class="clear"></div>
    <div class="har_block">
        {CATALOG_DETAIL_USER_SECTION_FIELDS}
    </div>
    <!-- BDP: complate_block -->
    <div class="clear"></div>
    <div class="akces_block">
        {USED_COMPLATE_TITLE}
        {USED_COMPLATE_CATALOG_ITEM}
    </div>
    <!-- EDP: complate_block -->

    <!-- BDP: features_block -->
    <div class="clear"></div>
    <div class="akces_block">
        {FEATURED_TITLE}
        {FEATURED_CATALOG_ITEM}
    </div>
    <!-- EDP: features_block -->

    <!-- BDP: comments -->
    <div class="clear"></div>
    <div class="module_header_block">
        <p>Отзывы:</p>
    </div>

    <div class="otziv_block">
        <ul>
            <!-- BDP: comments_list -->


            <li {COMMENT_LIST_CLASS} id="comment-{COMMENT_GOODS_ID}">
                {COMMENT_LIST_ADMIN}
                <div class="desc {COMMENT_LIST_DESC_CLASS_NAME}">

                    <span class="rate1">{COMMENT_LIST_RATING}</span>
                    <p><span>{COMMENT_LIST_DATE}/ <span>{COMMENT_LIST_FIO}</span></span></p>
                    <p><strong>Период эксплуатации:</strong><br>{COMMENT_LIST_PERIOD_OF_OPERATION}</p>
                    <p><strong>Достоинства:</strong><br>{COMMENT_LIST_DIGNITY}</p>
                    <p><strong>Недостатки:</strong><br>{COMMENT_LIST_SHORTCOMMINGS}</p>
                    <p><strong>Рекоммендации:</strong><br>{COMMENT_LIST_RECOMENDATIONS}</p>
                    <p><strong>Вывод:</strong><br>{COMMENT_LIST_CONCLUSION}</p>
                </div>
                <p class="right" id="{COMMENT_GOODS_ID}">
                    <!-- BDP: comment_helpful_yes -->
                    <span class="commen-helpful-yes" id="{COMMENT_GOODS_ARTIKUL}" >
                        Отзыв полезен?
                        <a href="#" id="{COMMENT_GOODS_ID}">Да</a>
                        <span id="commen-helpful-yes-{COMMENT_GOODS_ID}" >{COMMENT_HELPFUL_YES_VAL}</span> /
                        <span id="no-{COMMENT_GOODS_ID}"> <a href="#" id="{COMMENT_GOODS_ID}">Нет</a>  </span>

                        <span id="commen-helpful-no-{COMMENT_GOODS_ID}"> {COMMENT_HELPFUL_NO_VAL}</span>
                    </span>
                    <!-- EDP: comment_helpful_yes -->

                    <!-- BDP: comment_helpful_no -->
                <p class="right">Отзыв полезен?  Да {COMMENT_HELPFUL_YES_VAL} / Нет {COMMENT_HELPFUL_NO_VAL} </p>
                <!-- EDP: comment_helpful_no -->
                </p>
                <p class="{ADMIM_CLASS_NAME}"> {ADMIN_BUTTON_PANEL}</p>
            </li>

            <!-- EDP: comments_list -->

        </ul>
    </div>
    <!-- EDP: comments -->

    <p><a href="#" id="a-oform-block">Оставить свой отзыв</a></p>

    <div class="oform_block" id="otzivi">
        <form method="post" action="/catalog/addcomment" id="form1">
            <input type='hidden' value="{COMMENT_GOODS_ARTIKUL}" name="artikul" />

            <p><span>Все поля обязательны для заполнения</span></p>
            <p>Имя и фамилия:<br><input type="text"  name ="fio" value ="{COMMENT_FIO}"> <span class="error-message"></span></p>
            <p>Период эксплуатации: <br> <select name="period_of_operation">
                    <option>Менее месяца</option>
                </select><span class="error-message"></span></p>
            <p>Достоинства:<br><textarea name="dignity">{COMMENT_DIGNITY}</textarea><span class="error-message"></span></p>
            <p>Недостатки:<br><textarea name="shortcomings">{COMMENT_SHORTCOMMINGS}</textarea><span class="error-message"></span></p>
            <p>Рекомендации:<br><textarea name="recommendations">{COMMENT_RECOMENDATIONS}</textarea><span class="error-message"></span></p>
            <p>Вывод:<br><textarea name="conclusion">{COMMENT_CONCLUSION}</textarea><span class="error-message"></span></p>
            <div class="rate1">
                <div style="float: left; width: 100px;">Оцените товар:</div>
                <div>

                <script type="text/javascript">
                                      jQuery('.auto-submit-star').rating({});
                </script>

                <input name="star1" type="radio" class="star" value="1" checked/>
                <input name="star1" type="radio" class="star" value="2" />
                <input name="star1" type="radio" class="star" value="3" />
                <input name="star1" type="radio" class="star" value="4" />
                <input name="star1" type="radio" class="star" value="5" />
                </div>

            </div>
            <div class="clear"><span class="error-message"></span></div>



            <p><br><span class="simple">Введите текст показанный на картинке:</span></p>

            <div class="capcha">
                <p><span class="error-message"></span></p>
                <p >

                    <span><a href="#" class="a-captcha"><img width="130" height="60" alt="" src="/img/captcha/{CAPTCHA_ID}.png"></a></span>
                     <input type="hidden" name="captcha_id" id="captcha_id"  value="{CAPTCHA_ID}" />
                    <span class="captcha"> <input type="text" name="captcha_input" id="captcha_input"> </span> <span class="error-message"></span>
                </p>
            </div>
            <div class="clear"></div>
            <p><span class="simple"><a href="#" class="a-captcha">Обновить картинку</a></span> <a href="#" class="a-captcha"><img width="21" height="21" alt="" src="/img/reload.gif"></a></p>


            <p><input type="submit" class="button" value="Отправить отзыв"></p>
        </form>
    </div>








    <div class="clear"></div>

    <div class="clear"></div>
</div>
<!-- EDP: catalog_detail_body -->
