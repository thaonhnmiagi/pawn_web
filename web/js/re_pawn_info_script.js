// format input price
$(document).ready(function () {
    $('#formatPrice').on('input', function () {
        var inputValue = $(this).val().replace(/[^0-9]/g, '').replace(/^0+/, '');
        var formattedValue = inputValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        $(this).val(formattedValue);

        $('#price').val(inputValue);
    });
    $('#formatPrice').trigger('input');

    $("#start_date").datepicker({ dateFormat: 'dd-mm-yy' });
    $("#end_date").datepicker({ dateFormat: 'dd-mm-yy' });
    $("#extend_date").datepicker({ dateFormat: 'dd-mm-yy' });

    // Set event listener for start_date input change
    $("#start_date").on("change", function () {
        updateFields();
    });

    // Initial update when the page loads
    updateFields();

    $("#pawnForm").submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        var formData = new FormData($(this)[0]); // Get form data
        
        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: formData, 
            processData: false,
            contentType: false,
            success: function(response) {
                document.getElementById("printDialog").classList.remove("hidden");

                // Get the value from the input field
                let inputUserId = document.getElementById('input_user_id').value;
                let fullname = document.getElementById('fullname').value;
                let phone = document.getElementById('phone').value;
                let address = document.getElementById('address').value;
                let userType = document.getElementById('user_type').value;
                let type = document.getElementById('type').value;
                let formatPrice = document.getElementById('formatPrice').value;
                let interestRate = document.getElementById('interest_rate').value;
                let time = document.getElementById('time').value;
                let startDate = document.getElementById('start_date').value;
                let endDate = document.getElementById('end_date').value;
                let warehouse = document.getElementById('warehouse').value;
                

                // Set the value as the text content of the span
                document.getElementById('user_id_span').textContent = inputUserId;
                document.getElementById('full_name_span').textContent = fullname;
                document.getElementById('phone_span').textContent = phone;
                document.getElementById('address_span').textContent = address;
                document.getElementById('user_type_span').textContent = userType;
                document.getElementById('type_span').textContent = type;
                document.getElementById('price_span').textContent = formatPrice;
                document.getElementById('interest_rate_span').textContent = interestRate;
                document.getElementById('time_span').textContent = time;
                document.getElementById('start_time_span').textContent = startDate;
                document.getElementById('end_time_span').textContent = endDate;
                document.getElementById('warehouse_span').textContent = warehouse;
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    let closePreviewButton = document.getElementById('closePreview');
    if (closePreviewButton) {
        closePreviewButton.addEventListener('click', closePreviewDialog);
    }

    let printToPaperButton = document.getElementById('printToPaper');
    if (printToPaperButton) {
        printToPaperButton.addEventListener('click', function() {
            window.print();
        });
    }

    let previewPrintButton = document.getElementById('previewInfoPrint');
    if (previewPrintButton) {
        previewPrintButton.addEventListener('click', openInfoPrintDialog);
    }

    let closeInfoPreviewButton = document.getElementById('closeInfoPreview');
    if (closeInfoPreviewButton) {
        closeInfoPreviewButton.addEventListener('click', closeInfoPreviewDialog);
    }
});

function openInfoPrintDialog() {
    document.getElementById('printInfoDialog').classList.remove('hidden');

    // Get the value from the input field
    let inputUserId = document.getElementById('input_user_id').value;
    let fullname = document.getElementById('fullname').value;
    let phone = document.getElementById('phone').value;
    let address = document.getElementById('address').value;
    let userType = document.getElementById('user_type').value;
    let type = document.getElementById('type').value;
    let formatPrice = document.getElementById('formatPrice').value;
    let interestRate = document.getElementById('interest_rate').value;
    let time = document.getElementById('time').value;
    let startDate = document.getElementById('start_date').value;
    let endDate = document.getElementById('end_date').value;
    let warehouse = document.getElementById('warehouse').value;
    let extendDate;
    let extendDateField= document.getElementById('extend_date');
    var isVisible = extendDateField.offsetParent !== null && window.getComputedStyle(extendDateField).display !== 'none';
    if (extendDateField && isVisible) {
        extendDate = extendDateField.value;
    }
    

    // Set the value as the text content of the span
    document.getElementById('user_id_span').textContent = inputUserId;
    document.getElementById('full_name_span').textContent = fullname;
    document.getElementById('phone_span').textContent = phone;
    document.getElementById('address_span').textContent = address;
    document.getElementById('user_type_span').textContent = userType;
    document.getElementById('type_span').textContent = type;
    document.getElementById('price_span').textContent = formatPrice;
    document.getElementById('interest_rate_span').textContent = interestRate;
    document.getElementById('time_span').textContent = time;
    document.getElementById('start_time_span').textContent = startDate;
    document.getElementById('end_time_span').textContent = endDate;
    document.getElementById('extend_time_span').textContent = extendDate;
    document.getElementById('warehouse_span').textContent = warehouse;
}

function closePreviewDialog() {
    document.getElementById('printDialog').classList.add('hidden');
    window.location.href = '/views/user/search.php';
}

function closeInfoPreviewDialog() {
    document.getElementById('printInfoDialog').classList.add('hidden');
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