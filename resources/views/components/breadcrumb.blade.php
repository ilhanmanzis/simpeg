@props(['items'])

<nav class="flex items-center text-sm mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        @foreach ($items as $label => $url)
            <li class="inline-flex items-center">
                @if ($loop->last)
                    <span class="text-gray-800 dark:text-gray-200 font-semibold">
                        {{ $label }}
                    </span>
                @else
                    <a href="{{ $url }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ $label }}
                    </a>
                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
