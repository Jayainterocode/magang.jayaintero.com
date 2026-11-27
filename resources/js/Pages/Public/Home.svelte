<script module>
    export { default as layout } from '../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link } from '@inertiajs/svelte';
    import ProgramCard from '../../Components/Public/ProgramCard.svelte';
    import PostCard from '../../Components/Public/PostCard.svelte';
    import FaqList from '../../Components/Public/FaqList.svelte';

    let { hero, aboutHtml, programs, faqs, gallery, posts } = $props();

    const steps = [
        { title: 'Isi form lamaran', body: 'Lengkapi data diri, pendidikan, dan unggah CV serta surat pengantar.' },
        { title: 'Seleksi berkas', body: 'Tim Jaya Intero memeriksa data dan berkas yang kamu kirim.' },
        { title: 'Wawancara', body: 'Jika lolos berkas, jadwal wawancara dikirim lewat email.' },
        { title: 'Mulai magang', body: 'Peserta yang diterima mendapat email penerimaan dan jadwal mulai.' },
    ];
</script>

<svelte:head>
    <title>Program Magang Jaya Intero</title>
    <meta
        name="description"
        content="Info dan pendaftaran magang Jaya Intero untuk siswa SMK dan mahasiswa: renovasi, interior, dan material bangunan di Medan."
    />
</svelte:head>

<section class="bg-brand-gradient text-white">
    <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 md:grid-cols-[1.1fr_1fr] md:py-24">
        <div>
            <p class="label-tag text-white/80">#MagangJayaIntero</p>
            <h1 class="mt-4 text-4xl leading-[1.05] font-black uppercase sm:text-5xl lg:text-6xl">{hero.title}</h1>
            {#if hero.intro}
                <p class="mt-6 max-w-lg text-base leading-relaxed text-white/90 sm:text-lg">{hero.intro}</p>
            {/if}
            <div class="mt-8 flex flex-wrap gap-3">
                <Link href="/program" class="btn-accent">Lihat program magang</Link>
                <a href="#alur" class="btn-ghost-light">Cara mendaftar</a>
            </div>
        </div>

        <div
            class="flex aspect-[4/3] items-center justify-center overflow-hidden rounded-card border-4 border-white/90 bg-white/10 shadow-[12px_12px_0_0_rgb(45_85_146_/_0.6)]"
        >
            {#if gallery.length > 0}
                <img src={gallery[0].image_url} alt={gallery[0].caption} class="size-full object-cover" />
            {:else}
                <span class="label-tag px-6 text-center text-white/80">Foto: tim Jaya Intero di lokasi proyek</span>
            {/if}
        </div>
    </div>
</section>

<section id="program" class="scroll-mt-16 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="label-tag text-primary">#ProgramMagang</p>
                <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">Program yang sedang buka</h2>
            </div>
            <Link href="/program" class="text-sm font-bold text-primary hover:underline">Semua program →</Link>
        </div>

        {#if programs.length > 0}
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {#each programs as program (program.slug)}
                    <ProgramCard {program} />
                {/each}
            </div>
        {:else}
            <p class="mt-8 rounded-card border border-dashed border-line p-8 text-center text-muted">
                Belum ada program yang buka saat ini. Pantau halaman ini atau tanyakan lewat WhatsApp di bagian kontak.
            </p>
        {/if}
    </div>
</section>

<section id="alur" class="scroll-mt-16 bg-primary text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
        <p class="label-tag text-white/75">#AlurPendaftaran</p>
        <h2 class="mt-3 text-3xl font-extrabold uppercase sm:text-4xl">
            <span class="text-accent">4 langkah</span> jadi peserta magang
        </h2>

        <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {#each steps as step, index (step.title)}
                <li class="rounded-card border border-white/30 bg-white/8 p-6">
                    <span class="block text-6xl leading-none font-black text-accent [text-shadow:-4px_4px_0_var(--color-primary-dark)]" aria-hidden="true">
                        {index + 1}
                    </span>
                    <h3 class="mt-6 text-lg font-bold">{step.title}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/80">{step.body}</p>
                </li>
            {/each}
        </ol>
    </div>
</section>

{#if aboutHtml}
    <section class="bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 md:grid-cols-[1fr_1.4fr] md:py-20">
            <div>
                <p class="label-tag text-primary">#KenapaJayaIntero</p>
                <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">Belajar dari proyek sungguhan</h2>
                <Link href="/tentang" class="mt-6 inline-block text-sm font-bold text-primary hover:underline">Tentang magang →</Link>
            </div>
            <div class="rich-text">{@html aboutHtml}</div>
        </div>
    </section>
{/if}

{#if gallery.length > 1}
    <section class="bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="label-tag text-primary">#Galeri</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">Kegiatan magang</h2>
                </div>
                <Link href="/galeri" class="text-sm font-bold text-primary hover:underline">Lihat galeri →</Link>
            </div>
            <ul class="mt-10 grid grid-cols-2 gap-3 md:grid-cols-3">
                {#each gallery as item (item.id)}
                    <li class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-sm">
                        <img src={item.image_url} alt={item.caption} class="aspect-square w-full object-cover" loading="lazy" />
                    </li>
                {/each}
            </ul>
        </div>
    </section>
{/if}

{#if posts.length > 0}
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="label-tag text-primary">#Artikel</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">Kabar terbaru</h2>
                </div>
                <Link href="/artikel" class="text-sm font-bold text-primary hover:underline">Semua artikel →</Link>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                {#each posts as post (post.slug)}
                    <PostCard {post} />
                {/each}
            </div>
        </div>
    </section>
{/if}

{#if faqs.length > 0}
    <section class="bg-surface">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 md:grid-cols-[1fr_1.6fr] md:py-20">
            <div>
                <p class="label-tag text-primary">#FAQ</p>
                <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">Yang sering ditanyakan</h2>
                <Link href="/faq" class="mt-6 inline-block text-sm font-bold text-primary hover:underline">Semua pertanyaan →</Link>
            </div>
            <FaqList {faqs} />
        </div>
    </section>
{/if}
