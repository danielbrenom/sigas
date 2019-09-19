$().ready(function () {
    if (ons.isReady()) {
        $.get('/mobile/user/get-log-messages', {}, function (response) {
            if (response.error) {
                showToast(response.error);

            }
        })
    }
});

function profissionaisList(query) {
    query = query || null;
    $.get("/mobile/user/profissionais", {search: query}, function (response) {
        let area = $("#list-profs ons-lazy-repeat");
        area.empty();
        if (response.length === 0) {
            area.append('<ons-list-item>Não foram encontrados profissionais, tente buscar outro nome</ons-list-item>');
        }
        $.each(response, function (key, value) {
            let badge = '';
            if (value.confirmed) {
                badge = '<span class="badge badge-pill badge-success">Verificado</span>';
            } else {
                badge = '<span class="badge badge-pill badge-warning">Não verificado</span>';
            }
            $("#fSelectEsp ons-lazy-repeat").append('<ons-list-item modifier="chevron longdivider" onclick="selectP(' + value.id + ')" tappable>' + value.desc_especialidade + '</ons-list-item>');
            let item = '<ons-list-item class="item-custom" modifier="longdivider">' +
                '                        <div class="left">' +
                '                            <img class="list-item__thumbnail" src="http://placekitten.com/g/40/40">' +
                '                        </div>' +
                '                        <div class="center">' +
                '                            <div class="tweet-header">' +
                '                                <span class="list-item__title"><b>' + value.info_user_name + '</b></span>' +
                '                            </div>' +
                '                            <span class="list-item__content" style="width: 100%;">' + value.ue_desc_especialidade + '</span>' +
                '                            <span class="list-item__content">' + value.info_user_addr + '</span>' +
                '                            <ons-row class="option-buttons">' +
                '                                <ons-col>' + badge +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                </ons-col>' +
                '                                <ons-col>' +
                '                                    <ons-button modifier="quiet" onclick="viewDetails(' + value.u_id + ')">' +
                '                                        <ons-icon icon="fa-info"></ons-icon>' +
                '                                        <span class="reaction-no">informações</span>' +
                '                                    </ons-button>' +
                '                                </ons-col>' +
                '                            </ons-row>' +
                '                        </div>' +
                '                    </ons-list-item>';
            area.append(item);
        });
    });
}

function especialidadeList() {
    $.get("/mobile/user/get-especialidades", {}, function (data) {
        $.each(data[0], function (key, value) {
            $("#fSelectEsp ons-lazy-repeat").append('<ons-list-item modifier="chevron longdivider" onclick=" selectP(' + value.id + ')" tappable>' + value.desc_especialidade + '</ons-list-item>');
        });
    });
}

function procedureList(profid) {
    $.get('/mobile/user/get-profissional-info', {proc: true, prof: $("#profid").val()}, function (response) {
        $("#req-procd").empty();
        $.each(response[0], function (key, value) {
            $("#req-procd").append(
                '<option value="' + value.p_id_procedure + '">' + value.procedure_description + '</option>'
            );
        });

    })
}

function viewDetails(id) {
    let html;
    $.get('/mobile/user/get-profissional-info', {id_user: id}, function (data) {
        html = data;
    }).then(function () {
        $('#mainNavigator')[0].pushPage('detailsPage.html').then(function () {
            $('#fInformacoes').empty();
            $('#fInformacoes').append(html);
            let addr = formattAddr($("#addr_text").text());
            if (addr !== "") {
                $.get('https://nominatim.openstreetmap.org/search.php', {
                    q: addr,
                    format: 'json'
                }, function (response) {
                    console.log(response);
                    $("#geolink").attr('href', 'http://maps.google.com/?q=' + response[0].lat + ',' + response[0].lon);
                    let map = L.map("mapid", {
                        zoomControl: false,
                        dragging: false
                    }).setView([response[0].lat, response[0].lon], 15);
                    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiZGFuaWVsYnJlbm9tIiwiYSI6ImNqenR2ZXc0bzA0b2szaG12NGlxenJnZHgifQ.rVgxkTv_r5dDyL0WHuKn4Q', {
                        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                        maxZoom: 18,
                        id: 'mapbox.streets',
                        accessToken: 'pk.eyJ1IjoiZGFuaWVsYnJlbm9tIiwiYSI6ImNqenR2ZXc0bzA0b2szaG12NGlxenJnZHgifQ.rVgxkTv_r5dDyL0WHuKn4Q'
                    }).addTo(map);
                    let marker = L.marker([response[0].lat, response[0].lon]).addTo(map);
                    let title = response[0].display_name.split(',');
                    marker.bindPopup("<b>" + title[1] + ", " + title[0] + "</b>").openPopup();
                });
            }
        });
    });
}

