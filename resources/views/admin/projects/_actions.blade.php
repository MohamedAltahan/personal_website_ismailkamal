{{-- Secondary project actions: view, featured, visibility and a "more" menu (native <details>, works without JS). --}}
@if ($project->slug)
    <a href="{{ $project->url() }}" target="_blank" class="btn-ghost h-9 w-9 px-0 shrink-0" title="{{ __('View on site') }}"><x-icon name="external" :size="15" /></a>
@endif

<button type="button" x-data="ajaxToggle(@js(route('admin.projects.toggle', [$project, 'is_featured'])), @js((bool) $project->is_featured))"
        @click="toggle()" :disabled="busy" class="btn-ghost h-9 w-9 px-0 shrink-0"
        :class="value && '!bg-accent-500 !border-accent-500 !text-secondary-900'" title="{{ __('Featured') }}">
    <x-icon name="star" :size="15" />
</button>

<button type="button" x-data="ajaxToggle(@js(route('admin.projects.toggle', [$project, 'status'])), @js($project->status === 'active'))"
        @click="toggle()" :disabled="busy" class="btn-ghost h-9 w-9 px-0 shrink-0"
        :title="value ? @js(__('Published — click to hide')) : @js(__('Hidden — click to publish'))">
    <span x-show="value"><x-icon name="eye" :size="15" /></span>
    <span x-show="!value" x-cloak class="text-danger"><x-icon name="eye-off" :size="15" /></span>
</button>

<details class="relative shrink-0" x-data @click.outside="$el.removeAttribute('open')">
    <summary class="btn-ghost h-9 w-9 px-0 list-none [&::-webkit-details-marker]:hidden" title="{{ __('More') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
    </summary>
    <div class="absolute z-30 end-0 bottom-full mb-2 w-52 rounded-xl bg-raised border border-line shadow-xl overflow-hidden text-sm">
        <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center gap-2.5 px-3.5 h-10 hover:bg-canvas"><x-icon name="edit" :size="16" class="text-muted" /> {{ __('Edit') }}</a>
        @if ($project->slug)
            <a href="{{ $project->url() }}" target="_blank" class="flex items-center gap-2.5 px-3.5 h-10 hover:bg-canvas"><x-icon name="external" :size="16" class="text-muted" /> {{ __('View on site') }}</a>
        @endif
        <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}">
            @csrf
            <button class="w-full flex items-center gap-2.5 px-3.5 h-10 hover:bg-canvas"><x-icon name="copy" :size="16" class="text-muted" /> {{ __('Duplicate') }}</button>
        </form>
        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="border-t border-line"
              @submit.prevent="deleteResource($el.action, { title: @js(__('Delete this project?')), message: @js(__('The project page will be removed. Its images and videos stay in the media library.')), el: document.getElementById('project-{{ $project->id }}') })"
              onsubmit="return window.Alpine ? false : confirm(@js(__('Delete this project?')))">
            @csrf
            @method('DELETE')
            <button class="w-full flex items-center gap-2.5 px-3.5 h-10 text-danger hover:bg-danger-soft"><x-icon name="trash" :size="16" /> {{ __('Delete') }}</button>
        </form>
    </div>
</details>
