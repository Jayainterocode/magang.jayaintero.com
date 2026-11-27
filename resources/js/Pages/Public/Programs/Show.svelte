<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link, page } from '@inertiajs/svelte';
    import PageHero from '../../../Components/Public/PageHero.svelte';

    let { program } = $props();

    const contact = $derived(page.props.contact);
</script>

<svelte:head>
    <title>{program.title} · Magang Jaya Intero</title>
    <meta name="description" content={program.summary} />
</svelte:head>

<PageHero label={program.division} title={program.title} intro={program.summary}>
    <div class="mt-6">
        {#if program.is_open}
            <span class="label-tag rounded-md bg-accent px-3 py-1.5 text-primary-dark">Pendaftaran buka</span>
        {:else}
            <span class="label-tag rounded-md bg-white/20 px-3 py-1.5 text-white">Pendaftaran tutup</span>
        {/if}
    </div>
</PageHero>

<div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:py-16 lg:grid-cols-[1.6fr_1fr]">
    <div>
        <h2 class="text-xl font-extrabold text-primary uppercase">Tentang program</h2>
        <div class="rich-text mt-4">{@html program.description_html}</div>

        <h2 class="mt-12 text-xl font-extrabold text-primary uppercase">Kualifikasi</h2>
        <div class="rich-text mt-4">{@html program.qualifications_html}</div>
    </div>

    <aside class="h-fit rounded-card border border-line bg-surface p-6 lg:sticky lg:top-24">
        <dl class="space-y-4 text-sm">
            <div>
                <dt class="label-tag text-muted">Jenjang</dt>
                <dd class="mt-1 font-bold text-ink">{program.education_levels.join(', ')}</dd>
            </div>
            <div>
                <dt class="label-tag text-muted">Durasi</dt>
                <dd class="mt-1 font-bold text-ink">{program.duration_months} bulan</dd>
            </div>
            {#if program.quota}
                <div>
                    <dt class="label-tag text-muted">Kuota</dt>
                    <dd class="mt-1 font-bold text-ink">{program.quota} peserta</dd>
                </div>
            {/if}
            {#if program.opens_at || program.closes_at}
                <div>
                    <dt class="label-tag text-muted">Periode pendaftaran</dt>
                    <dd class="mt-1 font-bold text-ink">{program.opens_at ?? '—'} – {program.closes_at ?? '—'}</dd>
                </div>
            {/if}
        </dl>

        <div class="mt-6 border-t border-line pt-6">
            {#if program.is_open}
                <p class="text-sm text-muted">Form lamaran online segera tersedia. Sementara itu, tanyakan program ini lewat WhatsApp.</p>
            {:else}
                <p class="text-sm text-muted">Pendaftaran program ini sedang tutup.</p>
            {/if}
            {#if contact.whatsapp_link}
                <a href={contact.whatsapp_link} target="_blank" rel="noopener" class="btn-primary mt-4 w-full">Tanya via WhatsApp</a>
            {/if}
            <Link href="/program" class="mt-3 block text-center text-sm font-semibold text-primary hover:underline">← Semua program</Link>
        </div>
    </aside>
</div>
