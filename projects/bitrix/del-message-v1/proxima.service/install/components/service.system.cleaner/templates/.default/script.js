let ITB_STOP_FLAG = false;
function repair(id, item) {
    let table = $('#itb-repair-table tbody');
    let inputId = $('#itb-repair-id');
    let inputCounter = $('#itb-repair-count');
    BX.ajax.runComponentAction('proxima:service.system.cleaner', 'repair', {
        mode: 'class',
        data: {
            id: id,
            item: item
        },
    }).then(function (response) {
        inputId.val(response.data.repairId);
        inputCounter.val(Number(inputCounter.val())+1);
        //console.log(response);
        if (response.data.code === 1 || response.data.code === 2 || response.data.code === 0) {
            let className = 'success';
            if (response.data.code === 2) {
                className = 'warning';
            } else if (response.data.code === 0) {
                className = 'danger';
            }
            table.append('<tr class="table-' + className + '">' +
                '<td>' + response.data.repairId + '</td>' +
                '<td>' + response.data.message + '</td>' +
                '</tr>');
            if (ITB_STOP_FLAG) {
                end();
            } else {
                repair(response.data.repairId, item);
            }
        } else if (response.data.code === 10) {
            table.append('<tr class="table-success">' +
                '<td></td>' +
                '<td>Обработка завершена</td>' +
                '</tr>');
            end();
        } else {
            table.append('<tr class="table-danger">' +
                '<td>' + response.data.repairId + '</td>' +
                '<td>' + response.data.message + '</td>' +
                '</tr>');
            end();
        }
    }, function (response) {
        BX.UI.Dialogs.MessageBox.alert(
            'Ошибка: ' + response.errors[0].message,
        );
        end();
    });
}
function end() {
    ITB_STOP_FLAG = false;
    $('.itb-repair-button').prop('disabled', false);
    $('#itb-repair-id').prop('readonly', false);
    $('#itb-stop-button').prop('disabled', true);
}
$(document).on('click', '.itb-repair-button', function (e) {
    e.preventDefault();
     $('#itb-repair-table tbody').empty();
    $('.itb-repair-button').prop('disabled', true);
    $('#itb-repair-id').prop('readonly', true);
    $('#itb-stop-button').prop('disabled', false);
    let id = $('#itb-repair-id').val();
    let item = $(this).data('class');

    repair(id, item);
});
$(document).on('click', '#itb-stop-button', function (e) {
    e.preventDefault();
    ITB_STOP_FLAG = true;
});