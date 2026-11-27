<script>
    /**
     * The official Jaya Intero logo, cropped from the supplied file (never redrawn).
     * - "inline": the JL mark beside the name, for headers and small spaces.
     * - "lockup": the full mark + "JAYA INTERO" artwork, for blue areas with room.
     *
     * @type {{ variant?: 'inline' | 'lockup', tone?: 'dark' | 'light', subtitle?: string, class?: string }}
     */
    let { variant = 'inline', tone = 'dark', subtitle = 'Program Magang', class: className = '' } = $props();

    const titleColor = $derived(tone === 'light' ? 'text-white' : 'text-primary');
    const subtitleColor = $derived(tone === 'light' ? 'text-white/75' : 'text-muted');
</script>

{#if variant === 'lockup'}
    <img
        src="/images/logo-lockup.webp"
        alt="Jaya Intero"
        width="640"
        height="485"
        class="h-24 w-auto self-start rounded-2xl ring-1 ring-white/25 {className}"
    />
{:else}
    <span class="inline-flex items-center gap-3 {className}">
        <img src="/images/logo-mark.webp" alt="" width="192" height="192" class="size-10 shrink-0 rounded-lg" />
        <span class="inline-flex flex-col leading-none">
            <span class="text-base font-black tracking-tight {titleColor}">JAYA INTERO</span>
            {#if subtitle}
                <span class="label-tag mt-1 text-[10px] {subtitleColor}">{subtitle}</span>
            {/if}
        </span>
    </span>
{/if}
