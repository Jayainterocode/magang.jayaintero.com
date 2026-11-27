<script module>
    export { default as layout } from '../../Layouts/PublicLayout.svelte';
</script>

<script>
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
            <h1 class="mt-4 text-4xl leading-[1.05] font-black uppercase sm:text-5xl lg:text-6xl">
                Magang di proyek <span class="text-accent">nyata</span>
            </h1>
            <p class="mt-6 max-w-lg text-base leading-relaxed text-white/90 sm:text-lg">
                Untuk siswa SMK dan mahasiswa yang ingin belajar langsung di dunia renovasi, interior, dan material bangunan bersama tim Jaya Intero.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#program" class="btn-accent">Lihat program magang</a>
                <a href="#alur" class="btn-ghost-light">Cara mendaftar</a>
            </div>
        </div>

        <div
            class="flex aspect-[4/3] items-center justify-center rounded-card border-4 border-white/90 bg-white/10 shadow-[12px_12px_0_0_rgb(45_85_146_/_0.6)]"
        >
            <span class="label-tag px-6 text-center text-white/80">Foto: tim Jaya Intero di lokasi proyek</span>
        </div>
    </div>
</section>

<section id="alur" class="scroll-mt-16 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
        <p class="label-tag text-primary">#AlurPendaftaran</p>
        <h2 class="mt-3 text-3xl font-extrabold text-primary uppercase sm:text-4xl">4 langkah jadi peserta magang</h2>

        <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {#each steps as step, index (step.title)}
                <li class="rounded-card border border-line bg-white p-6">
                    <span
                        class="block text-6xl leading-none font-black text-accent [text-shadow:-4px_4px_0_var(--color-primary)]"
                        aria-hidden="true"
                    >
                        {index + 1}
                    </span>
                    <h3 class="mt-6 text-lg font-bold text-primary">{step.title}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{step.body}</p>
                </li>
            {/each}
        </ol>
    </div>
</section>

<section id="program" class="scroll-mt-16 bg-primary text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-20">
        <p class="label-tag text-white/75">#ProgramMagang</p>
        <h2 class="mt-3 text-3xl font-extrabold uppercase sm:text-4xl">
            Program <span class="text-accent">segera dibuka</span>
        </h2>
        <p class="mt-4 max-w-2xl leading-relaxed text-white/85">
            Daftar program, kuota, dan form lamaran online sedang disiapkan. Sementara itu, tanyakan info magang lewat WhatsApp di bagian kontak.
        </p>
    </div>
</section>
