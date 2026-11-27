<script>
    import { Link, page } from '@inertiajs/svelte';
    import BrandMark from '../Components/BrandMark.svelte';

    let { children } = $props();

    let isSidebarOpen = $state(false);

    const user = $derived(page.props.auth.user);
    const status = $derived(page.props.flash.status);

    const navigation = [
        { label: 'Dashboard', href: '/admin', isReady: true },
        { label: 'Lamaran', href: null, isReady: false },
        { label: 'Program Magang', href: null, isReady: false },
        { label: 'Konten', href: null, isReady: false },
        { label: 'Pengaturan', href: null, isReady: false },
    ];

    const roleLabels = { admin: 'Admin', editor: 'Editor konten' };
</script>

<div class="min-h-screen bg-surface lg:grid lg:grid-cols-[16rem_1fr]">
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-primary text-white transition-transform lg:static lg:translate-x-0 {isSidebarOpen
            ? 'translate-x-0'
            : ''}"
        aria-label="Menu admin"
    >
        <div class="flex h-16 items-center px-6">
            <BrandMark tone="light" subtitle="Admin Magang" />
        </div>

        <nav class="mt-4 space-y-1 px-3">
            {#each navigation as item (item.label)}
                {#if item.isReady}
                    <Link
                        href={item.href}
                        class="flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold {page.url === item.href
                            ? 'bg-white/15 text-white'
                            : 'text-white/80 hover:bg-white/10'}"
                    >
                        {item.label}
                    </Link>
                {:else}
                    <span class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-semibold text-white/45">
                        {item.label}
                        <span class="label-tag rounded bg-white/10 px-1.5 py-0.5 text-[10px]">Segera</span>
                    </span>
                {/if}
            {/each}
        </nav>
    </aside>

    {#if isSidebarOpen}
        <button
            type="button"
            class="fixed inset-0 z-30 bg-ink/40 lg:hidden"
            aria-label="Tutup menu"
            onclick={() => (isSidebarOpen = false)}
        ></button>
    {/if}

    <div class="flex min-w-0 flex-col">
        <header class="flex h-16 items-center justify-between border-b border-line bg-white px-4 sm:px-6">
            <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-lg text-primary lg:hidden"
                aria-label="Buka menu"
                onclick={() => (isSidebarOpen = true)}
            >
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
            </button>

            <div class="ml-auto flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-ink">{user.name}</p>
                    <p class="text-xs text-muted">{roleLabels[user.role] ?? user.role}</p>
                </div>
                <Link
                    href="/admin/logout"
                    method="post"
                    as="button"
                    class="rounded-lg border border-line px-3 py-2 text-sm font-semibold text-ink hover:border-primary hover:text-primary"
                >
                    Keluar
                </Link>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {#if status}
                <div class="mb-6 rounded-xl border border-secondary/40 bg-secondary/10 px-4 py-3 text-sm font-medium text-primary-dark" role="status">
                    {status}
                </div>
            {/if}
            {@render children()}
        </main>
    </div>
</div>
