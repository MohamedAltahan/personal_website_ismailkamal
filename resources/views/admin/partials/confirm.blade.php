<div x-data x-cloak x-show="$store.confirm.isOpen" x-transition.opacity.duration.150ms
     class="fixed inset-0 z-[80] grid place-items-center p-4 bg-secondary-900/50 backdrop-blur-[3px]"
     @keydown.escape.window="$store.confirm.isOpen && $store.confirm.answer(false)">
    <div @click.outside="$store.confirm.answer(false)" x-trap.noscroll="$store.confirm.isOpen"
         class="w-full max-w-[440px] rounded-2xl bg-raised border border-line shadow-2xl p-6 sm:p-7 text-center">
        <div class="grid place-items-center w-16 h-16 mx-auto rounded-full mb-5"
             :class="$store.confirm.danger ? 'bg-danger-soft text-danger' : 'bg-brand-soft text-primary-700 dark:text-accent-500'">
            <x-icon name="alert" :size="28" />
        </div>
        <h3 class="text-lg font-bold mb-2" x-text="$store.confirm.title || @js(__('Are you sure?'))"></h3>
        <p class="text-sm text-muted mb-7" x-text="$store.confirm.message || @js(__('This action cannot be undone.'))"></p>
        <div class="flex items-center gap-3">
            <button type="button" @click="$store.confirm.answer(true)" class="flex-1 h-11"
                    :class="$store.confirm.danger ? 'btn-danger' : 'btn-primary'"
                    x-text="$store.confirm.confirmLabel || @js(__('Confirm'))"></button>
            <button type="button" @click="$store.confirm.answer(false)" class="btn-ghost flex-1 h-11">{{ __('Cancel') }}</button>
        </div>
    </div>
</div>
