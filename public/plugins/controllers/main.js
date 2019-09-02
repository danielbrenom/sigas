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

    fn.procedureList = function (profid) {
        $.get('/mobile/get-profissional-info', {proc: true, prof: $("#profid").val()}, function (response) {
            $("#req-procd").empty();
            $.each(response[0], function (key, value) {
                $("#req-procd").append(
                    '<option value="' + value.p_id_procedure + '">' + value.procedure_description + '</option>'
                );
            });

        })
    };

    fn.viewDetails = function (id) {
        let html;
        $.get('/mobile/get-profissional-info', {id_user: id}, function (data) {
            html = data;
        }).then(function () {
            $('#mainNavigator')[0].pushPage('detailsPage.html').then(function () {
                $('#fInformacoes').empty();
                $('#fInformacoes').append(html);
                let addr = fn.formattAddr($("#addr_text").text());
                if (addr === "") {
                    $.get('https://nominatim.openstreetmap.org/search.php', {
                        q: addr,
                        format: 'json'
                    }, function (response) {
                        console.log(response);
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
    };

    fn.viewHistory = function (type_id) {
        $("#mainNavigator")[0].pushPage('userHistory.html').then(() => {
            $.get('/mobile/get-user-historic', {type: type_id}, function (response) {
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
                        let item = '<ons-list-item expandable>\n' +
                            value.procedure_description + ' de ' + value.desc_especialidade +
                            '  <div class="expandable-content">' +
                            '<ul>' +
                            '<li>Profissional: ' + value.prof_name + '</li>' +
                            '<li>Solicitado para: ' + dataShow.toLocaleDateString() + ' às ' + dataShow.toLocaleTimeString() + '</li>' +
                            '</ul>' +
                            '</div>\n' +
                            '</ons-list-item>';
                        list.append(item);
                    })
                }
            })
        });
    };

    fn.checkDates = function (id) {
        let state;
        $.get('/user-state', {}, function (response) {
            state = response.state;
        }).then(function () {
            if (state) {
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

    fn.singupForm = function (type) {
        if(type == 1) {
            $('#mainNavigator')[0].pushPage('singupForm.html');
        }else{
            $('#mainNavigator')[0].pushPage('singupProfForm.html');
        }
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
            $("#req-date").val(info.date.toLocaleDateString());
            $("#req-hour").val(info.date.toLocaleTimeString());
            $("#idProf").val($("#profid").val());
        });
    };

    fn.sendAppointment = function () {
        $("#form-appoint").submit();
    };

    fn.formattDate = function (date) {
        return [(date.getDate()) + '/' + (date.getMonth() + 1) + '/' + date.getFullYear()].join('');
    };

    fn.formattAddr = function (addr) {
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
    };
});