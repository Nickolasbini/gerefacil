@include('dashboard/master')
@include('dashboard/view_message')

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
                {{ Form::text('fromCep', $myCep, ['class' => 'form-control mt-3 mb-3', 'placeholder' => ucfirst(translate('my cep'))]) }}
                <p>
                    <?= ucfirst(translate('destination cep')) ?>
                </p>
                {{ Form::text('fromCep', $productOwnerCep, ['class' => 'form-control mt-3 mb-3', 'disabled' => 'disabled', 'placeholder' => ucfirst(translate('destination cep'))]) }}
                <div class="shipment-specifications">
                    <p>
                        @if($cepData['value'])
                            <?= ucfirst(translate('value')) ?>: {{\App\Helpers\Functions::formatMoney($cepData['value'])}}
                        @else
                            <?= ucfirst(translate('value')) ?>: <em>{{ucfirst(translate('not found'))}}</em>
                        @endif
                    </p>
                    <p>
                        @if($cepData['deliverTime'])
                            <?= ucfirst(translate('delivery time')) ?>: {{$cepData['deliverTime'] . ' ' . ucfirst(translate('days'))}}
                        @else
                            <?= ucfirst(translate('delivery time')) ?>: <em>{{ucfirst(translate('not found'))}}</em>
                        @endif
                    </p>
                </div>
                <div class=" d-flex flex-column row">
                    <a class="btn btn-success col-sm-10 col-md-6 m-auto mt-3 mb-3"><?= ucfirst(translate('proceed to purchase')) ?></a>
                    <a class="btn btn-secondary col-sm-10 col-md-6 m-auto mt-3 mb-3"><?= ucfirst(translate('decline purchase')) ?></a>
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
                <a class="btn btn-dark w-100 opacity-hover" href="{{\App\Helpers\Functions::viewLink('product/detail/'.$product->id)}}"><?= ucfirst(translate('add to cart')) ?></a>
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
                <a class="btn btn-primary opacity-hover mt-3 mb-3" href="{{\App\Helpers\Functions::viewLink('product/detail/'.$product->id)}}"><?= ucfirst(translate('add to cart')) ?></a>
            </div>
        </div>        
    </section>
@endif

@include('main_footer')

<script>
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