<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{if isset($title)}{$title}{else}Timpars{/if}</title>
    {block name='head'}{/block}
</head>
<body>
    <div class="hcLayoutBasicMainFixedAside">    
        <aside class="hcLayoutAside">
            <div class="asideWithProductBar">
                <div class="hcProductBar bgColorBrand1">
                    <div class="hcHeadSpace"><img src="https://d33wubrfki0l68.cloudfront.net/a3796f7e4995c2b1ac0a40c2fe3bbd3da33d8030/2606c/images/logo-timbuctoo.svg" alt="logo KNAW Humanities Cluster" class="logo"></div>

                    <nav>
                        <a href="{if !$logged_in}javascript:login(){else}{$logout_path}{/if}"><img src="https://d33wubrfki0l68.cloudfront.net/1daf89bb292fc2ae08bbbf62ff0b1cfa274f4017/bed28/images/icons/prdct-br-pages.svg" alt="Pages" class="icon"> {if !$logged_in}Login{else}Logout{/if}</a><a href="{$home_path}"><img src="https://d33wubrfki0l68.cloudfront.net/1daf89bb292fc2ae08bbbf62ff0b1cfa274f4017/bed28/images/icons/prdct-br-pages.svg" alt="Pages" class="icon"> Datasets</a>

                        {if $logged_in}
                        <a href="#" style="margin-left: 42px;">New Dataset</a>
                        {/if}
                    </nav>
                </div>
                <div class="colorBgGrey hcBlockScroll hcBasicSideMargin">
                    {if $logged_in}
                        <div class="hcLoginInfo">
                            {if isset($user_name)}{$user_name}{/if}
                        </div>
                    {/if}
                    <div class="hcHeadSpace">
                       {if isset($head)}
                        {$head}
                        {/if} 
                    </div>


                    {if isset($navColl)}
                        {$navColl}
                    {/if}
                </div>
            </div>
        </aside>
        <div class="hcLayoutMain hcContentContainer hcMarginBottom15">
            <div id="content">
                {block name='content'}
                    <p>Content</p>
                {/block}
            </div>
            {if isset($cursor)}
                <div id="cursors">
                    {if $cursor.prevCursor != ""}<a class="btnLink" href="{$home_path}browse/list/{$set}/{$listType}/{$cursor.prevCursor}">&#9668;</a>{/if}
                    {if $cursor.nextCursor != ""}<a class="btnLink" href="{$home_path}browse/list/{$set}/{$listType}/{$cursor.nextCursor}">&#9658;</a>{/if}
                </div>    
            {/if}
            {if !$logged_in}
                <div id="loginFormDiv"></div>
            {/if}
        </div>
    </div>
</body>
</html>