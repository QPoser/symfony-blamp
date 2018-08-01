

$( document ).ready(function () {
    $.ajax({
        type: 'GET',
        url: "/banner/vertical/get",
    }).done(function ( html ) {
        if ($( window ).width() < 768) {
            $('.banner-body .banner-vertical').html(html);
            $('#modal').modal('show');
        } else {
            $('.banner .banner-vertical').html(html);
        }
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

            myGeocoder.then(
                function (res) {
                    var center = res.geoObjects.get(0).geometry.getCoordinates();

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
        }
    }

    $( window ).scroll(function () {
        var aTop = $( window ).scrollTop();
        $('.banner-vertical img').css('top', aTop);
    });

    $('#protectButton').on('click', function () {
        let value = $('#review_create_form_assessment').val();
        if (value < 3) {
            $('#protectSpace').css('display', 'block');
            $(this).css('display', 'none');
        } else {
            $('form[name="review_create_form"]').submit();
        }
    });

    $('.protectFalse').on('click', function () {
         $("#review_create_form_assessment option[value='1']").remove();
         $("#review_create_form_assessment option[value='2']").remove();
         $('#protectSpace').css('display', 'none');
         $('#protectButton').css('display', 'block');
         $('#protectDescription').css('display', 'block');
    });
});

