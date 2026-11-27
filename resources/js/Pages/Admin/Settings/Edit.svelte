<script module>
    export { default as layout } from '../../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Form } from '@inertiajs/svelte';
    import PageHeader from '../../../Components/Admin/PageHeader.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { settings } = $props();
</script>

<svelte:head>
    <title>Pengaturan · Admin Magang Jaya Intero</title>
</svelte:head>

<PageHeader label="Situs" title="Pengaturan" description="Kontak di footer website dan status pendaftaran." />

<Form action="/admin/settings" method="put" class="mt-8 max-w-2xl space-y-6 rounded-card border border-line bg-white p-6 sm:p-8">
    {#snippet children({ errors, processing, recentlySuccessful })}
        <fieldset class="space-y-5">
            <legend class="label-tag text-primary">Kontak</legend>
            <Field label="Instagram" id="contact_instagram" error={errors.contact_instagram} hint="Tanpa @, contoh: jayaintero.id">
                <input id="contact_instagram" name="contact_instagram" class="field-input" value={settings.contact_instagram ?? ''} maxlength="100" />
            </Field>
            <Field label="Nomor WhatsApp" id="contact_whatsapp" error={errors.contact_whatsapp} hint="Contoh: 0821 6863 9543">
                <input id="contact_whatsapp" name="contact_whatsapp" type="tel" class="field-input" value={settings.contact_whatsapp ?? ''} maxlength="30" />
            </Field>
            <Field label="YouTube" id="contact_youtube" error={errors.contact_youtube}>
                <input id="contact_youtube" name="contact_youtube" class="field-input" value={settings.contact_youtube ?? ''} maxlength="100" />
            </Field>
            <Field label="Alamat" id="contact_address" error={errors.contact_address}>
                <textarea id="contact_address" name="contact_address" rows="2" class="field-input" maxlength="255" value={settings.contact_address ?? ''}></textarea>
            </Field>
        </fieldset>

        <fieldset class="space-y-3 border-t border-line pt-6">
            <legend class="label-tag text-primary">Pendaftaran</legend>
            <label class="flex items-start gap-3 rounded-xl bg-surface px-4 py-3 text-sm">
                <input type="checkbox" name="registration_open" value="1" checked={settings.registration_open} class="mt-0.5 size-4 rounded border-line" />
                <span>
                    <span class="font-semibold text-ink">Pendaftaran dibuka</span>
                    <span class="block text-muted">Matikan untuk menutup semua lamaran sekaligus, apa pun periode programnya.</span>
                </span>
            </label>
        </fieldset>

        <div class="flex items-center gap-4 border-t border-line pt-6">
            <button type="submit" class="btn-primary" disabled={processing}>{processing ? 'Menyimpan…' : 'Simpan pengaturan'}</button>
            {#if recentlySuccessful}<span class="text-sm font-semibold text-primary">Tersimpan.</span>{/if}
        </div>
    {/snippet}
</Form>
