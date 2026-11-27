<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import StatusBadge from '../../../Components/Admin/StatusBadge.svelte';
    import DeleteButton from '../../../Components/Admin/DeleteButton.svelte';
    import Pagination from '../../../Components/Pagination.svelte';

    let { posts } = $props();
</script>

<svelte:head>
    <title>Artikel · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Konten" title="Artikel" description="Berita, pengumuman, dan cerita kegiatan magang.">
    {#snippet actions()}
        <Link href="/admin/posts/create" class="btn-primary py-2.5">Tulis artikel</Link>
    {/snippet}
</PageHeader>

{#if posts.data.length === 0}
    <div class="mt-8 rounded-card border border-dashed border-line bg-white p-10 text-center">
        <p class="font-bold text-primary">Belum ada artikel</p>
    </div>
{:else}
    <div class="mt-8 overflow-x-auto rounded-card border border-line bg-white">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-surface">
                <tr class="label-tag text-muted">
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                {#each posts.data as post (post.id)}
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-bold text-ink">{post.title}</p>
                            {#if post.author}<p class="text-xs text-muted">oleh {post.author}</p>{/if}
                        </td>
                        <td class="px-5 py-4 text-muted">{post.category}</td>
                        <td class="px-5 py-4">
                            {#if post.is_published}
                                <StatusBadge tone="success">Terbit {post.published_at}</StatusBadge>
                            {:else}
                                <StatusBadge>Draf</StatusBadge>
                            {/if}
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <Link href="/admin/posts/{post.id}/edit" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Ubah</Link>
                            <DeleteButton href="/admin/posts/{post.id}" confirmMessage="Hapus artikel “{post.title}”?" />
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    <Pagination paginator={posts} />
{/if}
