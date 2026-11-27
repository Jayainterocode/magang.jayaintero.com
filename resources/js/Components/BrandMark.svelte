<script>
    /**
     * Text wordmark used until the official JL logo file is supplied.
     * The real logo must replace this, never be redrawn.
     */
    let { tone = 'dark', subtitle = 'Program Magang' } = $props();

    const titleColor = $derived(tone === 'light' ? 'text-white' : 'text-primary');
    const subtitleColor = $derived(tone === 'light' ? 'text-white/75' : 'text-muted');
</script>

<span class="inline-flex flex-col leading-none">
    <span class="text-lg font-black tracking-tight {titleColor}">
        JAYA <span class={tone === 'light' ? 'text-accent' : 'text-primary-dark'}>INTERO</span>
    </span>
    {#if subtitle}
        <span class="label-tag mt-1 text-[10px] {subtitleColor}">{subtitle}</span>
    {/if}
</span>
