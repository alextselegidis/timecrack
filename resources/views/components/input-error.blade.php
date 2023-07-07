@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'form-text text-danger']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
