<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
        <title>Painel - GereFacil</title>
        <link rel="icon" href="images/favicon.webp">

        <link rel="stylesheet" href="{{url('/css/app.css')}}">
        <link rel="stylesheet" href="{{url('/externalfeatures/bootstrap.css')}}">
    </head>
    <body>
        <x-app-layout>
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <?= ucfirst(translate('dashboard')) ?>
                </h2>
            </x-slot>
            <div class="container">
                <p class="h5 mt-5 mb-5">
                    <?= ucfirst(translate('my products')) ?>
                </p>
                <div class="ps-3 pe-3 row justify-content-around">
                    @if($products->count() > 0)
                        @foreach($products->items() as $product)
                            <div class="col-sm-10 col-md-3 m-1 p-3 pt-4 pb-4 border rounded text-center">
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
                        @endforeach
                        <div class="mt-3">
                            {{$products->links()}}
                        </div>
                    @else
                        <div class="d-flex flex-column justify-content-center">
                            <p class="h4 m-auto mb-3">
                                <?= ucfirst(ucfirst('no product registered yet!')) ?>
                            </p>
                            <div class="m-auto">
                                <a class="btn btn-dark opacity-hover" href="{{\App\Helpers\Functions::viewLink('dashboard/product/save', true)}}">
                                    <?= ucfirst(translate('register the first')) ?>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-app-layout>
    </body>
</html>
<script src="{{url('/externalfeatures/jquery.js')}}"></script>
<script src="{{url('/externalfeatures/bootstrap.js')}}"></script>
