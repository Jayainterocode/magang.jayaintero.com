<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Form } from '@inertiajs/svelte';
    import PageHero from '../../../Components/Public/PageHero.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { result = null, notFound = false, lookup = null } = $props();

    const statusTones = {
        accepted: 'bg-accent text-primary-dark',
        rejected: 'bg-line text-muted',
    };
</script>

<svelte:head>
    <title>Cek Status Lamaran · Magang Jaya Intero</title>
    <meta name="description" content="Cek status lamaran magang Jaya Intero dengan kode lamaran dan email." />
</svelte:head>

<PageHero label="#CekStatus" title="Cek status lamaran" intro="Masukkan kode lamaran dan email yang kamu pakai saat mendaftar." />

<section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 md:py-16">
    <Form action="/cek-status" method="post" class="rounded-card border border-line bg-white p-6 sm:p-8">
        {#snippet children({ errors, processing })}
            {#if errors.form}
                <p class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700" role="alert">{errors.form}</p>
            {/if}
            <div class="grid gap-6 sm:grid-cols-2">
                <Field label="Kode lamaran" id="code" error={errors.code}>
                    <input id="code" name="code" class="field-input uppercase" placeholder="MG-2026-0001" value={lookup?.code ?? ''} required maxlength="20" />
                </Field>
                <Field label="Email" id="email" error={errors.email}>
                    <input id="email" name="email" type="email" autocomplete="email" class="field-input" value={lookup?.email ?? ''} required maxlength="150" />
                </Field>
            </div>
            <button type="submit" class="btn-primary mt-6 w-full sm:w-auto" disabled={processing}>
                {processing ? 'Mencari…' : 'Cek status'}
            </button>
        {/snippet}
    </Form>

    {#if notFound}
        <div class="mt-8 rounded-card border border-line bg-surface p-6 text-center" role="status">
            <p class="font-bold text-ink">Lamaran tidak ditemukan</p>
            <p class="mt-1 text-sm text-muted">Pastikan kode lamaran dan email sama persis dengan yang ada di email konfirmasi.</p>
        </div>
    {/if}

    {#if result}
        <article class="mt-8 overflow-hidden rounded-card border border-line bg-white" aria-live="polite">
            <header class="bg-primary px-6 py-5 text-white">
                <p class="label-tag text-white/75">{result.code}</p>
                <p class="mt-1 text-lg font-bold">{result.full_name}</p>
                <p class="text-sm text-white/85">{result.program} · dikirim {result.submitted_at}</p>
            </header>
            <div class="p-6">
                <span class="label-tag inline-flex rounded-md px-3 py-1.5 {statusTones[result.status] ?? 'bg-secondary/15 text-primary-dark'}">
                    {result.status_label}
                </span>
                <p class="mt-3 text-ink">{result.status_description}</p>

                {#if result.timeline.length > 0}
                    <h2 class="label-tag mt-8 text-primary">Riwayat</h2>
                    <ol class="mt-4 space-y-4 border-l-2 border-line pl-5">
                        {#each result.timeline as entry (entry.id)}
                            <li class="relative">
                                <span class="absolute top-1.5 -left-[27px] size-3 rounded-full border-2 border-white bg-primary"></span>
                                <p class="text-sm font-bold text-ink">{entry.status_label}</p>
                                <p class="text-xs text-muted">{entry.date}</p>
                                {#if entry.note}<p class="mt-1 text-sm text-ink">{entry.note}</p>{/if}
                            </li>
                        {/each}
                    </ol>
                {/if}
            </div>
        </article>
    {/if}
</section>
