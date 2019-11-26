{extends file='page_huc.tpl'}
{block name='head'}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<link rel="stylesheet" href="https://huc-css-util.netlify.com/css/huc-util-huygens.css">-->
    <link href="{$home_path}css/huc_style.css" rel="stylesheet">
    <link href="{$home_path}css/additional_huc_style.css" rel="stylesheet">
    <link href="{$home_path}css/ccfstyle_huc.css" rel="stylesheet">
    <script src="{$home_path}js/jquery-3.3.1.min.js"></script>
    <script src="{$home_path}js/ccf_config_editor.js"></script>
    <script src="{$home_path}js/ccfparser_editor.js"></script>
    <script src="{$home_path}js/timpars.js"></script>
    {if isset($json)}
    <script>
            obj = {$json};
            $('document').ready(function(){literal}{{/literal}
            formBuilder.start(obj);
            {literal}}{/literal});
        </script>
    {/if}
{/block}