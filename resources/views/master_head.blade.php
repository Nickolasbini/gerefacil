<!DOCTYPE html>
<html lang="pt_BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>GereFacil</title>
		<link rel="icon" href="{{ asset('backgrounds/home-background.webp') }}">

        <!-- App CSS -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="{{ asset('externalfeatures/bootstrap.css') }}" rel="stylesheet">
    </head>
    <div aria-live="polite" aria-atomic="true">
        <div id="main-toast" class="toast mt-4 me-4" style="position:fixed; top:0; right:0; z-index:100000; background-color:rgb(255, 255, 255);">
          <div class="toast-header">
            <img src="{{asset('backgrounds/login.webp')}}" class="perfect-rounded mr-2 small-icon" alt="...">
            <strong class="mr-auto"><span class="secondary-color">GereFacil</span></strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="closeToast()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="toast-body"></div>
        </div>
    </div>

    <div id="master-modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Título</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="openModal(true)">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary negative-btn" data-dismiss="modal" onclick="openModal(true)">Fechar</button>
                <button type="button" class="btn btn-primary positive-btn">Confirmar</button>
            </div>
          </div>
        </div>
    </div>

    <div id="left-side-bar" class="position-absolute w-25 left-side-bar border-r shadow sticky-left" style="display:none;">
        <div class="container p2 mt-5">
            <div class="w-100 text-right">
                <a class="opacity-hover cursor-pointer primary-color" onclick="openLeftSideBar(true)">X</a>
            </div>
            <p class="h4 border-b pb-3">
                {{ucfirst(translate('categories'))}}
            </p>
            <div class="wrapper-of-categories d-flex flex-column justify-content-center"></div>
        </div>
    </div>
    <div id="oppener-of-left-side-bar" class="cursor-pointer" onclick="openLeftSideBar()">
        <a class="opacity-hover cursor-pointer secondary-color">V</a>
    </div>

    <div id="right-side-bar" class="position-absolute w-25 right-side-bar border-l shadow sticky-right" style="display:none;">
        <div class="container p2 mt-5">
            <div class="w-100 text-right">
                <a class="opacity-hover cursor-pointer primary-color" onclick="openRightSideBar(true)">X</a>
            </div>
            <p class="h4 border-l pb-3">
                {{ucfirst(translate('cart'))}}
            </p>
            <div class="wrapper-of-cart d-flex flex-column justify-content-center"></div>
        </div>
    </div>
    <div id="oppener-of-right-side-bar" class="cursor-pointer" onclick="openRightSideBar()">
        <a class="opacity-hover cursor-pointer secondary-color">V</a>
    </div>

    @include('dashboard/loader_of_page')
