<div x-data x-init="
        @if (session('success')) $store.toast.push(@js(session('success')), 'success'); @endif
        @if (session('error')) $store.toast.push(@js(session('error')), 'error'); @endif
        @foreach ($errors->all() as $e) $store.toast.push(@js($e), 'error', 6000); @endforeach
     "
     class="fixed bottom-5 inset-x-0 z-[70] flex flex-col items-center gap-2 pointer-events-none px-4">
    <template x-for="t in $store.toast.items" :key="t.id">
        <div x-transition.opacity.duration.200ms
             class="pointer-events-auto flex items-center gap-3 rounded-full ps-3 pe-2 py-2 shadow-xl text-sm max-w-md
                    bg-secondary-900 text-white border border-white/10">
            <span class="grid place-items-center w-6 h-6 rounded-full shrink-0"
                  :class="t.type === 'error' ? 'bg-danger' : 'bg-accent-500 text-secondary-900'">
                <svg x-show="t.type !== 'error'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                <svg x-show="t.type === 'error'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 7v6M12 17h.01"/></svg>
            </span>
            <span x-text="t.message" class="flex-1"></span>
            <button type="button" @click="$store.toast.dismiss(t.id)" class="grid place-items-center w-7 h-7 rounded-full text-white/50 hover:text-white hover:bg-white/10">
                <x-icon name="x" :size="14" />
            </button>
        </div>
    </template>
</div>
