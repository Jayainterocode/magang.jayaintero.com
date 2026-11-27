<script>
    /**
     * The official JL logo (vector file supplied by Jaya Intero, never redrawn).
     * Its "L" is white, so on light backgrounds it sits on a blue tile.
     * - "inline": compact logo + name, for headers and the admin sidebar.
     * - "lockup": larger logo + name for blue areas with room (footer, login).
     *
     * @type {{ variant?: 'inline' | 'lockup', tone?: 'dark' | 'light', subtitle?: string, class?: string }}
     */
    let { variant = 'inline', tone = 'dark', subtitle = 'Program Magang', class: className = '' } = $props();

    const isLight = $derived(tone === 'light' || variant === 'lockup');
    const titleColor = $derived(isLight ? 'text-white' : 'text-primary');
    const subtitleColor = $derived(isLight ? 'text-white/75' : 'text-muted');
</script>

<span class="inline-flex items-center {variant === 'lockup' ? 'gap-4' : 'gap-3'} {className}">
    {#if variant === 'lockup'}
        <img src="/images/logo-jl.svg" alt="" width="424" height="368" class="h-14 w-auto shrink-0" />
    {:else}
        <span class="grid size-10 shrink-0 place-items-center rounded-lg {isLight ? 'bg-white/15' : 'bg-brand-gradient'}">
            <img src="/images/logo-jl.svg" alt="" width="424" height="368" class="w-6" />
        </span>
    {/if}
    <span class="inline-flex flex-col leading-none">
        <span class="font-black tracking-tight {variant === 'lockup' ? 'text-2xl' : 'text-base'} {titleColor}">JAYA INTERO</span>
        {#if subtitle}
            <span class="label-tag mt-1 {variant === 'lockup' ? 'text-xs' : 'text-[10px]'} {subtitleColor}">{subtitle}</span>
        {/if}
    </span>
</span>
