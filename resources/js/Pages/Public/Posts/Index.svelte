<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import PageHero from '../../../Components/Public/PageHero.svelte';
    import PostCard from '../../../Components/Public/PostCard.svelte';
    import Pagination from '../../../Components/Pagination.svelte';

    let { posts } = $props();
</script>

<svelte:head>
    <title>Artikel · Magang Jaya Intero</title>
    <meta name="description" content="Kabar, pengumuman, dan cerita kegiatan magang di Jaya Intero." />
</svelte:head>

<PageHero label="#Artikel" title="Kabar magang" intro="Pengumuman, cerita kegiatan, dan tips untuk calon peserta magang." />

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 md:py-16">
    {#if posts.data.length === 0}
        <p class="rounded-card border border-dashed border-line p-10 text-center text-muted">Belum ada artikel.</p>
    {:else}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {#each posts.data as post (post.slug)}
                <PostCard {post} />
            {/each}
        </div>
        <Pagination paginator={posts} />
    {/if}
</section>
