<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { page } = $props();

    const hints = {
        home: {
            title: 'Judul besar di bagian atas beranda.',
            intro: 'Paragraf di bawah judul beranda.',
            body: 'Isi bagian "Kenapa magang di Jaya Intero" di beranda.',
        },
        about: {
            title: 'Judul halaman Tentang Magang.',
            intro: 'Paragraf pembuka halaman.',
            body: 'Isi lengkap halaman.',
        },
    };

    const hint = $derived(hints[page.key] ?? { title: '', intro: '', body: '' });
</script>

<svelte:head>
    <title>Ubah {page.label} · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Halaman" title="Ubah {page.label}" />

<Form action="/admin/pages/{page.key}" method="put" class="mt-8 max-w-3xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8">
    {#snippet children({ errors, processing })}
        <Field label="Judul" id="title" error={errors.title} hint={hint.title}>
            <input id="title" name="title" class="field-input" value={page.title} required maxlength="150" />
        </Field>

        <Field label="Pengantar" id="intro" error={errors.intro} hint={hint.intro}>
            <textarea id="intro" name="intro" rows="3" class="field-input" maxlength="500" value={page.intro ?? ''}></textarea>
        </Field>

        <Field label="Isi" id="body" error={errors.body} hint="{hint.body} Mendukung Markdown: ## judul, - daftar, **tebal**.">
            <textarea id="body" name="body" rows="14" class="field-input font-mono text-[13px]" required value={page.body}></textarea>
        </Field>

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan halaman'}</button>
            <Link href="/admin/pages" class="btn border border-line text-ink hover:border-primary">Batal</Link>
        </div>
    {/snippet}
</Form>
