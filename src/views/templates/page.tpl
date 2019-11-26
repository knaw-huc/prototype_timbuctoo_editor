<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>{if isset($title)}{$title}{else}Timpars{/if}</title>
        {block name='head'}{/block}
</head>
<body>
<div id="root">
    <div class="header">
        <a class="logoLink" href="{$home_path}">
            <img class="logoImg" src="{$home_path}img/DataHuygenslogoWhite2.png">
        </a>
    </div>
            <div id="content">
{block name='content'}
    <p>Content</p>
{/block}
</div>
{if isset($cursor)}
    <div id="cursors">
        {if $cursor.prevCursor != ""}<a class="editBtn" href="{$home_path}browse/list/{$set}/{$listType}/{$cursor.prevCursor}">&#9668;</a>{/if}
        {if $cursor.nextCursor != ""}<a class="editBtn" href="{$home_path}browse/list/{$set}/{$listType}/{$cursor.nextCursor}">&#9658;</a>{/if}
    </div>    
{/if}
</div>
</body>
</html>