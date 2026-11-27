<script module>
    export { default as layout } from '../../Layouts/PublicLayout.svelte';
</script>

<script>
    import PageHero from '../../Components/Public/PageHero.svelte';

    let { items } = $props();
</script>

<svelte:head>
    <title>Galeri · Magang Jaya Intero</title>
    <meta name="description" content="Foto kegiatan peserta magang dan proyek Jaya Intero." />
</svelte:head>

<PageHero label="#Galeri" title="Galeri kegiatan" />

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 md:py-16">
    {#if items.length === 0}
        <p class="rounded-card border border-dashed border-line p-10 text-center text-muted">Belum ada foto.</p>
    {:else}
        <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {#each items as item (item.id)}
                <li>
                    <figure class="overflow-hidden rounded-card border-4 border-white bg-white shadow-sm ring-1 ring-line">
                        <img src={item.image_url} alt={item.caption} class="aspect-[4/3] w-full object-cover" loading="lazy" />
                        <figcaption class="px-4 py-3 text-sm font-semibold text-ink">{item.caption}</figcaption>
                    </figure>
                </li>
            {/each}
        </ul>
    {/if}
</section>
