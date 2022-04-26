@include('dashboard/master')
@include('dashboard/view_message')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('my favorites')) ?>
        </h2>
    </x-slot>
    @if($favorites->count() > 0)
        <div class="d-flex justify-content-between container mt-5">
            <p class="h4 mb-5">
                <?= ucfirst(translate('favorites')) ?>:
            </p>
        </div>
        <div class="ps-3 pe-3 row justify-content-around container m-auto">
            @foreach($favorites->items() as $favorite)
                <div class="col-sm-10 col-md-3 m-1 p-3 pt-4 pb-4 border rounded text-center">
                    <div class="text-right">
                        <a class="btn btn-danger removeFavorite" data-favoriteId="{{$favorite->id}}">
                            X
                        </a>
                    </div>
                    <div class="ps-3 pe-3 mb-4 col-sm-10 col-md-8 m-auto">
                        <img class="img-fluid rounded" src="{{$favorite->productPhoto}}">
                    </div>
                    <p class="h5">
                        {{\App\Helpers\Functions::shortenText($favorite->productName, 25)}}
                    </p>
                    <a class="btn btn-dark w-100 opacity-hover" href="{{\App\Helpers\Functions::viewLink('product/detail/'.$favorite->productId)}}">{{ucfirst(translate('see product'))}}</a>
                </div>
            @endforeach
            <div class="mt-3">
                {{$favorites->links()}}
            </div>
        </div>
    @else
        <p class="h2 mb-5">
            <?= ucfirst(translate('no favorites')) ?>
        </p>
    @endif
</x-app-layout>

@include('main_footer')

<script>
    var translations = {
        showing: "<?= ucfirst(translate('showing')) ?>",
        of:      "<?= translate('of') ?>",
        to:      "<?= translate('to') ?>",
        results: "<?= translate('results') ?>"
    };

    $('.removeFavorite').on('click', function(){
        if("{{Auth::user()}}" == ''){
            return;
        }
        openLoader();
        var favoriteId = $(this).attr('data-favoriteId');
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/favorite/remove') }}",
            method: 'Post',
            data: {'favoriteId': favoriteId},
            dataType: 'JSON',
            success: function(result){
                if(result.success == true){
                    window.location.reload();
                }else{
                    alert(result.message);
                    return;
                }
            },
            complete: function(){
                openLoader(true);
            }
        });
    });
</script>
