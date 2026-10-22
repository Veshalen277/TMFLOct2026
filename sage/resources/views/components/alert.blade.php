@props([
  'type' => null,
  'message' => null,
])

@php($class = match ($type) {
  'success' => 'alert alert--success',
  'caution' => 'alert alert--caution',
  'warning' => 'alert alert--warning',
  default => 'alert',
})

<div {{ $attributes->merge(['class' => $class]) }}>
  {!! $message ?? $slot !!}
</div>
