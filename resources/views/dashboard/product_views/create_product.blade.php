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

    @include('dashboard/view_message')

    <section id="productSave" class="d-flex h-100">
        <div class="side-bar h-100">
            @include('dashboard/side_bar', ['title' => 'product'])
        </div>
        <div class="action-container w-100">
            {{ \Form::open(['route' => \App\Helpers\Functions::viewLink('dashboard/product/save'), 'id' => 'productSave', 'method' => 'post'])}}
                <div class="p-5">
                    <label><?= ucfirst(translate('product name')) ?></label>
                    @if($product)
                        <input name="name" type="text" value="{{$product->name}}">
                    @else
                        <input name="name" type="text">
                    @endif
                </div>
                <div class="p-5">
                    <label><?= ucfirst(translate('category')) ?></label>
                    {{ Form::select('category', $category ) }}
                </div>
                <div class="p-5">
                    <label><?= ucfirst(translate('price')) ?></label>
                    @if($product)
                        <input name="price" type="text" value="{{$product->price}}">
                    @else
                        <input name="price" type="text">
                    @endif
                </div>
                <div class="p-5">
                    <label><?= ucfirst(translate('quantity')) ?></label>
                    @if($product)
                        <input name="quantity" type="text" value="{{$product->quantity}}">
                    @else
                        <input name="quantity" type="text">
                    @endif
                </div>
                <div class="p-5">
                    <label><?= ucfirst(translate('product details')) ?></label>
                    @if($product)
                        {{ Form::text('productDetails', "$product->productDetails", ['class' => 'w-50 h-25 text-area-form']) }}
                    @else
                        {{ Form::text('productDetails', "", ['class' => 'w-50 h-25 text-area-form']) }}
                    @endif
                </div>
                @if($product)
                    <div>
                        {{ Form::hidden('productId', $product->id) }}
                    </div>
                @endif
                <div>
                    {{ Form::submit(ucfirst(translate('save'))) }}
                </div>
            {{ Form::close() }}
        </div>
    </section>
</x-app-layout>

<style>
    .text-area-form{
        /* here make the text start at the top */
    }
</style>