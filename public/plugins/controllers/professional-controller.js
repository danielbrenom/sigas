$(function () {
    let tempId;
    if (ons.isReady()) {
        $.get('/mobile/prof/get-log-messages', {}, function (response) {
            if (response.error) {
                let titleT = "", icontype = "";
                switch (response.error.code) {
                    case 0:
                        titleT = "Erro";
                        icontype = "error";
                        break;
                    case 1:
                        titleT = "Sucesso";
                        icontype = "success";
                        break;
                    default:
                        titleT = "Erro";
                        icontype = "error";
                        break;
                }
                swal({
                    title: titleT,
                    text: response.error.message,
                    timer: 3000,
                    icon: icontype,
                    buttons: false
                });
            }
        })
    }

    fn = {};

    fn.initializeCalendar = function () {
        try {
            let calendar = new FullCalendar.Calendar($(".calendarArea")[0], {
                plugins: ['moment', 'dayGrid', 'timeGrid', 'bootstrap', 'interaction', 'momentTimezone'],
                locale: 'pt-BR',
                themeSystem: "bootstrap",
                defaultView: 'weekGridDay',
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth, weekGridDay',
                },
                buttonText: {
                    month: 'mês'
                },
                allDaySlot: false,
                slotEventOverlap: false,
                height: 600,
                contentHeight: 550,
                displayEventTime: false,
                dateClick: function (info) {
                    if (info.view.type === 'dayGridMonth' || info.view.type === 'weekGridDay') {
                        calendar.changeView('oneGridDay');
                        calendar.gotoDate(info.date);
                    } else if (info.view.type === 'oneGridDay') {
                        swal({
                            title: "Confirmação",
                            text: 'Continuar escolha para data ' + info.date.toLocaleDateString() + '?',
                            buttons: {
                                no: {
                                    text: "Não",
                                    value: false
                                },
                                yes: {
                                    text: "Sim",
                                    value: true,
                                    className: 'btn-success'
                                }
                            }
                        }).then(r => {
                            if (r) {
                                fn.finishAppointment(info);
                            }
                        })
                    }
                },
                views: {
                    oneGridDay: {
                        type: 'timeGridDay',
                        duration: {days: 1},
                        buttonText: 'Day',
                        minTime: "08:00:00",
                        maxTime: "19:00:00"
                    },
                    weekGridDay: {
                        type: 'timeGridWeek',
                        duration: {days: 3},
                        buttonText: 'sem',
                        minTime: "08:00:00",
                        maxTime: "19:00:00"
                    }
                },
                timeZone: "local",
                eventSources: [
                    {
                        url: '/mobile/prof/get-schedule',
                        method: 'GET',
                        failure: function (e) {
                            console.log(e);
                        }
                    }
                ]
            });
            calendar.render();
        } catch (e) {
            console.log(e.message);
            console.log(e.stack)
        }
    };

    fn.editInfo = function () {
        $("#mainNavigator")[0].pushPage('editInfoForm.html');
    };

    fn.loadPacientes = function () {
        $.get('/mobile/prof/get-pacientes', {mode: 'list'}, function (response) {
            let list = $("#fHistPac ons-lazy-repeat");
            list.empty();
            $.each(response, function (key, value) {
                let item = '<ons-list-item modifier="chevron longdivider" tappable onclick="fn.loadPacienteInfo(' + value.id + ')">' +
                    value.user_name +
                    '</ons-list-item>';
                list.append(item);
            })
        })
    };

    fn.loadPacienteInfo = function (id) {
        swal({
            title: "Carregando",
            text: "Aguarde",
            buttons: false
        });
        $.get('/mobile/prof/get-pacientes', {mode: 'details', pac_id: id}, function (response) {
            $("#mainNavigator")[0].pushPage('pacProfile.html').then(() => {
                $.each(response, function (key, value) {
                    $("#" + key).empty().append(value);
                });
                let list = $("#fRegPac ons-lazy-repeat");
                list.empty();
                $.each(response.reg_types, function (key, value) {
                    let item = '<ons-list-item modifier="chevron longdivider" tappable onclick="fn.loadProcedures(' + value.historic_type + ')">' +
                        value.historic_type_description +
                        '</ons-list-item>';
                    list.append(item);
                })
            });
            swal.close();
        })
    }
});