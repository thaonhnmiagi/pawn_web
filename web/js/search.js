// format input datepicker
$(document).ready(function () {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            searchInfo();
        }
    });

    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            searchInfo();
        }
    });
});

function formatDate(inputDate) {
    var parts = inputDate.split("-");
    var formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
    return formattedDate;
}

function searchInfo() {
    var pawnIdValue = $('#input_pawn_id').val();
    var userIdValue = $('#input_user_id').val();
    var type = $('#type').val();
    var productDetail = $('#product_detail').val();

    var formatted_start_date = $('#start_date').val();
    var formatted_end_date = $('#end_date').val();
    var start_date = formatted_start_date ? formatDate(formatted_start_date) : null;
    var end_date = formatted_end_date ? formatDate(formatted_end_date) : null;

    var pawnStatus = $('#pawn_status').val();

    $('#error_message').html('');
    $('table tbody').empty();

    if (pawnIdValue !== '' || userIdValue !== '' || type !== '' || productDetail !== '' || formatted_start_date !== '' || formatted_end_date !== '' || pawnStatus !== '') {
        $.ajax({
            type: 'POST',
            url: 'check_pawn_info.php',
            data: {
                pawn_id: pawnIdValue,
                user_id: userIdValue,
                type: type,
                product_detail: productDetail,
                start_date: start_date,
                end_date: end_date,
                pawn_status: pawnStatus
            },
            success: function (response) {
                var data = JSON.parse(response);

                $('table tbody').empty();
                if (data.error) {
                    $('#error_message').html(data.error);
                } else {
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        var status = row.pawn_status === '0' ? 'Hết thời gian gia hạn' : 'Trong thời gian gia hạn';;

                        var newRow = '<tr>' +
                            '<td>' + row.id + '</td>' +
                            '<td>' + row.interest_rate_name + '</td>' +
                            '<td>' + row.product_detail + '</td>' +
                            '<td>' + row.price + '</td>' +
                            '<td>' + row.interest_rate_price + '</td>' +
                            '<td>' + row.interest_rate_time + ' tháng' + '</td>' +
                            '<td>' + row.start_date + '</td>' +
                            '<td>' + row.end_date + '</td>' +
                            '<td>' + status + '</td>' +
                            '<td>' + row.warehouse_id + '</td>' +
                            '<td class="actions">' +
                            '<a href="update_pawn_info.php?id=' + row.id + '">Cập nhật</a> ' +
                            '<a href="register_pawn_detail.php?id=' + row.id + '">' + (row.product_detail ? 'Cập nhật chi tiết' : 'Thêm chi tiết') + '</a> ' +
                            '<a href="delete_product.php?id=' + row.id + '">Xóa</a>' +
                            '</td>' +
                            '</tr>';

                        $('table tbody').append(newRow);
                    }
                }
            }
        })
    }
}