@if(!is_null(Session::get('viewMessage')))
    <section id="messages_wrapper" class="container sticky-top mt-3 mb-3">
        @if(Session::get('messageType') == 'success')
            <div id="viewMessager" class="alert alert-success d-flex justify-content-between container pt-3 pb-3" role="alert">
                {{ Session::get('viewMessage') }}
                <button type="button" class="close-message" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @elseif(Session::get('messageType') == 'failure')
            <div id="viewMessager" class="alert alert-danger d-flex justify-content-between container pt-3 pb-3" role="alert">
                {{ Session::get('viewMessage') }}
                <button type="button" class="close-message" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @elseif(Session::get('messageType') == 'alert')
            
        @endif
    </section>
@endif

<section id="custom-messages_wrapper" class="container sticky-top mt-3 mb-3" style="display:none;">
    <div id="custom-viewMessager" class="alert alert-success d-flex justify-content-between container pt-3 pb-3" role="alert">
        <a class="mt-auto-mb-auto"></a>
        <button type="button" class="close-message" data-isCustom="true" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
</section>


<script>
    $(document).ready(function(){
        $('#custom-messages_wrapper').hide();
    });

    var viewMessage = "{{ Session::get('viewMessage') }}";
    if(viewMessage != ''){
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('cleansessionmessage') }}",
            type: 'POST'
        });
    }

    $('.close-message').on('click', function(){
        $('#messages_wrapper').fadeOut();
        if($(this).attr('data-isCustom') != ''){
            awaitAndRemove('custom-messages_wrapper', false);
            return;
        }
        awaitAndRemove();
    });

    function addCustomMessage(message = ''){
        $('#custom-viewMessager a').text(message);
        $('#custom-messages_wrapper').show();
    }

    function awaitAndRemove(id = 'viewMessager', removeTag = true){
        var myInterval = setInterval(function(){
            if(removeTag == true){
                $('#' + id).remove();
            }else{
                $('#' + id).fadeOut();
            }
            clearInterval(myInterval);
        }, 350);
    }
</script>