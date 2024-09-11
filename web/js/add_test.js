$(document).ready(function () {

    $('#submitSample').attr("disabled", true);

    $('#selectSampCode').on('change', function () {
        val = $(this).val();

        $('#submitSample').attr("disabled", true);
        $("#testFee").val(0);

        $('#selectSampName optgroup option').each(function () {
            if (parseInt($(this).val()) !== 0) {
                $(this).remove();
            }
        });

        $.ajax({
            url: 'sample-names?',
            type: 'post',
            data: {val: val},
            success: function (data) {
                $.each(data, function (key, value) {
                    $('#selectSampName optgroup').append('<option data-id="' + value.id + '" value="' + value.sub_service_name + '">' + value.sub_service_name + '</option>');
                });
                $("#selectSampName").val('default');
                $('#selectSampName').selectpicker('refresh');
            }
        });
        $("#selectSampName").val('default');
        $('#selectSampName').selectpicker('refresh');

    });

    $('#selectSampName').on('change', function () {
        val = $(this).val();

        $.ajax({
            url: 'get-fee?',
            type: 'post',
            data: {val: val},
            success: function (data) {
                $("#testFee").val(data);
            }
        });


        $('#submitSample').attr("disabled", false);

    });

});

function addTest() {
    $('#addSampleModal').modal('show');
}

function delTest(id) {
    $('#delSampleModal').modal('show').find('#idTest').html(id);
}

function submitSample(id, cid) {
    $('#submitSample').attr("disabled", true);

    var req = id;
    var sample_code = String($('#selectSampCode').val());
    var sample_name = String($('#selectSampName').val());
    var sample_fee = String($('#testFee').val());

    obj = {
        req: req,
        sample_code: sample_code,
        sample_name: sample_name,
        sample_fee: sample_fee
    };

    $.ajax({
        url: 'add-test-data',
        type: 'post',
        data: {val: obj},
        success: function (data) {
            if (data === 'success') {
                $('#addSampleModal').modal('hide');
            }
        }
    });

	computeServiceFee();
}

function delSample(id, cid) {
    var idTest = String($('#idTest').text());

    $.ajax({
        url: 'del-test?',
        type: 'post',
        data: {val: idTest},
        success: function (data) {
            if (data === 'success') {
                $('#delSampleModal').modal('hide');
                reloadSample(id);
                // window.location.href = 'create?id=' + id + '&customer_id=' + cid;
            }
        }
    });
}

function reloadSample(id) {
    $.ajax({
        url: 'display-sample-test',
        type: 'get',
        data: {services_id: id},
        success: function (data) {
            $('#ajaxServiceTable').html(data);
            $('#total_option').val($('#totalOfAllTest').html());
        }
    });
}
