@include('master_head')

@if(!$admin)
    @include('header-menu', ['enableSearch' => \App\Helpers\Functions::viewLink('/')])
@endif

<div id="master-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title h4">Título</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="openModal(true, 'master-modal')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <?php $myCep = (Auth::user() && Auth::user()->cep ? Auth::user()->cep : null); ?>
            <div id="shipment-data" class="">
                <p>
                    <?= ucfirst(translate('my cep')) ?>
                </p>
                {{ Form::text('fromCep', $myCep, ['id' => 'my-cep', 'class' => 'form-control mt-3 mb-3', 'placeholder' => ucfirst(translate('my cep'))]) }}
                <p>
                    <?= ucfirst(translate('destination cep')) ?>
                </p>
                {{ Form::text('fromCep', $productOwnerCep, ['class' => 'form-control mt-3 mb-3', 'disabled' => 'disabled', 'placeholder' => ucfirst(translate('destination cep'))]) }}
                <div class="mt-3 mb-3">
                    <p>
                        <?= ucfirst(translate('types of shipment')) ?>
                    </p>
                    <select name="shipmentType" id="typesOfShipment" class="form-control">
                        <?php foreach($shipmentTypes as $keyName => $value){ ?>
                            <?php $selected = ($keyName == $selectedShipmentType ? 'selected' : ''); ?>
                            <option value="{{$value}}" {{$selected}}>{{$keyName}}</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="shipment-specifications">
                    <p id="shipment-value">
                        <span><?= ucfirst(translate('value')) ?>: </span>
                        <span class="value-here">
                            @if($cepData['value'])
                                {{\App\Helpers\Functions::formatMoney($cepData['value'])}}
                            @else
                                <em>{{ucfirst(translate('not found'))}}</em>
                            @endif
                        </span>
                    </p>
                    <p id="shipment-delivery-time">
                        <span><?= ucfirst(translate('delivery time')) ?>: </span>
                        <span class="value-here">
                            @if($cepData['deliverTime'])
                                {{$cepData['deliverTime'] . ' ' . ucfirst(translate('days'))}}
                            @else
                                <em>{{ucfirst(translate('not found'))}}</em>
                            @endif
                        </span>
                    </p>
                </div>
                <div class="d-flex justify-content-between border-t border-b pt-3 pb-3">
                    <span class="mt-auto mb-auto">
                        <?= ucfirst(translate('calculate shipment')) ?>
                    </span>
                    <a class="btn btn-secondary" id="searchForShipment"><img class="small-icon" src="{{asset('images/add-icon.webp')}}" alt="calculate shipment again"></a>
                </div>
                <div class=" d-flex flex-column row">
                    <a class="btn btn-primary col-sm-10 col-md-6 m-auto mt-3 mb-3 add-to-cart"><?= ucfirst(translate('add to cart')) ?></a>
                    <a class="btn btn-secondary col-sm-10 col-md-6 m-auto mt-3 mb-3" onclick="openModal(true, 'master-modal')"><?= ucfirst(translate('decline purchase')) ?></a>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary negative-btn" data-dismiss="modal" onclick="openModal(true, 'master-modal')">Fechar</button>
            <button type="button" class="btn btn-primary positive-btn">Confirmar</button>
        </div>
      </div>
    </div>
</div>

@if($admin)
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?= ucfirst(translate('product detail')); ?>
            </h2>
        </x-slot>
        <section id="productSave" class="d-flex d-flex justify-content-center mt-5">
            <div class="col-sm-10 col-md-3 m-1 p-3 pt-4 pb-4 border rounded text-center">
                <div class="w-100 text-right">
                    @if(Auth::user() && Auth::user()->id == $product->user_id)
                        <a class="btn btn-danger mb-1" onclick="removeProduct(<?= $product->id ?>)" title="<?= ucfirst(translate('remove')) ?>">
                            X
                        </a>
                    @endif
                </div>
                <div class="w-100 text-right">
                    <?php $typeOfIcon  = ($product->iLiked == true ? asset('images/hearth-icon.svg') : asset('images/hearth-icon-black.svg') ) ?>
                    <?php $typeOfTitle = ($product->iLiked == true ? ucfirst(translate('liked')) : ucfirst(translate('not liked'))) ?>
                    <img class="cursor-pointer opacity-hover likeBtn historyItem-<?= $product->id ?>" src="{{asset($typeOfIcon)}} "data-historyId="<?= $product->id ?>" title="<?= $typeOfTitle ?>">
                </div>
                <a>
                    {{asset('images/favorite-black.svg')}}
                </a>
                <div class="ps-3 pe-3 mb-4 col-sm-10 col-md-8 m-auto">
                    <img class="img-fluid rounded" src="{{$product->getPhotoAsBase64()}}">
                </div>
                <p class="h5">
                    {{\App\Helpers\Functions::shortenText($product->name, 25)}}
                </p>
                <div class="short-description mt-3 mb-3">
                    <p>{{\App\Helpers\Functions::shortenText($product->productDetails, 25)}}</p>
                </div>
                <div>
                    @if($product->promotion)
                        <small class="prior-price"><?= ucfirst(translate('former price')) ?></small>
                        <p>{{\App\Helpers\Functions::formatMoney($product->price)}} (<?= ucfirst(translate('promotion')) ?>)</p>
                    @else
                        <p>
                            {{\App\Helpers\Functions::formatMoney($product->price)}}
                        </p>
                    @endif
                </div>
                <a class="btn btn-dark w-100 opacity-hover add-to-cart"><?= ucfirst(translate('add to cart')) ?></a>
            </div>        
        </section>
    </x-app-layout>
@else
    <div class="container mt-5 mb-5">
        <h2 class="h2">
            <?= ucfirst(translate('product detail')); ?>
        </h2>
    </div>
    <section id="productSave" class="d-flex d-flex justify-content-center mt-5">
        <div class="col-sm-10 col-md-3 m-1 p-3 pt-4 pb-4 border rounded text-center">
            <div class="w-100 text-right">
                @if(Auth::user() && Auth::user()->id == $product->user_id)
                    <a class="btn btn-danger mb-1" onclick="removeProduct(<?= $product->id ?>)" title="<?= ucfirst(translate('remove')) ?>">
                        X
                    </a>
                @endif
            </div>
            <div class="w-100 text-left">
                <?php $typeOfIconLike  = ($product->iLiked == true ? asset('images/hearth-icon.svg') : asset('images/hearth-icon-black.svg') ) ?>
                <?php $typeOfTitleLike = ($product->iLiked == true ? ucfirst(translate('liked')) : ucfirst(translate('not liked'))) ?>

                <?php $typeOfIconFavorite  = ($product->myFavorite == true ? file_get_contents(asset('images/favorite-yellow.svg')) : file_get_contents(asset('images/favorite-black.svg')) ) ?>
                <?php $typeOfTitleFavorite = ($product->myFavorite == true ? ucfirst(translate('my favorite')) : ucfirst(translate('not my favorite'))) ?>
                <div class="d-flex">
                    <img class="cursor-pointer opacity-hover small-icon likeBtn historyItem-<?= $product->id ?>" src="{{asset($typeOfIconLike)}} "data-historyId="<?= $product->id ?>" title="<?= $typeOfTitleLike ?>">
                    <img class="cursor-pointer opacity-hover small-icon ms-3 favoriteBtn productItem-<?= $product->id ?>" src="{{$typeOfIconFavorite}} "data-historyId="<?= $product->id ?>" title="<?= $typeOfTitleFavorite ?>">
                </div>
            </div>
            <div class="ps-3 pe-3 mb-4 col-sm-10 col-md-8 m-auto">
                <img class="img-fluid rounded" src="{{$product->getPhotoAsBase64()}}">
            </div>
            <div class="mt-3 mb-3 text-left">
                <p class="h6">Titulo</p>
                <p>{{Form::text('productName', $product->name, ['class' => 'form-control', 'disabled' => 'disabled'])}}</p>
            </div>
            <div class="short-description mt-3 mb-3 text-left">
                <p class="h6">Detalhes</p>
                <p>{{Form::text('productDetails', $product->productDetails, ['class' => 'form-control', 'disabled' => 'disabled'])}}</p>
            </div>
            <div class="mt-3 mb-3 text-left">
                <p class="h6">Valor</p>
                @if($product->promotion)
                    <small class="prior-price"><?= ucfirst(translate('former price')) ?>: {{\App\Helpers\Functions::formatMoney($product->price)}}</small>
                    <p>
                        {{Form::text('price', \App\Helpers\Functions::formatMoney($product->promotionalPrice), ['class' => 'form-control', 'disabled' => 'disabled'])}} (<?= ucfirst(translate('promotion')) ?>)
                    </p>
                @else
                    <p>
                        {{Form::text('price', \App\Helpers\Functions::formatMoney($product->price), ['class' => 'form-control', 'disabled' => 'disabled'])}}
                    </p>
                @endif
            </div>
            <div class="row col-sm-10 col-md-8 m-auto">
                <a id="calculate-shipment" class="btn btn-secondary opacity-hover mt-3 mb-3"><?= ucfirst(translate('calculate shipment')) ?></a>
                <a class="btn btn-primary opacity-hover mt-3 mb-3 add-to-cart"><?= ucfirst(translate('add to cart')) ?></a>
            </div>
        </div>        
    </section>
@endif

@include('main_footer')

<script>
    var selectedShipmentType = "{{$selectedShipmentType}}";
    $(document).ready(function(){
        $('#typesOfShipment').val(selectedShipmentType);
    });

    function removeProduct(productId = null){
        if("{{Auth::user()}}" == ''){
            return;
        }
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/product/remove') }}",
            method: 'Post',
            data: {'productId': productId},
            dataType: 'JSON',
            success: function(result){
                window.location.href = "{{\App\Helpers\Functions::viewLink('dashboard/product')}}";
            }
        });
    }

    $('#calculate-shipment').on('click', function(){
        hideModalFooter();
        addTitle("{{ucfirst(translate('shipment'))}}");
        openModal();
    });

    $('#searchForShipment').on('click', function(){
        openLoader();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('calculateshipment') }}",
            method: 'Post',
            data: {'productId': "{{$product->id}}", cep: $('#my-cep').val(), shipmentType: $('#typesOfShipment').val()},
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    $('#shipment-value').find('.value-here').text(result.content.value);
                    $('#shipment-delivery-time').find('.value-here').text(result.content.deliverTime + ' ' + "{{ucfirst(translate('days'))}}");
                }else{
                    alert(result.message);
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    });

    $('.likeBtn').on('click', function(){
        if("{{Auth::user()}}" == ''){
            return;
        }
        openLoader();
        var productId = $(this).attr('data-historyId');
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/product/handlelikes') }}",
            method: 'Post',
            data: {'productId': productId},
            dataType: 'JSON',
            success: function(result){
                var tag = $('.historyItem-'+productId);
                if(result.success == true){
                    if(result.added == true){
                        tag.attr('src', "{{asset('images/hearth-icon.svg')}}");
                        tag.attr('title', "<?= ucfirst(translate('liked')) ?>");
                    }else{
                        tag.attr('src', "{{asset('images/hearth-icon-black.svg')}}");
                        tag.attr('title', "<?= ucfirst(translate('not liked')) ?>");
                    }
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    });
    
    $('.favoriteBtn').on('click', function(){
        if("{{Auth::user()}}" == ''){
            return;
        }
        openLoader();
        var productId = $(this).attr('data-historyId');
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/product/favoriteproduct') }}",
            method: 'Post',
            data: {'productId': productId},
            dataType: 'JSON',
            success: function(result){
                var tag = $('.productItem-'+productId);
                if(result.success == true){
                    if(result.added == true){
                        tag.attr('src', "{{file_get_contents(asset('images/favorite-yellow.svg'))}}");
                        tag.attr('title', "<?= ucfirst(translate('my favorite')) ?>");
                    }else{
                        tag.attr('src', "{{file_get_contents(asset('images/favorite-black.svg'))}}");
                        tag.attr('title', "<?= ucfirst(translate('not my favorite')) ?>");
                    }
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    });

    var orderId   = "{{$orderId}}"
    var productId = "{{$product->id}}"
    $('.add-to-cart').on('click', function(){
        openLoader();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('productorder/additem') }}",
            method: 'Post',
            data: {orderId: orderId, productId: productId},
            dataType: 'JSON',
            success: function(result){
                addMessageToToast(result.message);
                openModal(true);
            },
            complete: function(){
                openLoader(true);
            }
        });
    });
</script>