<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import StatusBadge from '../../../Components/Admin/StatusBadge.svelte';
    import DeleteButton from '../../../Components/Admin/DeleteButton.svelte';

    let { faqs } = $props();
</script>

<svelte:head>
    <title>FAQ · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Konten" title="FAQ" description="Urutan kecil tampil lebih dulu. Empat teratas juga tampil di beranda.">
    {#snippet actions()}
        <Link href="/admin/faqs/create" class="btn-primary py-2.5">Tambah FAQ</Link>
    {/snippet}
</PageHeader>

{#if faqs.length === 0}
    <div class="mt-8 rounded-card border border-dashed border-line bg-white p-10 text-center">
        <p class="font-bold text-primary">Belum ada FAQ</p>
    </div>
{:else}
    <ul class="mt-8 divide-y divide-line rounded-card border border-line bg-white">
        {#each faqs as faq (faq.id)}
            <li class="flex flex-wrap items-center gap-4 px-5 py-4">
                <span class="w-8 text-center text-sm font-bold text-muted">{faq.sort_order}</span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-ink">{faq.question}</p>
                    <p class="text-xs text-muted">{faq.category}</p>
                </div>
                {#if !faq.is_published}<StatusBadge>Disembunyikan</StatusBadge>{/if}
                <div class="whitespace-nowrap">
                    <Link href="/admin/faqs/{faq.id}/edit" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Ubah</Link>
                    <DeleteButton href="/admin/faqs/{faq.id}" confirmMessage="Hapus FAQ ini?" />
                </div>
            </li>
        {/each}
    </ul>
{/if}
