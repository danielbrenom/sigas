if (typeof (pacId) == 'undefined') {
    var pacId;
}
if (typeof (swiperagenda) == 'undefined') {
    var swiperagenda;
    var swiperpac;
    var swiperprofile;
}
$().ready(function () {
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
                // swal({
                //     title: titleT,
                //     text: response.error.message,
                //     timer: 3000,
                //     icon: icontype,
                //     buttons: false
                // });
                ons.notification.toast(titleT + "! " + response.error.message, {
                    timeout: 2000,
                    class: 'toast-' + icontype
                })
            }
        });
    }
    swiperagenda = new Swiper('.swiper-container-agenda', {
        direction: 'horizontal',
        loop: false,
        width: screen.width
    });
    swiperprofile = new Swiper('.swiper-container-profile', {
        direction: 'horizontal',
        loop: false,
        width: screen.width
    });

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
        // for (let i = 0; i < 50; i++) {
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
        // }

    })
}

function loadPacienteInfo(id) {
    ons.notification.toast("Carregando informações, aguarde...", {
        timeout: 1000,
    });
    pacId = id;
    $.get('/mobile/prof/get-pacientes', {mode: 'details', pac_id: id}, function (response) {
        $("#mainNavigator")[0].pushPage('pacProfile.html').then(() => {
            swiperpac = new Swiper('.swiper-container-pac', {
                direction: 'horizontal',
                loop: false,
                width: screen.width
            });
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
                            $("#addPrescArea").append('<input type="text" name="pacId" id="pacId" value="' + pacId + '">');
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
                            $("#rxInfos").append('<input type="text" name="pacId" id="pacId" value="' + pacId + '">');
                            $("#rxInfos").append('<input type="text" name="op" value="rx">');
                            $(e.currentTarget).trigger('submit', {'finished': options.finished});
                        }
                        break;
                    case 2:
                        inputs = $("#" + areas[type] + "Form").serializeArray();
                        console.log(inputs);
                        options.finished = true;
                        for (c = 0; c < inputs.length; c += 4) {
                            options.finished = inputs[c].value != "";
                            options.finished = inputs[c + 1].value != "";
                            options.finished = inputs[c + 2].value != "";
                        }
                        if (!options.finished) {
                            ons.notification.toast("Todos os campos devem ser preenchidos.", {timeout: 3000});
                        } else {
                            $("#remInfos").append('<input type="text" name="pacId" id="pacId" value="' + pacId + '">');
                            $("#remInfos").append('<input type="text" name="op" value="rx">');
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

function display(id, tab, index) {
    if (!$(`#${id}`).hasClass('active')) {
        $.each($(".profile_button_bar_" + tab + " ons-button"), function (key, value) {
            $(value).removeClass('active');
            // $(`#${value.id}-view`).hide('slide','left');
            // $(`#${value.id}-view.active`).removeClass('active');
        });
        $(`#${id}`).addClass('active');
        switch (tab) {
            case 'agenda':
                swiperagenda.slideTo(index);
                break;
            case 'profile':
                swiperprofile.slideTo(index);
                break;
            case 'pac':
                swiperpac.slideTo(index);
                break;
        }
        // $(`#${id}-view`).show('slide','left');
        // $(`#${id}-view`).addClass('active');
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