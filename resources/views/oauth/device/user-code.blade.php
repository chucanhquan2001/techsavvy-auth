@extends('layouts.guest')

@section('title', 'Mã thiết bị — ' . config('app.name'))

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6">
        <div
            class="w-full max-w-md rounded-2xl border border-white/60 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur-md sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-xl font-semibold tracking-tight text-slate-900">Nhập mã trên thiết bị</h1>
                <p class="mt-2 text-sm text-slate-600">Nhập mã hiển thị trên màn hình thiết bị hoặc ứng dụng.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200/80 bg-red-50/90 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('user_code') }}
                </div>
            @endif

            <form method="GET" action="{{ route('passport.device') }}" class="space-y-5">
                <div>
                    <label for="user_code" class="mb-1.5 block text-sm font-medium text-slate-700">Mã người dùng</label>
                    <input id="user_code" type="text" name="user_code" value="{{ old('user_code', request('user_code')) }}"
                        required autocomplete="off"
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 font-mono text-lg tracking-wider text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/15"
                        placeholder="XXXX-XXXX">
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-violet-500/30 transition hover:from-violet-500 hover:to-fuchsia-500">
                    Tiếp tục
                </button>
            </form>
        </div>
    </div>
@endsection
