<script>
    import { Link } from '@inertiajs/svelte';

    let { post } = $props();
</script>

<article class="flex h-full flex-col overflow-hidden rounded-card border border-line bg-white">
    <Link href="/artikel/{post.slug}" tabindex="-1" aria-hidden="true">
        {#if post.cover_image_url}
            <img src={post.cover_image_url} alt="" class="aspect-[16/9] w-full object-cover" loading="lazy" />
        {:else}
            <div class="bg-brand-gradient aspect-[16/9] w-full"></div>
        {/if}
    </Link>
    <div class="flex flex-1 flex-col p-6">
        <p class="label-tag text-primary">{post.category} · <span class="text-muted">{post.published_at}</span></p>
        <h3 class="mt-2 text-lg leading-snug font-bold text-ink">
            <Link href="/artikel/{post.slug}" class="hover:text-primary">{post.title}</Link>
        </h3>
        <p class="mt-2 text-sm leading-relaxed text-muted">{post.excerpt}</p>
    </div>
</article>
