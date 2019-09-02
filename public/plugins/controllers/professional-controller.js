$(function () {
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
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth',
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
                    if (info.view.type === 'dayGridMonth') {
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
    }
});