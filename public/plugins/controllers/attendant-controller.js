if (typeof (window.calendar) == 'undefined') {
    window.calendar;
}
$().ready(function () {
    if (ons.isReady()) {
        $.get('/mobile/attendant/get-log-messages', {}, function (response) {
            if (response.error) {
                showToast(response.error);
            }
        });
    }
});

function reload() {
    $(".calendarArea").empty().append("<ons-progress-circular indeterminate></ons-progress-circular>");
    $("#solic-view ons-list ons-lazy-repeat").empty().append("<ons-progress-circular indeterminate></ons-progress-circular>");
    $("#notif-view ons-list ons-lazy-repeat").empty().append("<ons-progress-circular indeterminate></ons-progress-circular>");
    $("#fHistPac ons-lazy-repeat").empty().append("<ons-progress-circular indeterminate></ons-progress-circular>");
    initializeCalendar();
    initializeSolics();
    handleNotifs(false);
    loadPacientes();
}

function initializeCalendar() {
    try {
        $(".calendarArea").empty();
        window.calendar = new FullCalendar.Calendar($(".calendarArea")[0], {
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
            height: 700,
            contentHeight: 580,
            displayEventTime: false,
            dateClick: function (info) {
                if (info.view.type === 'dayGridMonth' || info.view.type === 'weekGridDay') {
                    calendar.changeView('oneGridDay');
                    calendar.gotoDate(info.date);
                }
                // else if (info.view.type === 'oneGridDay') {
                //     swal({
                //         title: "Confirmação",
                //         text: 'Continuar escolha para data ' + info.date.toLocaleDateString() + '?',
                //         buttons: {
                //             no: {
                //                 text: "Não",
                //                 value: false
                //             },
                //             yes: {
                //                 text: "Sim",
                //                 value: true,
                //                 className: 'btn-success'
                //             }
                //         }
                //     }).then(r => {
                //         if (r) {
                //             fn.finishAppointment(info);
                //         }
                //     })
                // }
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
                    url: '/mobile/attendant/schedule',
                    extraParams: {
                        type: 'schedule',
                        pid: $("#prof-input").val()
                    },
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

function initializeSolics() {
    let area = $("#solic-view ons-list ons-lazy-repeat");
    $.get('/mobile/attendant/schedule', {type: 'solics', pid: $("#prof-input").val()}, function (response) {
        area.empty();
        if (response.length === 0) {
            area.empty();
            area.append("<ons-list-item>Não existem solicitações</ons-list-item>");
        }
        $.each(response, function (key, value) {
            let dataShow = new Date(value.a_solicited_for);
            let name = value.user_name == null ? "Usuário não cadastrado" : value.user_name;
            let itemSolic = '<ons-list-item class="item-custom" modifier="longdivider">' +
                '                                    <div class="left">' +
                '                                        <img class="list-item__thumbnail" src="http://placekitten.com/g/40/40">' +
                '                                    </div>' +
                '                                    <div class="center">' +
                '                                        <div class="tweet-header">' +
                '                                            <span class="list-item__title"><b>' + name + ' </b></span>' +
                '                                        </div>' +
                '                                        <span class="list-item__content" style="width: 100%">' + value.procedure_description + '  </span>' +
                '                                        <span class="list-item__content">Solicitado para: ' + dataShow.toLocaleDateString() + ' às ' + dataShow.toLocaleTimeString() + '  </span>' +
                '                                        <ons-row class="option-buttons">' +
                '                                            <ons-col>' +
                '                                                <ons-button modifier="quiet" onclick="handleAppoint(' + value.a_id + ', \'confirm\')">' +
                '                                                    <ons-icon icon="fa-check"></ons-icon>' +
                '                                                    <span class="reaction-no">Confirmar</span>' +
                '                                                </ons-button>' +
                '                                            </ons-col>' +
                '                                            <ons-col>' +
                '                                                <ons-button modifier="quiet" onclick="postpone(' + value.a_id + ', \'reschedule\')"">' +
                '                                                    <ons-icon icon="fa-clock"></ons-icon>' +
                '                                                    <span class="reaction-no">Adiar</span>' +
                '                                                </ons-button>' +
                '                                            </ons-col>' +
                '                                            <ons-col>' +
                '                                                <ons-button modifier="quiet" onclick="handleAppoint(' + value.a_id + ', \'cancel\')"">' +
                '                                                    <ons-icon icon="fa-times"></ons-icon>' +
                '                                                    <span class="reaction-no">Cancelar</span>' +
                '                                                </ons-button>' +
                '                                            </ons-col>' +
                '                                        </ons-row>' +
                '                                    </div>' +
                '                                </ons-list-item>';
            area.append(itemSolic);
        })
    })
}

function loadPacientes(query) {
    query = query || null
    $.get('/mobile/attendant/pacientes', {
        mode: 'list',
        search: query,
        pid: $("#prof-input").val()
    }, function (response) {
        let list = $("#fHistPac ons-lazy-repeat");
        list.empty();
        if (response.length === 0) {
            list.append('<ons-list-item>Não foram encontrados pacientes, tente buscar outro nome</ons-list-item>');
        }
        $.each(response, function (key, value) {
            let item = '<ons-list-item class="item-custom" modifier="longdivider">' +
                '                        <div class="left">' +
                '                            <img class="list-item__thumbnail" src="http://placekitten.com/g/40/40">' +
                '                        </div>' +
                '                        <div class="center">' +
                '                            <div class="tweet-header">' +
                '                                <span class="list-item__title"><b>' + value.user_name + '</b></span>' +
                '                            </div>' +
                '                            <span class="list-item__content">Plano de saúde: ' + value.desc_healthcare + '</span>' +
                '                            <ons-row class="option-buttons">' +
                '                                <ons-col>' +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                    <ons-button modifier="quiet" onclick="loadPacienteInfo(' + value.id + ')">' +
                '                                        <ons-icon icon="fa-info"></ons-icon>' +
                '                                        <span class="reaction-no">mais informações</span>' +
                '                                    </ons-button>' +
                '                                </ons-col>' +
                '                            </ons-row>' +
                '                        </div>' +
                '                    </ons-list-item>';
            list.append(item);
        })

    })
}

function loadPacienteInfo(id) {
    ons.notification.toast("Carregando informações, aguarde...", {
        timeout: 1000,
    });
    window.pacId = id;
    $.get('/mobile/attendant/pacientes', {mode: 'details', pac_id: id}, function (response) {
        $("#mainNavigator")[0].pushPage('pacProfile.html').then(() => {
            $.each(response, function (key, value) {
                $("#" + key).empty().append(value);
            });
            let list = $("#fRegPac ons-lazy-repeat");
            list.empty();
            $.each(response.reg_types, function (key, value) {
                let item = '<ons-list-item modifier="chevron longdivider" tappable onclick="loadProcedures(' + value.historic_type + ')">' +
                    value.historic_type_description +
                    '</ons-list-item>';
                list.append(item);
            })
        });
    })
}

function loadProcedures(type) {
    $.get('/mobile/attendant/pacientes', {mode: 'procedure', ptype: type, user: window.pacId}, function (response) {
        $("#mainNavigator")[0].pushPage('historicViewPage.html').then(() => {
            let area = $("#regisList ons-lazy-repeat");
            area.empty();
            $.each(response, function (key, value) {
                let title, desc, date, status, prof, able;
                switch (type) {
                    case 1:
                        title = value.procedure_description;
                        prof = ' com: ' + value.prof_name;
                        desc = ' de ' + value.desc_especialidade;
                        date = 'Em: ' + (value.confirmed_for == null ? new Date(value.solicited_for).toLocaleDateString() : new Date(value.confirmed_for).toLocaleDateString());
                        status = 'Status: ' + (value.confirmed_for == null ? 'Solicitado' : 'Confirmada');
                        able = value.procedure_description != null;
                        break;
                    case 2:
                        title = value.ue_exam_name;
                        prof = 'Código: ' + value.ue_exam_codigo;
                        desc = '';
                        date = value.solicited_for == null ? 'Apenas registro' : new Date(value.solicited_for).toLocaleDateString();
                        status = 'Notas: ' + value.ue_exam_notes;
                        able = true;
                        break;
                    case 4:
                        title = value.up_presc_medicamento;
                        prof = value.up_presc_posologia;
                        date = 'Dosagem: ' + value.up_presc_dosagem;
                        status = '';
                        desc = '';
                        able = value.up_presc_medicamento != null;
                        break;
                }
                if (able) {
                    let item = '<ons-list-item class="item-custom" modifier="longdivider">' +
                        '                        <div class="left">' +
                        // '                            <img class="list-item__thumbnail" src="http://placekitten.com/g/40/40">' +
                        '                        </div>' +
                        '                        <div class="center">' +
                        '                            <div class="tweet-header">' +
                        '                                <span class="list-item__title"><b>' + title + desc + '</b></span>' +
                        '                            </div>' +
                        '                            <span class="list-item__content">' + prof + '</span>' +
                        '                            <ons-row class="option-buttons">' +
                        '                                <ons-col>' + date +
                        '                                </ons-col>' +
                        '                                <ons-col style="margin-left: 10px">' +
                        status +
                        '                                </ons-col>' +
                        '                                <ons-col>' +
                        '                                </ons-col>' +
                        '                                <ons-col>' +
                        '                                </ons-col>' +
                        '                            </ons-row>' +
                        '                        </div>' +
                        '                    </ons-list-item>';
                    area.append(item);
                }
            });
        });
    });
}

function newAppointment() {
    $("#mainNavigator")[0].pushPage('newAppoint.html').then(()=>{
        $("#idProf").val($("#prof-input").val());
        $.get('/mobile/attendant/pacientes', {mode: 'appt'}, function (response) {
            $.each(response, function (key, value) {
                $("#req-pac").append('<option value="' + value.id + '">' + value.user_name + '</option>');
            })
        })
    })
}

function sendAppointment() {
    $("#form-appoint").submit();
}

function handleNotifs(op) {
    if (op) {
        $("#mainNavigator")[0].pushPage('notifPage.html');
    } else {
        $.get('/mobile/attendant/schedule', {type: 'notifs', pid: $("#prof-input").val()}, function (response) {
            let area = $("#notif-view ons-list ons-lazy-repeat");
            if (response.length == 0) {
                area.empty();
                area.append("<ons-list-item>Não existem registros</ons-list-item>");
            } else {
                area.empty();
                $.each(response, function (key, value) {
                    let start = new Date(value.n_dt_inicio), end = new Date(value.n_dt_fim);
                    if (value.n_dt_fim == null) {
                        end = start;
                    }
                    let item = '<ons-list-item class="item-custom" modifier="longdivider">' +
                        '           <div class="left">' +
                        '           </div>' +
                        '           <div class="center">' +
                        '                <div class="tweet-header">' +
                        '                      <span class="list-item__title"><b>' + value.n_notif_motivo + '</b></span>' +
                        '                </div>' +
                        '                <ons-row class="option-buttons">' +
                        '                     <ons-col>Em: ' + start.toLocaleDateString() + ' ' + start.toLocaleTimeString() +
                        '                     </ons-col>' +
                        '                     <ons-col>' +
                        '                     </ons-col>' +
                        '                     <ons-col>até ' + end.toLocaleDateString() + ' 18:00:00' +
                        '                     </ons-col>' +
                        '                     <ons-col>' +
                        '                    </ons-col>' +
                        '                 </ons-row>' +
                        '           </div>' +
                        '        </ons-list-item>';
                    area.append(item);
                })
            }
        })
    }
}

function editInfo() {
    $('#mainNavigator')[0].pushPage('editInfoForm.html');
}

function handleAppoint(id, op, qtd) {
    qtd = qtd | 0;
    let area = $("#solic-view ons-list ons-lazy-repeat");
    area.empty();
    area.append('<ons-progress-circular indeterminate></ons-progress-circular>');
    $.post('/mobile/attendant/solicitacoes', {ap_id: id, mode: op, pid: $("#prof-input").val(), postpone: qtd}, function (response) {
        showToast(response);
    }).then(() => {
        initializeSolics();
    });
}

function postpone(id, op) {
    ons.openActionSheet({
        title: "Adiar solicitação",
        cancelable: true,
        buttons: [
            'Em 1 dia',
            'Em 1 semana',
            'Escolher data',
            {
                label: 'Cancelar',
                icon: 'md-close'
            }
        ]
    }).then((index) => {
        console.log(index);
           switch (index) {
               case 0:
                   handleAppoint(id, op, 1);
                   break;
               case 1:
                   handleAppoint(id, op, 7);
                   break;
               case 2:
                   $("#mainNavigator")[0].pushPage("postponeCustom.html").then(()=>{
                       $("#solic-id").val(id);
                       $("#solic-op").val(op);
                   });
                   break;
           }
    });
}

function display(id, tab, index) {
    if (!$(`#${id}`).hasClass('active')) {
        $.each($(".profile_button_bar_" + tab + " ons-button"), function (key, value) {
            $(value).removeClass('active');
        });
        if (tab == 'agenda' && index == 2) {
            $("#notf-fab").show('fade');
        } else {
            $("#notf-fab").hide('fade');
        }
        if (tab == 'agenda' && index == 0) {
            $("#sched-fab").show('fade');
        } else {
            $("#sched-fab").hide('fade');
        }
        $(`#${id}`).addClass('active');
        $("#carousel-" + tab)[0].setActiveIndex(index);
    }
}

function showToast(obj) {
    let titleT = "", icontype = "";
    switch (obj.code) {
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
    ons.notification.toast(titleT + "! " + obj.message, {
        timeout: 2000,
        class: 'toast-' + icontype
    })
}

