const PHP = value => currency(value, { symbol: 'PHP' });

const doc_subtotal = $('#doc_subtotal');
const doc_amount = $('.doc_amount');
const doc_total_balance = $('#doc_total_balance');
const doc_to_pay = $('.doc_to_pay');
const doc_trust_fund = $('.doc_trust_fund');
const doc_general_fund = $('.doc_general_fund');
const doc_balance = $('.doc_balance');
const doc_total = $('#doc_total');
const particulars_fund_cluster = $('#particulars_fund_cluster');
const particulars_type = $('#particulars_type');
const type = $('#fund-cluster-info');
const fund_cluster = $('#fund_cluster');
const payment_method = $('#payment_type');
const mop_inputs = $('.mop_input');
const mop_total = $('#total_amount');
const official_receipt_form = $('#official-receipt-form');

function getSubtotal() {
    let total = 0;
    doc_amount.each(function () {
        total += Number($(this).val());
    });
    doc_subtotal.val(total);
}

function getBalance() {
    var closest_to_pay = 0;
    var closest_trust_fund = 0;
    var closest_amount = 0;
    doc_balance.each(function () {
        closest_to_pay = Number($(this).closest('tr').find('.doc_to_pay').val());
        closest_trust_fund = Number($(this).closest('tr').find('.doc_trust_fund').val());
        closest_amount = Number($(this).closest('tr').find('.doc_amount').val());

        $(this).val(closest_amount - (closest_to_pay));
    });
}

function getTotalBalance() {
    let trust_fund = 0;
    let to_pay = 0;
    doc_to_pay.each(function () {
        to_pay += Number($(this).val());
    });
    doc_trust_fund.each(function () {
        trust_fund += Number($(this).val());
    });
    let total_balance = Number(doc_subtotal.val()) - (to_pay);
    // = Number(doc_subtotal.val()) - (to_pay + trust_fund);
    if (total_balance > 0) {
        doc_total_balance.closest('.form-group').addClass('has-error');
        doc_total_balance.closest('.form-group').removeClass('has-success');
    } else {
        doc_total_balance.closest('.form-group').addClass('has-success');
        doc_total_balance.closest('.form-group').removeClass('has-error');
    }
    doc_total_balance.val(total_balance);
}

function getTotal() {
    let trust_fund = 0;
    let to_pay = 0;
    let total = 0;
    doc_to_pay.each(function () {
        to_pay += Number($(this).val());
    });
    doc_trust_fund.each(function () {
        trust_fund += Number($(this).val());
    });
    total = to_pay;
    doc_total.val(total);
}

function checkPayAmountsIfExceed() {
    doc_to_pay.each(function () {
        if ((Number($(this).val())) > Number($(this).closest('tr').find('.doc_amount').val())) {
            alertify.alert('To pay and Trust fund cannot exceed total amount to pay.');
            $(this).closest('tr').find('.doc_trust_fund').val(0);
            $(this).val(0);
        }
    });
}

function refreshDOCVal() {
    getSubtotal();
    getTotalBalance();
    getTotal();
    updateGeneralFund();
    $('#doc_subtotal_display').val(PHP(doc_subtotal.val()).format());
    $('#doc_total_balance_display').val(PHP(doc_total_balance.val()).format());
    $('#doc_total_display').val(PHP(doc_total.val()).format());
    $('.doc_balance_display').each(function () {
        $(this).val((PHP($(this).closest('tr').find('.doc_balance').val()).format()));
    });
}

function updateGeneralFund() {
    doc_general_fund.each(function () {
        to_pay_amount = Number($(this).closest('tr').find('.doc_to_pay').val());
        trust_fund_amount = Number($(this).closest('tr').find('.doc_trust_fund').val());
        if (trust_fund_amount > to_pay_amount) {
            alertify.alert('Trust fund cannot exceed To Pay');
            $(this).closest('tr').find('.doc_trust_fund').val(0);
            refreshDOCVal();
        } else {
            general_fund_amount = to_pay_amount - trust_fund_amount;
            $(this).val(general_fund_amount);
        }
    });
}

refreshDOCVal();

doc_to_pay.on('keyup', function () {
    $(this).closest('.doc_general_fund').val(Number(Number($(this).closest('.doc_amount').val()) - Number($(this).closest('.doc_to_pay').val())));
    checkPayAmountsIfExceed();
    getBalance();
    refreshDOCVal();
});

doc_trust_fund.on('keyup', function () {
    checkPayAmountsIfExceed();
    getBalance();
    refreshDOCVal();
});

fund_cluster.on('change', fundClusterChanged);

function fundClusterChanged() {
    if ($(fund_cluster).val() == 2) {
        doc_trust_fund.attr('readonly', false);
    } else {
        doc_trust_fund.attr('readonly', true);
        doc_trust_fund.val(0);
    }

    if ($(fund_cluster).val() != null) {
        particulars_fund_cluster.html($('#fund_cluster option:selected').text());
    } else {
        particulars_fund_cluster.html('N/A');
    }

    refreshDOCVal();
}

fundClusterChanged();

