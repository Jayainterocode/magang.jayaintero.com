<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { faq, nextSortOrder } = $props();

    const isEditing = $derived(faq !== null);
</script>

<svelte:head>
    <title>{isEditing ? 'Ubah' : 'Tambah'} FAQ · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="FAQ" title={isEditing ? 'Ubah FAQ' : 'Tambah FAQ'} />

<Form
    action={isEditing ? `/admin/faqs/${faq.id}` : '/admin/faqs'}
    method={isEditing ? 'put' : 'post'}
    class="mt-8 max-w-3xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8"
>
    {#snippet children({ errors, processing })}
        <Field label="Pertanyaan" id="question" error={errors.question}>
            <input id="question" name="question" class="field-input" value={faq?.question ?? ''} required maxlength="255" />
        </Field>

        <Field label="Jawaban" id="answer" error={errors.answer} hint="Mendukung Markdown sederhana.">
            <textarea id="answer" name="answer" rows="6" class="field-input" required value={faq?.answer ?? ''}></textarea>
        </Field>

        <div class="grid gap-6 sm:grid-cols-2">
            <Field label="Kategori" id="category" error={errors.category} hint="Contoh: Pendaftaran, Selama Magang">
                <input id="category" name="category" class="field-input" value={faq?.category ?? ''} required maxlength="50" />
            </Field>
            <Field label="Urutan" id="sort_order" error={errors.sort_order}>
                <input id="sort_order" name="sort_order" type="number" min="0" max="999" class="field-input" value={faq?.sort_order ?? nextSortOrder} required />
            </Field>
        </div>

        <label class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 text-sm font-semibold">
            <input type="checkbox" name="is_published" value="1" checked={faq?.is_published ?? true} class="size-4 rounded border-line" />
            Tampilkan di website
        </label>

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan FAQ'}</button>
            <Link href="/admin/faqs" class="btn border border-line text-ink hover:border-primary">Batal</Link>
        </div>
    {/snippet}
</Form>
