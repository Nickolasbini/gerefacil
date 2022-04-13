@include('dashboard/master')
@include('dashboard/view_message')

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
                    <small class="prior-price">Valor anterior</small>
                    <p>{{\App\Helpers\Functions::formatMoney($product->price)}} (Promoção)</p>
                @else
                    <p>
                        {{\App\Helpers\Functions::formatMoney($product->price)}}
                    </p>
                @endif
            </div>
            <a class="btn btn-dark w-100 opacity-hover" href="{{\App\Helpers\Functions::viewLink('product/detail/'.$product->id)}}">Eu quero</a>
        </div>        
    </section>
</x-app-layout>