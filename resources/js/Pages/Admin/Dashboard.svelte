<script module>
    export { default as layout } from '../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { Link, page } from '@inertiajs/svelte';
    import ApplicationStatusBadge from '../../Components/Admin/ApplicationStatusBadge.svelte';

    let { contentStats, applicationStats } = $props();

    const user = $derived(page.props.auth.user);

    const contentCards = $derived([
        { label: 'Program buka', value: contentStats.open_programs, note: `dari ${contentStats.published_programs} program tayang` },
        { label: 'Artikel terbit', value: contentStats.published_posts },
        { label: 'FAQ tayang', value: contentStats.faqs },
        { label: 'Foto galeri', value: contentStats.gallery_items },
    ]);

    const maxProgramCount = $derived(Math.max(1, ...(applicationStats?.by_program.map((program) => program.count) ?? [1])));
</script>

<svelte:head>
    <title>Dashboard · Admin Magang Jaya Intero</title>
</svelte:head>

<p class="label-tag text-primary">Dashboard</p>
<h1 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">Halo, {user.name}</h1>

{#if applicationStats}
    <section class="mt-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h2 class="label-tag text-muted">Lamaran</h2>
            <Link href="/admin/applications" class="text-sm font-bold text-primary hover:underline">Semua lamaran →</Link>
        </div>

        <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-[1.2fr_repeat(5,1fr)]">
            <div class="bg-brand-gradient rounded-card p-6 text-white">
                <p class="label-tag text-white/80">Total lamaran</p>
                <p class="mt-2 text-4xl font-black">{applicationStats.total}</p>
                <p class="mt-1 text-xs text-white/80">{applicationStats.this_week} masuk minggu ini</p>
            </div>
            {#each applicationStats.by_status as item (item.status)}
                <Link
                    href="/admin/applications?status={item.status}"
                    class="rounded-card border border-line bg-white p-5 transition hover:border-primary"
                >
                    <ApplicationStatusBadge status={item.status} label={item.label} />
                    <p class="mt-3 text-3xl font-black text-primary">{item.count}</p>
                </Link>
            {/each}
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <div class="rounded-card border border-line bg-white p-6">
                <h3 class="label-tag text-primary">Lamaran terbaru</h3>
                {#if applicationStats.latest.length === 0}
                    <p class="mt-4 text-sm text-muted">Belum ada lamaran masuk.</p>
                {:else}
                    <ul class="mt-2 divide-y divide-line">
                        {#each applicationStats.latest as application (application.id)}
                            <li class="flex items-center justify-between gap-4 py-3">
                                <div class="min-w-0">
                                    <Link href="/admin/applications/{application.id}" class="font-bold text-ink hover:text-primary">{application.full_name}</Link>
                                    <p class="truncate text-xs text-muted">{application.program} · {application.submitted_at}</p>
                                </div>
                                <ApplicationStatusBadge status={application.status} label={application.status_label} />
                            </li>
                        {/each}
                    </ul>
                {/if}
            </div>

            <div class="rounded-card border border-line bg-white p-6">
                <h3 class="label-tag text-primary">Lamaran per program</h3>
                {#if applicationStats.by_program.length === 0}
                    <p class="mt-4 text-sm text-muted">Belum ada data.</p>
                {:else}
                    <ul class="mt-4 space-y-3">
                        {#each applicationStats.by_program as program (program.title)}
                            <li>
                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="truncate text-ink">{program.title}</span>
                                    <span class="font-bold text-primary">{program.count}</span>
                                </div>
                                <div class="mt-1 h-2 rounded-full bg-surface">
                                    <div class="h-2 rounded-full bg-primary" style="width: {(program.count / maxProgramCount) * 100}%"></div>
                                </div>
                            </li>
                        {/each}
                    </ul>
                {/if}
            </div>
        </div>
    </section>
{/if}

<section class="mt-10">
    <h2 class="label-tag text-muted">Konten website</h2>
    <dl class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {#each contentCards as card (card.label)}
            <div class="rounded-card border border-line bg-white p-6">
                <dt class="label-tag text-muted">{card.label}</dt>
                <dd class="mt-2 text-4xl font-black text-primary">{card.value}</dd>
                {#if card.note}<dd class="mt-1 text-xs text-muted">{card.note}</dd>{/if}
            </div>
        {/each}
    </dl>
</section>
