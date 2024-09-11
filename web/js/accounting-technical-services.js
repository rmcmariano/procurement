// Accounting / Order Of Payment / Technical Services - Javascript

const PESO = value => currency(value, {
    symbol: 'PHP'
});

$(document).ready(function () {
    // Remove active for all items.
    $('.page-sidebar-menu li').removeClass('active');

    // highlight submenu item
    $('li a[href="' + this.location.pathname + '"]').parent().addClass('active');

    // Highlight parent menu item.
    $('ul a[href="' + this.location.pathname + '"]').parents('li').addClass('active');
});

function checkOop() {
    var submit = true;
    var fund = $('#fund').val();
    var incomeCode = $('#incomeCode').val();
    var toPay = $('input[type="number"][name="toPay[]"]');
    var trustFund = $('input[type="number"][name="trustFund[]"]');
    var libClusterFund = $('.lib-fundcluster');
    var filled = true;

    libClusterFund.each(function () {
        if (!$(this).val()) {
            filled = false;
        }
    });

    if (fund.length != 0 && incomeCode.length != 0 && filled) {
        if (fund == 2) {
            toPay.each(function (i) {
                if (!isNaN($(this).val()) && !isNaN($('#tf' + i).val()) && Number($(this).val()) != 0 && Number($('#tf' + i).val()) != 0) {
                    submit = true;
                } else {
                    submit = false;
                    alert('Please Fill in the Required Field/s.');
                }
            });
        } else {
            toPay.each(function () {
                if (!isNaN($(this).val()) && Number($(this).val()) != 0) {
                    submit = true;
                } else {
                    submit = false;
                    alert('Please Fill in the Required Field/s.');
                }
            });
        }
    } else {
        submit = false;
        alert('Please Fill in the Required Fields.');
    }

    if (Number(toPay.val()) != Number($('#amount0').val())) {
        alert('Must not have any remaining balance');
        submit = false;
    }

    return submit;
}

function validateDate() {
    var request = $('#request-request_date').val();
    var due = $('#request-due_date').val();
    if (request >= due) {
        alert('Due Date must ahead from Request Date.');
        $('#request-due_date').val('');
    }
}

function trustFundAmount(id, total) {
    var tf = $('#tf' + id).val();
    var gf = Number(total) - Number(tf);
    $('#gf' + id).val(gf);
    balance(0);
}

function fundCluster() {
    var select = $('#fund-id option:selected').val();
    if (select == '') {
        $('#tf0').attr({
            readonly: true,
            placeholder: 'Amount'
        }).val('');
    } else {
        if (select == 1) {
            $('#tf0').attr({
                readonly: true
            }).val('0');
            $('#gf0').val($('input[type="number"][id=amount0]').val());
        }
        if (select == 2) {
            $('#tf0').removeAttr('readonly').attr('placeholder', 'Amount').val('');
        }
    }
}

function balance(i) {
    if (Number($('#amount' + i).val()) < Number($('#tp' + i).val())) {
        alert('The amount you enter is greater than the total amount of the service');
        $('#tp' + i).val('');
    }

    var totalBal = 0;
    var bal = 0;
    var amount = Number($('#amount0').val());
    var topay = Number($('#tp0').val());
    var trust_fund = Number($('#tf0').val());
    bal = amount - topay;
    totalBal = totalBal + bal;
    $('#bal0').val(bal);

    $('#gf' + i).val(Number(Number($('#tp' + i).val()) - Number($('#tf' + i).val())));

    var total = Number($('#sum').val());
    $('#total').val(total);
    $('#totalBalance_display').css("background-color", "red");
    $('#amount0_display').val(PESO($('#amount0').val()).format());
    $('#sum_display').val(PESO($('#sum').val()).format());
    $('#totalBalance_display').val(PESO(totalBal).format());
    $('#total_display').val(PESO($('#total').val()).format());
}
$('#amount0_display').val(PESO($('#amount0').val()).format());
$('#sum_display').val(PESO($('#sum').val()).format());
$('#total_display').val(PESO($('#total').val()).format());
$('#gf0').val(0);

balance(0);

function addPar() {
    "use strict";
    var rowCount = $('tbody[id="particulars"] tr').length;
    $('#par' + (rowCount - 1)).after('<tr id="par' + rowCount + '">' +
        '<th>' +
        '<label style="color: #3c8dbc;">' + (rowCount + 1) + '</label>' +
        '</th>' +
        '<td>'
        +
        '<input type="text" id="code' + rowCount + '" class="form-control" name="code[]" maxlength="50" aria-required="true" aria-invalid="true" placeholder="Sample Code" required>' +
        '</td>' +
        '<td>'
        +
        '<textarea id="description' + rowCount + '" class="form-control" name="description[]" maxlength="500" rows="1" aria-required="true" required></textarea>' +
        '</td>' +
        '<td>'
        +
        '<input type="number" id="amount' + rowCount + '" class="form-control" name="amount[]" onchange="totalAmount();" aria-required="true" placeholder="0.00" required>' +
        '</td>' +
        '<td>' +
        '<img src="/opms/images/remove.png" id="rmv0" onclick="remove();" style="width:25px; height:25px; cursor: pointer;" alt="Remove" />' +
        '</td>' +
        '</tr>');
}

