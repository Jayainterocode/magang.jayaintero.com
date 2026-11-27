<script module>
    export { default as layout } from '../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { page } from '@inertiajs/svelte';

    let { stats } = $props();

    const user = $derived(page.props.auth.user);

    const cards = $derived([
        { label: 'Program buka', value: stats.open_programs, note: `dari ${stats.published_programs} program tayang` },
        { label: 'Artikel terbit', value: stats.published_posts },
        { label: 'FAQ tayang', value: stats.faqs },
        { label: 'Foto galeri', value: stats.gallery_items },
    ]);
</script>

<svelte:head>
    <title>Dashboard · Admin Magang Jaya Intero</title>
</svelte:head>

<p class="label-tag text-primary">Dashboard</p>
<h1 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">Halo, {user.name}</h1>

<dl class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    {#each cards as card (card.label)}
        <div class="rounded-card border border-line bg-white p-6">
            <dt class="label-tag text-muted">{card.label}</dt>
            <dd class="mt-2 text-4xl font-black text-primary">{card.value}</dd>
            {#if card.note}<dd class="mt-1 text-xs text-muted">{card.note}</dd>{/if}
        </div>
    {/each}
</dl>

<div class="mt-6 rounded-card border border-dashed border-line bg-white p-8 text-center">
    <p class="font-bold text-primary">Belum ada lamaran masuk</p>
    <p class="mx-auto mt-1 max-w-md text-sm text-muted">Statistik lamaran tampil di sini setelah form pendaftaran online dibuka (tahap 3).</p>
</div>
