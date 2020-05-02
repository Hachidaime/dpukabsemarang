<div id="toolbar">
    {foreach from=$data.toolbar key=k item=v}
    {$v}
    {/foreach}
</div>
{*$toolbar*}
<div class="main d-flex flex-column">
    {foreach from=$data.main key=k item=v}
    {$v}
    {/foreach}
</div>
{if $data.foot}
<div class="foot d-flex justify-content-center">
    {foreach from=$data.foot key=k item=v}
    <div class="mx-1">
        {$v}
    </div>
    {/foreach}
</div>
{/if}

<!-- Modal -->
{foreach from=$data.modal key=modalKey item=modal}
<div class="modal fade" id="{$modal.modalId|default:'myModal'}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-{$modal.modalSize|default:'lg'} modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{$modal.modalLabel}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {foreach from=$modal.modalBody key=k item=v}
                {$v}
                {/foreach}
            </div>
            {if $modal.modalFoot|count}
            <div class="modal-footer">
                {foreach from=$modal.modalFoot key=k item=v}
                {$v}
                {/foreach}
            </div>
            {/if}
        </div>
    </div>
</div>
{/foreach}