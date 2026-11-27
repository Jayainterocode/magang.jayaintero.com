<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import DeleteButton from '../../../Components/Admin/DeleteButton.svelte';

    let { items } = $props();
</script>

<svelte:head>
    <title>Galeri · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Konten" title="Galeri" description="Foto kegiatan peserta magang dan proyek. Enam teratas tampil di beranda.">
    {#snippet actions()}
        <Link href="/admin/gallery/create" class="btn-primary py-2.5">Tambah foto</Link>
    {/snippet}
</PageHeader>

{#if items.length === 0}
    <div class="mt-8 rounded-card border border-dashed border-line bg-white p-10 text-center">
        <p class="font-bold text-primary">Belum ada foto</p>
    </div>
{:else}
    <ul class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {#each items as item (item.id)}
            <li class="overflow-hidden rounded-card border border-line bg-white">
                <img src={item.image_url} alt={item.caption} class="aspect-[4/3] w-full object-cover" loading="lazy" />
                <div class="p-4">
                    <p class="text-sm font-semibold text-ink">{item.caption}</p>
                    <p class="text-xs text-muted">Urutan {item.sort_order}</p>
                    <div class="mt-3 -ml-3">
                        <Link href="/admin/gallery/{item.id}/edit" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Ubah</Link>
                        <DeleteButton href="/admin/gallery/{item.id}" confirmMessage="Hapus foto ini?" />
                    </div>
                </div>
            </li>
        {/each}
    </ul>
{/if}
