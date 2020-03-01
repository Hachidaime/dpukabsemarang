{foreach from=$data.item key=k item=v}
<div class="gallery-item mb-3 px-2">
    <div class="card">
        <a href="{$smarty.const.UPLOAD_URL}img/gallery/{$v.id}/{$v.upload_gallery}" data-toggle="lightbox" data-gallery="gallery" data-title="{$v.judul}" data-footer="{$v.tanggal|date_format:$smarty.const.SMARTY_DATE_FORMAT}">
            <img src="{$smarty.const.UPLOAD_URL}img/gallery/{$v.id}/{$v.upload_gallery}" class="card-img-top">
        </a>
        <div class="card-body border-top">
            <div class="div">Judul : {$v.judul}</div>
            <div class="div">Tangal : {$v.tanggal|date_format:$smarty.const.SMARTY_DATE_FORMAT}</div>
        </div>
    </div>
</div>
{/foreach}