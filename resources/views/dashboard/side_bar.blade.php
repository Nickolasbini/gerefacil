@if ($title == 'product')
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

    <script src="{{url('/externalfeatures/side_bar_functions.js')}}"></script>
@else

@endif