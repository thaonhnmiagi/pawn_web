// format input price
$(document).ready(function () {
    formatPrice('#formatPrice', '#price');
    formatPrice('#formatPrepayment', '#prepayment');

    $("#start_date").datepicker({ dateFormat: 'dd-mm-yy' });
    $("#end_date").datepicker({ dateFormat: 'dd-mm-yy' });
    $("#extend_date").datepicker({ dateFormat: 'dd-mm-yy' });

    // Set event listener for start_date input change
    $("#start_date").on("change", function () {
        updateFields();
    });

    // Initial update when the page loads
    updateFields();
});

function formatPrice(inputId, hiddenInputId) {
    $(inputId).on('input', function () {
        var inputValue = $(this).val().replace(/[^0-9]/g, '').replace(/^0+/, '');
        var formattedValue = inputValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        $(this).val(formattedValue);

        $(hiddenInputId).val(inputValue);
    });
    $(inputId).trigger('input');
}

// set start_date, end_date
var startDateInput = document.getElementById("start_date");
var endDateInput = document.getElementById("end_date");

function updateFields() {
    var select = document.getElementById("type");
    var priceInput = document.getElementById("interest_rate");
    var timeInput = document.getElementById("time");

    var selectedOption = select.options[select.selectedIndex];
    var timeValue = selectedOption.getAttribute('data-type');

    // load price,time fields according to select
    if (priceInput.value && isFirstLoad) {
        priceInput.value = priceInput.value;
        isFirstLoad = false;
    } else {
        priceInput.value = selectedOption.getAttribute("data-price");
    }

    var time = selectedOption.getAttribute("data-time");
    timeInput.value = time ? selectedOption.getAttribute("data-time") + " tháng" : "";

    // load end_date based on the selected start_date
    if (timeValue) {
        if (!startDateInput.value) {
            var today = new Date();
            var todayFormatted = $.datepicker.formatDate('dd-mm-yy', today);
            startDateInput.value = todayFormatted;
        }

        var startDate = $.datepicker.parseDate('dd-mm-yy', startDateInput.value);
        var endDate = new Date(startDate);
        endDate.setMonth(startDate.getMonth() + parseInt(timeValue));

        var endDateFormatted = $.datepicker.formatDate('dd-mm-yy', endDate);
        endDateInput.value = endDateFormatted;
    }
}