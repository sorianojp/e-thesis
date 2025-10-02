@props(['thesis', 'compact' => false])

@php
    $score = $thesis->plagiarism_score;
    $checkedAt = $thesis->plagiarism_checked_at;
    $report = $thesis->plagiarism_report ?? [];
    $sources = collect(data_get($report, 'sources', []));
@endphp

@php
    $containerClass = $compact ? 'space-y-1 text-xs text-gray-600' : 'space-y-1 text-sm';
    $scoreClass = $compact ? 'font-semibold text-gray-900 text-sm' : 'font-semibold text-gray-900';
    $summaryTextClass = $compact ? 'text-xs text-gray-500' : 'text-xs text-gray-500';
    $sourcesHeadingClass = $compact ? 'font-medium text-gray-700 text-xs' : 'font-medium text-gray-700';
@endphp

@if (!empty($report))
    <div class="{{ $containerClass }}">
        <div class="{{ $scoreClass }}">{{ $score ?? '—' }}%</div>
        @if ($checkedAt)
            <div class="{{ $summaryTextClass }}">Checked {{ $checkedAt->diffForHumans() }}</div>
        @endif
        @if ($sources->isNotEmpty())
            <div class="{{ $compact ? 'text-xs text-gray-600' : 'text-xs text-gray-600' }}">
                <p class="{{ $sourcesHeadingClass }}">Top sources:</p>
                <ul class="list-disc ms-4 space-y-0.5">
                    @foreach ($sources as $source)
                        <li>
                            <span class="font-semibold">{{ data_get($source, 'score', '—') }}%</span>
                            <span>{{ data_get($source, 'title') ?? (data_get($source, 'url') ?? 'Unlabeled source') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@elseif ($checkedAt)
    <p class="{{ $compact ? 'text-xs text-gray-500' : 'text-sm text-gray-500' }}">Plagiarism scan completed but no details were returned.</p>
@else
    <p class="{{ $compact ? 'text-xs text-gray-500' : 'text-sm text-gray-500' }}">Plagiarism scan pending.</p>
@endif