type.on('change', function () {
    if ($(this).val() == '') {
        particulars_type.html('N/A');
    } else {
        particulars_type.html($('#fund-cluster-info option:selected').text());
    }
});

// Not working anymore
mop_inputs.on('change', function () {
    if (Number(mop_total.val()) > Number(doc_total.val())) {
        mop_total.closest('.form-group').removeClass('has-success');
        mop_total.closest('.form-group').addClass('has-error');
    } else {
        mop_total.closest('.form-group').removeClass('has-error');
        mop_total.closest('.form-group').addClass('has-success');
    }
});

mop_inputs.closest('td').hide();

$('.mop_total_hide').closest('th').hide();

payment_method.on('change', function () {
    if ($(this).val() == 4) {
        $('.hidables').fadeOut();
    } else {
        $('.hidables').fadeIn();
    }
});

// TODO on submit validation
official_receipt_form.on('submit', function () {
    var error = false;
    var errorMessage = "";
    if (Number(doc_total.val()) != Number($('#total_amount').val()) && payment_method.val() != 4) {
        error = true;
        // errorMessage.push('Division of Collection Total must be the same as Mode of Payment Total');
        // alertify.alert('<span class="glyphicon glyphicon-exclamation-sign"></span> Error!', 'Division of Collection Total must be the same as Mode of Payment Total');
        errorMessage += '<div class="row">Division of Collection Total must be the same as Mode of Payment Total</div>';
    }

    if ($('#check').is(':checked') && payment_method.val() != 4) {
        var check_total = 0;
        $('.check_amount_class').each(function () {
            check_total += Number($(this).val());
        });
        if (Number($('#total2').val()) != check_total) {
            error = true;
            // alertify.alert('<span class="glyphicon glyphicon-exclamation-sign"></span> Error!', 'Sum of all Checks must be the same as total amount input on Mode of Payment - Check');
            // errorMessage.push('Sum of all Checks must be the same as total amount input on Mode of Payment - Check');
            errorMessage += '<div class="row">Sum of all Checks must be the same as total amount input on Mode of Payment - Check</div>';
        }
    }

    if ($('#lddap').is(':checked') && payment_method.val() != 4) {
        var lddap_total = 0;
        $('.lddap_amount_class').each(function () {
            lddap_total += Number($(this).val());
        });
        if (Number($('#total4').val()) != lddap_total) {
            error = true;
            // alertify.alert('<span class="glyphicon glyphicon-exclamation-sign"></span> Error!', 'Sum of all LDDAP must be the same as total amount input on Mode of Payment - LDDAP');
            errorMessage += '<div class="row">Sum of all LDDAP must be the same as total amount input on Mode of Payment - LDDAP</div>';
        }
    }

    if (error) {
        alertify.alert('<span class="glyphicon glyphicon-exclamation-sign"></span> Error!', errorMessage);
        return false;
    }

    return;
});

$('.mop_total').on('change', calculateMOP);

function calculateMOP() {
    var sumOfAllTotal = 0;
    $('.mop_total').each(function () {
        sumOfAllTotal += Number($(this).val());
    });
    mop_total.val(sumOfAllTotal);
    $('#total_amount_display').val(PHP(sumOfAllTotal).format());
    if (Number(mop_total.val()) != Number(doc_total.val())) {
        mop_total.closest('.form-group').removeClass('has-success');
        mop_total.closest('.form-group').addClass('has-error');
    } else {
        mop_total.closest('.form-group').removeClass('has-error');
        mop_total.closest('.form-group').addClass('has-success');
    }
}

$('#cash').on('change', function () {
    // swal('Test', 'Test', 'error');
    if (this.checked) {
        $('#total1').attr('readonly', false);
    } else {
        $('#total1').attr('readonly', true);
        $('#total1').val(0);
    }
    calculateMOP();
});

$('#check').on('change', function () {
    if (this.checked) {
        $('#total2').attr('readonly', false);
    } else {
        $('#total2').attr('readonly', true);
        $('#total2').val(0);
    }
    calculateMOP();
});

$('#lddap').on('change', function () {
    if (this.checked) {
        $('#total4').attr('readonly', false);
    } else {
        $('#total4').attr('readonly', true);
        $('#total4').val(0);
    }
    calculateMOP();
});

