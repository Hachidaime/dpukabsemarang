<table class="bootstrap-table" data-search="{$data.search|default:'true'}" data-title="{$smarty.session.title}" {$data.data}>
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