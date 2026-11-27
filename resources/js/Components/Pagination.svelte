<script>
    import { Link } from '@inertiajs/svelte';

    /** @type {{ paginator: { current_page: number, last_page: number, prev_page_url: string | null, next_page_url: string | null } }} */
    let { paginator } = $props();
</script>

{#if paginator.last_page > 1}
    <nav class="mt-8 flex items-center justify-between gap-4" aria-label="Halaman">
        {#if paginator.prev_page_url}
            <Link href={paginator.prev_page_url} preserveScroll class="text-sm font-semibold text-primary hover:underline">← Sebelumnya</Link>
        {:else}
            <span></span>
        {/if}
        <span class="text-sm text-muted">Halaman {paginator.current_page} dari {paginator.last_page}</span>
        {#if paginator.next_page_url}
            <Link href={paginator.next_page_url} preserveScroll class="text-sm font-semibold text-primary hover:underline">Berikutnya →</Link>
        {:else}
            <span></span>
        {/if}
    </nav>
{/if}
