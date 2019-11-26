<nav id="navigationFromHeaders" class="navigation hcAlignVertical">
    <a href="{$home_path}browse/metadata/{$dataset}">Metadata</a>
    <a href="#"><em>Collections</em></a>
    {foreach from=$collections item=collection}
        <a href="{$home_path}browse/list/{$dataset}/{$collection.name}">{$collection.label}</a>
    {/foreach}
    {if $logged_in}
    <button onclick="addCollection()">Add collection</button>
    {/if}
</nav>