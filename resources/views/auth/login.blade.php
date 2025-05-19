@extends('layouts.app')

@section('content')
<div class="flex justify-center">
    <div class="w-full sm:max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-center text-2xl font-bold mb-6 text-gray-800">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input id="email" type="email" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <input id="password" type="password" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">
            </div>

            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="mr-2">
                    <label for="remember" class="text-sm text-gray-700">Lembrar-me</label>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Entrar
                </button>
                <a href="{{ route('register') }}" class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                    Criar conta
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
