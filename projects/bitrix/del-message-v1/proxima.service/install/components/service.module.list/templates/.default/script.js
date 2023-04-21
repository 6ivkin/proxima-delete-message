function updateModule(moduleId, gridId) {
    let messageBox = BX.UI.Dialogs.MessageBox.confirm(
        '<div class="ui-alert ui-alert-warning ui-alert-icon-warning">' +
        '    <span class="ui-alert-message"><strong>ВНИМАНИЕ!</strong> Обновление модулей - потенциально опасная операция, которая может привести к неработоспособности системы. Обновить модуль?</span>' +
        '</div>',
        'Обновление модуля ' + moduleId,
        function (messageBox) {
            BX.ajax.runComponentAction(
                'proxima:service.module.list',
                'update',
                {
                    mode: 'ajax',
                    data: {
                        moduleId: moduleId,
                    },
                }
            ).then(function (response) {
                messageBox.close();
                if (response.data === true) {
                    BX.UI.Dialogs.MessageBox.alert(
                        '<div class="ui-alert ui-alert-success">' +
                        '    <span class="ui-alert-message">Обновление модуля ' + moduleId + ' успешно завершено</span>' +
                        '</div>',
                        'Обновление',
                    );
                } else {
                    BX.UI.Dialogs.MessageBox.alert(
                        '<div class="ui-alert ui-alert-danger ui-alert-icon-danger">' +
                        '    <span class="ui-alert-message">При обновлении модуля ' + moduleId + ' произошла ошибка. Смотри лог "updater"</span>' +
                        '</div>',
                        'Обновление'
                    );
                }
                let gridObject = BX.Main.gridManager.getById(gridId);
                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', {apply_filter: 'N', clear_nav: 'N'});
                }
            }, function (response) {
                messageBox.close();
                BX.UI.Dialogs.MessageBox.alert(
                    'Ошибка: ' + response.errors[0].message,
                    'Обновление',
                );
            });
        }
    )
}