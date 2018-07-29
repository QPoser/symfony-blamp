

$( document ).ready(function () {
    $.ajax({
        type: 'GET',
        url: "/banner/vertical/get",
    }).done(function ( html ) {
        $('.banner-vertical').html(html);
    }).fail(function () {
        $('.banner-vertical').css('display', 'none');
    });

    $.ajax({
        type: 'GET',
        url: "/banner/horizontal/get",
    }).done(function ( html ) {
        $('.banner-horizontal').html(html);
    }).fail(function () {
        $('.banner-horizontal').css('display', 'none');
    });

    if (typeof address !== 'undefined') {
        ymaps.ready(init);

        function init(){
            var myGeocoder = ymaps.geocode('Новосибирск, ' + address);

            var center = 1;

            myGeocoder.then(
                function (res) {
                    center = res.geoObjects.get(0).geometry.getCoordinates();

                    var myMap = new ymaps.Map("map", {
                        // Координаты центра карты.
                        // Порядок по умолчнию: «широта, долгота».
                        // Чтобы не определять координаты центра карты вручную,
                        // воспользуйтесь инструментом Определение координат.
                        center: center,
                        // Уровень масштабирования. Допустимые значения:
                        // от 0 (весь мир) до 19.
                        zoom: 16
                    });

                    if (typeof companyName !== 'undefined') {
                        res.geoObjects.get(0).properties.set('iconContent', companyName);
                        res.geoObjects.get(0).properties.set('name', companyName);
                    }

                    myMap.geoObjects.add(res.geoObjects);
                },
                function (err) {
                    console.log('Ошибка загрузки карты')
                }
            );

            // Создание карты.



            myGeocoder.then(
                function (res) {
                    var nearest = res.geoObjects.get(0);
                    var name = nearest.properties.get('name');
                    nearest.properties.set('iconContent', name);
                    nearest.options.set('preset', 'islands#redStretchyIcon');

                },

            );
        }
    }
});