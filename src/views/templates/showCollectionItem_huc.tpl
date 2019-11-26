{extends file='standardHead_huc.tpl'}
{block name='content'}
 <div id="hcBlockText hcBasicSideMargin">
        <div class="hcHeadSpace hcBasicSideMargin">
      <h1>Collection item</h1>
    </div>{if $logged_in}
<div id="controls">
     {if $permissions.WRITE}<a class="btnLink" href="{$editURI}">Edit entry</a>{/if}
        {if $permissions.DELETE}<a class="btnLink" href="javascript:drop_collection_item('{$dataset}', '{$collection}', '{$uri}')">Drop entry</a>{/if}
    </div>{/if}
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
</div>
 {/block}