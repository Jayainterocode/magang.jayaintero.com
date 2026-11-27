<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4070b3">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-inertia::head>
            <title>{{ config('app.name') }}</title>
        </x-inertia::head>
    </head>
    <body class="bg-white font-sans text-ink antialiased">
        <x-inertia::app />
    </body>
</html>
