@include('dashboard/master')
@include('dashboard/view_message')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('my orders')); ?>
        </h2>
    </x-slot>
    <section id="purchases" class="container mt-t mb-t p-4">
        <select name="status" id="statusSelector" class="form-control mt-2 mb-2">
            @foreach($status as $statusId => $statusTranslation)
                {{ $selected = ($statusId == $selectedStatus) ? 'selected' : ''}}
                <option value="{{$statusId}}" {{$selected}}>{{$statusTranslation}}</option>
            @endforeach
        </select>
        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="order-wrapper p-3 mt-3 mb-3 rounded shadow">
                    <div class="order-data row">
                        <div class="col-md-6">
                            <img class="img-fluid rounded" src="{{$order->productDetails[0]['productPhoto']}}">
                        </div>
                        <div class="col-md-6 d-flex flex-column justify-content-around text-right">
                            <p class="h5 border-b">
                                Valor: {{\App\Helpers\Functions::formatMoney($order->productDetails[0]['parcialSum'])}} 
                            </p>
                            <p class="h5 border-b">
                                Valor frete: {{\App\Helpers\Functions::formatMoney($order->shippingPrice)}}
                            </p>
                            <p class="h5 border-b">
                                Quantidade: {{$order->productDetails[0]['quantity']}} <small>|un</small>
                            </p>
                        </div>
                    </div>
                    <div class="status mt-5 mb-5 w-100 btn btn-outline-{{$order->getStatusCorrespondentColor()}}" style="pointer-events: none !important;">
                        <p class="h6">
                            Status: {{$order->getStatusTranslated()}}
                        </p>
                    </div>
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
                                    Valor: {{\App\Helpers\Functions::formatMoney($productOrder['parcialSum'])}} 
                                </p>
                                <p class="h5 border-b">
                                    Valor frete: {{\App\Helpers\Functions::formatMoney($order->shippingPrice)}}
                                </p>
                                <p class="h5 border-b">
                                    Quantidade: {{$productOrder['quantity']}} <small>|un</small>
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
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

    var basePath = "{{\App\Helpers\Functions::viewLink('dashboard/order/mycart')}}";
    $('#statusSelector').on('change', function(){
        var value = $(this).val();
        basePath += '?status=' + value;
        window.location.href = basePath;
    });
</script>

<style>
    .hidden-full-description{
        display: none;
    }
</style>