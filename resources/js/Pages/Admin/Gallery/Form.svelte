<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { item } = $props();

    const isEditing = $derived(item !== null);
</script>

<svelte:head>
    <title>{isEditing ? 'Ubah' : 'Tambah'} Foto · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Galeri" title={isEditing ? 'Ubah foto' : 'Tambah foto'} />

<!-- Files cannot be sent with a real PUT, so updates POST with a spoofed _method. -->
<Form
    action={isEditing ? `/admin/gallery/${item.id}` : '/admin/gallery'}
    method="post"
    class="mt-8 max-w-2xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8"
>
    {#snippet children({ errors, processing, progress })}
        {#if isEditing}
            <input type="hidden" name="_method" value="put" />
        {/if}

        <Field label="Foto" id="image" error={errors.image} hint="JPG, PNG, atau WebP. Maks 4 MB. Pakai foto asli kegiatan Jaya Intero.">
            {#if item?.image_url}
                <img src={item.image_url} alt="" class="mb-3 aspect-[4/3] w-full max-w-xs rounded-lg object-cover" />
            {/if}
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="field-input py-2.5" required={!isEditing} />
        </Field>

        {#if progress}
            <progress value={progress.percentage} max="100" class="w-full">{progress.percentage}%</progress>
        {/if}

        <Field label="Keterangan" id="caption" error={errors.caption}>
            <input id="caption" name="caption" class="field-input" value={item?.caption ?? ''} required maxlength="255" />
        </Field>

        <Field label="Urutan" id="sort_order" error={errors.sort_order} hint="Angka kecil tampil lebih dulu.">
            <input id="sort_order" name="sort_order" type="number" min="0" max="999" class="field-input" value={item?.sort_order ?? 0} required />
        </Field>

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan foto'}</button>
            <Link href="/admin/gallery" class="btn border border-line text-ink hover:border-primary">Batal</Link>
        </div>
    {/snippet}
</Form>
