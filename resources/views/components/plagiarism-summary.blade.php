@props(['thesis', 'compact' => false])

@php
    $score = $thesis->plagiarism_score;
    $checkedAt = $thesis->plagiarism_checked_at;
    $report = $thesis->plagiarism_report ?? [];
    $sources = collect(data_get($report, 'sources', []));
@endphp

@if (!empty($report))
    <div class="space-y-1 text-sm">
        <div class="font-semibold text-gray-900">{{ $score ?? '—' }}%</div>
        @if ($checkedAt)
            <div class="text-xs text-gray-500">Checked {{ $checkedAt->diffForHumans() }}</div>
        @endif
        @if ($sources->isNotEmpty())
            <div class="text-xs text-gray-600">
                <p class="font-medium text-gray-700">Top sources:</p>
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
    <p class="text-sm text-gray-500">Plagiarism scan completed but no details were returned.</p>
@else
    <p class="text-sm text-gray-500">Plagiarism scan pending.</p>
@endif
