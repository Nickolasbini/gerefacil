@include('master_head')
@include('header-menu', ['enableSearch' => \App\Helpers\Functions::viewLink('/')])

@if($categories)
    <section id="categories-list" class="container-fluid p-1">
        <div class="row text-center justify-content-center mt-5 mb-5 pb-5 border-b">
            @foreach($categories as $id => $category)
                <?php $categoryChosen = ($id == $selectedCategory ? 'btn-dark' : 'btn-secondary') ?>
                <div class="col-sm-10 col-md-3 m-2 btn {{$categoryChosen}} row text-center justify-content-center category-button" data-categoryId="{{$id}}">
                    {{ucfirst($category)}}
                </div>    
            @endforeach
        </div>
    </section>
@endif

<section id="products-list" class="container mt-5 mb-5">
    @if($products->count() > 0)
        <div class="d-flex justify-content-between">
            <p class="h2 mb-5">
                <?= ucfirst(translate('products')) ?>:
            </p>
            <div class="col-sm-10 col-md-4">
                {{ Form::select('filteringOptions', $filteringOptions, $filter, ['id' => 'filteringOptions', 'class' => 'form-control'] ) }}
            </div>
        </div>
        <div class="ps-3 pe-3 row justify-content-around">
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
        </div>
    @else
        <p class="h2 mb-5">
            <?= ucfirst(translate('no product')) ?>
        </p>
    @endif
</section>

@include('main_footer')

<script>
    var searchedWord = "{{$search}}";
    var filter       = "{{$filter}}";
    $(document).ready(function(){
        if(searchedWord != '' && filter != 'category'){
            $('#menu-header-input').val(searchedWord);
        }
    });

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

    $('.category-button').on('click', function(){
        var href = "{{\App\Helpers\Functions::viewLink('/')}}";
        href += '?search=' + $(this).attr('data-categoryId') + '&filter=category';
        window.location.href = href;
    });

    $('#filteringOptions').on('change', function(){
        var href = "{{\App\Helpers\Functions::viewLink('/')}}";
        href += '?filter=' + $(this).val();
        window.location.href = href;
    });
</script>
<style>
    .hidden-description{
        display: none;
    }
</style>