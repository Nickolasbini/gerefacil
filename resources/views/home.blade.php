@include('master_head')
@include('header-menu', ['enableSearch' => \App\Helpers\Functions::viewLink('/')])

@if($categories)
    <section id="categories-list" class="container-fluid p-1">
        <div class="row text-center justify-content-center mt-5 mb-5 pb-5 border-b">
            @foreach($categories as $id => $category)
                <div class="col-sm-10 col-md-3 m-2 btn btn-secondary row text-center justify-content-center" data-categoryId="{{$id}}">
                    {{ucfirst($category)}}
                </div>    
            @endforeach
        </div>
    </section>
@endif

<section id="products-list" class="container mt-5 mb-5">
    @if($products->count() > 0)
        <p class="h2 mb-5">
            <?= ucfirst(translate('products')) ?>:
        </p>
        <div class="ps-3 pe-3 row justify-content-around">
            @foreach($products->items() as $product)
                <div class="col-sm-10 col-md-3 p-3 pt-4 pb-4 border rounded text-center">
                    <div class="ps-3 pe-3 mb-5 col-sm-10 col-md-10 m-auto">
                        <img class="img-fluid rounded" src="{{$product->getPhotoAsBase64()}}">
                    </div>
                    <p class="h5">
                        {{\App\Helpers\Functions::shortenText($product->name, 25)}}
                    </p>
                    <div class="short-description mt-3 mb-3">
                        <p>{{\App\Helpers\Functions::shortenText($product->productDetails, 25)}}</p>
                        @if(strlen($product->productDetails) > 25)
                            <a id="description-show-more" class="btn btn-primary opacity-hover">Ver detalhes</a>
                        @endif
                    </div>
                    <p class="complete-description hidden-description">
                        {{$product->productDetails}}
                    </p>
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
        </div>
    @else
        <p class="h2 mb-5">
            <?= ucfirst(translate('no product')) ?>
        </p>
    @endif
</section>

<script>
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
</script>
<style>
    .hidden-description{
        display: none;
    }
</style>