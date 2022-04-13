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
            <div id="viewMessager" class="alert alert-failure d-flex justify-content-between container pt-3 pb-3" role="alert">
                {{ Session::get('viewMessage') }}
                <button type="button" class="close-message" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @elseif(Session::get('messageType') == 'alert')
            
        @endif
    </section>
@endif


<script>
    var viewMessage = "{{ Session::get('viewMessage') }}";
    if(viewMessage != ''){
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('cleansessionmessage') }}",
            type: 'POST'
        });
    }

    $('.close-message').on('click', function(){
        $('#messages_wrapper').fadeOut();
        awaitAndRemove();
    });

    function awaitAndRemove(){
        var myInterval = setInterval(function(){
            $('#viewMessager').remove();
            clearInterval(myInterval);
        }, 2000);
    }
</script>