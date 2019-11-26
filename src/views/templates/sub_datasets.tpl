{foreach from=$sets item=set}
    <a href="{$home_path}browse/metadata/{$set.dataSetId}" class="sub" style="margin-left: 50px;">{if !is_null($set.title)}{$set.title.value}{else}{$set.dataSetName}{/if}</a>    
{/foreach}