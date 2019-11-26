{extends file='standardHead.tpl'}
{block name='content'}
<h2>Timbuctoo data sets</h2>
    {foreach from=$sets item=set}
        <div class="personListItem">
            <p><a href="{$home_path}browse/set/{$set.dataSetId}">{if !is_null($set.title)}{$set.title.value}{else}{$set.dataSetName}{/if}</a>
                {if !is_null($set.description)}<br>{$set.description.value}{/if}</p>
            <p><a class="editBtn" href="{$home_path}browse/metadata/{$set.dataSetId}">Metadata</a></p>
        </div>
    
    {/foreach}

{/block}