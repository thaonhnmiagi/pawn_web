var myPieChart;

$(document).ready(function () {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
    });

    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
    });

    $("#start_date, #end_date").change(function () {
        $('#period').val('custom');
    });

    $("#period").change(function () {
        var selectedValue = $(this).val();
        if (selectedValue !== 'custom') {
            $('#start_date').val('');
            $('#end_date').val('');
        }
    });

    $('#statisticsForm').submit(function (event) {
        event.preventDefault();
        $('#dashboard_error_message').html('');
        if (typeof myPieChart !== 'undefined') {
            myPieChart.destroy();
        }

        $.ajax({
            type: 'POST',
            url: 'dashboard.php',
            data: $(this).serialize(),
            success: function (response) {
                var data = JSON.parse(response);
                if (data.error) {
                    $('#dashboard_error_message').html(data.error);
                } else {
                    drawPieChart(data);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });
});

// format input datepicker
function formatDate(inputDate) {
    var parts = inputDate.split("-");
    var formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
    return formattedDate;
}

function drawPieChart(data) {
    var ctx = document.getElementById('myPieChart').getContext('2d');

    var labels = data.map(function (item) {
        return [item.loan_amount_label, item.interest_rate_label, item.profit_label];
    });

    var datasets = data.map(function (item) {
        return {
            labels: [item.loan_amount_label, item.interest_rate_label, item.profit_label],
            data: [item.total_loan_amount, item.total_interest_rate, item.total_profit],
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
            ],
        };
    });

    myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels.flat(),
            datasets: datasets,
        },
    });
}