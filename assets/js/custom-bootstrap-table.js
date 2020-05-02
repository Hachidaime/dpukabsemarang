/**
 * * assets/js/custom-bootstrap-table.js
 * ? Javascript untuk handle Bootstrap Table
 */

/**
 * * Mendefinisikan bariable
 */
let $table = $('.bootstrap-table');
let selections = [];

/**
 * * Function ready
 */
$(function () {
    // TODO: Inisiasi table
    initTable();
    // * Table Options
    $table.addClass('table-sm');

    // * Click Add button
    $('#add').on('click', function () {
        window.location.href = `${base_url}/${controller}/${method}/add`;
    });
});

/* Bootstrap Table */
function getIdSelections() {
    return $.map($table.bootstrapTable('getSelections'), function (row) {
        return row.id
    })
}

function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, selections) !== -1
    })
    return res
}

function detailFormatter(index, row) {
    let html = []
    $.each(row, function (key, value) {
        html.push(/*html*/`<p><b>${key}:</b> ${value}</p>`);
    });
    return html.join('');
}

function operateFormatter(value, row, index) {
    return [
        /*html*/`<a class="edit" href="javascript:void(0)" title="Edit">`,
        /*html*/`<i class="fas fa-edit text-warning"></i>`,
        /*html*/`</a>  `,
        /*html*/`<a class="remove" href="javascript:void(0)" title="Remove">`,
        /*html*/`<i class="fas fa-trash text-danger"></i>`,
        /*html*/`</a>`
    ].join('')
}

function viewFormatter(value, row, index) {
    return [
        /*html*/`<a class="info" href="javascript:void(0)" title="Info">`,
        /*html*/`<i class="fas fa-clipboard text-info"></i>`,
        /*html*/`</a>  `
    ].join('')
}

function coordFormatter(value, row, index) {
    return [
        /*html*/`<a class="coord" href="javascript:void(0)" title="Edit Koordinat">`,
        /*html*/`<i class="fas fa-edit text-warning"></i>`,
        /*html*/`</a>  `
    ].join('')
}

function rowStyle(row, index) {
    if (row.segment > 0) {
        return {
            classes: "clr-food-egg"
        }
    }
    if (row.new == true) {
        return {
            classes: "clr-food-cherry"
        }
    }
    return "bg-light";
}

window.operateEvents = {
    'click .edit': function (e, value, row, index) {
        window.location.href = `${base_url}/${controller}/${method}/edit/${row.id}`;
    },
    'click .remove': function (e, value, row, index) {
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
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.value) {
                let url = `${base_url}/${controller}/${method}/remove`;
                $.post(url, `id=${row.id}`, function (data) {
                    makeAlert(data);
                    $table.bootstrapTable('refresh');
                });
            }
        });
    }
}

window.viewEvents = {
    'click .info': function (e, value, row, index) {
        let modal = $('#myModal');
        modal.modal('show');

        let title = $table.data('title');
        modal.find('.modal-title').text(`Detail ${title}`);
        modal.find('.modal-body').html(row.view);
    }
}

window.coordEvents = {
    'click .coord': function (e, value, row, index) {
        let no_jalan = $('.myForm').find('#no_jalan').val();

        let modal = $('#koordinatModal');
        modal.modal('show');
        modal.find('.modal-title').text(`Edit Koordinat #${row.row}`);

        let selectinput = ['perkerasan', 'kondisi'];

        let myForm = $('.koordinatForm');
        $.each(row, function (k, v) {
            if (selectinput.includes(k)) {
                // console.log(k);
                if (v != null || v != 'undefined') {
                    myForm.find(`#${k}`).val(v);
                    myForm.find(`#${k}`).selectpicker('refresh');
                }
                else {
                    myForm.find(`#${k}`).val('0');
                    myForm.find(`#${k}`).selectpicker('refresh');
                }
            }
            else if (k == 'foto' && v != null) {
                let url = `${base_url}/FileHandler/checkUploadedFile`;
                let foto_url = `img/jalan/${no_jalan}/${row.row}/${v}`;
                let params = {};
                params['filepath'] = foto_url;

                $.post(url, params, function (data) {
                    if (data.status == 200) {
                        myForm.find(`#${k}`).val(v);
                        let preview = myForm.find('#previewfoto');
                        preview.show();
                        preview.find('a').attr('href', data.file);
                        preview.find('img').attr('src', data.file);

                        let file_action = myForm.find('#file-actionfoto');
                        file_action.show();
                        file_action.find('span.filename').text(v);
                        file_action.find('a').attr('href', data.file);
                    }
                }, 'json');
            }
            else {
                myForm.find(`#${k}`).val(v);
            }

        });
        myForm.find(`#index`).val(row.row - 1);
        myForm.find(`#tag`).val('edit');
    }
}

function initTable() {
    $table.bootstrapTable('destroy').bootstrapTable({
        exportDataType: 'all',
        exportTypes: ['csv', 'excel', 'pdf'],
        exportOptions: {
            jspdf: { orientation: 'l' }
        },
        forceExport: true
    });

    $table.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table',
        function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

            // save your data, here just save the current page
            selections = getIdSelections()
            // push or splice the selections if you want to save all data selections
        })
    $table.on('all.bs.table', function (e, name, args) {
        // console.log(name, args)
    })
}