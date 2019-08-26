$(function () {
    if (ons.isReady()) {
        $.get('/mobile/get-log-messages', {}, function (response) {
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

    fn.especialidadeList = function () {
        $.get("/mobile/get-especialidades", {}, function (data) {
            $.each(data[0], function (key, value) {
                $("#fSelectEsp ons-lazy-repeat").append('<ons-list-item modifier="chevron longdivider" onclick="fn.selectP(' + value.id + ')" tappable>' + value.desc_especialidade + '</ons-list-item>');
            });
        });
    };

    fn.alert = function () {
        console.log("Called", $("#alert"));
        let dialog = $('#alert');

        if (dialog) {
            dialog.show();
        } else {
            ons.createElement('dialog.html', {append: true})
                .then(function (dialog) {
                    dialog.show();
                });
        }
    };

    fn.hideDialog = function (id) {
        document
            .getElementById(id)
            .hide();
    };

    fn.viewDetails = function (id) {
        let html;
        $.get('/mobile/get-profissional-info', {id_user: id}, function (data) {
            html = data;
        }).then(function () {
            $('#mainNavigator')[0].pushPage('detailsPage.html').then(function () {
                $('#fInformacoes').empty();
                $('#fInformacoes').append(html);
            });
        });
    };

    fn.checkDates = function (id) {
        let state;
        $.get('/user-state', {}, function (response) {
            state = response.state;
        }).then(function () {
            if (state) {
                $("#mainNavigator")[0].pushPage('checkDatePage.html').then(function () {
                    // $("#calendarArea").fullCalendar({
                    //     monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    // });
                    let formatter = new Intl.DateTimeFormat('pt-BR');
                    try {
                        let calendar = new FullCalendar.Calendar($("#callendarArea")[0], {
                            plugins: ['dayGrid', 'timeGrid', 'bootstrap', 'interaction', 'moment', 'momentTimezone'],
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
                            fullday: false,
                            allDaySlot: false,
                            slotEventOverlap: false,
                            height: 600,
                            dateClick: function (info) {
                                if (info.view.type === 'dayGridMonth') {
                                    calendar.changeView('timeGridDay');
                                    calendar.gotoDate(info.date);
                                } else if (info.view.type === 'timeGridDay') {
                                    swal({
                                        title: "Confirmação",
                                        text: 'Continuar escolha para data ' + fn.formattDate(info.date) + '?',
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
                                }
                            },
                            timeZone: "UTC",
                            eventSources: [
                                {
                                    url: '/mobile/get-schedule',
                                    method: 'GET',
                                    extraParams: {
                                        id_professional: id
                                    },
                                    failure: function (e) {
                                        console.log("erro" + e.toString());
                                    }
                                }
                            ]
                        });
                        calendar.render();
                    } catch (e) {
                        console.log(e.message);
                        console.log(e.stack)
                    }
                });
            } else {
                swal({
                    title: "Atenção",
                    text: "Você deve estar logado para solicitar um procedimento.",
                    icon: "warning",
                    buttons: false,
                    timer: 4000
                }).then(() => {
                    $('#mainNavigator')[0].resetToPage('mainPage.html')
                })
            }
        })

    };

    fn.selectP = function (id) {
        let html;
        $.get("/mobile/get-profissionais", {esp: id}, function (data) {
            html = data;
            // if ($("#Tab1 .page__content #prof_found").length)
            //     $("#Tab1 .page__content #prof_found").remove();
            // $("#Tab1 .page__content").append(data);
        }).then(function () {
            $('#mainNavigator')[0].pushPage('professionalsPage.html').then(function () {
                $('#fResultados').empty();
                $('#fResultados').append(html);
            })
        });
    };

    fn.open = function () {
        $("#menu")[0].open();
    };

    fn.load = function (page) {
        let content = $('#content')[0],
            menu = $('#menu')[0];
        content.load(page).then(menu.close.bind(menu));
    };

    fn.singupForm = function () {
        $('#mainNavigator')[0].pushPage('singupForm.html');
    };

    fn.profileHandler = function (page) {
        let html;
        $.get('/user-state', {}, function (data) {
            if (data.state) {
                $.get('/mobile/user-profile', {}, function (response) {
                    html = response;
                }).then(function () {
                    page.find('#form-area').empty().append(html);
                })
            } else {
                $.get('/mobile/login-form', {}, function (response) {
                    html = response;
                }).then(function () {
                    page.find('#form-area').empty().append(html);
                })
            }
        })
    };

    fn.editInfo = function () {
        $('#mainNavigator')[0].pushPage('editInfoForm.html');
    };

    fn.finishAppointment = function (info) {
        $("#mainNavigator")[0].pushPage('finishAppoint.html').then(() => {
            $("#req-date").val(fn.formattDate(info.date));
            $("#idProf").val($("#profid").val());
        });
    };

    fn.sendAppointment = function (data) {
        $("#form-appoint").submit();
    };

    fn.formattDate = function (date) {
        return [(date.getDate() + 1) + '/' + (date.getMonth() + 1) + '/' + date.getFullYear()].join('');
    }
});