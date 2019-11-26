{extends file='standardHead.tpl'}
{block name='content'}
<h2>Places</h2>
{assign var ="i" value="0"}
{foreach from=$names item="name"}
    {$i = $i +1}
        <p id="item_{$i}" class='item'>{$name.name}</p>
    <p><a id="editBtn_{$i}" class="editBtn" href="{$home_path}examples/edit_place/{$name.uri}">Edit entry</a></p>
{/foreach}
{/block}