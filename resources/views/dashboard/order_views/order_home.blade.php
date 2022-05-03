@include('dashboard/master')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('sale')); ?>
        </h2>
    </x-slot>
    @include('dashboard/view_message')

    <section id="sales" class="container mt-t mb-t">
        @if($orders->count() > 0)
            
        @else
            <p class="mt- mb-t">
                {{ucfirst(translate('no order'))}}
            </p>
        @endif
    </section>


</x-app-layout>