function totalAmount() {
    var total = 0;
    $('input[type="number"][name="amount[]"]').each(function () {
        total = total + Number($(this).val());
    });
    $('#request-total_amount').val(total.toFixed(2));
}

function orCategory() {
    var select = $('#or_category option:selected').val();
    if (select == '') {
        $('input[type="text"][name="or_number"]').val('00000');
    } else {
        $.ajax({
            url: 'getornumber',
            type: 'post',
            data: {
                id: select
            },
            success: function (data) {
                console.log(data);
                if (data['next'] <= data['end']) {
                    $('input[type="text"][name="or_number"]').val(data['next']);
                }
            },
        });
    }
}

function checkType() {
    $.ajax({
        url: 'getchecktype',
        type: 'post',
        data: {},
        success: function (data) {
            var rowCount = $('tbody[id="checkTable"] tr').length;
            $.each(data, function (key, value) {
                console.log(value.check_code);
                $('#check_type' + (rowCount - 1)).append('<option value="' + value.id + '">' + value.check_code + '</option>');
            });
        },
        error: function (xhr, status, error) { },
    });
}


$('.lib-fundcluster').on('change', function () {
    var general_val = Number($(this).closest('tr').find('.general-column.active-column').html());
    var trust_val = Number($(this).closest('tr').find('.trust-column.active-column').html());
    var trust = $(this).closest('tr').find('.trust-column.active-column');
    var general = $(this).closest('tr').find('.general-column.active-column');
    var current_fundcluster = Number($(this).val());
    var holder = $(this).closest('tr').find('.holder');

    // General
    if (current_fundcluster == 1) {
        if (holder.val() != '') {
            general.html(holder.val());
            holder.val('');
        } else {
            general.html(trust_val);
            trust.empty();
        }
    }
    if (current_fundcluster == 2) {
        if (holder.val() != '') {
            trust.html(holder.val());
            general.empty();
            holder.val('');
        } else {
            trust.html(general_val);
            general.empty();
        }
    }
    getLibTotal();
});
getLibTotal();
function getLibTotal() {

    getLibSectionTotal('.co-column', '.general-column', '.client', 'co_client_general_total');
    getLibSectionTotal('.co-column', '.general-column', '.itdi', 'co_itdi_general_total');
    getLibSectionTotal('.co-column', '.trust-column', '.client', 'co_client_trust_total');
    getLibSectionTotal('.co-column', '.trust-column', '.itdi', 'co_itdi_trust_total');

    getLibSectionTotal('.ps-column', '.general-column', '.client', 'ps_client_general_total');
    getLibSectionTotal('.ps-column', '.general-column', '.itdi', 'ps_itdi_general_total');
    getLibSectionTotal('.ps-column', '.trust-column', '.client', 'ps_client_trust_total');
    getLibSectionTotal('.ps-column', '.trust-column', '.itdi', 'ps_itdi_trust_total');

    getLibSectionTotal('.mooe-column', '.general-column', '.client', 'mooe_client_general_total');
    getLibSectionTotal('.mooe-column', '.general-column', '.itdi', 'mooe_itdi_general_total');
    getLibSectionTotal('.mooe-column', '.trust-column', '.client', 'mooe_client_trust_total');
    getLibSectionTotal('.mooe-column', '.trust-column', '.itdi', 'mooe_itdi_trust_total');

    getClientToPayTotalPerSection('#co_client_general_total', '#co_client_trust_total', '#co_client_to_pay_total');
    getClientToPayTotalPerSection('#ps_client_general_total', '#ps_client_trust_total', '#ps_client_to_pay_total');
    getClientToPayTotalPerSection('#mooe_client_general_total', '#mooe_client_trust_total', '#mooe_client_to_pay_total');
}

function getLibSectionTotal(sectionNameClass, clusterFundColumnClass, counterpartNameClass, totalCellName) {
    var total = 0;
    $(sectionNameClass + clusterFundColumnClass + counterpartNameClass).each(function () {
        total += Number($(this).html());
    });

    $('#' + totalCellName).html(total);
}

function getClientToPayTotalPerSection(generalCellID, trustCellID, totalCellID) {
    total = 0;
    total += Number($(generalCellID).html()) + Number($(trustCellID).html());
    $(totalCellID).html(total);

}