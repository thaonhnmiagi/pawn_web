var startDateInput = document.getElementById("start_date");
var endDateInput = document.getElementById("end_date");

function updateFields() {
    var select = document.getElementById("type");
    var priceInput = document.getElementById("interest_rate");
    var timeInput = document.getElementById("time");
    // var startDateInput = document.getElementById("start_date");
    // var endDateInput = document.getElementById("end_date");

    var selectedOption = select.options[select.selectedIndex];
    var timeValue = selectedOption.getAttribute('data-type');

    // load price,time fields according to select
    priceInput.value = selectedOption.getAttribute("data-price");
    timeInput.value = selectedOption.getAttribute("data-time");

    // load end_date based on the selected start_date
    if (timeValue) {
        if (!startDateInput.value) {
            var today = new Date();
            var todayFormatted = today.toISOString().split('T')[0];
            startDateInput.value = todayFormatted;
        }

        var startDate = new Date(startDateInput.value);
        var endDate = new Date(startDate);
        endDate.setMonth(startDate.getMonth() + parseInt(timeValue));
        console.log('startDate: ', startDate);
        console.log('endDate: ', endDate);

        var endDateFormatted = endDate.toISOString().split('T')[0];
        console.log('endDateFormatted: ', endDateFormatted);
        endDateInput.value = endDateFormatted;
    }
}

// Attach event listener to start_date input
startDateInput.addEventListener("input", updateFields);

// Initial update when the page loads
updateFields();