@include('dashboard/master')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($product)
                <?= ucfirst(translate('edit product')); ?>
            @else
                <?= ucfirst(translate('create product')); ?>
            @endif
            
        </h2>
    </x-slot>

    @include('dashboard/view_message')

    <section id="productSave" class="d-flex h-100">
        <div class="side-bar h-100">
            @include('dashboard/side_bar', ['title' => 'product'])
        </div>
        <div class="action-container w-100">
            {{ \Form::open(['route' => \App\Helpers\Functions::viewLink('dashboard/product/save'), 'enctype' => 'multipart/form-data', 'id' => 'productSave', 'class' => 'container', 'method' => 'post'])}}
                <div class="mt-3 mb-3 row col-8">
                    <label class="mt-2 h5"><?= ucfirst(translate('product name')) ?></label>
                    @if($product)
                        <input name="name" type="text" value="{{$product->name}}" class="form-control rounded">
                    @else
                        <input name="name" type="text" class="form-control rounded">
                    @endif
                </div>
                <div class="mt-3 mb-3 row col-8">
                    <label class="mt-2 h5"><?= ucfirst(translate('category')) ?></label>
                    {{ Form::select('category', $category ) }}
                </div>
                <div class="mt-3 mb-3 row col-8">
                    <label class="mt-2 h5"><?= ucfirst(translate('price')) ?></label>
                    @if($product)
                        <input name="price" id="price" type="text" class="form-control rounded" value="{{$product->price}}">
                    @else
                        <input name="price" id="price" type="text" class="form-control rounded">
                    @endif
                </div>
                <div class="mt-3 mb-3 row col-8">
                    <label class="mt-2 h5"><?= ucfirst(translate('quantity')) ?></label>
                    @if($product)
                        <input name="quantity" id="quantity" type="text" class="form-control rounded" value="{{$product->quantity}}">
                    @else
                        <input name="quantity" id="quantity" type="text" class="form-control rounded">
                    @endif
                </div>
                <div class="mt-3 mb-3 row col-8">
                    <label class="mt-2 h5"><?= ucfirst(translate('product details')) ?></label>
                    @if($product)
                        <textarea class="form-control" rows="6">{{$product->productDetails}}</textarea>
                    @else
                        <textarea class="form-control" rows="6"></textarea>
                    @endif
                </div>
                <div class="mt-3 mb-3 row col-8">
                    <input id="browse" type="file" name="files[]" multiple class="myfrm" style="z-index: 1;" onchange="readFile()">
                    <div id="preview" class="row"></div>
                </div>
                @if($product)
                    <div>
                        {{ Form::hidden('productId', $product->id) }}
                    </div>
                @endif
                <div class="mt-3 mb-3 row col-8">
                    {{Form::button(ucfirst(translate('save')), ['id' => 'sendForm', 'class' => 'btn btn-success button-aspect'])}}
                </div>
            {{ Form::close() }}
        </div>
    </section>
</x-app-layout>

<script>
    var productId = "<?php echo ($product ? $product->id : null); ?>";
    $(document).ready(function(){
        if(productId != ''){
            var parsedPrice = parseToCurrency($('#price').val());
            $('#price').val(parsedPrice);
        }
    });

    $('#sendForm').on('click', function(){
        var failure = false;
        if($('#price').val() == '' || $('#quantity').val() == ''){
            alert('fields required');
            return;
        }
        $(this).submit();
    });

    $('#quantity').on('input', function(){
        var quantity = $(this).val();
        parsedQuantity = quantity.replace(/\D/g,'');
        $(this).val(parsedQuantity);
    });
    $('#price').on('input', function(){
        var currentText = $(this).val();
        var lastLetter = currentText[currentText.length - 1];
        if(lastLetter != '.' && isNaN(lastLetter)){
            currentText = currentText.replace(lastLetter, '');
        }
        currentText = parseToCurrency(currentText);
        $(this).val(currentText);
    });
    function parseToCurrency(value = 10){
        if(isNaN(value)){
            value = value.replace('R$', '');
            value = value.replace('$', '');
            value = value.replace('$$', '');
            value = value.replace(',', '.');
        }
        var currencyForm = "{{env('CURRENCY_LANG')}}";
        var currencyType = "{{env('CURRENCY_TYPE')}}";
        var formatter = new Intl.NumberFormat(currencyForm, {
            style: 'currency',
            currency: currencyType,
        });
        return formatter.format(value);
    }

    function readFile() {
        if (this.files && this.files[0]) {
            var FR= new FileReader();
            FR.addEventListener("load", function(e) {
            document.getElementById("img").src       = e.target.result;
            document.getElementById("b64").innerHTML = e.target.result;
            }); 
            FR.readAsDataURL( this.files[0] );
        }
    }

    const EL_browse  = document.getElementById('browse');
    const EL_preview = document.getElementById('preview');
    const readImage = file => {
        if ( !(/^image\/(png|jpe?g|gif)$/).test(file.type) )
            return EL_preview.insertAdjacentHTML('beforeend', `<div><?= ucfirst(translate('unsupported format')) ?> ${file.type}: ${file.name}</div>`);

        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const img  = new Image();
                img.addEventListener('load', () => {
                EL_preview.appendChild(img);
                // EL_preview.insertAdjacentHTML('beforeend', `<div>${file.name} ${img.width}×${img.height} ${file.type} ${Math.round(file.size/1024)}KB</div>`);
            });
            img.src = reader.result;
        });
        reader.readAsDataURL(file);  
        };

        EL_browse.addEventListener('change', ev => {
        EL_preview.innerHTML = ''; // Clear Preview
        const files = ev.target.files;
        if (!files || !files[0]) return alert('File upload not supported');
        [...files].forEach( readImage );
    });

    $('#preview').on('change', function(){
        var imgs = $(this).find('img');
        imgs.each(function(){
            $(this).addClass('img-wrapper');
        });
    });
</script>

<style>
    .text-area-form{
        /* here make the text start at the top */
    }
    .button-aspect{
        background-color: #157347;
    }
    .button-aspect:hover{
        opacity: 0.8!important;
        transition: 0.2s!important;
    }
    #preview > img{
        width: 200px;
        height: 200px;
    }
    @media (max-width < 760px){
        #preview > img{
            width: 70px;
            height: 70px;
        }
    }
</style>