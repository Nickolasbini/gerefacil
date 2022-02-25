<form method="post" action="https://rh.crcpr.org.br/avaliacao/listbyuser" enctype="multipart/form-data">

    {{csrf_field()}}
    <div class="input-group hdtuto control-group lst increment" >
        <input type="file" name="files[]" multiple class="myfrm form-control">
        <div class="input-group-btn"> 
            <input type="submit">
        </div>
    </div>
</form>
Maximun is 20 per form