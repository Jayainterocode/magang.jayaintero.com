<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link, router } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import ApplicationStatusBadge from '../../../Components/Admin/ApplicationStatusBadge.svelte';
    import Pagination from '../../../Components/Pagination.svelte';

    let { applications, filters, options } = $props();

    let search = $state(filters.search ?? '');
    let programId = $state(filters.program_id ?? '');
    let status = $state(filters.status ?? '');
    let educationLevel = $state(filters.education_level ?? '');

    /** @type {ReturnType<typeof setTimeout> | undefined} */
    let searchTimer;

    const activeFilters = $derived(
        Object.fromEntries(
            Object.entries({ search, program_id: programId, status, education_level: educationLevel }).filter(([, value]) => value !== ''),
        ),
    );

    const exportUrl = $derived(`/admin/applications/export?${new URLSearchParams(activeFilters).toString()}`);

    function applyFilters() {
        router.get('/admin/applications', activeFilters, { preserveState: true, preserveScroll: true, replace: true });
    }

    function onSearchInput() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 350);
    }

    function resetFilters() {
        search = '';
        programId = '';
        status = '';
        educationLevel = '';
        applyFilters();
    }
</script>

<svelte:head>
    <title>Lamaran · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Rekrutmen" title="Lamaran" description="{applications.total} lamaran sesuai filter.">
    {#snippet actions()}
        <a href={exportUrl} class="btn border border-line bg-white py-2.5 text-ink hover:border-primary">Ekspor CSV</a>
    {/snippet}
</PageHeader>

<div class="mt-8 grid gap-3 rounded-card border border-line bg-white p-4 sm:grid-cols-2 lg:grid-cols-[2fr_1.5fr_1fr_1fr_auto]">
    <label class="sr-only" for="search">Cari</label>
    <input id="search" type="search" placeholder="Cari nama, email, kode, sekolah…" class="field-input" bind:value={search} oninput={onSearchInput} />

    <label class="sr-only" for="program">Program</label>
    <select id="program" class="field-input" bind:value={programId} onchange={applyFilters}>
        <option value="">Semua program</option>
        {#each options.programs as program (program.id)}
            <option value={String(program.id)}>{program.title}</option>
        {/each}
    </select>

    <label class="sr-only" for="status">Status</label>
    <select id="status" class="field-input" bind:value={status} onchange={applyFilters}>
        <option value="">Semua status</option>
        {#each options.statuses as option (option.value)}
            <option value={option.value}>{option.label}</option>
        {/each}
    </select>

    <label class="sr-only" for="education_level">Jenjang</label>
    <select id="education_level" class="field-input" bind:value={educationLevel} onchange={applyFilters}>
        <option value="">Semua jenjang</option>
        {#each options.educationLevels as level (level.value)}
            <option value={level.value}>{level.label}</option>
        {/each}
    </select>

    <button type="button" class="rounded-xl px-4 text-sm font-semibold text-primary hover:bg-surface" onclick={resetFilters}>Reset</button>
</div>

{#if applications.data.length === 0}
    <div class="mt-6 rounded-card border border-dashed border-line bg-white p-10 text-center">
        <p class="font-bold text-primary">Tidak ada lamaran</p>
        <p class="mt-1 text-sm text-muted">Belum ada lamaran masuk, atau tidak ada yang cocok dengan filter.</p>
    </div>
{:else}
    <div class="mt-6 overflow-x-auto rounded-card border border-line bg-white">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="border-b border-line bg-surface">
                <tr class="label-tag text-muted">
                    <th class="px-5 py-3">Pelamar</th>
                    <th class="px-5 py-3">Program</th>
                    <th class="px-5 py-3">Pendidikan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                {#each applications.data as application (application.id)}
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-4">
                            <Link href="/admin/applications/{application.id}" class="font-bold text-ink hover:text-primary">{application.full_name}</Link>
                            <p class="text-xs text-muted">{application.code} · {application.email}</p>
                        </td>
                        <td class="px-5 py-4 text-ink">{application.program}</td>
                        <td class="px-5 py-4 text-muted">{application.education_level} · {application.institution}</td>
                        <td class="px-5 py-4"><ApplicationStatusBadge status={application.status} label={application.status_label} /></td>
                        <td class="px-5 py-4 whitespace-nowrap text-muted">{application.submitted_at}</td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    <Pagination paginator={applications} />
{/if}
