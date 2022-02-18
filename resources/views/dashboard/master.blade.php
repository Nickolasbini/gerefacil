<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
        <title>Painel - GereFacil</title>
        <link rel="icon" href="images/favicon.webp">

        <link rel="stylesheet" href="{{url('/css/app.css')}}">
        <link rel="stylesheet" href="{{url('/externalfeatures/bootstrap.css')}}">
    </head>
    <body>
        <x-app-layout>
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ ucfirst(translate($title)) }}
                </h2>
            </x-slot>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <x-jet-welcome />
                    </div>
                </div>
            </div>
        </x-app-layout>
    </body>
</html>
<script src="{{url('/externalfeatures/jquery.js')}}"></script>
<script src="{{url('/externalfeatures/bootstrap.js')}}"></script>