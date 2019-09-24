if (typeof (window.pacId) == 'undefined') {
    window.pacId;
}
$().ready(function () {
    if (ons.isReady()) {
        $.get('/mobile/prof/get-log-messages', {}, function (response) {
            if (response.error) {
                showToast(response.error);
            }
        });
    }
});


function initializeCalendar() {
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
                    url: '/mobile/prof/get-schedule?type=schedule',

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
    $.get('/mobile/prof/get-schedule', {type: 'solics'}, function (response) {
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
                // '                                                <ons-button modifier="quiet" onclick="handleAppoint(' + value.a_id + ', \'confirm\')"">' +
                // '                                                    <ons-icon icon="fa-clock"></ons-icon>' +
                // '                                                    <span class="reaction-no">Adiar</span>' +
                // '                                                </ons-button>' +
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
    $.get('/mobile/prof/pacientes', {mode: 'list', search: query}, function (response) {
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
    $.get('/mobile/prof/pacientes', {mode: 'details', pac_id: id}, function (response) {
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
    $.get('/mobile/prof/pacientes', {mode: 'procedure', ptype: type, user: window.pacId}, function (response) {
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

function loadAttendants() {
    $.get('/mobile/prof/attendant', {}, function (response) {
        let area = $("#attendeeArea ons-list");
        area.empty();
        $.each(response, function (key, value) {
            let check = value.is_att === true ? "checked" : "";
            let item = '<ons-list-item>' +
                '<label class="left">' +
                '        <ons-checkbox name="fSelects[]" value="' + value.id_attendant + '" ' + check + ' input-id="check-' + key + '"></ons-checkbox>' +
                '      </label>' +
                '      <label for="check-' + key + '" class="center">' +
                value.user_name +
                '      </label>' +
                '</ons-list-item>';
            area.append(item);
        })
    })
}

function loadProcedureGer() {
    $.get('/mobile/prof/procedure', {}, function (response) {
        let area = $("#procedureGArea ons-list");
        area.empty();
        $.each(response, function (key, value) {
            let check = value.is_proc === true ? "checked" : "";
            let item = '<ons-list-item tappable>' +
                '       <label class="left">' +
                '        <ons-checkbox name="fSelects[]" value="' + value.p_id + '" ' + check + ' input-id="check-' + key + '"></ons-checkbox>' +
                '      </label>' +
                '      <label for="check-' + key + '" class="center">' +
                value.p_procedure_description +
                '      </label>' +
                '</ons-list-item>';
            area.append(item);
        });
    });
}

function loadHealthcareGer() {
    $.get('/mobile/prof/healthcare', {}, function (response) {
        let area = $("#healthGArea ons-list");
        area.empty();
        $.each(response, function (key, value) {
            let check = value.is_hc === true ? "checked" : "";
            let item = '<ons-list-item tappable>' +
                '       <label class="left">' +
                '        <ons-checkbox name="fSelects[]" value="' + value.hc_id + '" ' + check + ' input-id="check-' + key + '"></ons-checkbox>' +
                '      </label>' +
                '      <label for="check-' + key + '" class="center">' +
                value.hc_desc_healthcare +
                '      </label>' +
                '</ons-list-item>';
            area.append(item);
        });
    });
}

function insertHistoric(type) {
    let areas = ['pres', 'rx', 'rem', 'note'];
    $("#mainNavigator")[0].pushPage('addHistoric.html').then(() => {
        $.each(areas, function (key, value) {
            $("#" + value + "Area").hide('fast');
        });
        $("#" + areas[type] + "Area").slideDown();
        $("#" + areas[type] + "Form").submit(function (e, options) {
            options = options || {};
            if (!options.finished) {
                e.preventDefault();
                let inputs;
                switch (type) {
                    case 0:
                        inputs = $("#" + areas[type] + "Form").serializeArray();
                        options.finished = true;
                        for (c = 0; c < inputs.length; c += 3) {
                            options.finished = inputs[c].value != "";
                            options.finished = inputs[c + 1].value != "";
                            options.finished = inputs[c + 2].value != "";
                        }
                        if (!options.finished) {
                            ons.notification.toast("Todos os campos devem ser preenchidos ou pelo menos um registro deve ser inserido na lista", {timeout: 3000});
                        } else {
                            $("#addPrescArea").append('<input type="text" name="pacId" id="pacId" value="' + window.pacId + '">');
                            $("#addPrescArea").append('<input type="text" name="op" value="prescription">');
                            $(e.currentTarget).trigger('submit', {'finished': options.finished});
                        }
                        break;
                    case 1:
                        inputs = $("#" + areas[type] + "Form").serializeArray();
                        console.log(inputs);
                        options.finished = true;
                        for (c = 0; c < inputs.length; c += 3) {
                            options.finished = inputs[c].value != "";
                            options.finished = inputs[c + 1].value != "";
                            options.finished = inputs[c + 2].value != "";
                        }
                        if (!options.finished) {
                            ons.notification.toast("Todos os campos devem ser preenchidos.", {timeout: 3000});
                        } else {
                            $("#rxInfos").append('<input type="text" name="pacId" id="pacId" value="' + window.pacId + '">');
                            $("#rxInfos").append('<input type="text" name="op" value="rx">');
                            $(e.currentTarget).trigger('submit', {'finished': options.finished});
                        }
                        break;
                    case 2:
                        inputs = $("#" + areas[type] + "Form").serializeArray();
                        console.log(inputs);
                        options.finished = true;
                        for (c = 0; c < inputs.length; c += 5) {
                            options.finished = inputs[c].value != "";
                            options.finished = inputs[c + 1].value != "";
                            options.finished = inputs[c + 2].value != "";
                        }
                        if (!options.finished) {
                            ons.notification.toast("Todos os campos devem ser preenchidos.", {timeout: 3000});
                        } else {
                            $("#remInfos").append('<input type="text" name="pacId" id="pacId" value="' + window.pacId + '">');
                            $("#remInfos").append('<input type="text" name="op" value="rem">');
                            $(e.currentTarget).trigger('submit', {'finished': options.finished});
                        }
                        break;
                }
            }
        });
    });
}

function addRem() {
    let remInfo = $("#presForm").serializeArray().slice(0, 3);
    if (remInfo[0].value == "" || remInfo[1].value == "" || remInfo[2].value == "") {
        ons.notification.toast('Todos os campos devem ser preenchidos', {timeout: 3000});
    } else {
        $("#presForm input[name='fMedic[]']").val("");
        $("#presForm input[name='fDose[]']").val("");
        $("#presForm textarea[name='fPoso[]']").val("");
        $("#prescList").show('fast');
        let div = '<ons-list-item>' + remInfo[0].value + ' - ' + remInfo[1].value + '</ons-list-item>';
        $("#prescList").append(div);
        $.each(remInfo, function (key, value) {
            let input = '<input name="' + value.name + '" value="' + value.value + '"/>';
            $("#addPrescArea").append(input);
        })
    }

}

function editInfo(type) {
    switch (type) {
        case 1:
            $.get('/mobile/prof/profile', {type: 'prof'}, function (response) {
                $("#mainNavigator")[0].pushPage('editInfoProfForm.html').then(() => {
                    $.each(response, function (key, value) {
                        if (key !== "info_user_addr") {
                            $("#" + key).val(value);
                        }
                    });
                });
            });
            break;
        case 0:
            $("#mainNavigator")[0].pushPage('editInfoPesForm.html');
            break;
        case 2:
            $("#mainNavigator")[0].pushPage('manageAttend.html');
            break;
        case 3:
            $("#mainNavigator")[0].pushPage('manageProced.html');
            break;
            case 4:
            $("#mainNavigator")[0].pushPage('manageHc.html');
            break;
    }
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
        $(`#${id}`).addClass('active');
        $("#carousel-" + tab)[0].setActiveIndex(index);
    }
}

function handleAppoint(id, op) {
    let area = $("#solic-view ons-list ons-lazy-repeat");
    area.empty();
    area.append('<ons-progress-circular indeterminate></ons-progress-circular>');
    $.post('/mobile/prof/handle-solicitacoes', {ap_id: id, mode: op}, function (response) {
        showToast(response);
    }).then(() => {
        initializeSolics();
    });
}

function handleNotifs(op) {
    if (op) {
        $("#mainNavigator")[0].pushPage('notifPage.html');
    } else {
        $.get('/mobile/prof/get-schedule', {type: 'notifs'}, function (response) {
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

function addAddress() {
    let addr = '<ons-list-item class="input-items end">' +
        '                            <div class="left">' +
        '                                <ons-icon icon="fa-map-marker-alt" class="list-item__icon"></ons-icon>' +
        '                            </div>' +
        '                            <ons-input style="width: 80%" id="info_user_addr" modifier="material" name="fEnd[]"' +
        '                                       type="text"' +
        '                                       placeholder="Endereço Adicional" float validate></ons-input>' +
        '                            <button type="button" class="fab fab--mini" onclick="removeAddress()"><i class="zmdi zmdi-minus"></i></button>' +
        '                        </ons-list-item>';
    $(".addr-area").append(addr);
}

function removeAddress() {
    $(".addr-area ons-list-item:last-child").remove();
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