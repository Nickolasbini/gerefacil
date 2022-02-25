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
            <div id="content">
                @if(isset($content))
                    {!! $content !!}
                    {{ $page->links() }}
                @endif
            </div>
            <div id="pagination-master"></div>
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
    console.log(translations);

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
</script>
