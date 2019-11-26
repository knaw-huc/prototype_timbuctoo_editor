{extends file='standardHead.tpl'}
{block name='content'}
 <div id="controls">
        <a class="editBtn" href="{$home_path}edit/metadata/{$id}">Edit metadata</a>
        <a class="editBtn" href="{$home_path}delete/metadata/{$id}">Delete dataset</a>
    </div>
    <div id="formtext">
        <h2>Metadata</h2>
        <div class="formTbl">
            {foreach $data as $element}
            <div class="formRow">
                <div class="formLbl">{$element@key}</div>
                <div class="formCell"> {$element|nl2br}
                </div>
            </div>
            {/foreach}
        </div>
    </div>
{/block}