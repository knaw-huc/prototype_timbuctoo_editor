{extends file='standardHead.tpl'}
{block name='content'}
<h2>Persons</h2>
<div id="controls">
   <a class="editBtn" href="">New entry</a> 
</div>
{foreach from=$persons item="person"}
    <div class="personListItem">
        <p>{$person.name}</p>
        <p>
            <a class="editBtn" href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Persons/{$person.uri}">View entry</a>
        </p>
    </div>
{/foreach}
{/block}