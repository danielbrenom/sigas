$().ready(function () {
    if (ons.isReady()) {
        $.get('/mobile/front/get-log-messages', {}, function (response) {
            if (response.error) {
                showToast(response.error);
            }
        });

    }
});

function profissionaisList(query) {
    query = query || null;
    $.get("/mobile/front/profissionais", {search: query}, function (response) {
        let area = $("#list-profs ons-lazy-repeat");
        area.empty();
        if (response.length === 0) {
            area.append('<ons-list-item>Não foram encontrados profissionais, tente buscar outro nome</ons-list-item>');
        }
        $.each(response, function (key, value) {
            let badge = '';
            if(value.confirmed){
                badge = '<span class="badge badge-pill badge-success">Verificado</span>';
            }else{
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

function singupForm(type) {
    if (type == 1) {
        $('#mainNavigator')[0].pushPage('singupForm.html');
    } else {
        $('#mainNavigator')[0].pushPage('singupProfForm.html');
    }
}

function selectP(id) {
    let html;
    $.get("/mobile/front/profissionais", {esp: id}, function (data) {
        html = data;
    }).then(function () {
        $('#mainNavigator')[0].pushPage('professionalsPage.html').then(function () {
            $('#fResultados').empty();
            $('#fResultados').append(html);
        })
    });
}

function viewDetails(id) {
    let html;
    $.get('/mobile/front/get-profissional-info', {id_user: id}, function (data) {
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

function checkDates(id) {
    $('#mainNavigator')[0].popPage().then(() => {
        showToast({code: 0, message: "Você deve estar logado para solicitar um procedimento."})
    }).then(() => {
        $("ons-tab[label='Login']").click();
        // $("#mainTabbar")[0].setActiveTab(1, {duration: 0.2, delay: 0.4, timing: 'ease-in'});
    })
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

function formattAddr(addr) {
    let string = "";
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