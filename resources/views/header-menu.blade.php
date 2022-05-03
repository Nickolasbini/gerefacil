<?php $chosenLanguage = (session()->get('userLanguage') ? session()->get('userLanguage') : env('USER_LANGUAGE')) ?>
<div class="w-100 text-right">
  <select id="language-selector" class="position-top rounded mt-2 me-5 ms-auto">
      <?php $languages = ['en', 'pt'] ?>
      @foreach($languages as $language)
          <?php $selectedAttribute = ($language == $chosenLanguage ? 'selected=""' : '') ?>
          <option value="{{$language}}" {{$selectedAttribute}} class="banner-icon">{{strtoupper($language)}}</option>
      @endforeach
  </select>
</div>
{{ Form::open(['route' => 'change.language', 'method' => 'post'])}}
    {{ Form::hidden('seletedLanguage',  $chosenLanguage, ['id' => 'myLanguage'])}}
{{ Form::close() }}
<nav class="navbar navbar-expand-lg navbar-light bg-light pt-5 pb-4 ps-3 pe-3">
    <a class="navbar-brand" href="{{\App\Helpers\Functions::viewLink('/')}}">GereFacil</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto col-md-4">
            <li class="nav-item active">
                <a class="nav-link" href="#"><?= ucfirst(translate('products')) ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><?= ucfirst(translate('categories')) ?></a>
            </li>
            <li class="nav-item">
                <div id="cart_flag" style="display:none;"></div>
                <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('cart')}}"><?= ucfirst(translate('cart')) ?></a>
            </li>
          </ul>
          @if(isset($enableSearch))
            <div id="menu-header-searcher" action="{{$enableSearch}}" class="row col-md-6">
                <input id="menu-header-input" class="rounded  col-md-8 shadow" type="search" placeholder="{{ ucfirst(translate('search')) }}" aria-label="Search">
                <a class="btn btn-primary col-md-2" onclick="searchProducts()" style="margin-left: -5px;">{{ ucfirst(translate('search')) }}</a>
            </div>
          @endif
          <div class="col-md-2">
            <ul class="navbar-nav mr-auto d-flex justify-content-around">
              @if(Auth::user())
                  <li class="nav-item">
                      <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('dashboard/home'); }}"><?= ucfirst(translate('menu')) ?></a>
                  </li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                          <?= ucfirst(translate('log out')) ?>
                      </a>
                    </li>
                  </form>
              @else
                <li class="nav-item">
                    <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('login')}}"><?= ucfirst(translate('login')) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('register')}}"><?= ucfirst(translate('register')) ?></a>
                </li>
              @endif
          </ul>
      </div>
    </div>
</nav>
<script>
    $(document).ready(function(){
        getNumberOfProductsOnCurrentCart();
    });

    function searchProducts(){
        var href  = $('#menu-header-searcher').attr('action');
        var value = $('#menu-header-input').val();
        if(value == ''){
            window.location.href = href + '/';
        }else{
            window.location.href = href + '/?search=' + value;
        }
    }

    $('.navbar-toggler').on('click', function(){
        if($('#navbarSupportedContent').css('display') == 'block'){
            $('#navbarSupportedContent').hide();
        }else{
            $('#navbarSupportedContent').show();
            $('#menu-header-searcher').find('a').css('margin-left', 'unset');
        }
    });

    $('#language-selector').on('change', function(){
        $('#myLanguage').val($(this).val())
        $('#myLanguage').parent().submit();
    });

    function getNumberOfProductsOnCurrentCart(){
        if("{{Auth::user()}}" == ''){
            return;
        }
        $.ajax({
            url: "{{ \App\Helpers\Functions::viewLink('dashboard/order/getnumberofproducts') }}",
            type: 'POST',
            dataType: 'JSON',
            success: function(result){
                if(result.number > 0){
                    $('#cart_flag').text(result.number);
                    $('#cart_flag').show();
                }else{
                    $('#cart_flag').hide();
                }
            }
        });
    }
</script>

<style>
    #cart_flag{
        width: 10px;
        height: 10px;
        /*background-color: red;*/
        border-radius: 100%;
        position: absolute;
        margin-top: 5px;
    }
</style>