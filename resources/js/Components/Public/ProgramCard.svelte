<script>
    import { Link } from '@inertiajs/svelte';

    let { program } = $props();
</script>

<article class="flex h-full flex-col rounded-card border border-line bg-white p-6 text-ink transition hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_var(--color-primary)]">
    <div class="flex items-start justify-between gap-3">
        <p class="label-tag text-primary">{program.division}</p>
        {#if program.is_open}
            <span class="label-tag rounded-md bg-accent px-2 py-1 text-[10px] text-primary-dark">Buka</span>
        {:else}
            <span class="label-tag rounded-md bg-line px-2 py-1 text-[10px] text-muted">Tutup</span>
        {/if}
    </div>
    <h3 class="mt-3 text-xl leading-snug font-extrabold text-primary">{program.title}</h3>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-muted">{program.summary}</p>
    <dl class="mt-5 flex flex-wrap gap-x-5 gap-y-1 text-xs">
        <div><dt class="sr-only">Jenjang</dt><dd class="font-bold">{program.education_levels.join(' · ')}</dd></div>
        <div><dt class="sr-only">Durasi</dt><dd class="font-bold">{program.duration_months} bulan</dd></div>
        {#if program.closes_at}
            <div><dt class="inline text-muted">Tutup </dt><dd class="inline font-bold">{program.closes_at}</dd></div>
        {/if}
    </dl>
    <Link href="/program/{program.slug}" class="btn-primary mt-6 w-full">Lihat detail</Link>
</article>
