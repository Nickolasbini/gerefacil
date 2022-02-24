<section id="messages_wrapper" class="container pt-3 pb-3">
    @if(!is_null(Session::get('viewMessage')))
        @if(Session::get('messageType') == 'success')
            <div id="viewMessager" class="alert alert-success d-flex justify-content-between" role="alert">
                {{ Session::get('viewMessage') }}
                <button type="button" class="close-message" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @elseif(Session::get('messageType') == 'failure')

        @elseif(Session::get('messageType') == 'alert')
            
        @endif
    @endif
</section>

<script>
    $.ajax({
        url: "{{ \App\Helpers\Functions::viewLink('dashboard/cleansessionmessage') }}",
        type: 'POST'
    });

    $('.close-message').on('click', function(){
        $('#messages_wrapper').fadeOut();
    });
</script>