{$data.pager}

<div class="row" id="gallery-item"></div>

<div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-labelledby="lightboxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-body">
                <img src="" class="img-fluid mx-auto d-block mb-3">
                <div class="text-center">
                    <span id="data-judul">Lorem, ipsum dolor.</span>
                    (<span id="data-tanggal">{$smarty.now|date_format:$smarty.const.SMARTY_DATE_FORMAT}</span>)
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadGallery();
    });
</script>
{/literal}