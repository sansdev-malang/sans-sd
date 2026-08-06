@extends('errors.layout')
@section('title', 'Sesi Telah Habis')
@section('code', '419')
@section('icon', 'clock')
@section('message', 'Sesi keamanan Anda telah berakhir karena halaman ini terlalu lama dibiarkan terbuka. Jangan khawatir, silakan muat ulang (refresh) halaman ini untuk melanjutkan.')
@section('action')
    <a href="{{ url()->previous() }}" class="inline-flex justify-center items-center gap-2 px-8 py-3.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all shadow-sm hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 dark:focus:ring-offset-slate-900">
        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
        Muat Ulang
    </a>
@endsection
