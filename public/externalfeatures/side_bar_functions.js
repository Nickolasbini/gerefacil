// do here like a mouse hover to open else, stays closed
$('#side-bar-button').on('click', function(){
    $(this).parents('.side-bar').toggleClass('close');
    if($(this).parents('.side-bar').hasClass('close') == true){
        $('.side-bar-option').addClass('p-3');
        $('.side-bar-option').addClass('white-hover');
        $('.side-bar-option').addClass('borders-gray');
        $('.side-bar-option a').hide();
    }else{
        $('.side-bar-option').removeClass('p-3');
        $('.side-bar-option').removeClass('white-hover');
        $('.side-bar-option').removeClass('borders-gray');
        $('.side-bar-option a').show();
    }
});