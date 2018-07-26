

$( document ).ready(function () {
    $.ajax({
        type: 'GET',
        url: "/banner/get",
    }).done(function ( html ) {
        $('.banner').html(html);
    }).fail(function () {
        $('.banner').css('display', 'none');
    });
});