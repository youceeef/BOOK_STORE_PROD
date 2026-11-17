@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-200 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 rounded-xl shadow-sm text-sm py-2.5 px-3.5 bg-white text-gray-900 placeholder-gray-400']) !!}>
