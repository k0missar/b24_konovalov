BX.ready(function(){
    BX.bindDelegate(document.body, 'click', {className: 'otus-procedure'}, function(e){
        e.preventDefault();
        let procedureId = this.getAttribute('data-id');
        let doctorId = this.getAttribute('data-doctor');

        // Создаём контейнер для формы
        let content = BX.create("div", {
            props: { className: "otus-popup-form" },
            children: [
                BX.create("div", {
                    html: '<label>ФИО пациента:</label><br>'
                }),
                BX.create("input", {
                    attrs: { type: "text", id: "otus-patient-name", style: "width:100%;" }
                }),
                BX.create("div", {
                    style: "margin-top:10px;",
                    children: [
                        BX.create("label", {html: "Дата и время:"}),
                        BX.create("input", {
                            attrs: { type: "text", id: "otus-datetime", style: "width:100%;" },
                            events: {
                                click: function(){
                                    BX.calendar({
                                        node: this,
                                        field: this,
                                        bTime: true
                                    });
                                }
                            }
                        })
                    ]
                })
            ]
        });

        // Создаём попап
        let popup = new BX.PopupWindow("otus-procedure-popup", null, {
            content: content,
            width: 400,
            height: 260,
            closeIcon: true,
            titleBar: "Запись на процедуру",
            buttons: [
                new BX.PopupWindowButton({
                    text: "Сохранить",
                    className: "popup-window-button-accept",
                    events: {
                        click: function(){
                            let fio = BX("otus-patient-name").value;
                            let datetime = BX("otus-datetime").value;

                            if(!fio || !datetime){
                                alert("Укажите ФИО и выберите дату/время");
                                return;
                            }

                            BX.ajax({
                                url: '/local/components/otus/doctor.procedure/ajax.php',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    doctorId: doctorId,
                                    procedureId: procedureId,
                                    datetime: datetime,
                                    fio: fio,
                                    sessid: BX.bitrix_sessid()
                                },
                                onsuccess: function(result){
                                    window.location.href = 'https://cc40595.tw1.ru/services/lists/19/view/0/';
                                },
                                onfailure: function(error){
                                    console.error(error);
                                    alert('Ошибка записи');
                                }
                            });
                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: "Отмена",
                    className: "popup-window-button-cancel",
                    events: { click: function(){ popup.close(); } }
                })
            ]
        });

        popup.show();
    });
});
