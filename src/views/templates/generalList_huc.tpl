{extends file='standardHead_huc.tpl'}
{block name='content'}
    <div id="hcBlockText hcBasicSideMargin">
        <div class="hcHeadSpace hcBasicSideMargin">
            <h1>{$list_title}</h1>
        </div>
        {if $logged_in}
            <div id="controls">
                {if $permissions.CREATE}<a class="btnLink" href="{$home_path}create/{$set}/{$id}">New entry</a> {/if}
            {if $permissions.EDIT_COLLECTION_METADATA}<a class="btnLink" href="{$home_path}edit/settings/{$set}/{$id}">Settings</a>{/if}
            </div>{/if}
            {foreach from=$list item="element"}
                <div class="personListItem">
                    <p>{$element.label.value}</p>
                    <p>
                        <a class="btnLink" href="{$home_path}show/{$set}/{$listType|replace: 'List': ''}/{$element.uri|base64_encode}">View entry</a>
                    </p>
                </div>
            {/foreach}
        </div>
        {/block}