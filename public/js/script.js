
$(document).ready(function () {
    $('#product_name').val('');
    $('#quantity_in_stock').val('');
    $('#price_per_item').val('');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            clearError();
        }
    });

    $('#stock-form').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: '/',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                console.log('response', response)
                if (response.status === 'success') {
                    let {
                        data
                    } = response;
                    $('#product_name').val('');
                    $('#quantity_in_stock').val('');
                    $('#price_per_item').val('');
                    $('tbody').append(
                        `<tr data-id="${data.id}">
                            <td class="product_name">${data.product_name}</td>
                            <td class="quantity_in_stock">${data.quantity_in_stock}</td>
                            <td class="price_per_item">${data.price_per_item}</td>
                            <td>${formatDate(data.datetime_submitted)}</td>
                            <td class="total_value">${data.quantity_in_stock * data.price_per_item}</td>
                            <td>
                                <button type="button" class="btn btn-primary edit-btn" data-id="${data.id}" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil-square"></i></button>
                                <button type="button" class="btn btn-danger delete-btn" data-id="${data.id}" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>`
                    );
                    let total = 0;
                    $('tbody tr').each(function () {
                        total += parseInt($(this).find('td.total_value').text());
                    });
                    $('tfoot tr td:last').text(total);
                } else {
                    if (response.errors) {
                        showError(response.errors);
                    } else {
                        alert(response.message);
                    }

                }
            },
            error: function (response) {
                console.log('response', response)
                if (response.responseJSON.errors) {
                    showError(response.responseJSON.errors);
                } else {
                    alert(response.responseJSON.message);
                }
            }
        });
    });

    $('#editModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let id = button.parents('tr').data('id');
        let modal = $(this);
        modal.find('.modal-body input#id').val(id);
        modal.find('.modal-title').text(`Edit stock ` + $(`tr[data-id="${id}"] td.product_name`).text().trim());
        modal.find('.modal-body input#id').val(id);
        modal.find('.modal-body input[name="product_name"]').val($(`tr[data-id="${id}"] td.product_name`).text().trim());
        modal.find('.modal-body input[name="quantity_in_stock"]').val($(`tr[data-id="${id}"] td.quantity_in_stock`).text().trim());
        modal.find('.modal-body input[name="price_per_item"]').val($(`tr[data-id="${id}"] td.price_per_item`).text().trim());
    })

    $('#deleteModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let name = button.parents('tr').find('td.product_name').text().trim();
        let id = button.data('id');
        let modal = $(this);
        modal.find('.modal-title').text(`Delete stock ` + name);
        modal.find('.delete').data('id', id);
    })

    $(document).off('submit', '.edit-form').on('submit', '.edit-form', function (e) {
        e.preventDefault();
        let id = $(this).find('input#id').val();
        $.ajax({
            url: `/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    let {data} = response;
                    $(`tr[data-id="${id}"] td.product_name`).text(data.product_name);
                    $(`tr[data-id="${id}"] td.quantity_in_stock`).text(data.quantity_in_stock);
                    $(`tr[data-id="${id}"] td.price_per_item`).text(data.price_per_item);
                    $(`tr[data-id="${id}"] td.datetime_submitted`).text(data.datetime_submitted);
                    $(`tr[data-id="${id}"] td.total_value`).text(data.quantity_in_stock * data.price_per_item);
                    let total = 0;
                    $('tbody tr').each(function () {
                        total += parseInt($(this).find('td.total_value').text());
                    });
                    resetIndex()
                    $('tfoot tr td:last').text(total);
                } else {
                    if (response.errors) {
                        showError(response.errors);
                    } else {
                        alert('Server Error');
                    }
                }
            },
            complete: function () {
                $('#editModal').modal('hide');
            }
        })

    })

    $(document).off('click', '.delete').on('click', '.delete', function () {
        id = $(this).data('id');

        $.ajax({
            url: `/${id}`,
            method: 'DELETE',
            success: function (response) {
                if (response.status === 'success') {
                    $(`tr[data-id="${id}"]`).remove();
                    resetIndex();                 
                    let total = 0;
                    $('tbody tr').each(function () {
                        total += parseInt($(this).find('td.total_value').text());
                    });
                    
                    $('tfoot tr td:last').text(total);
                } else {
                    if (response.errors) {
                        showError(response.errors);
                    } else {
                        alert('Server Error');
                    }
                }
            },
            complete: function () {
                $('#deleteModal').modal('hide');
                resetIndex();
            },
            error: function (response) {
                console.log('response', response)
                if (response.responseJSON.errors) {
                    showError(response.responseJSON.errors);
                } else {
                    alert(response.responseJSON.message);
                }
            }
        });

    })

});
function resetIndex(){
    $('tbody tr').each(function (i, tr) {
        $(tr).attr('data-id', i);
        $(tr).find('[data-id]').attr('data-id', i);
    });
}

function showError(errors) {
    clearError();
    let html = `<div class="alert alert-danger alert-dismissible fade show" role="alert">`;
    Object.keys(errors).forEach(key => {
        html += `${errors[key][0]} <br>`;
    });
    html += `<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    $('#error-alert').html(html);
    $('#error-alert').show();
}

function clearError() {
    $('#error-alert').html('');
    $('#error-alert').hide();
}

function formatDate(datetime) {
    let date = new Date(datetime);
    date = new Date(date.toLocaleString('en-US', {timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone}));
    return date.toLocaleString();
}  