{extends file='standardHead.tpl'}
{block name='content'}
<h2>Simple names</h2>
{assign var ="i" value="0"}
{foreach from=$names item="name"}
    {$i = $i +1}
        <p id="item_{$i}" class='item'>{$name.name}</p>
    <p><a id="editBtn_{$i}" class="editBtn" href="javascript:edit_simple_person({$i})">Edit entry</a></p>
    <input type="hidden" id="uri_{$i}" value="{$name.uri}">
{/foreach}
{/block}