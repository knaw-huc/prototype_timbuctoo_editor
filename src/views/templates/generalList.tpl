{extends file='standardHead.tpl'}
{block name='content'}
<h2>{$list_title}</h2>
<div id="controls">
   <a class="editBtn" href="">New entry</a> 
</div>
{foreach from=$list item="element"}
    <div class="personListItem">
        <p>{$element.label.value}</p>
        <p>
            <a class="editBtn" href="{$home_path}show/{$set}/{$listType|replace: 'List': ''}/{$element.uri|base64_encode}">View entry</a>
        </p>
    </div>
{/foreach}
{/block}