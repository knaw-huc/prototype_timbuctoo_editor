{extends file='standardHead.tpl'}
{block name='content'}
<h2>Timbuctoo dataset {$name}</h2>
    {foreach from=$lists item=list}
    <a class="editBtn" href="{$home_path}browse/list/{$set}/{$list.name}">{$list.label}</a>
    {/foreach}
{/block}