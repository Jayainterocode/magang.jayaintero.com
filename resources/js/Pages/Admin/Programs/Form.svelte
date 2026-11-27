<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form, Link } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { program, educationLevels } = $props();

    const isEditing = $derived(program !== null);
</script>

<svelte:head>
    <title>{isEditing ? 'Ubah' : 'Tambah'} Program · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Program Magang" title={isEditing ? `Ubah: ${program.title}` : 'Tambah program'} />

<Form
    action={isEditing ? `/admin/programs/${program.id}` : '/admin/programs'}
    method={isEditing ? 'put' : 'post'}
    class="mt-8 max-w-3xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8"
>
    {#snippet children({ errors, processing })}
        <div class="grid gap-6 sm:grid-cols-2">
            <Field label="Judul program" id="title" error={errors.title}>
                <input id="title" name="title" class="field-input" value={program?.title ?? ''} required maxlength="150" />
            </Field>
            <Field label="Divisi" id="division" error={errors.division} hint="Contoh: Desain Interior, Drafter">
                <input id="division" name="division" class="field-input" value={program?.division ?? ''} required maxlength="100" />
            </Field>
        </div>

        <fieldset>
            <legend class="field-label">Jenjang pendidikan</legend>
            <div class="flex flex-wrap gap-3">
                {#each educationLevels as level (level.value)}
                    <label class="flex items-center gap-2 rounded-xl border border-line px-4 py-2.5 text-sm font-semibold">
                        <input
                            type="checkbox"
                            name="education_levels[]"
                            value={level.value}
                            checked={program?.education_levels.includes(level.value) ?? false}
                            class="size-4 rounded border-line"
                        />
                        {level.label}
                    </label>
                {/each}
            </div>
            {#if errors.education_levels || errors['education_levels.0']}
                <p class="field-error">{errors.education_levels ?? errors['education_levels.0']}</p>
            {/if}
        </fieldset>

        <Field label="Ringkasan" id="summary" error={errors.summary} hint="Satu-dua kalimat untuk kartu program. Maks 300 karakter.">
            <textarea id="summary" name="summary" rows="2" class="field-input" required maxlength="300" value={program?.summary ?? ''}></textarea>
        </Field>

        <Field label="Deskripsi" id="description" error={errors.description} hint="Mendukung Markdown: ## judul, - daftar, **tebal**.">
            <textarea id="description" name="description" rows="8" class="field-input font-mono text-[13px]" required value={program?.description ?? ''}></textarea>
        </Field>

        <Field label="Kualifikasi" id="qualifications" error={errors.qualifications} hint="Tulis sebagai daftar, satu baris diawali tanda -">
            <textarea id="qualifications" name="qualifications" rows="5" class="field-input font-mono text-[13px]" required value={program?.qualifications ?? ''}></textarea>
        </Field>

        <div class="grid gap-6 sm:grid-cols-2">
            <Field label="Durasi (bulan)" id="duration_months" error={errors.duration_months}>
                <input id="duration_months" name="duration_months" type="number" min="1" max="12" class="field-input" value={program?.duration_months ?? 3} required />
            </Field>
            <Field label="Kuota peserta" id="quota" error={errors.quota} hint="Kosongkan jika tidak dibatasi.">
                <input id="quota" name="quota" type="number" min="1" max="500" class="field-input" value={program?.quota ?? ''} />
            </Field>
            <Field label="Pendaftaran dibuka" id="opens_at" error={errors.opens_at}>
                <input id="opens_at" name="opens_at" type="date" class="field-input" value={program?.opens_at ?? ''} />
            </Field>
            <Field label="Pendaftaran ditutup" id="closes_at" error={errors.closes_at}>
                <input id="closes_at" name="closes_at" type="date" class="field-input" value={program?.closes_at ?? ''} />
            </Field>
        </div>

        <label class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 text-sm font-semibold">
            <input type="checkbox" name="is_published" value="1" checked={program?.is_published ?? false} class="size-4 rounded border-line" />
            Tampilkan di website
        </label>

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan program'}</button>
            <Link href="/admin/programs" class="btn border border-line text-ink hover:border-primary">Batal</Link>
        </div>
    {/snippet}
</Form>
