{extends file='standardHead_huc.tpl'}
{block name='content'}
<div id="hcBlockText hcBasicSideMargin">
        <div class="hcHeadSpace hcBasicSideMargin">
      <h1>Persons</h1>
    </div>
<div id="controls">
   <a class="btnLink" href="{$home_path}create/{$set}/{$id}">New entry</a>

</div>
{foreach from=$persons item="person"}
    <div class="personListItem">
        <p>{$person.name}</p>
        <p>
            <a class="btnLink" href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Persons/{$person.uri}">View entry</a>
        </p>
    </div>
{/foreach}
</div>
{/block}