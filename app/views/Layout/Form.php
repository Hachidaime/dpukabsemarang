<div class="container mb-3">
    <form class="{$data.formClass|default:'myForm'}" enctype="multipart/form-data" autocomplete="off">
        {foreach from=$data.form key=k item=v}
        {if $v.type eq 'hidden'}
        <input type="{$v.type}" name="{$v.name}" id="{$v.id}" value="{$data.detail[$v.name]}">
        {elseif $v.type eq 'switch'}
        <div class="form-group row">
            {if !$data.mini}
            <div class="col-lg-3 col-md-4">&nbsp;</div>
            {/if}
            <div class="{if !$data.mini}col-lg-9 col-md-8{else}col-12{/if}">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="{$v.id}" name="{$v.name}" {if $data.detail[$v.name]}checked{/if}> <label class="custom-control-label" for="{$v.id}">{$v.label}</label>
                </div>
            </div>
        </div>
        {else}
        <div class="form-group row">
            <label for="{$v.id}" class="{if !$data.mini}col-lg-3 col-md-4{else}col-12{/if} col-form-label">{$v.label}{if $v.required}<sup><i class="text-danger fas fa-asterisk"></i></sup>{/if}</label>
            <div class="{if !$data.mini}col-md-6{else}col-12{/if}">
                {if $v.type eq 'password'}
                <input type="password" class="form-control" id="{$v.id}" name="{$v.name}" value="">
                {elseif $v.type eq 'select'}
                <select class="form-control selectpicker" id="{$v.id}" name="{$v.name}" data-size="5" data-live-search="true" data-style="border">
                    <option value="0">&nbsp;</option>
                    {html_options options=$v.options selected={$data.detail[$v.name]}}
                </select>
                {elseif $v.type eq 'textarea'}
                <textarea class="form-control" id="{$v.id}" name="{$v.name}" rows="3">{$data.detail[$v.name]}</textarea>
                {elseif $v.type eq 'img'}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon{$v.id}">
                            <i class="fas fa-file-image"></i>
                        </span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input file-upload" id="inputGroupFile{$v.id}" aria-describedby="inputGroupFileAddon{$v.id}" accept="image/*" data-id="{$v.id}">
                        <label class="custom-file-label" for="inputGroupFile{$v.id}">Choose file</label>
                    </div>
                </div>
                <input type="hidden" class="input-file" name="{$v.name}" id="{$v.id}" value="{$data.detail[$v.name]}" data-id="{$v.id}" />
                <div id="preview{$v.id}" class="mt-2" style="display:none;">
                    <a href="{$smarty.const.SERVER_BASE}/upload/img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" data-toggle="lightbox" data-title="{$data.detail[$v.name]}">
                        <img src="{$smarty.const.SERVER_BASE}/upload/img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" class="img-fluid" width="300">
                    </a>
                </div>
                <ul id="file-action{$v.id}" class="list-group mt-2" style="display: none">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px2">
                        <span class="filename">{$data.detail[$v.name]}</span>
                        <a class="badge badge-light badge-pill" title="Download" href="{$smarty.const.UPLOAD_URL}img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" download><i class="fas fa-download"></i></a>
                    </li>
                </ul>
                {elseif $v.type eq 'video'}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon{$v.id}">
                            <i class="fas fa-file-video"></i>
                        </span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input file-upload" id="inputGroupFile{$v.id}" aria-describedby="inputGroupFileAddon{$v.id}" accept="video/*" data-id="{$v.id}">
                        <label class="custom-file-label" for="inputGroupFile{$v.id}">Choose file</label>
                    </div>
                </div>
                <input type="hidden" class="input-file" name="{$v.name}" id="{$v.id}" value="{$data.detail[$v.name]}" data-id="{$v.id}" />
                <ul id="file-action{$v.id}" class="list-group mt-2" style="display: none">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px2">
                        <span class="filename">{$data.detail[$v.name]}</span>
                        <a class="badge badge-light badge-pill" title="Download" href="{$smarty.const.UPLOAD_URL}video/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" download><i class="fas fa-download"></i></a>
                    </li>
                </ul>
                {elseif $v.type eq 'pdf'}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon{$v.id}">
                            <i class="fas fa-file-pdf"></i>
                        </span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input file-upload" id="inputGroupFile{$v.id}" aria-describedby="inputGroupFileAddon{$v.id}" accept="application/pdf" data-id="{$v.id}">
                        <label class="custom-file-label" for="inputGroupFile{$v.id}">Choose file</label>
                    </div>
                </div>
                <input type="hidden" class="input-file" name="{$v.name}" id="{$v.id}" value="{$data.detail[$v.name]}" data-id="{$v.id}" />
                <ul id="file-action{$v.id}" class="list-group mt-2" style="display: none">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px2">
                        <span class="filename">{$data.detail[$v.name]}</span>
                        <a class="badge badge-light badge-pill" title="Download" href="{$smarty.const.SERVER_BASE}/upload/pdf/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" download><i class="fas fa-download"></i></a>
                    </li>
                </ul>
                {elseif $v.type eq 'kml'}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon{$v.id}">
                            <i class="fas fa-file-code"></i>
                        </span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input file-upload" id="inputGroupFile{$v.id}" aria-describedby="inputGroupFileAddon{$v.id}" accept="application/kml" data-id="{$v.id}">
                        <label class="custom-file-label" for="inputGroupFile{$v.id}">Choose file</label>
                    </div>
                </div>
                <input type="hidden" class="input-file" name="{$v.name}" id="{$v.id}" value="{$data.detail[$v.name]}" data-id="{$v.id}" />
                <ul id="file-action{$v.id}" class="list-group mt-2" style="display: none">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px2">
                        <span class="filename">{$data.detail[$v.name]}</span>
                        <a class="badge badge-light badge-pill" title="Download" href="{$smarty.const.SERVER_BASE}/upload/kml/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" download><i class="fas fa-download"></i></a>
                    </li>
                </ul>
                {elseif $v.type eq 'date'}
                <div class="input-group">
                    <input type="text" class="form-control datepicker" id="{$v.id}" name="{$v.name}" aria-describedby="date-trigger{$v.id}" value="{$data.detail[$v.name]|date_format:$smarty.const.SMARTY_DATE_FORMAT}">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text date-trigger" id="date-trigger{$v.id}" data-id="{$v.id}"><i class="fas fa-calendar-alt"></i></button>
                    </div>
                </div>
                {elseif $v.type eq 'range'}
                <div class="row mx-1 my-1">
                    <input type="range" class="col-10 form-control-range custom-range" id="{$v.id}" name="{$v.name}" value="{$data.detail[$v.name]|default:'0'}" oninput="Output{$v.id}.value = {$v.id}.value">
                    <output class="col-2 text-center" name="Output{$v.name}" id="Output{$v.id}">{$data.detail[$v.name]|default:'0'}</output>
                </div>
                {elseif $v.type eq 'range10'}
                <div class="row mx-1 my-1">
                    <input type="range" class="col-10 form-control-range custom-range" id="{$v.id}" name="{$v.name}" min="0" max="10" value="{$data.detail[$v.name]|default:'0'}" oninput="Output{$v.id}.value = {$v.id}.value">
                    <output class="col-2 text-center" name="Output{$v.name}" id="Output{$v.id}">{$data.detail[$v.name]|default:'0'}</output>
                </div>
                {elseif $v.type eq 'range5'}
                <div class="row mx-1 my-1">
                    <input type="range" class="col-10 form-control-range custom-range" id="{$v.id}" name="{$v.name}" min="0" max="5" value="{$data.detail[$v.name]|default:'0'}" oninput="Output{$v.id}.value = {$v.id}.value">
                    <output class="col-2 text-center" name="Output{$v.name}" id="Output{$v.id}">{$data.detail[$v.name]|default:'0'}</output>
                </div>
                {elseif $v.type eq 'color'}
                <input type="text" class="form-control colorpicker" id="{$v.id}" name="{$v.name}" value="{$data.detail[$v.name]|default:'#ffffff'}">
                {elseif $v.type eq 'tag'}
                <input type="text" class="form-control tags" id="{$v.id}" name="{$v.name}" value="{$data.detail[$v.name]}">
                {elseif $v.type eq 'plain-text'}
                <input type="text" class="form-control-plaintext border rounded px-2" id="{$v.id}" readonly value="{$data.detail[$v.name]}">
                {elseif $v.type eq 'plain-textarea'}
                <textarea class="form-control-plaintext border rounded px-2" id="{$v.id}" readonly style="resize: none;">{$data.detail[$v.name]}</textarea>
                {elseif $v.type eq 'plain-img'}
                {if $data.detail[$v.name] ne ''}
                <div id="preview{$v.id}" class="mt-2">
                    <a href="{$smarty.const.SERVER_BASE}/upload/img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" data-toggle="lightbox" data-title="{$data.detail[$v.name]}">
                        <img src="{$smarty.const.SERVER_BASE}/upload/img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" class="img-fluid" width="300">
                    </a>
                </div>
                <ul id="file-action{$v.id}" class="list-group mt-2">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px2">
                        <span class="filename">{$data.detail[$v.name]}</span>
                        <a class="badge badge-light badge-pill" title="Download" href="{$smarty.const.UPLOAD_URL}img/{$data.controller|lower}/{$data.detail.id}/{$data.detail[$v.name]}" download><i class="fas fa-download"></i></a>
                    </li>
                </ul>
                {else}
                -
                {/if}
                {elseif $v.type eq 'token'}
                <div class="input-group">
                    <input type="text" class="form-control-plaintext text-selection-none" readonly id="{$v.id}" name="{$v.name}" aria-describedby="token-trigger{$v.id}" value="{$data.detail[$v.name]}">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text token-trigger" id="token-trigger{$v.id}" data-id="{$v.id}"><i class="fas fa-key"></i></button>
                    </div>
                </div>
                {else}
                <input type=" {$v.type}" class="form-control" id="{$v.id}" name="{$v.name}" value="{$data.detail[$v.name]}">
                {/if}
                {if $v.helper ne ''}<div class="text-muted pl-3"><em><small>{$v.helper}</small></em></div>{/if}
            </div>
        </div>
        {/if}
        {/foreach}
    </form>
</div>

<div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-labelledby="lightboxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-body">
                <img src="" class="img-fluid mx-auto d-block mb-3">
            </div>
        </div>
    </div>
</div>