$('#deposit').on('change', function () {
    if (this.checked) {
        $('#total3').attr('readonly', false);
    } else {
        $('#total3').attr('readonly', true);
        $('#total3').val(0);
    }
    calculateMOP();
});
function modeOfPayment(id, gf, tf) {
    var checkbox = $('input[type="checkbox"][id="' + id + '"]:checked');
    if (checkbox.val()) {
        if (checkbox.val() == 2) {
            $('#checkDetails').removeAttr('hidden');
            $('input[type="text"][name="check_type[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="text"][name="check_bank_name[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="text"][name="check_bank_branch[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="text"][name="check_number[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="date"][name="check_date[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="number"][name="check_amount[]"]').removeAttr('readonly').attr('required', true);
        }
        if (checkbox.val() == 4) {
            $('#lddapDetails').removeAttr('hidden');
            $('input[type="text"][name="lddap_name[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="text"][name="lddap_bank_branch[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="text"][name="lddap_number[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="date"][name="lddap_date[]"]').removeAttr('readonly').attr('required', true);
            $('input[type="number"][name="lddap_amount[]"]').removeAttr('readonly').attr('required', true);
        }
        $('.' + id).removeAttr('readonly').attr('required', true);

    } else {
        if (id == "check") {
            $('#checkDetails').attr('hidden', true);
            $('input[type="text"][name="check_type[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="text"][name="check_bank_name[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="text"][name="check_bank_branch[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="text"][name="check_number[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="date"][name="check_date[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="number"][name="check_amount[]"]').removeAttr('required').attr('readonly', true).val('');
            // alert();
            $('#general' + $('input[type="checkbox"][id="' + id + '"]').val()).val('0');
            $('#trust' + $('input[type="checkbox"][id="' + id + '"]').val()).val('0');
            // amountOfPayment($('input[type="checkbox"][id="' + id + '"]').val());
        }
        if (id == "lddap") {
            $('#lddapDetails').attr('hidden', true);
            $('input[type="text"][name="lddap_name[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="text"][name="lddap_bank_branch[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="text"][name="lddap_number[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="date"][name="lddap_date[]"]').removeAttr('required').attr('readonly', true).val('');
            $('input[type="number"][name="lddap_amount[]"]').removeAttr('required').attr('readonly', true).val('');
            $('#general' + $('input[type="checkbox"][id="' + id + '"]').val()).val('0');
            $('#trust' + $('input[type="checkbox"][id="' + id + '"]').val()).val('0');
            // amountOfPayment($('input[type="checkbox"][id="' + id + '"]').val());
        }
        $('.' + id).removeAttr('required').attr('readonly', true);
    }
}

function addLddapDetails() {
    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var today = d.getFullYear() + '-' +
        (('' + month).length < 2 ? '0' : '') + month + '-' +
        (('' + day).length < 2 ? '0' : '') + day;
    "use strict";

    var rowCount = $('tbody[id="lddapTable"] tr').length;

    if (rowCount >= 10) {
        return;
    }

    $('#lddapDetails' + (rowCount - 1)).after('<tr class="lddap-details-spawn"  id="lddapDetails' + rowCount + '">' +
        '<td>' + (rowCount + 1) + '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="lddap_name[]" id="lddap_name' + rowCount + '" placeholder="LDDAP Name" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="lddap_bank_branch[]" id="lddap_bank_branch' + rowCount + '" placeholder="Bank Branch Name" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="lddap_number[]" id="lddap_number' + rowCount + '" placeholder="Check Number" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="date" min= "1996-12-09" max="' + today + '" name="lddap_date[]"  id="lddap_date' + rowCount + '" placeholder="Date"  required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control lddap_amount_class" type="number" name="lddap_amount[]" id="lddap_amount' + rowCount + '" placeholder="0.00" required />' +
        '</div>' +
        '</td>' +
        '</tr>');
}

$('#removelddapDetails').on('click', removelddapDetails);

function removelddapDetails() {
    if ($('.lddap-details-spawn')[1]) {
        $('#lddapTable').children().last().remove();
    }
}

function addCheckDetails() {
    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var today = d.getFullYear() + '-' +
        (('' + month).length < 2 ? '0' : '') + month + '-' +
        (('' + day).length < 2 ? '0' : '') + day;

    "use strict";
    var rowCount = $('.check-details-spawn').length;

    if (rowCount >= 10) {
        return;
    }

    $('#checkDetails' + (rowCount - 1)).after('<tr class="check-details-spawn" id="checkDetails' + rowCount + '">' +
        '<td>' + (rowCount + 1) + '</td>' +
        '<td>'
        +
        '<div class="form-group col-lg-12">' +
        '<select class="form-control" name="check_type[]" id="check_type' + rowCount + '" >' +
        '<option value="">Select Check Type</option>' +
        '</select>' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="check_bank_name[]" id="check_bank_name' + rowCount + '" placeholder="Bank Name" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="check_bank_branch[]" id="check_bank_branch' + rowCount + '" placeholder="Bank Branch Name" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="text" name="check_number[]" id="check_number' + rowCount + '" placeholder="Check Number" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control" type="date" min= "1996-12-09" max="' + today + '" name="check_date[]" id="check_date' + rowCount + '" required />' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<div class="form-group">' +
        '<input class="form-control check_amount_class" type="number" name="check_amount[]" id="check_amount' + rowCount + '" placeholder="0.00" required />' +
        '</div>' +
        '</td>' +
        '</tr>');

    checkType();
}

$('#removeDetails').on('click', removeDetails);

function removeDetails() {
    if ($('.check-details-spawn')[1]) {
        $('#checkTable').children().last().remove();
    }
}

function checkType() {
    $.ajax({
        url: 'get-check-type',
        type: 'post',
        data: {},
        success: function (data) {
            var rowCount = $('.check-details-spawn').length;
            $.each(data, function (key, value) {
                $('#check_type' + (rowCount - 1)).append('<option value="' + value.id + '">' + value.check_code + '</option>');
            });
        },
        error: function (xhr, status, error) { },
    });
}