@include('dashboard/master')
@include('dashboard/view_message')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('my sales')); ?>
        </h2>
    </x-slot>
    <section id="purchases" class="col-sm-10 col-md-6 m-auto row p-2">
        <div class="mt-5 mb-5 bg-primary-color shadow p-3 rounded">
            <p class="h4">
                {{ ucfirst(translate('date period')) }}
            </p>
            <div class="d-flex justify-content-between mt-3 mb-3">
                <div class="col-4">
                    <input id="from" type="date" class="form-control rounded" value="{{$from}}">
                </div>
                <div class="col-4">
                    <input id="to" type="date" class="form-control rounded" value="{{$to}}">
                </div>
            </div>
            <p class="h4">
                {{ ucfirst(translate('status')) }}
            </p>
            <select name="status" id="statusSelector" class="form-control mt-2 mb-2">
                @foreach($status as $statusId => $statusTranslation)
                    {{ $selected = ($statusId == $selectedStatus) ? 'selected' : ''}}
                    <option value="{{$statusId}}" {{$selected}}>{{$statusTranslation}}</option>
                @endforeach
            </select>
            <div class="d-flex justify-content-center p-2">
                <a id="filter" class="btn btn-secondary opacity-hover p-3 rounded secundary-color rounded">
                    {{ ucfirst(translate('filter')) }}
                </a>
            </div>
        </div>
        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="order-wrapper p-3 mt-3 mb-3 rounded shadow">
                    <div class="order-data row">
                        <?php $hasProducts = (count($order->productDetails) > 0 ? true : false) ?>
                        @if($hasProducts)
                            <?php $nextStatus = $order->getNextStatusTranslated() ?>
                            @if($nextStatus)
                                @if($order->getNextStatusNumber() < 5)
                                    <div class="mt-2 mb-5 d-flex justify-content-between border-b pb-3 wrapper-of-next-status">
                                        <div class="form-check">
                                            <input class="form-check-input nextStatus" type="checkbox" value="{{$order->getNextStatusNumber()}}" id="label-ofNewStatus-{{$order->id}}">
                                            <label class="form-check-label" for="label-ofNewStatus-{{$order->id}}">
                                                {{$nextStatus}}
                                            </label>
                                        </div>
                                        <div class="button-of-next-status" style="display: none;">
                                            <a class="save-new-status btn btn-success" data-orderId="{{$order->id}}" data-statusNumber="{{$order->getNextStatusNumber()}}">{{ucfirst(translate('save new status'))}}</a>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <div class="w-100 text-right h6 mb-4">{{\App\Helpers\Functions::formatDate($order->productDetails[0]['updated_at'])}}</div>
                            <div class="col-md-6">
                                <img class="img-fluid rounded" src="{{$order->productDetails[0]['productPhoto']}}">
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-around text-right">
                                <p class="h5 border-b">
                                    {{ucfirst(translate('price'))}}: {{\App\Helpers\Functions::formatMoney($order->productDetails[0]['parcialSum'])}} 
                                </p>
                                <p class="h5 border-b">
                                    {{ucfirst(translate('shipment price'))}}: {{\App\Helpers\Functions::formatMoney($order->shippingPrice)}}
                                </p>
                                <p class="h5 border-b">
                                    {{ucfirst(translate('quantity'))}}: {{$order->productDetails[0]['quantity']}} <small>|un</small>
                                </p>
                            </div>
                        @else
                            <p class="h5">
                                {{ucfirst(translate('no products!'))}}
                            </p>
                        @endif
                    </div>
                    <div class="status mt-5 mb-5 w-100 btn btn-outline-{{$order->getStatusCorrespondentColor()}}" style="pointer-events: none !important;">
                        <p class="h6">
                            {{ucfirst(translate('status'))}}: {{$order->getStatusTranslated()}}
                        </p>
                    </div>
                    @if($hasProducts)
                        <div class="w-100 text-center mt-3 mb-3 show-full-details">
                            <a class="btn btn-light opacity-hover p-3 rounded secundary-color" title="{{ucfirst(translate('see details'))}}">
                                {{ucfirst(translate('show'))}}
                            </a>
                        </div>
                        <div class="full-products mt-5 pt-3 hidden-full-description">
                            @foreach($order->productDetails as $productOrder)
                            <div class="order-data cursor-pointer row mt-5 mb-5" title="{{ucfirst(translate('see details'))}}">
                                <div class="col-md-2">
                                    <img class="img-fluid rounded" src="{{$productOrder['productPhoto']}}">
                                </div>
                                <div class="col-md-10 d-flex flex-column justify-content-around text-right">
                                    <p class="h5 border-b">
                                        {{ucfirst(translate('price'))}}: {{\App\Helpers\Functions::formatMoney($productOrder['parcialSum'])}} 
                                    </p>
                                    <p class="h5 border-b">
                                        {{ucfirst(translate('shipment price'))}}: {{\App\Helpers\Functions::formatMoney($order->shippingPrice)}}
                                    </p>
                                    <p class="h5 border-b">
                                        {{ucfirst(translate('quantity'))}}: {{$productOrder['quantity']}} <small>|un</small>
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            <div id="pagination" class="w-100 text-center">
                {{$orders->links()}}
            </div>
        @else
            <p class="mt- mb-t">
                {{ucfirst(translate('no order'))}}
            </p>
        @endif
    </section>
</x-app-layout>

<script>
    $('.show-full-details').on('click', function(){
        $(this).parents('.order-wrapper').find('.full-products').toggleClass('hidden-full-description');
    });

    var basePath = "{{\App\Helpers\Functions::viewLink('dashboard/sale/list')}}";
    var urlParameters = {
        status: "{{$selectedStatus}}",
        from  : "{{$from}}",
        to    : "{{$to}}"   
    }
    $('#statusSelector').on('change', function(){
        urlParameters['status'] = $(this).val();
    });
    $('#from').on('change', function(){
        $('#to').attr('min', $(this).val());
    });
    $('#to').on('change', function(){
        $('#from').attr('max', $(this).val());
    });

    $('#filter').on('click', function(){
        var fromDate = $('#from').val();
        var toDate   = $('#to').val();
        var status   = $('#statusSelector').val();
        urlParameters['from']   = fromDate;
        urlParameters['to']     = toDate;
        urlParameters['status'] = status;
        sendParametersAndReload();
    });

    function sendParametersAndReload(){
        var url = basePath + '/' + urlParameters['status'] + '/' + urlParameters['from'] + '/' + urlParameters['to'];
        window.location.href = url;
    }

    $('.nextStatus').on('click', function(){
        var parentTag = $(this).parents('.wrapper-of-next-status');
        if($(this).is(':checked') == true){
            parentTag.find('.button-of-next-status').show();
        }else{
            parentTag.find('.button-of-next-status').hide();
        }
    });

    $('.save-new-status').on('click', function(){
        var orderId      = $(this).attr('data-orderId');
        var statusNumber = $(this).attr('data-statusNumber');
        if(saveStatus(orderId, statusNumber) == true){
            $(this).parents('.order-wrapper').remove();
        }
        return;
    });

    function saveStatus(orderId, status){
        openLoader();
        var valueToReturn = false;
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/order/updatestatus') }}",
            method: 'POST',
            data: {orderId: orderId, status: status},
            dataType: 'JSON',
            success: function(result){
                addCustomMessage(result.message);
                valueToReturn = result.success;
            },
            complete: function(){
                openLoader(true);
            }
        });
        return valueToReturn;
    }
</script>

<style>
    .hidden-full-description{
        display: none;
    }
</style>