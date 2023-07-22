$(document).ready(function () {

    $(".form_prevent_multiple_submits").submit(function(event){
        $('.button_submit').prop('disabled', true);
        setTimeout(function () {
            $('.button_submit').prop('disabled', false);
        }, 3000);
    });

    // $('.button_prevent_multiple_submits').on('click', function () {
    //     $(this).attr('disabled', 'true');
    //     $('.form_prevent_multiple_submits').submit();
    // });

    // $("#form-create").submit(function(event){
    //     $('#button_submit').prop('disabled', true);
    //     setTimeout(function () {
    //         $('#button_submit').prop('disabled', false);
    //     }, 5000);
    // });

});
