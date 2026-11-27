<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { post } = $props();

    const isEditing = $derived(post !== null);
</script>

<svelte:head>
    <title>{isEditing ? 'Ubah' : 'Tulis'} Artikel · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Artikel" title={isEditing ? `Ubah: ${post.title}` : 'Tulis artikel'} />

<!-- Files cannot be sent with a real PUT, so updates POST with a spoofed _method. -->
<Form
    action={isEditing ? `/admin/posts/${post.id}` : '/admin/posts'}
    method="post"
    class="mt-8 max-w-3xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8"
>
    {#snippet children({ errors, processing, progress })}
        {#if isEditing}
            <input type="hidden" name="_method" value="put" />
        {/if}

        <div class="grid gap-6 sm:grid-cols-[2fr_1fr]">
            <Field label="Judul" id="title" error={errors.title}>
                <input id="title" name="title" class="field-input" value={post?.title ?? ''} required maxlength="150" />
            </Field>
            <Field label="Kategori" id="category" error={errors.category} hint="Contoh: Kegiatan, Pengumuman">
                <input id="category" name="category" class="field-input" value={post?.category ?? ''} required maxlength="50" />
            </Field>
        </div>

        <Field label="Ringkasan" id="excerpt" error={errors.excerpt} hint="Tampil di kartu artikel. Maks 300 karakter.">
            <textarea id="excerpt" name="excerpt" rows="2" class="field-input" required maxlength="300" value={post?.excerpt ?? ''}></textarea>
        </Field>

        <Field label="Isi artikel" id="body" error={errors.body} hint="Mendukung Markdown: ## judul, - daftar, **tebal**, [tautan](https://…).">
            <textarea id="body" name="body" rows="14" class="field-input font-mono text-[13px]" required value={post?.body ?? ''}></textarea>
        </Field>

        <Field label="Gambar sampul" id="cover_image" error={errors.cover_image} hint="JPG, PNG, atau WebP. Maks 2 MB.">
            {#if post?.cover_image_url}
                <div class="mb-3 flex items-center gap-4">
                    <img src={post.cover_image_url} alt="" class="h-20 w-32 rounded-lg object-cover" />
                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="remove_cover_image" value="1" class="size-4 rounded border-line" />
                        Hapus gambar ini
                    </label>
                </div>
            {/if}
            <input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp" class="field-input py-2.5" />
        </Field>

        {#if progress}
            <progress value={progress.percentage} max="100" class="w-full">{progress.percentage}%</progress>
        {/if}

        <label class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 text-sm font-semibold">
            <input type="checkbox" name="is_published" value="1" checked={post?.is_published ?? false} class="size-4 rounded border-line" />
            Terbitkan di website
        </label>

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan artikel'}</button>
            <Link href="/admin/posts" class="btn border border-line text-ink hover:border-primary">Batal</Link>
        </div>
    {/snippet}
</Form>
