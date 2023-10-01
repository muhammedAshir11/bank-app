$(document).ready(function () {
    loadStatementData();
    $('.section').not('#homeSection').hide();
    $('.navigationButton').on('click', function () {
        $('.section').hide(); // Hide all sections
        $('#' + (this.innerHTML).toLowerCase() + 'Section').show(); // Show the Deposit section
    });
});

const loadStatementData = () => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/fetch_statements',
        method: 'POST',
        success: function (response) {
            // Append data
            $('#statementData').html(response.statementTable);
            $('#balanceAmount').html(response.currentBalance);

            // Data table pagination
            new DataTable('#statementDataTable', {
                paging: true, // Enable pagination
                pageLength: 5, // Set the number of records per page to 5
                searching: false, // Disable search
                lengthChange: false, // Disable per page dropdown
            });
        },
        error: function (error) {
            $('#statementData').html('<h5>No data found...!</h5>');
        },
    });
};


// All transaction function start

const transactions = (transactionType, buttonElement) => {

    let formId = transactionType === 'deposit' ? 'depositForm' : (transactionType === 'withdraw' ?
        'withdrawForm' : 'transferForm');

    // Get the URL from the data-url attribute of the form
    const ajaxUrl = $('#' + formId).data('url');

    // Get form data
    const formData = $('#' + formId).serialize();

    // Send AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: ajaxUrl, // Use the URL from the data attribute
        method: 'POST',
        data: formData,
        beforeSend: function () {
            // Disable button to avoid multiple clicking
            $(buttonElement).prop('disabled', true);
        },
        success: function (response) {
            $('#' + formId + ' input[type="text"]').val('');
            loadStatementData();

            // Show success message
            showMessages(transactionType + 'Msg', response.message)
        },
        error: function (xhr) {
            // Show error message
            showMessages(transactionType + 'Msg', xhr.responseJSON.message, false)
        },
        complete: function () {
            $(buttonElement).prop('disabled', false);
        }
    });
}

// All transaction function end

const showMessages = (id, message, status = true) => {
    status = status === true ? 'text-success' : 'text-danger';
    $('#' + id).html(message);
    $('#' + id).removeClass('hidden');
    $('#' + id).addClass(status);
    setTimeout(() => {
        $('#' + id).html('');
        $('#' + id).addClass('hidden');
        $('#' + id).removeClass(status);
    }, 3000);
}