<script>
    /** @type {{ label: string, title: string, intro?: string | null, children?: import('svelte').Snippet }} */
    let { label, title, intro = null, children } = $props();
</script>

<section class="bg-brand-gradient text-white">
    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 md:py-20">
        <p class="label-tag text-white/80">{label}</p>
        <h1 class="mt-3 max-w-3xl text-3xl leading-[1.1] font-black uppercase sm:text-5xl">{title}</h1>
        {#if intro}
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/90 sm:text-lg">{intro}</p>
        {/if}
        {#if children}{@render children()}{/if}
    </div>
</section>
