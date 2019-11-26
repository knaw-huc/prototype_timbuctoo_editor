{extends file='standardHead_huc.tpl'}
{block name='content'}
    <div class="hcBlockText hcBasicSideMargin">
<div class="hcHeadSpace hcBasicSideMargin">
      <h1>Datasets</h1>
    </div>
    {foreach from=$sets item=set}
        <div class="personListItem" style="margin-bottom: 40px;">
            <p><a href="{$home_path}browse/metadata/{$set.dataSetId}">{$set.title}</a>
                <br>{$set.description}<br>
            <!--<a href="{$home_path}browse/metadata/{$set.dataSetId}">Metadata</a></p>-->
        </div>
    
    {/foreach}
</div>
{/block}