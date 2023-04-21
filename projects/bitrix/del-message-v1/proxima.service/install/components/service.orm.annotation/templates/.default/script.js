function executeCommand(gridId) {
    let ids = BX.Main.gridManager.getById(gridId).instance.rows.getSelectedIds();
    BX.ajax.runComponentAction('proxima:service.orm.annotation', 'executeCommand', {
        mode: 'ajax',
        data: {
            ids: ids,
        },
    }).then(function (response) {
        console.log(response); // fixme

        let popup = BX.PopupWindowManager.create('command-popup', null, {
            width: 1000,
            height: document.body.clientHeight - 100,
            offsetTop: 0,
            titleBar: 'Выполнение команды',
            draggable: true,
            darkMode: false,
            autoHide: false,
            lightShadow: true,
            closeIcon: true,
            closeByEsc: true,
            overlay: true,
            buttons: [
                new BX.PopupWindowButton({
                    text: 'Закрыть',
                    className: 'webform-button-link-cancel',
                    events: {
                        click: function () {
                            this.popupWindow.close();
                            this.popupWindow.destroy();
                        }
                    }
                }),
            ]
        });
        popup.setContent(response.data);
        popup.show();

        let gridObject = BX.Main.gridManager.getById(gridId);
        if (gridObject.hasOwnProperty('instance')) {
            gridObject.instance.reloadTable('POST', {apply_filter: 'Y', clear_nav: 'N'});
        }

    }, function (response) {
        console.log(response); // fixme
        BX.UI.Dialogs.MessageBox.alert(
            'Ошибка: ' + response.errors[0].message,
        );
    });
}

