{extends file='editorHead_huc.tpl'}
{block name='content'}
<div id="hcBlockText hcBasicSideMargin">
         <div class="hcHeadSpace hcBasicSideMargin">
      <h1>Collection {$collectionTitle}</h1>
    </div>
    <form id="ccform" method="post" action="{$action}">
        <div class="component">
            <div class="componentHeader">Settings</div>
            <div class="element">
                <div class="label">Title field</div>
                <div class="control">
                    <select id="title_field" name="title_field">
                        <option value="0">--</option>
                        {foreach from=$fields item=item}
                            <option {if $active == $item.uri} value="0" selected{else}value="{$item.uri}"{/if}>{$item.name}</option>
                         {/foreach}
                    </select>
                </div>
            </div>
            <div class="element">
                <div class="label">Collection name</div>
                <div class="control">
                    <input id="collection_name" name="collection_name" type="text" size="50" value="{$collectionTitle}">
                </div>
            </div>
        </div>
                <div id="btnFrame">
                    <input type="hidden" name="dataset" value="{$datasetID}">
                    <input type="hidden" name="collection" value="{$collectionID}">
                    <input type="hidden" name="uri" value="{$collectionURI}">
                    <input id="OKbtn" type="submit" value="Submit">
                    <input id="resetBtn" type="button" value="Back" onclick="history.back()">
                </div>
    </form>
     </div>
{/block}