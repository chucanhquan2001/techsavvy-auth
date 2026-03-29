@extends('layouts.guest')

@section('title', 'Ủy quyền ứng dụng — ' . config('app.name'))

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6">
        <div
            class="w-full max-w-lg rounded-2xl border border-white/60 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur-md sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-xl font-semibold tracking-tight text-slate-900">Ủy quyền truy cập</h1>
                <p class="mt-2 text-sm text-slate-600">
                    <span class="font-medium text-slate-800">{{ $client->name }}</span> muốn truy cập tài khoản của bạn.
                </p>
            </div>

            <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-700">
                <p class="font-medium text-slate-900">Đăng nhập với</p>
                <p class="mt-1">{{ $user->email }}</p>
                @if (! empty($client->redirect_uris[0] ?? null))
                    <p class="mt-3 text-xs text-slate-500">Sau khi ủy quyền, bạn sẽ được chuyển về ứng dụng (redirect URI đã đăng ký).</p>
                @endif
            </div>

            @if (count($scopes) > 0)
                <div class="mb-8">
                    <p class="mb-3 text-sm font-medium text-slate-800">Quyền được yêu cầu</p>
                    <ul class="space-y-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                        @foreach ($scopes as $scope)
                            <li class="flex gap-2">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-500"></span>
                                <span>
                                    <span class="font-mono text-xs text-violet-700">{{ $scope->id }}</span>
                                    @if ($scope->description)
                                        <span class="text-slate-600"> — {{ $scope->description }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <form method="post" action="{{ route('passport.authorizations.deny') }}" class="order-2 sm:order-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-400/25 sm:w-auto">
                        Từ chối
                    </button>
                </form>
                <form method="post" action="{{ route('passport.authorizations.approve') }}" class="order-1 sm:order-2">
                    @csrf
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-500/30 transition hover:from-violet-500 hover:to-fuchsia-500 focus:outline-none focus:ring-4 focus:ring-violet-500/40 sm:w-auto">
                        Cho phép
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
