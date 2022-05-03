@include('master_head')
@include('header-menu', ['enableSearch' => \App\Helpers\Functions::viewLink('/')])

<section class="container mt-5 mb-5 col-sm-10 col-md-8">
    <div>
        <p class="h4 mt-5 mb-5">
            {{ ucfirst(translate('my cart')) }}
        </p>
    </div>
    <section id="cart-list" class="row" data-orderId="{{ $order->id }}">
        <div class="col-md-7">
            @foreach($productOrder as $orderedProduct)
                <div class="row border-t pt-3 mb-4 aProduct">
                    <?php $orderedProduct->addProductObject() ?>
                    <div class="d-flex">
                        <div class="col-sm-10 col-md-4">
                            <img src="{{ $orderedProduct->product->getPhotoAsBase64() }}" alt="product photo" class="img-fluid rounded p-3">
                        </div>
                        <div class="col-sm-10 col-md-8 p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between mt-auto mb-auto">
                                <label class="">
                                    {{ $orderedProduct->product->name }}
                                </label>
                                <a class="btn btn-danger remove-item-from-cart" data-productOrderId="{{$orderedProduct->id}}">X</a>
                            </div>
                            <div class="d-flex justify-content-between mt-auto mb-auto">
                                <div class="d-flex">
                                    <a class="btn btn-light m-2 ps-2 pe-2 cursor-pointer quantity-handler" data-productOrderId="{{$orderedProduct->id}}" data-operation="-">
                                        -
                                    </a>
                                    <a class="btn btn-light m-2 ps-2 pe-2 product-order-quantity" style="cursor:default">
                                        {{ $orderedProduct->quantity }}
                                    </a>
                                    <a class="btn btn-light m-2 ps-2 pe-2 cursor-pointer quantity-handler" data-productOrderId="{{$orderedProduct->id}}" data-operation="+">
                                        +
                                    </a>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="text-right">
                                        <small class="product-price">{{ \App\Helpers\Functions::formatMoney($orderedProduct->product->price); }} |un</small>
                                    </div>
                                    <div class="text-right product-total-sum">
                                        {{ \App\Helpers\Functions::formatMoney($orderedProduct->totalSum); }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-5">
            <div class="p-3 border shadow rounded">
                <div id="sub-total" class="d-flex justify-content-between p-1">
                    <label>
                        Subtotal
                    </label>
                    <label class="sub-total-price">
                        {{ \App\Helpers\Functions::formatMoney($order->getSubTotal()); }}
                    </label>
                </div>
                <p class="p-1">
                    Calcular frete
                </p>
                <div id="cep-calculator" class="d-flex">
                    <input id="cep-value" class="border w-100" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;" type="text" value="{{Auth::user()->cep}}">
                    <a class="p-2 btn btn-dark" style="border-top-left-radius: unset; border-bottom-left-radius: unset; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                        Icone
                    </a>
                </div>
                <div id="cep-options">
                    @foreach($shipmentTypes as $optionName => $optionValue)
                        <a class="mt-2 mb-2 cursor-pointer shipment-option btn btn-secondary" data-optionNumber="{{ $optionValue }}">
                            {{ $optionName }}
                        </a>
                    @endforeach
                </div>
                <div id="loader-of-shipment" class="text-center" style="display:none;">
                    <div class="spinner-border text-warning mt-5" role="status"></div>
                </div>
                <div id="result-of-shipment" class="border-b pb-3" style="display:none;">
                    <div id="total-price" class="text-right d-flex justify-content-between p-1">
                        <div>Valor do frete</div>
                        <div class="value"></div>
                    </div>
                    <div id="delivery-time" class="text-right d-flex justify-content-between p-1">
                        <div>Prazo de entrega</div>
                        <div class="value"></div>
                    </div>
                </div>
                <div id="wrapper-of-total-price" style="display:none" class="mt-4">
                    <div id="total-price-of-purchase" class="text-right d-flex justify-content-between p-1">
                        <div>Total</div>
                        <div class="value h5"></div>
                    </div>
                </div>
                <div class="mt-3 mb-2">
                    <a class="btn btn-success w-100" onclick="calculateShipmentPrice()">iconeSacola Finalizar Compra</a>
                </div>
            </div>
            <div class="m-2">
                <div id="descount-button">
                    <a>BTN adicionar cupom de desconto</a>
                </div>
                <div id="descount-input" class="d-flex">
                    <input class="border" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;" type="text" placeholder="insira o cupom">
                    <a class="p-2 btn btn-dark" style="border-top-left-radius: unset; border-bottom-left-radius: unset; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                        Adicionar
                    </a>
                </div>
            </div>
            <div class="m-2 mt-5 row">
                <a id="pay-order" class="p-2 btn btn-success" href="{{\App\Helpers\Functions::viewLink('order/pay/?orderId='.$order->id)}}">
                    {{ucfirst(translate('pay'))}}
                </a>
            </div>
        </div>
    </section>
    <div class="">
        <a href="{{\App\Helpers\Functions::viewLink('/')}}">
            Voltar
        </a>
    </div>
</section>

@include('main_footer')

<script>
    // allows only numbers and request CEP when reached max number (8)
    var formerCEP = '';
    $('#cep-value').on('keypress', function(){
        var enteredCep = $(this).val();
        formatedCep = onlyNumbers(enteredCep);

        if(formatedCep.length >= 8){
            calculateShipmentPrice();
        }else{
            formerCEP = formatedCep;
        }
        $(this).val(formerCEP);
    });

    function onlyNumbers(value){
        return value.replace(/\D/g, "");
    }

    $('#cep-calculator a').on('click', function(){
        if($('#cep-value').val().length == ''){
            $(this).parent().find('input').focus();
        }
        calculateShipmentPrice();
    });

    var typeOfShipmentSelected = null;
    $('.shipment-option').on('click',function(){
        $('.shipment-option').removeClass('selected-shipment-type');
        $('.shipment-option').addClass('btn-secondary');
        $('.shipment-option').removeClass('btn-primary');

        typeOfShipmentSelected = $(this).attr('data-optionNumber');
        $(this).addClass('btn-primary');
        $(this).removeClass('btn-secondary');
        $(this).addClass('selected-shipment-type');
        calculateShipmentPrice();
    });

    function calculateShipmentPrice(){
        var enteredCEP   = $('#cep-value').val();
        if(enteredCEP == ''){
            addMessageToToast("{{ ucfirst(translate('enter a cep')) }}");
            $(this).parent().find('input').focus();
            return;
        }
        if(enteredCEP.length < 8){
            addMessageToToast("{{ ucfirst(translate('invalid cep')) }}");
            return;
        }
        if(typeOfShipmentSelected == '' || typeOfShipmentSelected == null){
            addMessageToToast("{{ ucfirst(translate('choose a type of shipment')) }}");
            return;
        }
        $('#loader-of-shipment').show();
        $('#result-of-shipment').hide();
        $('#wrapper-of-total-price').hide();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('calculateordershipment') }}",
            method: 'POST',
            data: {orderId: $('#cart-list').attr('data-orderId'), shipmentType: typeOfShipmentSelected, deliveryCEP: enteredCEP},
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    var shipmentPrice = result.content.value;
                    $('#total-price').find('.value').text(shipmentPrice);
                    var deliveryTime  = result.content.deliveryTime;
                    var textToUse     = (deliveryTime == 1 ? deliveryTime + " {{ ucfirst(translate('day')) }}" : deliveryTime + " {{ ucfirst(translate('days')) }}");
                    $('#delivery-time').find('.value').text(textToUse);
                    $('#total-price-of-purchase').find('.value').text(result.total);

                    $('#result-of-shipment').show();
                    $('#wrapper-of-total-price').show();
                }else{
                    addMessageToToast(result.message);
                }
            },
            complete: function(){
                $('#loader-of-shipment').hide();
            }
        });
    }

    $('.quantity-handler').on('click', function(){
        var productOrderId = $(this).attr('data-productOrderId');
        var operation      = $(this).attr('data-operation');
        handleProductOrderQuantity(productOrderId, operation, $(this));
    });

    function handleProductOrderQuantity(productOrderId, operation, tagElement){
        if(operation != '+' && operation != '-'){
            addMessageToToast("{{ ucfirst(translate('unknown operation')) }}");
            return;
        }
        if(productOrderId == ''){
            addMessageToToast("{{ ucfirst(translate('no product selected')) }}");
            return;
        }
        openLoader();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('handleproductorderquantity') }}",
            method: 'POST',
            data: {productOrderId: productOrderId, operation: operation},
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    if(result.wasRemoved == true){
                        tagElement.parents('.aProduct').remove();
                    }
                    tagElement.parents('.aProduct').find('.product-order-quantity').text(result.data.quantity);
                    tagElement.parents('.aProduct').find('.product-price').text(result.data.productPrice + ' |un');
                    tagElement.parents('.aProduct').find('.product-total-sum').text(result.data.totalSum);
                    
                    $('.sub-total-price').text(result.data.subTotal);
                }else{
                    addMessageToToast(result.message);
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    }

    $('.remove-item-from-cart').on('click', function(){
        var productOrderId = $(this).attr('data-productOrderId');
        var tagElement = $(this).parents('.aProduct');
        openLoader();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('productorder/removeproductorder') }}",
            method: 'POST',
            data: {productOrderId: productOrderId},
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    tagElement.remove();
                    var hasProduct = result.hasProducts;
                    var totalSum   = result.totalSum;
                    if(hasProduct == true){
                        $('.sub-total-price').text(totalSum)
                    }else{
                        window.location.href = "{{\App\Helpers\Functions::viewLink('/')}}";
                    }
                }else{
                    addMessageToToast(result.message);
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    });

    $('#pay-order').on('click', function(){
        var href         = $(this).attr('href');
        var shipmentType = $('.selected-shipment-type').attr('data-optionnumber');
        shipmentType     = (shipmentType == undefined ? '' : shipmentType);
        var newHref      = href + '&shipmentType=' + shipmentType;
        $(this).attr('href', newHref);
    });
</script>