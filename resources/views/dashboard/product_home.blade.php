@include('dashboard/master')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('product')); ?>
        </h2>
    </x-slot>

    @include('dashboard/view_message')

    <div class="d-flex">
        @include('dashboard/side_bar', ['title' => 'product'])
        <div class="action-container w-100">
            @if(isset($content))
                {!! $content !!}
                {{ $page->links() }}
            @endif
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
</script>
