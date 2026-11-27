<script module>
    export { default as layout } from '../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHero from '../../Components/Public/PageHero.svelte';

    let { page } = $props();
</script>

<svelte:head>
    <title>{page.title} · Magang Jaya Intero</title>
    {#if page.intro}<meta name="description" content={page.intro} />{/if}
</svelte:head>

<PageHero label="#TentangMagang" title={page.title} intro={page.intro} />

<section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 md:py-16">
    <div class="rich-text">{@html page.body_html}</div>
    <Link href="/program" class="btn-primary mt-12">Lihat program magang</Link>
</section>
