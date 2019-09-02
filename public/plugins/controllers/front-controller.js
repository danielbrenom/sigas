$(function () {
    if (ons.isReady()) {
        $.get('/mobile/front/get-log-messages', {}, function (response) {
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
        $.get("/mobile/front/get-especialidades", {}, function (data) {
            $.each(data[0], function (key, value) {
                $("#fSelectEsp ons-lazy-repeat").append('<ons-list-item modifier="chevron longdivider" onclick="fn.selectP(' + value.id + ')" tappable>' + value.desc_especialidade + '</ons-list-item>');
            });
        });
    };

    fn.singupForm = function (type) {
        if (type == 1) {
            $('#mainNavigator')[0].pushPage('singupForm.html');
        } else {
            $('#mainNavigator')[0].pushPage('singupProfForm.html');
        }
    };

    fn.selectP = function (id) {
        let html;
        $.get("/mobile/front/get-profissionais", {esp: id}, function (data) {
            html = data;
        }).then(function () {
            $('#mainNavigator')[0].pushPage('professionalsPage.html').then(function () {
                $('#fResultados').empty();
                $('#fResultados').append(html);
            })
        });
    };

    fn.viewDetails = function (id) {
        let html;
        $.get('/mobile/front/get-profissional-info', {id_user: id}, function (data) {
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

    fn.checkDates = function (id) {
        swal({
            title: "Atenção",
            text: "Você deve estar logado para solicitar um procedimento.",
            icon: "warning",
            buttons: false,
            timer: 4000
        }).then(() => {
            $('#mainNavigator')[0].resetToPage('mainPage.html')
        })
    };
});