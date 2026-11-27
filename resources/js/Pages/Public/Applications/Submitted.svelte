<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';

    let { application } = $props();

    let isCopied = $state(false);

    async function copyCode() {
        try {
            await navigator.clipboard.writeText(application.code);
            isCopied = true;
        } catch {
            isCopied = false;
        }
    }
</script>

<svelte:head>
    <title>Lamaran terkirim · Magang Jaya Intero</title>
    <meta name="robots" content="noindex" />
</svelte:head>

<section class="bg-brand-gradient text-white">
    <div class="mx-auto max-w-2xl px-4 py-16 text-center sm:px-6 md:py-24">
        <p class="label-tag text-white/80">Lamaran terkirim</p>
        <h1 class="mt-3 text-3xl leading-tight font-black uppercase sm:text-5xl">Terima <span class="text-accent">kasih</span></h1>
        <p class="mt-5 text-white/90">Lamaranmu untuk <strong>{application.program}</strong> sudah kami terima.</p>

        <div class="mx-auto mt-10 max-w-sm rounded-card border-4 border-white/90 bg-white p-6 text-ink shadow-[10px_10px_0_0_rgb(45_85_146_/_0.6)]">
            <p class="label-tag text-muted">Kode lamaran</p>
            <p class="mt-2 text-3xl font-black tracking-wide text-primary">{application.code}</p>
            <button type="button" class="mt-3 text-sm font-semibold text-primary hover:underline" onclick={copyCode}>
                {isCopied ? 'Tersalin' : 'Salin kode'}
            </button>
        </div>
    </div>
</section>

<section class="mx-auto max-w-2xl px-4 py-12 text-center sm:px-6">
    <p class="text-muted">
        Kode yang sama dikirim ke <strong class="text-ink">{application.email}</strong>. Simpan kode ini untuk memantau status lamaran.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <Link href="/cek-status" class="btn-primary">Cek status lamaran</Link>
        <Link href="/" class="btn border border-line text-ink hover:border-primary">Kembali ke beranda</Link>
    </div>
</section>
