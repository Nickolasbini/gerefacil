@include('dashboard/master')
@include('dashboard/view_message')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('my products')); ?>
        </h2>
    </x-slot>

    <section id="productSave" class="d-flex">
        <div class="left-side col-sm-12 col-md-2 border-r">
            <ul>
                <li class="d-flex p-2 mt-5 mb-5 white-hover rounded cursor-pointer">
                    <img class="small-icon me-2" src="{{ asset('images/add-icon.webp') }}">
                    <a class="opacity-hover btn" href="{{\App\Helpers\Functions::viewLink('/dashboard/product/save')}}"><?= ucfirst(translate('create product')) ?></a>
                </li>
                <li class="d-flex p-2 mt-5 mb-5 white-hover rounded cursor-pointer">
                    <img class="small-icon me-2" src="{{ asset('images/list-icon.webp') }}">
                    <a class="opacity-hover btn" href="{{\App\Helpers\Functions::viewLink('/dashboard/product', true)}}"><?= ucfirst(translate('list products')) ?></a>
                </li>
            </ul>
        </div>
        <div class="right-side col-sm-12 col-md-9 m-auto">
            <p class="h5 mt-5 mb-5">
                @if($product)
                    <?= ucfirst(translate('edit product')) ?>
                @else
                    <?= ucfirst(translate('create product')) ?>
                @endif
            </p>
            <div class="action-container">
                {{ \Form::open(['route' => \App\Helpers\Functions::viewLink('dashboard/product/save'), 'enctype' => 'multipart/form-data', 'id' => 'saveProduct-form', 'class' => 'container', 'method' => 'post'])}}
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
                        <label class="mt-2 h5"><?= ucfirst(translate('weight')) ?></label>
                        @if($product)
                            <input name="weight" id="weight" type="text" class="form-control rounded" value="{{$product->weight}}">
                        @else
                            <input name="weight" id="weight" type="text" class="form-control rounded">
                        @endif
                    </div>
                    <div class="mt-3 mb-3 row col-8">
                        <label class="mt-2 h5"><?= ucfirst(translate('length')) ?></label>
                        @if($product)
                            <input name="length" id="length" type="text" class="form-control rounded" value="{{$product->length}}">
                        @else
                            <input name="length" id="length" type="text" class="form-control rounded">
                        @endif
                    </div>
                    <div class="mt-3 mb-3 row col-8">
                        <label class="mt-2 h5"><?= ucfirst(translate('width')) ?></label>
                        @if($product)
                            <input name="width" id="width" type="text" class="form-control rounded" value="{{$product->width}}">
                        @else
                            <input name="width" id="width" type="text" class="form-control rounded">
                        @endif
                    </div>
                    <div class="mt-3 mb-3 row col-8">
                        <label class="mt-2 h5"><?= ucfirst(translate('product details')) ?></label>
                        @if($product)
                            <textarea class="form-control" name="productDetails" rows="6">{{$product->productDetails}}</textarea>
                        @else
                            <textarea class="form-control" name="productDetails" rows="6"></textarea>
                        @endif
                    </div>
                    <div class="mt-3 mb-3 row col-8 mt-5 mb-5">
                        <a id="clickOnImgInput" class="btn btn-primary col-sm-10 col-md-5 m-auto mt-5 mb-3"><?= ucfirst(translate('add photo')); ?></a>
                        <input id="browse" style="display: none;" type="file" name="files[]" multiple class="myfrm" style="z-index: 1;" onchange="readFile()">
                        <div id="preview">
                            @if($product)
                                <img class="img-fluid m-auto rounded" src="{{$product->getPhotoAsBase64()}}">
                            @endif
                        </div>
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
        </div>
    </section>
</x-app-layout>

@include('main_footer')

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
        $('#price').val(val);
        $('#saveProduct-form').submit();
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
    var val = null;
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
        val = value;
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

    $('#clickOnImgInput').on('click', function(){
        $('#browse').click();
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
        margin: auto !important;
    }
    @media (max-width: 720px){
        #preview > img{
            width: 70px;
            height: 70px;
        }
    }
</style>