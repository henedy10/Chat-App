@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/10 bg-[#0f172a] text-slate-200 focus:border-[#6366f1] focus:ring-[#6366f1] rounded-md shadow-sm placeholder-slate-400']) }}>
