@php
    $status = session('status');
    $success = session('success');
    $error = session('error');
@endphp

@if ($status || $success || $error || $errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="space-y-3">
            @if ($status)
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-900">
                    {{ $status }}
                </div>
            @endif

            @if ($success)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
                    {{ $success }}
                </div>
            @endif

            @if ($error)
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-900">
                    {{ $error }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-900">
                    <div class="font-semibold mb-2">Verifică următoarele:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
