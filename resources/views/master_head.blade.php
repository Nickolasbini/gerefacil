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
            <strong class="mr-auto"><span class="primary-color">GereFacil</span></strong>
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
    function openModal(close = false){
        if(close == false){
            $('#master-modal').modal('show');
        }else{
            $('#master-modal').modal('hide');
        }
    }
    function addModalContent(title = '', message = '', showFooter = false){
        $('#master-modal').find('.modal-title').text(title);
        $('#master-modal').find('.modal-body').html(message);
        if(showFooter == false){
            $('#master-modal').find('.modal-footer').hide();
        }else{
            $('#master-modal').find('.modal-footer').find('.negative-btn').text(showFooter['negativeBtn']);
            $('#master-modal').find('.modal-footer').find('.positive-btn').text(showFooter['positiveBtn']);
        }
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
</script>

