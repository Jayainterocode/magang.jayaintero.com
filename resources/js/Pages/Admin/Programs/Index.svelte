<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import StatusBadge from '../../../Components/Admin/StatusBadge.svelte';
    import DeleteButton from '../../../Components/Admin/DeleteButton.svelte';
    import Pagination from '../../../Components/Pagination.svelte';

    let { programs } = $props();
</script>

<svelte:head>
    <title>Program Magang · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Rekrutmen" title="Program Magang" description="Program yang tampil di halaman publik dan bisa dilamar.">
    {#snippet actions()}
        <Link href="/admin/programs/create" class="btn-primary py-2.5">Tambah program</Link>
    {/snippet}
</PageHeader>

{#if programs.data.length === 0}
    <div class="mt-8 rounded-card border border-dashed border-line bg-white p-10 text-center">
        <p class="font-bold text-primary">Belum ada program</p>
        <p class="mt-1 text-sm text-muted">Tambahkan program pertama agar bisa tampil di website.</p>
    </div>
{:else}
    <div class="mt-8 overflow-x-auto rounded-card border border-line bg-white">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-line bg-surface">
                <tr class="label-tag text-muted">
                    <th class="px-5 py-3">Program</th>
                    <th class="px-5 py-3">Jenjang</th>
                    <th class="px-5 py-3">Periode</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                {#each programs.data as program (program.id)}
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-bold text-ink">{program.title}</p>
                            <p class="text-xs text-muted">{program.division}</p>
                        </td>
                        <td class="px-5 py-4 text-muted">{program.education_levels.join(', ')}</td>
                        <td class="px-5 py-4 text-muted">
                            {program.opens_at ?? '—'} – {program.closes_at ?? '—'}
                        </td>
                        <td class="px-5 py-4">
                            {#if !program.is_published}
                                <StatusBadge>Draf</StatusBadge>
                            {:else if program.is_open}
                                <StatusBadge tone="accent">Buka</StatusBadge>
                            {:else}
                                <StatusBadge tone="success">Tutup</StatusBadge>
                            {/if}
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <Link href="/admin/programs/{program.id}/edit" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Ubah</Link>
                            <DeleteButton href="/admin/programs/{program.id}" confirmMessage="Hapus program “{program.title}”?" />
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    <Pagination paginator={programs} />
{/if}
