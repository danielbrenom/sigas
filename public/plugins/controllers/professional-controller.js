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
}

function initializeSolics() {

}

function loadPacientes() {
    $.get('/mobile/prof/get-pacientes', {mode: 'list'}, function (response) {
        let list = $("#fHistPac ons-lazy-repeat");
        list.empty();
        for (let i = 0; i < 50; i++) {
            $.each(response, function (key, value) {
                // let item = '<ons-list-item modifier="chevron longdivider" tappable onclick="fn.loadPacienteInfo(' + value.id + ')">' +
                //     value.user_name +
                //     '</ons-list-item>';
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
        }

    })
}

function loadPacienteInfo(id) {
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

function editInfo(type) {
    if (type) {
        $.get('/mobile/prof/get-profile', {type: 'prof'}, function (response) {
            $("#mainNavigator")[0].pushPage('editInfoProfForm.html').then(() => {
                $.each(response, function (key, value) {
                    $("#" + key).val(value);
                })
            });
        });
    } else {
        $("#mainNavigator")[0].pushPage('editInfoPesForm.html');
    }
}

function display(id, tab) {
    if (!$(`#${id}`).hasClass('active')) {
        $.each($(".profile_button_bar_" + tab + " ons-button"), function (key, value) {
            $(value).removeClass('active');
            $(`#${value.id}-view`).hide('fast');
        });
        $(`#${id}`).addClass('active');
        $(`#${id}-view`).show('fast');
    }
}

function displayProfile(id) {
    if (!$(`#${id}`).hasClass('active')) {
        $.each($(".profile_button_bar_profile ons-button"), function (key, value) {
            $(value).removeClass('active');
            $(`#${value.id}-view`).hide('fast');
        });
        $(`#${id}`).addClass('active');
        $(`#${id}-view`).show('fast');
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


// ons.openActionSheet({
//     cancelable: true,
//     buttons: [
//         'Share Tweet via...',
//         'Add to Moment',
//         'I don\'t like this Tweet',
//         'Report Tweet',
//         {
//             label: 'Cancel',
//             icon: 'md-close'
//         }
//     ]
// })