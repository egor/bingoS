<!-- BDP: news -->

{NEWS_LIST_ADMIN}

{PAGINATION}

<div class="{NEWS_BLOCK_CLASS}">
    <!-- BDP: news_item -->
    <div class="post">
        <!-- BDP: news_item_pic -->
        <a href="/news/{NEWS_ADRESS}" title="{NEWS_HEADER}"><img src="{NEWS_ITEM_SRC_PIC}" class="post_image" alt="{NEWS_HEADER}" title="{NEWS_HEADER}" /></a>
        <!-- EDP: news_item_pic -->

        <a href="/news/{NEWS_ADRESS}" class="post_title" title="{NEWS_HEADER}">{NEWS_HEADER}</a>
        <span class="post_date">{DATE}</span>
        <div class="post_description">
            {NEWS_PREVIEW}
        </div>

        {NEWS_ITEM_ADMIN}

    </div>
    <!-- EDP: news_item -->
</div>

{PAGINATION}

<!-- EDP: news -->

<!-- BDP: news_detail -->
<div class="text">

    {NEWS_DETAIL_ADMIN}

    {PIC}
    <span class="date">{NEWS_DATE}</span>
    <div class="description1">
        
    {NEWS_BODY}
    </div>

    <div class="bookmarks">
      {SOC_BUTTONS}  
    </div>
</div>
<!-- EDP: news_detail -->