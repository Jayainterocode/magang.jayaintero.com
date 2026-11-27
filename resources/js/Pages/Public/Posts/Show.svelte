<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';

    let { post } = $props();
</script>

<svelte:head>
    <title>{post.title} · Magang Jaya Intero</title>
    <meta name="description" content={post.excerpt} />
    <meta property="og:title" content={post.title} />
    <meta property="og:description" content={post.excerpt} />
    {#if post.cover_image_url}<meta property="og:image" content={post.cover_image_url} />{/if}
</svelte:head>

<article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 md:py-16">
    <Link href="/artikel" class="text-sm font-semibold text-primary hover:underline">← Semua artikel</Link>
    <p class="label-tag mt-8 text-primary">{post.category} · <span class="text-muted">{post.published_at}</span></p>
    <h1 class="mt-3 text-3xl leading-tight font-black text-primary sm:text-4xl">{post.title}</h1>
    <p class="mt-4 text-lg leading-relaxed text-muted">{post.excerpt}</p>

    {#if post.cover_image_url}
        <img src={post.cover_image_url} alt="" class="mt-8 aspect-[16/9] w-full rounded-card object-cover" />
    {/if}

    <div class="rich-text mt-10">{@html post.body_html}</div>
</article>
