<div id="loader-spinner" class="text-center" style="display:none;">
  <div class="spinner-border text-warning mt-5" role="status"></div>
</div>

<script>
    var scrollTop = 0;
    function openLoader(close = false){
        scrollTop = $(window).scrollTop();
        if(close == true){
            $('body').css('overflow', 'scroll');
            $(document).scrollTop(scrollTop);
            $('#loader-spinner').hide();
        }else{
            $('body').css('overflow', 'hidden');
            $(document).scrollTop(0);
            $('#loader-spinner').show();
        }
    }
</script>

<style>
    #loader-spinner{
        position: absolute!important;
        top: 0!important;
        left: 0!important;
        right: 0!important;
        bottom: 0!important;
        z-index: 1000000!important;
        background: #2d2d2d78!important;
    }
    .spinner-border{
        width: 5rem!important;
        height: 5rem!important;
    }
</style>
