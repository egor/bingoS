<!-- BDP: comments -->
<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.ui.stars.min.js"></script>
<script type="text/javascript" src="/js/jq-comments.js"></script>


<div class="wrap_comments">
    <div id="message" style="display:{MESSAGE_STYLE}; border: 1px solid; padding: 5px; margin-bottom:15px;"> {COMMENT_MESSAGE}</div>
    <a class="toggle" id="toggle">Оставить свой отзыв</a>
    <div class="wrap_comments_form show_hide_comment" style="display:{FORM_STYLE};" >
            <h3>Оставить свой отзыв:</h3>
            <span class="contact_title small">Поля отмеченные <span class="required">*</span> обязательны для заполнения</span>
        <h4 class="contact_title">Уважаемые посетители сайта, нам очень важно Ваше мнение!<br>Высказать свое мнение Вы можете ответив на несколько несложных вопросов.</h4>
        <form class="basket_form" method="POST" action="{COMMENTS_FORM_ACTION}">
            <input type="hidden" name="goods_id" value="{COMMENTS_GOODS_ID}">
    
            <span class="f_name {ERROR_FIO_CLASS_NAME}">Имя <span class="required">*</span></span>
            <input type="text" class="f_text {ERROR_FIO_CLASS_NAME}" value="{COMMENTS_FIO}" name="name_1">
            {ERROR_FIO_MESSAGE}

                                    <div class="wrap_select"><span class="f_name select">Период эксплуатации:</span>
            <select class="f_select product_item" name="status">
                <option value="Менее месяца" {COMMENTS_PERIOD_OF_OPERATION_INDEX_1}>Менее месяца</option>
                <option value="от 1 до 3 мес." {COMMENTS_PERIOD_OF_OPERATION_INDEX_2}>от 1 до 3 мес.</option>
                <option value="от 3 до 12 мес." {COMMENTS_PERIOD_OF_OPERATION_INDEX_3}>от 3 до 12 мес.</option>
                <option value="от 1 до 3 лет" {COMMENTS_PERIOD_OF_OPERATION_INDEX_4}>от 1 до 3 лет</option>
                <option value="свыше 3-х лет" {COMMENTS_PERIOD_OF_OPERATION_INDEX_5}>свыше 3-х лет</option>
            </select>
            </div>

            <span class="f_name">Достоинства</span>
            <textarea name="name_2" class="f_textarea">{COMMENTS_DIGNITY}</textarea>

            <span class="f_name">Недостатки</span>
            <textarea name="name_3" class="f_textarea">{COMMENTS_SHORTCOMINGS}</textarea>

            <span class="f_name">Рекомендации</span>
            <textarea name="name_4" class="f_textarea">{COMMENTS_RECOMMENDATIONS}</textarea>

            <span class="f_name {ERROR_CONCLUSION_CLASS_NAME}">Вывод <span class="required">*</span></span>
            <textarea name="name_5" class="f_textarea {ERROR_CONCLUSIONCLASS_NAME}"> {COMMENTS_CONCLUSION} </textarea>
            {ERROR_CONCLUSION_MESSAGE}
             <div class="rate_stars">
                <span>Оцените товар</span>
                    <div class="rate">
                        <input type="radio" name="rate" {COMMENTS_RATE_CHECKED_1} value="1" title="Плохо" />
                        <input type="radio" name="rate" {COMMENTS_RATE_CHECKED_2}  value="2" title="Хорошо" />
                        <input type="radio" name="rate" {COMMENTS_RATE_CHECKED_3}  value="3" title="Хорошо" />
                        <input type="radio" name="rate" {COMMENTS_RATE_CHECKED_4}  value="4" title="Отлично" />
                        <input type="radio" name="rate" {COMMENTS_RATE_CHECKED_5}  value="5" title="Отлично" />
                    </div>
                <span class="caption"></span>
            </div>
             
            <!-- BDP: comments_captcha -->                
            <span class="protect_title product_item">Введите текст показанный на картинке <span class="required">*</span></span>
            <span class="protect">
                <input type="hidden" name="captcha_id" id="captcha_id"  value="{CAPTCHA_ID}" />
                <a href="#" onclick="captchaReload(); return false;"><img src="/img/captcha/{CAPTCHA_ID}.png"></a>
                <span class="wrap_input">
                    <input type="text" class="{ERROR_CAPTCHA_CLASS_NAME}" value="" name="captcha_input">
                    </span>
            </span>
                    
           {ERROR_CAPTCHA_MESSAGE}
            <br clear="all">
            <a class="comments-captcha" href="#" onclick="captchaReload(); return false;">Обновить картинку</a>
            <!-- EDP: comments_captcha --> 
            <br clear="all">
            <div class="button">
                <input type="submit" name="submit" value="Отправить отзыв">
                <span></span>
            </div>
        </form>
    </div>
    <div class="title_comments" id="comments"><strong>{COMMENTS_GOODS_MAIN_HEADER}</strong> {COMMENTS_GOODS_HEADER}</div>
    
    {PAGINATION}
    
     <div class="comments">
        <!-- BDP: comments_items -->
        <div class="wrap_item">
            <div class="item {COMMENTS_ACTIVE_STYLE}">
                <span class="date">{COMMENTS_ITEM_DAY}&nbsp;/&nbsp;</span><span class="author">{COMMENTS_ITEM_FIO}</span>
                {COMMENTS_ITEM_POINTS}
               
                <div class="comment_description">
                <strong class="name">Период эксплуатации:</strong>
                {COMMENTS_ITEM_PERIOD_OF_OPERATION}<br><br>
                <strong class="name">Достоинства: </strong>
                {COMMENTS_ITEM_DIGNITY}
                <br><br>
                <strong class="name">Недостатки:</strong>
                {COMMENTS_ITEM_SHORTCOMINGS}
                
                <br><br>                            
                <strong class="name">Рекоммендации:</strong>
                {COMMENTS_ITEM_RECOMMENDATIONS}
                <br><br>
                <strong class="name">Вывод: </strong>
                {COMMENTS_ITEM_CONCLUSION}
                 </div>
            </div>
                 
                 <!-- BDP: admin_buttons -->     
                 <div class="panel">
                    <a class="delete" href="/commentsactions/delete/{COMMENTS_ITEM_ID}{COMMENTS_ITEM_GET_VALUE}" onclick="return confirm('Удалить комментарий ?');">Удалить</a>
                    <a class="edit" href="/commentsactions/view/{COMMENTS_ITEM_ID}{COMMENTS_ITEM_GET_VALUE}" >Редактировать</a>
                    <a class="qoute" href="#" onclick="addAdminMessage(); return false;">Ответить</a>
                    <a class="public" href="{COMMENTS_ACTIVE_LINK_URL}">{COMMENTS_ACTIVE_LINK_TEXT}</a>
                 </div>
                 <!-- EDP: admin_buttons -->  
                
                 <!-- BDP: comment_vote_link -->
                    <div class="comment_vote">
                        Отзыв полезен? 
                        <a href="#" onclick="commentVote('{COMMENTS_ITEM_ID}', 'yes', this); return false;" class="yes">Да</a> 
                        <span class="yes_count" id="{COMMENTS_ITEM_ID}">{TIP_HELPFUL_YES}</span> / 
                        <a href="#" onclick="commentVote('{COMMENTS_ITEM_ID}', 'no', this); return false;" class="no">Нет</a> 
                        <span class="no_count" id="{COMMENTS_ITEM_ID}">{TIP_HELPFUL_NO}
                        </span>
                    </div>
               <!-- EDP: comment_vote_link -->
               
               <!-- BDP: comment_vote_no_link -->
                    <div class="comment_vote">
                        Отзыв полезен? 
                       <span class="yes-no"> Да </span>
                        <span class="yes_count">{TIP_HELPFUL_YES}</span> / 
                        <span class="yes-no"> Нет </span>
                        <span class="no_count">{TIP_HELPFUL_NO}
                        </span>
                    </div>
               <!-- EDP: comment_vote_no_link -->
               
               
               
               <div class="wrap_quote_item" id="form">
                   <form action="/commentsactions/adminmessage/{COMMENTS_ITEM_ID}" method="POST">
                        <div class="quote_item">
                            <span class="author">Администрация. Ответить на это сообщение.</span>
                            <div class="comment_admin_description">
                                <input type="hidden" name="goods_id" value="{COMMENTS_GOODS_ID}">
                                <textarea style="" name="admin_message"></textarea>
                                <div class="button">
                                    <input type="submit" value="Отправить" name="submit">                        
                                    <span></span>
                                </div>
                            </div>
                        </div>        
                  </form>             
                </div>
                                
                 <!-- BDP: comment_admin_block -->
                <div class="wrap_quote_item">
                    <div class="quote_item">
                        <span class="date">{COMMENT_ADMIN_BLOCK_DATE}</span> / <span class="author">Администрация</span>
                        <div class="comment_description">  {COMMENT_ADMIN_BLOCK_CONCLUSION} </div>
                        <!-- BDP: comment_admin_block_admin_buttons -->
                        <div class="panel-admin">
                            <a class="delete" href="/commentsactions/delete/{COMMENTS_ITEM_ADMIN_ID}{COMMENTS_ITEM_GET_VALUE}" onclick="return confirm('Удалить комментарий ?');">Удалить</a>
                         
                        </div>    
                         <!-- EDP: comment_admin_block_admin_buttons -->
                    </div>
                </div>
               <!-- EDP: comment_admin_block -->
                                   
        </div>
         <!-- EDP: comments_items -->
      
           
        
    </div>
{PAGINATION}
</div>
<!-- EDP: comments -->