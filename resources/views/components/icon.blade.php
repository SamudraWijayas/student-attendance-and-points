@props(['name', 'class' => 'w-5 h-5'])

<i data-lucide="{{ $name }}" {{ $attributes->merge(['class' => $class . ' inline-block align-middle stroke-[1.8]']) }}></i>

