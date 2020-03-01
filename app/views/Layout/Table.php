<table class="bootstrap-table" data-toolbar="#toolbar" data-search="{$data.search|default:'true'}" data-show-refresh="true" data-show-toggle="false" data-show-fullscreen="false" data-show-columns="false" data-show-columns-toggle-all="false" data-detail-view="false" data-show-export="true" data-click-to-select="false" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-show-pagination-switch="false" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="false" data-side-pagination="server" data-url="{$data.url}" data-response-handler="responseHandler" data-title="{$smarty.session.title}" data-row-style="rowStyle">
    <thead>
        {foreach from=$data.thead key=k item=v}
        <tr>
            {foreach from=$v key=c item=i}
            {if $i.field eq 'row'}
            {assign var=theaddata value='data-halign="center" data-align="right" data-width="50"'}
            {elseif $i.field eq 'operate'}
            {assign var=theaddata value='data-halign="center" data-align="center" data-width="100" data-formatter="operateFormatter" data-events="operateEvents"'}
            {elseif $i.field eq 'view'}
            {assign var=theaddata value='data-halign="center" data-align="center" data-width="50" data-formatter="viewFormatter" data-events="viewEvents"'}
            {elseif $i.field eq 'coord'}
            {assign var=theaddata value='data-halign="center" data-align="center" data-width="50" data-formatter="coordFormatter" data-events="coordEvents"'}
            {else}
            {assign var=theaddata value=$i.data}
            {/if}
            <th data-field="{$i.field}" data-valign="top" {$theaddata}>{$i.title}</th>
            {/foreach}
        </tr>
        {/foreach}
    </thead>
</table>