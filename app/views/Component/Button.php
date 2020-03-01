{if $data.tag eq 'button'}
<button type="button" id="{$data.id}" class="btn btn-{$data.color|default:'light'} {$data.class}" style="width: {$data.width}px">{$data.html}</button>
{else}
<a type="button" id="{$data.id}" class="btn btn-{$data.color|default:'light'} {$data.class}" style="width: {$data.width}px">{$data.html}</a>
{/if}