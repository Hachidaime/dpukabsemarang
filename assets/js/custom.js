/**
 * * asset/js/custom.js
 * ? Javascript Global
 */

/**
 * * Ready Function
 */
$(document).ready(function () {
    // TODO: Clear localStorage
    localStorage.clear();

    /**
     * TODO: Submit Log In while press ENTER button
     */
    $("#loginForm input").keypress(function (e) {
        if (e.which == 13) {
            login();
        }
    });

    $('.btn-login').click(function () {
        login();
    });

    $('.btn-logout').click(function () {
        logout();
    })

    $('.btn-menu').click(function () {
        let menu_id = $(this).data('id');
        setMenu(menu_id);
    });

    $('.btn-add').click(function () {
        window.location.href = `${base_url}/${controller}/${method}/add`;
    });

    $('.btn-back').click(function () {
        window.history.back();
    });

    /**
     * * Submit Form Input while submit button clicked
     */
    $('.btn-submit').click(function () {
        // * Mendefinisikan variable
        let url = `${base_url}/${controller}/${method}/submit`;
        let params = $('.myForm').serialize();

        // TODO: Cek #mySwitch exist
        let mySwitch = $("#mySwitch");
        if (mySwitch.length) {
            // TODO: Set parameter dari nilai #mySwitch
            params += '&' + mySwitch.serialize();
        }

        // TODO: Post Form Input Data dengan Ajax Request
        $.post(url, params, function (data) {
            // TODO: Menampilkan Alert
            makeAlert(data);

            // TODO: Cek success
            if (Object.keys(data)[0] == 'success') {
                // TODO: Redirect ke halaman utama Controller
                setTimeout(function () {
                    window.location.href = `${base_url}/${controller}/${method}`;
                }, 3000);
            }
        }, "json");
    });

    // TODO: Menjalankan jQuery Datepicker melalui button
    $('.date-trigger').click(function () {
        let id = $(this).data('id');
        let picker = $(`#${id}.datepicker`);
        if (picker.datepicker('widget').is(':visible')) {
            picker.datepicker('hide');
        }
        else {
            picker.datepicker('show');
        }
    });


    /**
     * * File Upload
     */
    $('.file-upload').change(function () {
        /**
         * * Mendefinisikan variable
         */
        let input = $(this);
        let id = input.data('id');
        let preview = $(`#preview${id}`);
        let file_action = $(`#file-action${id}`);
        let files = input[0].files[0];
        let accept = input.attr('accept');
        let url = `${base_url}/FileHandler/Upload`;

        /**
         * * Mendefinisikan Input Data
         */
        let fd = new FormData();
        fd.append('file', files);
        fd.append('accept', accept);

        // ToDO: Ajax Request
        $.ajax({
            url: url,
            type: 'post',
            data: fd,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {
                // TODO: Menampilkan Alert
                makeAlert(data.alert);

                // TODO: Cek Status Upload
                if (Object.keys(data.alert)[0] == 'warning') {
                    // ? Upload Berhasil

                    // TODO: Menampilkan preview gambar
                    preview.show();
                    preview.find('img').attr({
                        'src': data.source,
                        'alt': data.filename
                    });
                    preview.find('a').attr({
                        'href': data.source
                    })

                    // TODO: Menampilkan link download dari file yang diupload
                    file_action.show();
                    file_action.find('.filename').text(data.filename);
                    file_action.find('a').attr('href', data.source);

                    // TODO: Set input value untuk file upload
                    $(`#${id}`).val(data.filename);
                }
            }
        });
    });

    /**
     * * Menampilkan Preview & Link Download Tersimpan
     */
    $('.input-file').each(function (idx, row) {
        let id = $(this).data('id');
        let preview = $(`#preview${id}`);
        let file_action = $(`#file-action${id}`);

        // TODO: Cek file exist
        if ($(this).val() != '') {
            // TODO: Menampilkan Preview & Link Download
            preview.show();
            file_action.show();
        }
        else {
            // ! Sembunyikan Preview & Link Download
            preview.hide();
            file_action.hide();
        }
    });

    $('.btn-gallery-page').click(function () {
        $('.page-item').removeClass("active");
        let page = $(this).data('page');
        $(this).parent().addClass("active");
        loadGallery(page);
    });

    /**
     * * Mendefinisikan jQuery Datepicker
     */
    $(".datepicker").datepicker({
        dateFormat: "dd/mm/yy",
        showAnim: "slideDown"
    });

    /**
     * * Mendefinisikan ColorPickerSliders
     */
    $('.colorpicker').ColorPickerSliders({
        flat: true,
        swatches: ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#f8f9fa', '#343a40', '#ffffff'],
        customswatches: false,
        previewformat: 'hex',
        order: {}
    });

    /**
     * * Mendefinisikan Bootstrap Tagsinput
     */
    $('.tags').tagsinput({
        tagClass: function (item) {
            return 'badge badge-info mr-1';
        }
    });

    /**
     * * Menambahkan Tag pada Bootstrap Tagsinput
     * * saat select change
     */
    $('select').change(function () {
        /**
         * * Mendefinisikan variable
         */
        let selected_opt = $(this).find('option:selected').text();
        let id = $(this).attr('id');
        let old_tag = localStorage.getItem(id);
        let tags = $('.tags');

        // TODO: Cek old tag
        if (old_tag != null) {
            // TODO: Remove old tag
            tags.tagsinput('remove', old_tag);
        }

        // TODO: Add new tag
        localStorage.setItem(id, selected_opt);
        tags.tagsinput('add', selected_opt);
    }).change();

    $('.token-trigger').click(function () {
        let url = `${base_url}/Otentifikasi/getKey`;
        let id = $(this).data('id');

        $.post(url, function (data) {
            $(`#${id}`).val(data);
        }, "json");
    }).click();

    /**
     * * Menampilkan Lightbox Modal
     */
    $('#lightboxModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget) // ? Button that triggered the modal
        let recipient = button.data('whatever') // ? Extract info from data-* attributes
        // ? If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // ? Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        let modal = $(this)
        modal.find('.modal-title').text(`New message to ${recipient}`)
        modal.find('.modal-body input').val(recipient)
    });

    $('.btn-gen-coord').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success mx-1',
                cancelButton: 'btn btn-danger mx-1'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            icon: 'warning',
            text: 'This action will reset segmentation.',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.value) {
                genSegment();
            }
        });
    });

    $('#panjang_text').val($('#panjang').val());

    $('#koordinatModal').on('hidden.bs.modal', function () {
        let modal = $('#koordinatModal');
        modal.find('.modal-body').html();
        clearKoordinatModal();
    });

    $('.btn-cancel-koordinat').click(function () {
        let modal = $('#koordinatModal');
        modal.modal('hide');
    });

    $('.btn-submit-koordinat').click(function () {
        let params = $('.koordinatForm').serialize();
        let url = `${base_url}/${controller}/Koordinat/submit`;
        let modal = $('#koordinatModal');

        $.post(url, params, function (data) {
            makeAlert(data);
            if (Object.keys(data)[0] == 'success') {
                modal.modal('hide');
                $table.bootstrapTable('refresh');
            }
        }, "json");
    });

    $('.btn-sidebar-open').click(function () {
        openNav();
    });

    $('.btn-sidebar-close').click(function () {
        closeNav();
    });

    $(".nav-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    const searchGisForm = $('.searchGisForm');

    searchGisForm.find('select#kepemilikan').change(function () {
        let kepemilikan = this.value;
        let url = `${base_url}/Gis/index/jalan`;
        let params = {};
        params['kepemilikan'] = kepemilikan;

        // let jalan_opt = searchGisForm.find('select#no_jalan');

        clearLines();
        // let html = [];
        // html.push(/*html*/`<option value="0">&nbsp;</option>`);
        // jalan_opt.html(html.join(''));
        // jalan_opt.selectpicker('refresh');

        $.post(url, $.param(params), function (data) {
            if (Object.keys(data).length > 0) {
                loadLines();
            }
            else {
                makeAlert(JSON.parse('{"danger":["Data tidak ditemukan."]}'));
                // initMap();
            }

            // if (Object.keys(data).length != 0) {
            //     html.push(/*html*/`<option value="all">Semua</option>`);
            //     $.each(data, function (k, v) {
            //         html.push(/*html*/`<option value="${k}">${k} -> ${v}</option>`);
            //     });
            // }

            // jalan_opt.html(html.join(''));
            // jalan_opt.selectpicker('refresh');

        }, 'json');
    });

    $('input[type=checkbox]#jalan_provinsi').change(function () {
        loadSwitch();
    });

    $('input[type=checkbox]#perkerasan').change(function () {
        loadSwitch();
    });

    $('input[type=checkbox]#kondisi').change(function () {
        loadSwitch();
    });

    $('input[type=checkbox]#segmentasi').change(function () {
        if (this.checked) loadSegmentasi();
        else clearSegmentasi();
    });

    $('input[type=checkbox]#awal').change(function () {
        if (this.checked) loadAwal();
        else clearAwal();
    });

    $('input[type=checkbox]#akhir').change(function () {
        if (this.checked) loadAkhir();
        else clearAkhir();
    });

    $('.btn-search-gis').click(function () {
        let params = searchGisForm.serialize();
    });

}).ajaxStart(function () {
    // TODO: Menampilkan loading spinner
    $('.loading').show();
}).ajaxStop(function () {
    // TODO: Menyembunyikan loading spinner
    $('.loading').hide();
});

$(document).on('click', '[data-toggle="lightbox"]', function (event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});

window.onscroll = function () { scrollFunction() };