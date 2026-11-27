<script>
    import { Link, page } from '@inertiajs/svelte';
    import BrandMark from '../Components/BrandMark.svelte';

    let { children } = $props();

    let isMenuOpen = $state(false);

    const contact = $derived(page.props.contact);

    const navigation = [
        { label: 'Program', href: '/program' },
        { label: 'Tentang', href: '/tentang' },
        { label: 'Artikel', href: '/artikel' },
        { label: 'Galeri', href: '/galeri' },
        { label: 'FAQ', href: '/faq' },
    ];

    /** @param {string} href */
    function isActive(href) {
        const path = page.url.split('?')[0];

        return path === href || path.startsWith(`${href}/`);
    }
</script>

<div class="flex min-h-screen flex-col">
    <header class="sticky top-0 z-40 border-b border-line/70 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <Link href="/" aria-label="Beranda Magang Jaya Intero">
                <BrandMark />
            </Link>

            <nav class="hidden items-center gap-8 md:flex" aria-label="Navigasi utama">
                {#each navigation as item (item.href)}
                    <Link
                        href={item.href}
                        class="text-sm font-semibold transition hover:text-primary {isActive(item.href) ? 'text-primary' : 'text-ink'}"
                        aria-current={isActive(item.href) ? 'page' : undefined}
                    >
                        {item.label}
                    </Link>
                {/each}
                <Link href="/program" class="btn-primary py-2.5">Daftar Magang</Link>
            </nav>

            <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-lg text-primary md:hidden"
                aria-expanded={isMenuOpen}
                aria-controls="mobile-menu"
                aria-label={isMenuOpen ? 'Tutup menu' : 'Buka menu'}
                onclick={() => (isMenuOpen = !isMenuOpen)}
            >
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    {#if isMenuOpen}
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    {:else}
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    {/if}
                </svg>
            </button>
        </div>

        {#if isMenuOpen}
            <nav id="mobile-menu" class="border-t border-line bg-white px-4 pb-4 md:hidden" aria-label="Navigasi seluler">
                {#each navigation as item (item.href)}
                    <Link
                        href={item.href}
                        class="block py-3 text-base font-semibold {isActive(item.href) ? 'text-primary' : 'text-ink'}"
                        onclick={() => (isMenuOpen = false)}
                    >
                        {item.label}
                    </Link>
                {/each}
                <Link href="/program" class="btn-primary mt-2 w-full" onclick={() => (isMenuOpen = false)}>Daftar Magang</Link>
            </nav>
        {/if}
    </header>

    <main class="flex-1">
        {@render children()}
    </main>

    <footer id="kontak" class="bg-primary text-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.2fr_1fr]">
            <div>
                <BrandMark tone="light" />
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/80">
                    Belajar langsung di proyek renovasi, interior, dan material bangunan bersama tim Jaya Intero di Medan.
                </p>
            </div>

            <ul class="grid gap-4 text-sm sm:grid-cols-2">
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" /></svg>
                    <span><span class="label-tag block text-white/60">Instagram</span>{contact.instagram}</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 20l1.3-3.9A8 8 0 1 1 8 19z" stroke-linejoin="round" /></svg>
                    <span>
                        <span class="label-tag block text-white/60">WhatsApp</span>
                        <a href={contact.whatsapp_link} class="underline-offset-2 hover:underline" target="_blank" rel="noopener">{contact.whatsapp}</a>
                    </span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="4" /><path d="M10 9.5v5l4.5-2.5z" fill="currentColor" /></svg>
                    <span><span class="label-tag block text-white/60">YouTube</span>{contact.youtube}</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z" /><circle cx="12" cy="9.5" r="2.5" /></svg>
                    <span><span class="label-tag block text-white/60">Alamat</span>{contact.address}</span>
                </li>
            </ul>
        </div>
        <div class="border-t border-white/15">
            <p class="mx-auto max-w-6xl px-4 py-5 text-xs text-white/60 sm:px-6">
                © {new Date().getFullYear()} Jaya Intero. Semua hak dilindungi.
            </p>
        </div>
    </footer>
</div>
