<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import ApplicationStatusBadge from '../../../Components/Admin/ApplicationStatusBadge.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { application, statuses, emailedStatuses } = $props();

    let selectedStatus = $state(application.status);

    const willEmail = $derived(selectedStatus !== application.status && emailedStatuses.includes(selectedStatus));

    const sections = $derived([
        { title: 'Data diri', rows: application.personal },
        { title: 'Pendidikan', rows: application.education },
        { title: 'Rencana magang', rows: application.internship },
    ]);
</script>

<svelte:head>
    <title>{application.code} · Admin Magang Jaya Intero</title>
</svelte:head>

<Link href="/admin/applications" class="text-sm font-semibold text-primary hover:underline">← Semua lamaran</Link>

<div class="mt-4 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="label-tag text-primary">{application.code} · masuk {application.submitted_at}</p>
        <h1 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">{application.personal['Nama lengkap']}</h1>
        <p class="mt-1 text-sm text-muted">
            <Link href="/program/{application.program.slug}" class="font-semibold text-primary hover:underline">{application.program.title}</Link>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <ApplicationStatusBadge status={application.status} label={application.status_label} />
        {#if application.phone_link}
            <a href={application.phone_link} target="_blank" rel="noopener" class="btn border border-line bg-white py-2 text-ink hover:border-primary">WhatsApp</a>
        {/if}
        <a href="mailto:{application.personal['Email']}" class="btn border border-line bg-white py-2 text-ink hover:border-primary">Email</a>
    </div>
</div>

<div class="mt-8 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
    <div class="space-y-6">
        {#each sections as section (section.title)}
            <section class="rounded-card border border-line bg-white p-6">
                <h2 class="label-tag text-primary">{section.title}</h2>
                <dl class="mt-4 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    {#each Object.entries(section.rows) as [label, value] (label)}
                        <div>
                            <dt class="text-xs text-muted">{label}</dt>
                            <dd class="mt-0.5 text-sm font-semibold break-words text-ink">{value ?? '—'}</dd>
                        </div>
                    {/each}
                </dl>
            </section>
        {/each}

        <section class="rounded-card border border-line bg-white p-6">
            <h2 class="label-tag text-primary">Motivasi</h2>
            <p class="mt-3 text-sm leading-relaxed whitespace-pre-line text-ink">{application.motivation}</p>
        </section>

        <section class="rounded-card border border-line bg-white p-6">
            <h2 class="label-tag text-primary">Berkas</h2>
            <ul class="mt-4 divide-y divide-line">
                {#each application.documents as document (document.id)}
                    <li class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-ink">{document.type}</p>
                            <p class="truncate text-xs text-muted">{document.original_name} · {document.size_kb} KB</p>
                        </div>
                        <a href={document.url} target="_blank" rel="noopener" class="shrink-0 rounded-lg px-3 py-1.5 text-sm font-semibold text-primary hover:bg-surface">Buka</a>
                    </li>
                {/each}
            </ul>
        </section>
    </div>

    <div class="space-y-6">
        <section class="rounded-card border border-line bg-white p-6">
            <h2 class="label-tag text-primary">Ubah status</h2>
            <Form action="/admin/applications/{application.id}/status" method="put" resetOnSuccess={['note']} class="mt-4 space-y-4">
                {#snippet children({ errors, processing })}
                    <Field label="Status" id="status" error={errors.status}>
                        <select id="status" name="status" class="field-input" bind:value={selectedStatus}>
                            {#each statuses as status (status.value)}
                                <option value={status.value}>{status.label}</option>
                            {/each}
                        </select>
                    </Field>
                    <Field label="Catatan" id="note" error={errors.note} hint="Contoh: jadwal wawancara, alasan, atau info lanjutan.">
                        <textarea id="note" name="note" rows="4" class="field-input" maxlength="2000"></textarea>
                    </Field>
                    <label class="flex items-start gap-3 text-sm text-ink">
                        <input type="checkbox" name="is_visible_to_applicant" value="1" checked class="mt-0.5 size-4 rounded border-line" />
                        <span>Tampilkan catatan ke pelamar (di halaman cek status dan email)</span>
                    </label>
                    {#if willEmail}
                        <p class="rounded-xl bg-accent/30 px-4 py-3 text-xs font-semibold text-primary-dark">
                            Pelamar akan menerima email pemberitahuan untuk status ini.
                        </p>
                    {/if}
                    <button type="submit" class="btn-primary w-full" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan status'}</button>
                {/snippet}
            </Form>
        </section>

        <section class="rounded-card border border-line bg-white p-6">
            <h2 class="label-tag text-primary">Riwayat</h2>
            <ol class="mt-4 space-y-5 border-l-2 border-line pl-5">
                {#each application.history as entry (entry.id)}
                    <li class="relative">
                        <span class="absolute top-1.5 -left-[27px] size-3 rounded-full border-2 border-white bg-primary"></span>
                        <p class="text-sm font-bold text-ink">
                            {entry.from ? `${entry.from} → ${entry.to}` : entry.to}
                        </p>
                        <p class="text-xs text-muted">{entry.date} · {entry.by}</p>
                        {#if entry.note}
                            <p class="mt-1 text-sm text-ink">{entry.note}</p>
                            <p class="mt-0.5 text-[11px] font-semibold {entry.is_visible_to_applicant ? 'text-primary' : 'text-muted'}">
                                {entry.is_visible_to_applicant ? 'Terlihat pelamar' : 'Internal'}
                            </p>
                        {/if}
                    </li>
                {/each}
            </ol>
        </section>
    </div>
</div>
