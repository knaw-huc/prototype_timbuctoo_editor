{extends file='page.tpl'}
{block name='head'}
    <link href="{$home_path}css/timpars.css" rel="stylesheet">
    <link href="{$home_path}css/ccfstyle.css" rel="stylesheet">
    <script src="{$home_path}js/jquery-3.3.1.min.js"></script>
    <script src="{$home_path}js/ccf_config_editor.js"></script>
    <script src="{$home_path}js/ccfparser_editor.js"></script>
    <script src="{$home_path}js/timpars.js"></script>
    <script>
            obj = {$json};
            $('document').ready(function(){literal}{{/literal}
            formBuilder.start(obj);
            {literal}}{/literal});
        </script>
{/block}