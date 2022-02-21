
@include('dashboard/master')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?= ucfirst(translate('sale')); ?>
        </h2>
    </x-slot>

</x-app-layout>