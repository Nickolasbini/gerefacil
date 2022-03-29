<!DOCTYPE html>
<html lang="pt_BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Bootstrap -->
        <link href="{{ asset('externalfeatures/bootstrap.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="relative flex items-top justify-center bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <div class="container mt-5 mb-5">
            <div class="d-flex justify-content-center">
                <label for="search-products"><?= ucfirst(translate('search for products'))  ?></label>
                <input id='search-products' type="text" placeholder="...">
            </div>
        </div>

        <section id="products-list" class="container mt-5">
            <p class="h2 mb-5">
                <?= ucfirst(translate('products')) ?>:
            </p>
            @foreach($products as $product)
                <div class="col-sm-10 col-md-3 p-2 pt-4 pb-4 border-t border-b text-center">
                    <div class="ps-3 pe-3 mb-5">
                        <img class="img-fluid rounded" src="{{$product->getPhotoAsBase64()}}">
                    </div>
                    <p class="h5">
                        {{$product->name}}
                    </p>
                    <div class="short-description mt-3 mb-3">
                        <p>{{$product->description}}</p>
                        <a id="description-show-more" class="btn btn-primary opacity-hover">Ler mais</a>
                    </div>
                    <p class="complete-description hidden-description">
            
                    </p>
                    <div>
                        @if($product->promotion)
                            <small class="prior-price">Valor anterior</small>
                            <p>{{\App\Helpers\Functions::formatMoney($product->price)}} (Pormoção)</p>
                        @else
                            <p>
                                {{\App\Helpers\Functions::formatMoney($product->price)}}
                            </p>
                        @endif
                    </div>
                    <a class="btn btn-dark w-100 opacity-hover" href="{{\App\Helpers\Functions::viewLink('product/detail/'.$product->id)}}">Eu quero</a>
                </div>
            @endforeach
        </section>

    </body>
</html>

<script src="{{ asset('externalfeatures/jquery.js') }}"></script>
<script src="{{ asset('externalfeatures/bootstrap.js') }}"></script>