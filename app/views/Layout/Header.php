{assign var=active value='text-white'}
{if $data.controller ne 'Gis'}
<img src="{$smarty.const.SERVER_BASE}/assets/img/header.jpeg" id="header-img" class="img-fluid w-100 header-banner" height="200px">
{/if}
<nav class="navbar navbar-expand-lg bg-transparent navbar-dark sticky-top p-0">
    <div class="container-md d-flex flex-column justify-content-beetween p-0">
        <div class="bg-primary w-100">
            <button class="navbar-toggler mr-2" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-lg-none" href="{$smarty.const.BASE_URL}/Home">
                <img src="{$smarty.const.SERVER_BASE}/assets/img/navlogo.png" alt="Logo" style="width:200px;">
            </a>
            <a class="active py-2 loading2" style="display: none;">
                <div class="spinner-border spinner-border-sm text-light" role="status">
                </div>
            </a>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav align-content-start flex-wrap">
                    {foreach from=$menu key=k item=v}
                    <li class="nav-item">
                        <a class="nav-link {if $data.controller eq $v.class_name}{$active}{/if} px-3" href="{$smarty.const.BASE_URL}/{$v.class_name}">{$v.name|upper}</a>
                    </li>
                    {/foreach}
                    {if isset($smarty.session.admin) && isset($smarty.session.USER)}
                    {foreach from=$module key=idx item=row}
                    {assign var=first value = $row.action|@key}
                    {if $row.action|count > 1}
                    <li class="nav-item dropdown">
                        <a class="nav-link {if $data.controller eq $row.controller}{$active}{/if} dropdown-toggle px-3" href="#" data-toggle="dropdown">
                            {$row.module_name|upper}
                        </a>
                        <div class="dropdown-menu py-0">
                            {foreach from=$row.action key=k item=v}
                            <a class="dropdown-item py-1" href="{$smarty.const.BASE_URL}/{$row.controller}/{$v.method}">{$v.action_name}</a>
                            {/foreach}
                        </div>
                    </li>
                    {else}
                    <li class="nav-item">
                        <a class="nav-link {if $data.controller eq $row.controller}{$active}{/if} px-3" href="{$smarty.const.BASE_URL}/{$row.controller}/{$row.action.$first.method}">{$row.module_name|upper}</a>
                    </li>
                    {/if}
                    {/foreach}
                    {/if}
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="navbar-item active py-3 loading" style="display: none;">
                        <div class="spinner-border spinner-border-sm text-light" role="status">
                        </div>
                        <span class="text-light">Please Wait...</span>
                    </li>
                    {if isset($smarty.session.admin) && isset($smarty.session.USER)}
                    <li class="nav-item px-3 dropdown active">
                        <a class="nav-link" href="#" id="navbardrop" data-toggle="dropdown">
                            <span class="fas fa-map-marker-alt"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right py-0">
                            {foreach from=$system key=k item=v}
                            <a class="dropdown-item py-1 text-{if $k eq $smarty.session.USER.menu_id}body{else}black-50{/if} btn-menu" href="javascript:void(0);" data-id="{$k}">{$v}</a>
                            {/foreach}
                        </div>
                    </li>
                    <a class="nav-link px-3 btn-logout" href="javascript:void(0);">LOG OUT</a>
                    {else}
                    <a class="nav-link px-3 {if $data.controller eq 'Login'}{$active}{/if}" href="{$smarty.const.BASE_URL}/Login">LOG IN</a>
                    {/if}
                </ul>
            </div>
        </div>
        <div class="px-2 pb-2 pt-0 bg-light w-100 text-center border-bottom">
            <span class="title-wrapper h1">{$smarty.session.title}</span>
        </div>
    </div>
</nav>