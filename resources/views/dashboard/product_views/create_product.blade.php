@include('dashboard/master')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($product)
                <?= ucfirst(translate('edit product')); ?>
            @else
                <?= ucfirst(translate('create product')); ?>
            @endif
            
        </h2>
    </x-slot>

    <section id="productSave" class="container">
        {{ \Form::open(['route' => \App\Helpers\Functions::viewLink('/dashboard/product/save'), 'id' => 'productSave', 'method' => 'post'])}}
            <div class="p-5">
                <label><?= ucfirst(translate('product name')) ?></label>
                @if($product)
                    <input id="name" type="text" value="{{$product->name}}">
                @else
                    <input id="name" type="text">
                @endif
            </div>
            <div class="p-5">
                <label><?= ucfirst(translate('category')) ?></label>
                {{ Form::select('category', $category ) }}
            </div>
            <div class="p-5">
                <label><?= ucfirst(translate('price')) ?></label>
                @if($product)
                    <input id="price" type="text" value="{{$product->price}}">
                @else
                    <input id="price" type="text">
                @endif
            </div>
            <div class="p-5">
                <label><?= ucfirst(translate('quantity')) ?></label>
                @if($product)
                    <input id="quantity" type="text" value="{{$product->quantity}}">
                @else
                    <input id="quantity" type="text">
                @endif
            </div>
            <div class="p-5">
                <label><?= ucfirst(translate('product details')) ?></label>
                @if($product)
                    {{ Form::text('productDetails', "$product->productDetails") }}
                @else
                    {{ Form::text('productDetails', "") }}
                @endif
            </div>
        {{ Form::close() }}
    </section>

</x-app-layout>