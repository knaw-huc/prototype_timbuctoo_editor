{extends file='standardHead_huc.tpl'}
{block name='content'}
    <div id="hcBlockText hcBasicSideMargin">
        <div class="hcHeadSpace hcBasicSideMargin">
      <h1>{$person.name}</h1>
    </div>
    <div id="controls">
        <a class="btnLink" href="{$editURI}">Edit entry</a>
        <a class="btnLink" href="{$editURI}">Drop entry</a>
    </div>
    <div id="formtext">
        <div class="formTbl">
            <div class="formRow">
                <div class="formLbl">Alternative name(s):</div>
                <div class="formCell">
                    {foreach from=$person.aka item=altName}
                        {$altName}
                    {/foreach}    
                </div>
            </div>
            <div class="formRow">
                <div class="formLbl">Gender:</div>
                <div class="formCell">
                    {$person.gender}
                </div>
            </div>
            <div class="formRow">
                <div class="formLbl">Born:</div>
                <div class="formCell">
                {$person.birth_date}{if $person.birth_place != ""}{if $person.birth_date != ""} {/if}{$person.birth_place}{/if}
            </div>
        </div>
        <div class="formRow">
            <div class="formLbl">Died:</div>
            <div class="formCell">
            {$person.death_date}{if $person.death_place != ""}{if $person.death_date != ""} {/if}{$person.death_place}{/if}
        </div>
    </div>
    <div class="formRow">
        <div class="formLbl">Residence:</div>
        <div class="formCell">
            {foreach from=$person.residence item=place}
                <a href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Residence/{$place.uri}">{$place.location}</a><br>
            {/foreach}
        </div>
    </div>
    <div class="formRow">
        <div class="formLbl">Occupation(s):</div>
        <div class="formCell">
            {foreach from=$person.occupation item=place}
                <a href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Occupation/{$place.uri}">{$place.tim_description}</a> {$place.years}<br>
            {/foreach}
        </div>
    </div>
    <div class="formRow">
        <div class="formLbl">Education:</div>
        <div class="formCell">
            {foreach from=$person.education item=place}
                <a href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Education/{$place.uri}">{$place.tim_description}</a> {$place.years}<br>
            {/foreach}
        </div>
    </div>
    <div class="formRow">
        <div class="formLbl">Biography:</div>
        <div class="formCell">
            {$person.bio|nl2br}
            {if $person.bio != ""}
                <a href="{$home_path}show/clusius_" class="moreLink">More...</a>{/if}
            </div>
        </div>
        <div class="formRow">
            <div class="formLbl">Fields of interest:</div>
            <div class="formCell">
                {foreach from=$person.interest item=value}
                    <a href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Fields_of_interest/{$value.uri}">{$value.interest}</a><br>
                {/foreach}
            </div>
        </div>
        <div class="formRow">
            <div class="formLbl">Membership:</div>
            <div class="formCell">
                {foreach from=$person.member item=value}
                    <a href="{$home_path}show/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Memberships/{$value.uri}">{$value.title}</a><br>
                {/foreach}
            </div>
        </div>
    </div>
</div>
<div id="footerControls">
    <a class="btnLink" href="javascript:history.back();">Back</a>
</div>            
            </div>
{/block}