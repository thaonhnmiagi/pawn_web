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

    let printToPaperButton = document.getElementById('printToPaper');
    if (printToPaperButton) {
        printToPaperButton.addEventListener('click', function() {
            window.print();
        });
    }
    
    let previewPrintButton = document.getElementById('previewPrint');
    if (previewPrintButton) {
        previewPrintButton.addEventListener('click', openPrintDialog);
    }
    
    let closePreviewButton = document.getElementById('closePreview');
    if (closePreviewButton) {
        closePreviewButton.addEventListener('click', closePreviewDialog);
    }
});

function openPrintDialog() {
    var table = document.getElementById('searchTable');
    var previewTable = document.getElementById('previewTable');
    previewTable.innerHTML = '';

    var columnsToCopy = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]; 

    for (var i = 0; i < table.rows.length; i++) {
        var newRow = previewTable.insertRow(); 

        for (var j = 0; j < columnsToCopy.length; j++) {
            var cell = newRow.insertCell(); 
            var sourceCell = table.rows[i].cells[columnsToCopy[j]];
            cell.innerHTML = sourceCell.innerHTML; 
        }
    }

    document.getElementById('printDialog').classList.remove('hidden');
}

function closePreviewDialog() {
    document.getElementById('printDialog').classList.add('hidden');
}

function formatDate(inputDate) {
    var parts = inputDate.split("-");
    var formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
    return formattedDate;
}

function formatDateSearch(inputDate) {
    var date = new Date(inputDate);
    var formattedDate = ('0' + date.getDate()).slice(-2) + '-' +
        ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
        date.getFullYear();
    return formattedDate;
}

// Add a new function to show the confirmation modal
function showConfirmationModal(pawnInfoID, pawnDetailID) {
    $('#confirmationModal').show();

    // Handle 'Yes' button click
    $('#confirmDelete').on('click', function () {
        window.location.href = 'search.php?pawnInfoID=' + pawnInfoID + '&pawnDetailID=' + pawnDetailID;
        $('#confirmationModal').hide();
    });

    // Handle 'Cancel' button click
    $('#cancelDelete').on('click', function () {
        $('#confirmationModal').hide();
    });

    // Handle modal close button click
    $('.close').on('click', function () {
        $('#confirmationModal').hide();
    });
}

function closeConfirmationModal() {
    $('#confirmationModal').hide();
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
                        var start_date = formatDateSearch(row.start_date);
                        var end_date = formatDateSearch(row.end_date);

                        var newRow = '<tr>' +
                            '<td>' + row.id + '</td>' +
                            '<td>' + row.interest_rate_name + '</td>' +
                            '<td>' + row.product_detail + '</td>' +
                            '<td>' + row.price + '</td>' +
                            '<td>' + row.interest_rate_price + '%' + '</td>' +
                            '<td>' + row.interest_rate_time + ' tháng' + '</td>' +
                            '<td>' + start_date + '</td>' +
                            '<td>' + end_date + '</td>' +
                            '<td>' + status + '</td>' +
                            '<td>' + row.warehouse_id + '</td>' +
                            '<td class="actions">' +
                            '<button type="button" onclick="window.location.href=\'update_pawn_info.php?userID=' + row.user_id + '&pawnInfoID=' + row.id + '\'">Cập nhật</button> ' +
                            '<button type="button" onclick="window.location.href=\'register_pawn_detail.php?id=' + row.id + '\'">' + (row.product_detail ? 'Cập nhật chi tiết' : 'Thêm chi tiết') + '</button> ' +
                            '<button type="button" onclick="window.location.href=\'history.php?userID=' + row.user_id + '&pawnInfoID=' + row.id + '&pawnDetailID=' + row.pawn_detail_id + '\'">Lịch sử</button> ' +
                            '<button type="button" onclick="showConfirmationModal(' + row.id + ',' + row.pawn_detail_id + ');">Xóa</button>' +
                            '</td>' +
                            '</tr>';

                        $('table tbody').append(newRow);
                    }
                }
            }
        })
    }
}