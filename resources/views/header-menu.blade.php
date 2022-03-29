<nav class="navbar navbar-expand-lg navbar-light bg-light pt-5 pb-4 ps-3 pe-3">
    <a class="navbar-brand" href="#">GereFacil</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto col-md-6">
        <li class="nav-item active">
          <a class="nav-link" href="#"><?= ucfirst(translate('products')) ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><?= ucfirst(translate('categories')) ?></a>
        </li>
        @if(Auth::user())
            <li class="nav-item">
                <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('dashboard/home'); }}"><?= ucfirst(translate('menu')) ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href=""><?= ucfirst(translate('logout')) ?></a>
            </li>
        @else
          <li class="nav-item">
              <a class="nav-link" href="{{\App\Helpers\Functions::viewLink('register')}}"><?= ucfirst(translate('register')) ?></a>
          </li>
        @endif
      </ul>
      @if(isset($enableSearch))
        <div id="menu-header-searcher" action="{{$enableSearch}}" class="row col-md-6">
            <input id="menu-header-input" class="rounded  col-md-8 shadow" type="search" placeholder="Search" aria-label="Search">
            <a class="btn btn-primary col-md-2" onclick="searchProducts()" style="margin-left: -5px;">Search</a>
        </div>
      @endif
    </div>
</nav>
<script>
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
</script>