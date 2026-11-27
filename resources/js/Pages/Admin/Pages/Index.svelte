<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';

    let { pages } = $props();
</script>

<svelte:head>
    <title>Halaman · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Konten" title="Halaman" description="Teks utama di halaman tetap website." />

<ul class="mt-8 divide-y divide-line rounded-card border border-line bg-white">
    {#each pages as page (page.key)}
        <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
            <div>
                <p class="label-tag text-primary">{page.label}</p>
                <p class="mt-1 font-bold text-ink">{page.title}</p>
                <p class="text-xs text-muted">{page.updated_at ? `Diubah ${page.updated_at}` : 'Belum pernah diubah'}</p>
            </div>
            <Link href="/admin/pages/{page.key}/edit" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Ubah</Link>
        </li>
    {/each}
</ul>
