$(document).ready(function() {
    $(document).on('click', '.item-diff-view', function (evt) {
        evt.preventDefault();
        let windowSize = BX.GetWindowSize();
        BX.ajax.runComponentAction('proxima:service.module.check', 'getDiff', {
            mode: 'ajax',
            data: {
                params : $(this).data('params')
            }
        }).then(
            function (response) {
                let popup = BX.PopupWindowManager.create('detailsPopup', null, {
                    width: 1200,
                    height: windowSize.innerHeight - 100, // document.body.clientHeight - 100,
                    offsetTop: 0,
                    titleBar: 'Отличия файлов',
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
            },
            function (response) {
                console.log(response);
                alert(response.errors[0].message);
            }
        );
    })
});