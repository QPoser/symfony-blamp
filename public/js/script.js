

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
});