function viewHistory() {
    let type_id = $("#history-filter").val();
    $.get('/mobile/user/historic', {type: type_id}, function (response) {
        let list = $("#historyList>ons-lazy-repeat");
        list.empty();
        if (response.length === 0) {
            let item = '<ons-list-item>' +
                'Não foram encontrados registros para este usuário' +
                '</ons-list-item>';
            list.append(item);
        } else {
            $.each(response, function (ket, value) {
                let dataShow = value.confirmed_for == null ? value.solicited_for : value.confirmed_for;
                dataShow = new Date(dataShow);
                let title = value.procedure_description,
                    prof = 'Profissional: ' + value.prof_name,
                    desc = value.desc_especialidade,
                    date = (value.confirmed_for == null ? 'Solicitado para: ' : 'Confirmado para: ') + dataShow.toLocaleDateString() + ' às ' + dataShow.toLocaleTimeString(),
                    able = value.confirmed_for == null;
                if (parseInt(value.id_status) === 4) {
                    date = 'Cancelado';
                    able = false;
                }
                let item = '<ons-list-item class="item-custom" modifier="longdivider">' +
                    '                        <div class="left">' +
                    '                        </div>' +
                    '                        <div class="center">' +
                    '                            <div class="tweet-header">' +
                    '                                <span class="list-item__title"><b>' + title + '</b></span>' +
                    '                            </div>' +
                    '                            <span class="list-item__content" style="width: 100%">' + prof + '</span>' +
                    '                            <span class="list-item__content" style="width: 100%">Especialidade: ' + desc + '</span>' +
                    '                            <span class="list-item__content">' + date + '</span>';
                if (able) {
                    item += '                                        <ons-row class="option-buttons">' +
                        '                                            <ons-col>' +
                        '                                            </ons-col>' +
                        '                                            <ons-col>' +
                        '                                                <ons-button modifier="quiet" onclick="postpone(' + value.id + ', \'reschedule\')"">' +
                        '                                                    <ons-icon icon="fa-clock"></ons-icon>' +
                        '                                                    <span class="reaction-no">Adiar</span>' +
                        '                                                </ons-button>' +
                        '                                            </ons-col>' +
                        '                                            <ons-col>' +
                        '                                                <ons-button modifier="quiet" onclick="handleAppoint(' + value.id + ', \'cancel\')"">' +
                        '                                                    <ons-icon icon="fa-times"></ons-icon>' +
                        '                                                    <span class="reaction-no">Cancelar</span>' +
                        '                                                </ons-button>' +
                        '                                            </ons-col>' +
                        '                                        </ons-row>';
                }
                item += '                        </div>' +
                    '                    </ons-list-item>';
                list.append(item);
            })
        }
    });
}

function loadPrescription() {
    $.get('/mobile/user/historic', {type: 4}, function (respose) {
        let area = $("#list-presc ons-lazy-repeat");
        area.empty();
        if (respose.length == 0) {
            area.append("<ons-list-item>Não existem prescrições disponíveis.</ons-list-item>")
        }
        $.each(respose, function (key, value) {
            let title, desc, date, status, prof, able;
            title = value.up_presc_medicamento;
            prof = value.up_presc_posologia;
            date = 'Dosagem: ' + value.up_presc_dosagem;
            status = '';
            desc = '';
            able = value.up_presc_medicamento != null;
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
        })
    })
}

function checkDates(id) {
    $("#mainNavigator")[0].pushPage('checkDatePage.html').then(function () {
        let formatter = new Intl.DateTimeFormat('pt-BR');
        try {
            let calendar = new FullCalendar.Calendar($("#callendarArea")[0], {
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
                                finishAppointment(info);
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
                        url: '/mobile/user/get-schedule',
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
}

function selectP(id) {
    let html;
    $.get("/mobile/user/profissionais", {esp: id}, function (data) {
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
}

function open() {
    $("#menu")[0].open();
}

function load(page) {
    let content = $('#content')[0],
        menu = $('#menu')[0];
    content.load(page).then(menu.close.bind(menu));
}

function singupForm(type) {
    if (type == 1) {
        $('#mainNavigator')[0].pushPage('singupForm.html');
    } else {
        $('#mainNavigator')[0].pushPage('singupProfForm.html');
    }
}

function editInfo() {
    $.get('/mobile/user/profile', {}, function (response) {
        $('#mainNavigator')[0].pushPage('editInfoForm.html').then(() => {
            $.each(response, function (key, value) {
                $("#" + key).val(value);
            });
        })
    });
}

function finishAppointment(info) {
    $("#mainNavigator")[0].pushPage('finishAppoint.html').then(() => {
        $("#req-date").val(info.date.toLocaleDateString());
        $("#req-hour").val(info.date.toLocaleTimeString());
        $("#idProf").val($("#profid").val());
    });
}

function sendAppointment() {
    $("#form-appoint").submit();
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
                $("#mainNavigator")[0].pushPage("postponeCustom.html").then(() => {
                    $("#solic-id").val(id);
                    $("#solic-op").val(op);
                });
                break;
        }
    });
}

function handleAppoint(id, op, qtd) {
    qtd = qtd | 0;
    let area = $("#solic-view ons-list ons-lazy-repeat");
    area.empty();
    area.append('<ons-progress-circular indeterminate></ons-progress-circular>');
    $.post('/mobile/user/appoint', {ap_id: id, mode: op, postpone: qtd}, function (response) {
        showToast(response);
    }).then(() => {
        viewHistory();
    });
}

function formattDate(date) {
    return [(date.getDate()) + '/' + (date.getMonth() + 1) + '/' + date.getFullYear()].join('');
}

function formattAddr(addr) {
    let string = "";
    console.log(addr);
    if (addr.match(/^tv\.?/i)) {
        string = addr.replace(/^tv\.?/gi, "travessa")
    }
    if (addr.match(/^av\.?/i)) {
        string = addr.replace(/^av\.?/gi, "avenida")
    }
    if (addr.match(/^r\.?/i)) {
        string = addr.replace(/^r\.?/gi, "rua")
    }
    if (addr.match(/^rod\.?/i)) {
        string = addr.replace(/^rod\.?/gi, "rodovia")
    }
    return string;
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
    });
}

function display(id, tab, index) {
    if (!$(`#${id}`).hasClass('active')) {
        $.each($(".profile_button_bar_" + tab + " ons-button"), function (key, value) {
            $(value).removeClass('active');
        });
        $(`#${id}`).addClass('active');
        $("#carousel-" + tab)[0].setActiveIndex(index);
    }
}