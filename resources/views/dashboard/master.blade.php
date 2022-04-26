<html lang="<?= env('USER_LANGUAGE'); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
        <title>Painel - GereFacil</title>
        <link rel="icon" href="images/favicon.webp">

        <link rel="stylesheet" href="{{url('/css/app.css')}}">
        <link rel="stylesheet" href="{{url('/externalfeatures/bootstrap.css')}}">
    </head>
</html>
@include('dashboard/loader_of_page')
<script src="{{url('/externalfeatures/jquery.js')}}"></script>
<script src="{{url('/externalfeatures/bootstrap.js')}}"></script>
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
</script>
<style>
    .small-icon{
        width: 1.5em;
        height: 1.5em;
    }
</style>