{foreach from=$data.item key=k item=v}
<div class="col-lg-3 col-md-4 col-sm-6 gallery-item mb-2">
    <a href="{$smarty.const.UPLOAD_URL}img/gallery/{$v.id}/{$v.upload_gallery}" data-toggle="lightbox" data-gallery="gallery" data-title="{$v.judul}" data-footer="{$v.tanggal|date_format:$smarty.const.SMARTY_DATE_FORMAT}">
        <img src="{$smarty.const.UPLOAD_URL}img/gallery/{$v.id}/{$v.upload_gallery}" class="img-fluid">
    </a>
    <div class="div">Judul : {$v.judul}</div>
    <div class="div">Tangal : {$v.tanggal|date_format:$smarty.const.SMARTY_DATE_FORMAT}</div>
</div>
{/foreach}