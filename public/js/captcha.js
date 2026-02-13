$('#reload-captcha').on('click', function () {
    $.ajax({
        type: 'GET',
        url: 'reload-captcha',
        success: function (data) {
            $('#captcha-img').html(data.captcha);
        }
    });
});