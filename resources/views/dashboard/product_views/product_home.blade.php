@include('dashboard/master')
@include('dashboard/view_message')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('my products')) ?>
        </h2>
    </x-slot>
    <div class="row">
        <div class="left-side col-sm-12 col-md-2 border-r">
            <ul>
                <li class="d-flex p-2 mt-5 mb-5 white-hover rounded cursor-pointer">
                    <img class="small-icon me-2" src="{{ asset('images/add-icon.webp') }}">
                    <a class="opacity-hover btn" href="{{\App\Helpers\Functions::viewLink('/dashboard/product/save')}}"><?= ucfirst(translate('create product')) ?></a>
                </li>
                <li class="d-flex p-2 mt-5 mb-5 white-hover rounded cursor-pointer">
                    <img class="small-icon me-2" src="{{ asset('images/list-icon.webp') }}">
                    <a class="opacity-hover btn" href="{{\App\Helpers\Functions::viewLink('/dashboard/product', true)}}"><?= ucfirst(translate('list products')) ?></a>
                </li>
            </ul>
        </div>
        <div class="right-side col-sm-12 col-md-9 m-auto">
            <p class="h5 mt-5 mb-5">
                <?= ucfirst(translate('registred products')) ?>
            </p>
            <div class="ps-3 pe-3 row justify-content-around">
                @if($products->count() > 0)
                    @foreach($products->items() as $product)
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
    </div>
</x-app-layout>
<script>
    var currentPage = "{{ isset($pageNumber) ? $pageNumber : 1 }}";
    // setting url to edit buttons
    $('.delete-button').on('click', function(){
        var productId = $(this).parents('tr').attr('data-id');
        if(productId === undefined){
            alert('unexpected error');
            return;
        }
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/product/remove') }}",
            method: 'POST',
            dataType: 'JSON',
            data: {productId: productId},
            success: function(result){
                if(result.success == true){
                    // update list and set success message to session in order to display the message
                    window.location.reload("{{ \App\Helpers\Functions::viewLink('dashboard/product/remove') }}" + '/' + currentPage);
                }else{
                    // don't reload page and show message by js (do not reload page)
                }
            }
        });
    });

    var translations = {
        showing: "<?= ucfirst(translate('showing')) ?>",
        of:      "<?= translate('of') ?>",
        to:      "<?= translate('to') ?>",
        results: "<?= translate('results') ?>"
    };
    $('#table-filter').on('input', function(){
        var value = $(this).val();
        if(value == '')
            return;
        $('#table-sectors').hide();
        $('#content').find('nav').hide();
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/product/list') }}" + '?q=' + value,
            method: 'Get',
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    $('#pagination-master').html(result.content);
                    $('#pagination-master').show();
                }else{
                    $('#pagination-master').html('');
                    $('#pagination-master').hide();
                    $('#table-sectors').show();
                    $('#content').find('nav').show();
                }
            }
        });
    });

    function removeProduct(productId = null){
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
</script>
