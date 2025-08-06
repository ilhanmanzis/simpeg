@php
    $page = 'Dashboard';
    $selected = 'Dashboard';
    $title = 'Dashboard';
@endphp

<x-layout>
    <x-slot:page>{{ $page }}</x-slot:page>
    <x-slot:selected>{{ $selected }}</x-slot:selected>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-4 mx-auto max-w-[var(--breakpoint-2xl)] md:p-6">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 space-y-6 xl:col-span-7">
                @include('partials.metric-group.metric-group-01')
                @include('partials.chart.chart-01')
            </div>
            <div class="col-span-12 xl:col-span-5">
                @include('partials.chart.chart-02')
            </div>
            <div class="col-span-12">
                @include('partials.chart.chart-03')
            </div>
            <div class="col-span-12 xl:col-span-5">
                @include('partials.map-01')
            </div>
            <div class="col-span-12 xl:col-span-7">
                @include('partials.table.table-01')
            </div>
        </div>
    </div>

</x-layout>
