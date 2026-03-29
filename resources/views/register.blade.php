@extends('layouts.guest')

@section('title', 'Đăng ký — ' . config('app.name'))

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6">
        <a href="{{ url('/') }}"
            class="mb-8 flex items-center gap-2 text-slate-600 transition hover:text-violet-700">
            <span
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-600 text-lg font-bold text-white shadow-lg shadow-violet-500/25">{{ mb_strtoupper(mb_substr((string) config('app.name', 'A'), 0, 1)) }}</span>
            <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Auth') }}</span>
        </a>

        <div
            class="w-full max-w-[420px] rounded-2xl border border-white/60 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur-md sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Tạo tài khoản</h1>
                <p class="mt-2 text-sm text-slate-600">Chỉ mất vài giây để bắt đầu.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200/80 bg-red-50/90 px-4 py-3 text-sm text-red-800"
                    role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Họ và tên</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                        autofocus
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/15 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500/15 @enderror"
                        placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/15 @error('email') border-red-300 @enderror"
                        placeholder="you@example.com">
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Mật khẩu</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/15 @error('password') border-red-300 @enderror"
                        placeholder="Tối thiểu 6 ký tự">
                    <p class="mt-1.5 text-xs text-slate-500">Mật khẩu tối thiểu 6 ký tự.</p>
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-violet-500/30 transition hover:from-violet-500 hover:to-fuchsia-500 focus:outline-none focus:ring-4 focus:ring-violet-500/40 active:scale-[0.98]">
                    Đăng ký
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-600">
                Đã có tài khoản?
                <a href="{{ route('login') }}"
                    class="font-semibold text-violet-700 underline-offset-2 hover:underline">Đăng nhập</a>
            </p>
        </div>
    </div>
@endsection