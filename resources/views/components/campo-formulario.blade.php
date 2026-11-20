{{--
    Campo de formulario con el estilo de Post Venta / Contacto (Figma: label 16 + input 40px con borde #DEDFE0).
    $nombre, $label, $placeholder, $tipo (text|email|tel|date|textarea|select|file), $opciones (select), $requerido, $filas, $clase
--}}
@php
    $tipo ??= 'text';
    $id = 'campo-' . str_replace(['[', ']'], ['-', ''], $nombre);
    $base = 'mt-[8px] w-full rounded-[4px] border bg-white px-[15px] text-[14px] placeholder:text-black/50 focus:border-marca focus:outline-none ' . ($errors->has($nombre) ? 'border-marca' : 'border-linea');
@endphp
<div class="{{ $clase ?? '' }}">
    <label for="{{ $id }}" class="text-[16px] leading-[1.5]">{{ $label }}</label>

    @if ($tipo === 'textarea')
        <textarea id="{{ $id }}" name="{{ $nombre }}" rows="{{ $filas ?? 6 }}" placeholder="{{ $placeholder ?? '' }}" @if (!empty($requerido)) required @endif
                  class="{{ $base }} py-[12px]">{{ old($nombre) }}</textarea>
    @elseif ($tipo === 'select')
        <div class="relative">
            <select id="{{ $id }}" name="{{ $nombre }}" @if (!empty($requerido)) required @endif
                    class="{{ $base }} h-[40px] appearance-none pr-[40px] {{ old($nombre) ? '' : 'text-black/50' }}" onchange="this.classList.toggle('text-black/50', !this.value)">
                <option value="">{{ $placeholder ?? 'Seleccionar' }}</option>
                @foreach ($opciones as $opcion)
                    <option value="{{ $opcion }}" class="text-black" @selected(old($nombre) === $opcion)>{{ $opcion }}</option>
                @endforeach
            </select>
            <img src="{{ asset('assets/img/dixter/icon-chevron-select.svg') }}" alt="" width="24" height="24" class="pointer-events-none absolute right-[12px] top-[16px]">
        </div>
    @elseif ($tipo === 'file')
        <label for="{{ $id }}" class="{{ $base }} flex h-[40px] cursor-pointer items-center justify-between gap-2">
            <span data-nombre-archivo class="truncate text-black/50">{{ $placeholder ?? 'Seleccionar archivo' }}</span>
            <img src="{{ asset('assets/img/dixter/icon-upload.svg') }}" alt="" width="24" height="24" class="shrink-0">
        </label>
        <input id="{{ $id }}" name="{{ $nombre }}" type="file" class="sr-only" accept="{{ $accept ?? '' }}"
               onchange="const s=this.previousElementSibling.querySelector('[data-nombre-archivo]'); s.textContent=this.files[0]?.name || '{{ $placeholder ?? 'Seleccionar archivo' }}'; s.classList.toggle('text-black/50', !this.files[0]);">
    @else
        <input id="{{ $id }}" name="{{ $nombre }}" type="{{ $tipo }}" value="{{ old($nombre) }}" placeholder="{{ $placeholder ?? '' }}"
               @if (!empty($requerido)) required @endif @if ($tipo === 'date') max="{{ now()->format('Y-m-d') }}" @endif
               class="{{ $base }} h-[40px]">
    @endif

    @error($nombre)<p class="mt-[4px] text-[13px] text-marca">{{ $message }}</p>@enderror
</div>
