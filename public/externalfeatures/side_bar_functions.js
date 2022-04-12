var screenWidth = 0;
$(document).ready(function(){
    screenWidth = $(window).width();
    handleScreenWidth();

    // detect screen change
    $(window).on('resize', function() {
        screenWidth = $(this).width();
        handleScreenWidth();
    });
});

var mini = true;
function toggleSidebar() {
    if (mini) {
        document.getElementsByClassName("mySlidebar")[0].style.width = "300px";
        document.getElementsByClassName("action-container")[0].style.marginLeft = "300px";
        $('.side-bar-option').find('a').show();
        $('.side-bar-option').removeClass('p-3');
        $('#side-bar-button').hide();
        this.mini = false;
    } else {
        document.getElementsByClassName("mySlidebar")[0].style.width = "85px";
        document.getElementsByClassName("action-container")[0].style.marginLeft = "85px";
        $('.side-bar-option').find('a').hide();
        $('.side-bar-option').addClass('p-3');
        $('#side-bar-button').show();
        this.mini = true;
    }
}

// activates and deactivate certain thing accordingly to screen width
function handleScreenWidth(){
    if(screenWidth < 760){
        $('.mySlidebar').attr('onmouseover', '');
        $('.mySlidebar').attr('onmouseout', '');
        $('.side-bar-option').removeClass('p-3');
    }else{
        if(mini == true){
            $('.side-bar-option').find('a').hide();
        }
        $('.mySlidebar').attr('onmouseover', 'toggleSidebar()');
        $('.mySlidebar').attr('onmouseout', 'toggleSidebar()');
        $('.side-bar-option').addClass('p-3');        
    }
}