@props(['thesis', 'compact' => false])

@php
    $score = $thesis->plagiarism_score;
    $checkedAt = $thesis->plagiarism_checked_at;
    $report = $thesis->plagiarism_report ?? [];
    $sources = collect(data_get($report, 'sources', []));
@endphp

@php
    $containerClass = $compact ? 'space-y-1 text-xs text-gray-600' : 'space-y-1 text-sm';
    $scoreClass = $compact ? 'font-semibold text-sm' : 'font-semibold text-base';
    $summaryTextClass = $compact ? 'text-xs text-gray-500' : 'text-xs text-gray-500';
    $sourcesHeadingClass = $compact ? 'font-medium text-gray-700 text-xs' : 'font-medium text-gray-700';

    $scoreValue = is_numeric($score) ? (float) $score : null;
    $scoreColorClass = 'text-gray-900';
    $iconColorClass = $compact ? 'text-gray-400' : 'text-gray-400';

    if (!is_null($scoreValue)) {
        if ($scoreValue <= 9) {
            $scoreColorClass = 'text-green-600';
            $iconColorClass = 'text-green-500';
        } elseif ($scoreValue <= 50) {
            $scoreColorClass = 'text-orange-500';
            $iconColorClass = 'text-orange-500';
        } else {
            $scoreColorClass = 'text-red-500';
            $iconColorClass = 'text-red-500';
        }
    }
@endphp

@if (!empty($report))
    <div class="{{ $containerClass }}">
        <div class="flex items-center gap-2">
            <svg class="{{ $compact ? 'h-4 w-4' : 'h-5 w-5' }} {{ $iconColorClass }}"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 1.5 1.5 3-3" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 21.75c-4.97-1.35-7.5-4.05-7.5-8.156V5.663L12 2.25l7.5 3.413v7.931c0 4.106-2.53 6.806-7.5 8.156z" />
            </svg>
            <div class="{{ $scoreClass }} {{ $scoreColorClass }}">{{ $score ?? '—' }}%</div>
        </div>
        @if ($checkedAt)
            <div class="flex items-center gap-2 {{ $summaryTextClass }}">
                <svg class="{{ $compact ? 'h-4 w-4' : 'h-5 w-5' }} text-gray-400" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                </svg>
                <span>Checked {{ $checkedAt->diffForHumans() }}</span>
            </div>
        @endif
        @if ($sources->isNotEmpty())
            <div class="{{ $compact ? 'text-xs text-gray-600' : 'text-xs text-gray-600' }}">
                <p class="inline-flex items-center gap-2 {{ $sourcesHeadingClass }}">
                    <svg class="{{ $compact ? 'h-3.5 w-3.5' : 'h-4 w-4' }} text-gray-400"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    Top sources:
                </p>
                <ul class="list-disc ms-4 space-y-0.5">
                    @foreach ($sources as $source)
                        <li>
                            <span class="font-semibold">{{ data_get($source, 'score', '—') }}%</span>
                            <a href="{{ data_get($source, 'url') }}" class="text-indigo-600 hover:underline"
                                target="_blank" rel="noopener noreferrer">
                                {{ data_get($source, 'title') ?? 'Unlabeled source' }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@elseif ($checkedAt)
    <p class="inline-flex items-center gap-2 {{ $compact ? 'text-xs text-gray-500' : 'text-sm text-gray-500' }}">
        <svg class="{{ $compact ? 'h-4 w-4' : 'h-5 w-5' }} text-indigo-500" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.25 11.25h1.5v3m-.75-6a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
        Plagiarism scan completed but no details were returned.
    </p>
@else
    <p class="inline-flex items-center gap-2 {{ $compact ? 'text-xs text-gray-500' : 'text-sm text-gray-500' }}">
        <svg class="{{ $compact ? 'h-4 w-4' : 'h-5 w-5' }} text-gray-400" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
        Plagiarism scan pending.
    </p>
@endif
