{extends file='standardHead.tpl'}
{block name='content'}
    <div id="controls">
        <a class="editBtn" href="#">Edit entry</a>
        <a class="editBtn" href="#">Drop entry</a>
    </div>
    <div id="formtext">
        <h2>{$interest.tim_value}</h2>
        <div class="formTbl">
            <div class="formRow">
                <div class="formLbl">Title:</div>
                <div class="formCell">
                    {$interest.title}
                </div>
            </div>
            <div class="formRow">
                <div class="formLbl">Field of interest:</div>
                <div class="formCell">
                {$interest.tim_value}
            </div>
        </div>
        <div class="formRow">
            <div class="formLbl">Description:</div>
            <div class="formCell">
            {$interest.description}
        </div>
    </div>
    <div class="formRow">
        <div class="formLbl">Persons:</div>
        <div class="formCell">
            {foreach from=$interest.persons item=person}
                <a href="{$home_path}show/clusius_Persons/{$person.uri}">{$person.name}</a><br>
            {/foreach}
        </div>
    </div>
    </div>
</div>
<div id="footerControls">
    <a class="editBtn" href="javascript:history.back();">Back</a>
</div>            

{/block}