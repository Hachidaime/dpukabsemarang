<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column mx-1">
            <li class="nav-item">
                <a class="nav-link text-{if $data.controller eq $smarty.const.DEFAULT_CONTROLLER}body{else}black-50{/if}" href="{$smarty.const.BASE_URL}/{$smarty.const.DEFAULT_CONTROLLER}"><i class="fas fa-home"></i>&nbsp;Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-{if $data.controller eq 'ViewGis'}body{else}black-50{/if}" href="{$smarty.const.BASE_URL}/ViewGis"><i class="fas fa-map"></i>&nbsp;View GIS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-{if $data.controller eq 'About'}body{else}black-50{/if}" href="{$smarty.const.BASE_URL}/About"><i class="fas fa-info-circle"></i>&nbsp;About</a>
            </li>
        </ul>

        {if $smarty.session.USER.menu_id}
        <div id="accordion" class="mx-1">
            {foreach from=$module key=idx item=row}
            <div class="card border-light">
                <div class="card-header p-0">
                    {if $data.controller eq $row.controller}
                    <a class="nav-link text-body" data-toggle="collapse" href="#collapse{$idx}">
                        <i class='fas' id="collapseIcon{$idx}">&#xf07c;</i>
                        &nbsp;{$row.module_name}
                    </a>
                    {else}
                    <a class="nav-link text-black-50" data-toggle="collapse" href="#collapse{$idx}">
                        <i class='fas' id="collapseIcon{$idx}">&#xf07b;</i>
                        &nbsp;{$row.module_name}
                    </a>
                    {/if}
                </div>
                <div id="collapse{$idx}" class="collapse {if $data.controller eq $row.controller}show{/if}" data-parent="#accordion" data-id="{$idx}">
                    <div class="card-body pl-3 py-0">
                        <ul class="nav flex-column">
                        {foreach from=$row.action key=k item=v}
                            <li class="nav-item">
                                {if $data.method eq $v.method}
                                <a class="nav-link py-1 text-body" href="{$smarty.const.BASE_URL}/{$row.controller}/{$v.method}">
                                    <i class='fas' id="collapse{$idx}Icon">&#xf15c;</i>
                                    &nbsp;{$v.action_name}</li>
                                </a>
                                {else}
                                <a class="nav-link py-1 text-black-50" href="{$smarty.const.BASE_URL}/{$row.controller}/{$v.method}">
                                    <i class='fas' id="collapse{$idx}Icon">&#xf15b;</i>
                                    &nbsp;{$v.action_name}</li>
                                </a>
                                {/if}
                            </li>
                        {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
        {/if}
    </div>
</nav>
