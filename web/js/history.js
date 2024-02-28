$(document).ready(function () {
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
    var table = document.getElementById('historyTable');
    var previewTable = document.getElementById('previewTable');
    previewTable.innerHTML = '';

    var columnsToCopy = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ,14]; 

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