{extends file='standardHead.tpl'}
{block name='content'}
<div id="controls">
        <a class="editBtn" href="{$editURI}">Edit entry</a>
        <a class="editBtn" href="javascript:drop_collection_item('{$dataset}', '{$collection}', '{$uri}')">Drop entry</a>
    </div>
<h2>Collection item</h2>
<div id="formtext">
    <div class="formTbl">
    {foreach from=$data item=item key=key}
        <div class="formRow">
                <div class="formLbl">{$item.key}:</div>
                <div class="formCell">
                    {$item.value}
                </div>
            </div>
     {/foreach}
    </div>
</div>
 {/block}