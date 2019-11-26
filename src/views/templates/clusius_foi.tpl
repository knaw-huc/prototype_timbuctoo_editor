{extends file='standardHead.tpl'}
{block name='content'}
<h2>Clusius Fields of Interests</h2>
{assign var ="i" value="0"}
{foreach from=$fields item="field"}
    {$i = $i +1}
     <p id="item_{$i}" class='item'>{$field.field}</p>
    <p><a id="editBtn_{$i}" class="editBtn" href="javascript:edit_interest_field({$i})">Edit entry</a></p>
    <input type="hidden" id="uri_{$i}" value="{$field.uri}">   
{/foreach}
{/block}