</html>
<script src="{{ asset('externalfeatures/jquery.js') }}"></script>
<script src="{{ asset('externalfeatures/bootstrap.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    /* Modal methods */
    function openModal(close = false, id = 'master-modal'){
        if(close == false){
            $('#'+id).modal('show');
        }else{
            $('#'+id).modal('hide');
        }
    }
    function addTitle(title = '', id = 'master-modal'){
        $('#'+id).find('.modal-title').text(title);
    }
    function addModalContent(title = '', message = '', showFooter = false, id = 'master-modal'){
        $('#'+id).find('.modal-title').text(title);
        $('#'+id).find('.modal-body').html(message);
        if(showFooter == false){
            $('#'+id).find('.modal-footer').hide();
        }else{
            $('#'+id).find('.modal-footer').find('.negative-btn').text(showFooter['negativeBtn']);
            $('#'+id).find('.modal-footer').find('.positive-btn').text(showFooter['positiveBtn']);
        }
    }
    function addModalFooterData(cancelMessage = 'Fechar', confirmMessage = 'Confirmar', id = 'master-modal'){
        $('#'+id).find('.negative-btn').text(cancelMessage);
        $('#'+id).find('.positive-btn').text(confirmMessage);
    }

    function hideModalFooter(id = 'master-modal'){
        $('#'+id).find('.modal-footer').hide();
    }

    var messageToDispaly = "{{ session()->get('viewMessage') }}";
    var type = "{{ session()->get('messageType') }}";
    var screenWidht = 0;
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        $('#main-toast').toast();
        setDelayToToast();
        if(messageToDispaly != ''){
            $('#main-toast').find('.toast-body').text(messageToDispaly);
            $('#main-toast').toast('show');
            cleanViewMessage();
        }
        $('#master-modal').modal();
        screenWidht = $(window).scrollTop();
        fetchCategories();
        fetchMyCart();
    });

    function cleanViewMessage(){
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('cleansessionmessage') }}",
            type: 'POST'
        });
    }

    function setDelayToToast(delayTimeInSeconds = '5000', toastId = 'main-toast'){
        $('#'+toastId).attr('data-bs-delay', delayTimeInSeconds);
    }

    function closeToast(idOfElement = 'main-toast'){
        $('#'+idOfElement).toast('hide');
    }

    function addMessageToToast(message){
        if(message != ''){
            $('#main-toast').find('.toast-body').text(message);
            $('#main-toast').toast('show');
            cleanViewMessage();
        }
    }

    /* Side bars */
    function openLeftSideBar(close = false){
        if(close == false){
            $('#left-side-bar').show();
        }else{
            $('#left-side-bar').hide();
        }
    }

    function openRightSideBar(close = false){
        if(close == false){
            $('#right-side-bar').show();
        }else{
            $('#right-side-bar').hide();
        }
    }

    // fetches all categories to put on left side menu
    var basePath = "{{\App\Helpers\Functions::viewLink('/')}}";
    function fetchCategories(){
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('category/list') }}",
            type: 'POST',
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    var html = '';
                    var data = result.content;
                    for(var categoryId in data){
                        html += '<div class="col-md-1 m-2 btn category-button-left-side-bar" data-categoryId="'+ categoryId +'">';
                        html += '<a class="opacity-hover cursor-pointer primary-color" href="'+basePath+'/?search='+categoryId+'&filter=category">'+data[categoryId]+'</a>';
                        html += '</div>';
                    }
                    $('.wrapper-of-categories').html(html);
                }
            }
        });
    }

    function fetchMyCart(){
        if("{{Session::get('authUser-id')}}" == ''){
            $('#oppener-of-right-side-bar').remove();
            return;
        }
        return;
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('productorder/listcart') }}",
            type: 'POST',
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    var html = '';
                    var data = result.content;
                    for(i = 0; i < data.length; i++){
                        html += '<div class="col-md-1 m-2 btn category-button-left-side-bar" data-categoryId="'+ categoryId +'">';
                        html += '<a class="opacity-hover cursor-pointer primary-color" href="'+basePath+'/?search='+categoryId+'&filter=category">'+data[categoryId]+'</a>';
                        html += '</div>';
                    }
                    $('.wrapper-of-categories').html(html);
                }
            }
        });
    }
</script>

<style>
    .left-side-bar{
        top: 0;
        bottom: 0;
        left: 0;
        background: gray;
        z-index: 100000;
    }
    .left-side-bar-oppener-button{
        top:  0;
        left: 0;
    }
    #oppener-of-left-side-bar{
        position: fixed;
        background: rgb(71, 123, 129);
        padding: 2%;
        left: 0;
        top: 50%;
    }
    .right-side-bar{
        top: 0;
        bottom: 0;
        right: 0;
        background: gray;
        z-index: 100000;
    }
    #oppener-of-right-side-bar{
        position: fixed;
        background: rgb(71, 123, 129);
        padding: 2%;
        right: 0;
        top: 50%;
    }
    .small-icon{
        width: 1.5em;
        height: 1.5em;
    }

    .sticky-left{
        position: sticky !important;
        float: left;
        height: 100vh;
    }
    .sticky-right{
        position: sticky !important;
        float: right;
        height: 100vh;
    }
</style>