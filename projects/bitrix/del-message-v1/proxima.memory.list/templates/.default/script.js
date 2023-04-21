/**
 * @param id
 * @param gridId
 */
function deleteUserMessagesItem(id, gridId) {
    BX.UI.Dialogs.MessageBox.confirm(
        'Вы уверены что хотите удалить все сообщения у пользователя?',
        'Удалить',
        function (messageBox) {
            BX.ajax.runComponentAction('proxima:proxima.memory.list', 'deleteUserMessages', {
                mode: 'ajax', //ajax
                data: {
                    id: id,
                },
            }).then(function (response) {
                let gridObject = BX.Main.gridManager.getById(gridId);
                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', {apply_filter: 'Y', clear_nav: 'N'});
                }
                messageBox.close();
            }, function (response) {
                messageBox.close();
                BX.UI.Dialogs.MessageBox.alert(
                    'Ошибка: ' + response.errors[0].message,
                );
            });
        },
        function (messageBox) {

        }
    );
}