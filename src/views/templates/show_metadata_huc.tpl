{extends file='standardHead_huc.tpl'}
{block name='content'}
    <div id="hcBlockText hcBasicSideMargin">
        <div class="hcHeadSpace hcBasicSideMargin">
            <h1>Metadata</h1>
        </div>
        {if $logged_in}
            <div id="controls">
                {if $permissions.EDIT_DATASET_METADATA}<a class="btnLink" href="{$home_path}edit/metadata/{$id}">Edit metadata</a>{/if}
                {if $permissions.REMOVE_DATASET}<a class="btnLink" href="javascript:drop_dataset('{$id}')">Drop dataset</a>{/if}
                {if $data.Published == 'No' && $permissions.PUBLISH_DATASET} <a class="btnLink" href="javascript:publish_dataset('{$id}')">Publish</a>{/if}
            </div>
        {/if}
        <div style="display:table;">
            {foreach $data as $element}
                <div style="display:table-row;">
                    <div style="display:table-cell;font-weight: bold;padding:4px;">{$element@key}:</div>
                    <div style="display: table-cell;padding:4px;"> {$element|nl2br}
                    </div>
                </div>
            {/foreach}

        </div>
    </div>
{/block}