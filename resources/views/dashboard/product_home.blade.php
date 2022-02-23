@include('dashboard/master')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('product')); ?>
        </h2>
    </x-slot>

    <div class="d-flex">
        <div class="side-bar h-100">
            <div class="mt-5 mb-5 d-flex justify-content-center button-wrapper">
                <a id="side-bar-button" href="{{\App\Helpers\Functions::viewLink('/dashboard/product')}}" title="back to product dashboard">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
            <div class="mt-5 mb-5 d-flex justify-content-around side-bar-option">
                <img class="small-icon" src="{{ asset('images/add-icon.webp') }}">
                <a href="{{\App\Helpers\Functions::viewLink('/dashboard/product/save')}}"><?= ucfirst(translate('create product')) ?></a>
            </div>
            <div class="mt-5 mb-5 d-flex justify-content-around side-bar-option">
                <img class="small-icon" src="{{ asset('images/list-icon.webp') }}">
                <a href="{{\App\Helpers\Functions::viewLink('/dashboard/product/list')}}"><?= ucfirst(translate('list products')) ?></a>
            </div>
        </div>
        <div class="action-container w-100">
            @if(isset($content))
                {!! $content !!}
                {{ $page->links() }}
            @endif
            
        </div>
    </div>

</x-app-layout>
<script>
    // do here like a mouse hover to open else, stays closed

    $('#side-bar-button').on('click', function(){
        $(this).parents('.side-bar').toggleClass('close');
        if($(this).parents('.side-bar').hasClass('close') == true){
            $('.side-bar-option').addClass('p-3');
            $('.side-bar-option').addClass('white-hover');
            $('.side-bar-option').addClass('borders-gray');
            $('.side-bar-option a').hide();
        }else{
            $('.side-bar-option').removeClass('p-3');
            $('.side-bar-option').removeClass('white-hover');
            $('.side-bar-option').removeClass('borders-gray');
            $('.side-bar-option a').show();
        }
    });
</script>

<style>
    .side-bar{
        border-right: 1px solid #bfb7b791;
        width: 15%;
    }
    .side-bar-option, .button-wrapper{
        cursor: pointer;
    }
    .borders-gray{
        border-top: 1px solid #ccc9c9;
        border-bottom: 1px solid #ccc9c9;
    }
    .white-hover:hover{
        background-color: #fff;
    }
    .close{
        width: 5%;
        z-index: 5;
    }

    @media (max-width: 768px) {
        .side-bar{
            width: 30%;
            text-align: center;
        }
        .side-bar img{
            display: none;
        }
        .side-bar-option{
            padding: unset!important;
            padding-top: 5%;
            padding-bottom: 5%;
        }
        .close{
            width: 10%;
        }
    }

    
</style>