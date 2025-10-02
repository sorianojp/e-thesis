<div class="border border-gray-200 rounded-lg p-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="font-semibold text-gray-900">{{ $chapterLabel }}</h4>
            @php($status = $chapter->status ?? 'not submitted')
            <p class="text-xs text-gray-500 mt-1">
                Status:
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium capitalize {{ $status === 'pending'
                        ? 'bg-yellow-100 text-yellow-800'
                        : ($status === 'approved'
                            ? 'bg-green-100 text-green-800'
                            : ($status === 'rejected'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-gray-100 text-gray-700')) }}">
                    {{ $status }}
                </span>
            </p>
            @if ($chapter && $chapter->updated_at)
                <p class="text-xs text-gray-500 mt-1">Last updated: {{ $chapter->updated_at->format('F d, Y h:i A') }}</p>
            @endif
            @if ($chapter)
                <div class="mt-3 text-sm text-gray-600">
                    <p class="font-semibold text-gray-900">Plagiarism Scan</p>
                    <div class="mt-1">
                        <x-plagiarism-summary :thesis="$chapter" :compact="true" />
                    </div>
                </div>
            @endif
        </div>
        <div class="mt-3 md:mt-0 space-x-3">
            @if ($chapter && $chapter->thesis_pdf_path)
                <a class="text-indigo-600 hover:underline text-sm" href="{{ route('theses.download', [$chapter, 'thesis']) }}">Download manuscript</a>
            @endif
        </div>
    </div>

    @if (!$chapter)
        <p class="mt-3 text-sm text-gray-500">Waiting for student upload.</p>
    @else
        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('adviser.theses.approve', $chapter) }}">
                @csrf
                <x-primary-button type="submit">Approve</x-primary-button>
            </form>
            <form method="POST" action="{{ route('adviser.theses.reject', $chapter) }}">
                @csrf
                <x-danger-button type="submit">Reject</x-danger-button>
            </form>
        </div>
    @endif
</div>
