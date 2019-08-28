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

    fn.viewDetails = function (id) {
        let html;
        $.get('/mobile/get-profissional-info', {id_user: id}, function (data) {
            html = data;
        }).then(function () {
            $('#mainNavigator')[0].pushPage('detailsPage.html').then(function () {
                $('#fInformacoes').empty();
                $('#fInformacoes').append(html);
                let addr = $("#addr_text").text();
                if(addr.match("/tv\./"))
                $.get('https://nominatim.openstreetmap.org/search.php', {
                    q: 'Travessa barão do triunfo 706',
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
                    let title =  response[0].display_name.split(',');
                    marker.bindPopup("<b>"+title[1]+"</b>").openPopup();
                });
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
    }
});