<form id="mySwitch">
    {foreach from=$data.access key=a item=i}
    <legend class="text-primary">{$i.name}</legend>
    <div class="d-flex flex-wrap justify-content-start mb-3">
        {assign var=color value=$smarty.const.BS_COLOR}
        {foreach from=$i.module key=b item=j}
        {if $b > 8}
        {assign var=key value=$b % 8}
        {else}
        {assign var=key value=$b}
        {/if}
        <div class="p-2 col-sm-4">
            <ul class="list-group">
                <li class="list-group-item list-group-item-{$color[$key]}">
                    <h5>{$j.name}</h5>
                </li>
                {foreach from=$j.action key=c item=k}
                <li class="list-group-item">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="action{$c}" name="action[{$a}][{$b}][{$c}]" {$k.checked}>
                        <label class="custom-control-label" for="action{$c}">{$k.name}</label>
                    </div>
                </li>
                {/foreach}
            </ul>
        </div>
        {/foreach}
    </div>
    {/foreach}
</form>