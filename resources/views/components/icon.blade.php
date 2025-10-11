@props([
    'name',
    'class' => 'h-4 w-4',
    'strokeWidth' => '1.5',
])

@php
    $svgAttributes = $attributes
        ->class($class)
        ->merge([
            'xmlns' => 'http://www.w3.org/2000/svg',
            'viewBox' => '0 0 24 24',
            'fill' => 'none',
            'stroke' => 'currentColor',
            'stroke-width' => $strokeWidth,
            'aria-hidden' => 'true',
        ]);
@endphp

@switch($name)
    @case('arrow-right')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    @break
    @case('user-group')
       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
</svg>

        @break
    @case('arrow-right-long')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H3" />
        </svg>
    @break

    @case('check')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    @break

    @case('circle-check')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z" />
        </svg>
    @break

    @case('eye')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.367 4.5 12 4.5c4.63 0 8.573 3.009 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.633 19.5 12 19.5c-4.63 0-8.573-3.009-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
    @break

    @case('download')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 12 16.5 16.5 12" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
        </svg>
    @break

    @case('clock')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
    @break

    @case('logout')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3H6.75A2.25 2.25 0 0 0 4.5 5.25v13.5A2.25 2.25 0 0 0 6.75 21h6.75a2.25 2.25 0 0 0 2.25-2.25V15" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9m0 0 3 3m-3-3 3-3" />
        </svg>
    @break

    @case('mail')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25 12 13.5 21 8.25" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4.5 6h15A1.5 1.5 0 0 1 21 7.5v9A1.5 1.5 0 0 1 19.5 18h-15A1.5 1.5 0 0 1 3 16.5v-9A1.5 1.5 0 0 1 4.5 6z" />
        </svg>
    @break

    @case('plus')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
        </svg>
    @break

    @case('refresh')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 1 12.75-5.25" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 4.5v4.5H12.75" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 12a7.5 7.5 0 0 1-12.75 5.25" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 19.5v-4.5H11.25" />
        </svg>
    @break

    @case('shield-check')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 1.5 1.5 3-3" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 21.75c-4.97-1.35-7.5-4.05-7.5-8.156V5.663L12 2.25l7.5 3.413v7.931c0 4.106-2.53 6.806-7.5 8.156z" />
        </svg>
    @break

    @case('search')
        <svg {{ $svgAttributes }}>
            <circle cx="11" cy="11" r="6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
        </svg>
    @break
    @case('book-open')
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
        </svg>

    @break
    @case('trash')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 9v6m6-6v6M4.5 7.5h15m-1.5 0V18A2.25 2.25 0 0 1 15.75 20.25H8.25A2.25 2.25 0 0 1 6 18V7.5m3-3h6a2.25 2.25 0 0 1 2.25 2.25V7.5h-10.5V6.75A2.25 2.25 0 0 1 9 4.5z" />
        </svg>
    @break

    @case('upload')
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
</svg>

    @break

    @case('users')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a8.25 8.25 0 1 1 15 0" />
        </svg>
    @break

    @case('document-text')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6m1.5 2.25H8.25A2.25 2.25 0 0 1 6 16.5V5.25A2.25 2.25 0 0 1 8.25 3h5.379a2.25 2.25 0 0 1 1.591.659l3.121 3.121a2.25 2.25 0 0 1 .659 1.591V16.5a2.25 2.25 0 0 1-2.25 2.25z" />
        </svg>
    @break

    @case('certificate')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m9 12.75 1.5 1.5 3-3M9 7.5h6M12 6V4.5m6 3.75v8.25A2.25 2.25 0 0 1 15.75 18H8.25A2.25 2.25 0 0 1 6 15.75V8.25A2.25 2.25 0 0 1 8.25 6H9" />
        </svg>
    @break

    @case('user-plus')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 21v-2.25A3.75 3.75 0 0 0 11.25 15h-3A3.75 3.75 0 0 0 4.5 18.75V21" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.75 11.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8.25v4.5m2.25-2.25h-4.5" />
        </svg>
    @break

    @case('x-mark')
        <svg {{ $svgAttributes }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    @break

    @default
        <svg {{ $svgAttributes }}></svg>
@endswitch
