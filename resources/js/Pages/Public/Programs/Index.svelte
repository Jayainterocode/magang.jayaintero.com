<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import PageHero from '../../../Components/Public/PageHero.svelte';
    import ProgramCard from '../../../Components/Public/ProgramCard.svelte';

    let { programs } = $props();

    let selectedLevel = $state('Semua');

    const levels = $derived(['Semua', ...new Set(programs.flatMap((program) => program.education_levels))]);

    const visiblePrograms = $derived(
        selectedLevel === 'Semua' ? programs : programs.filter((program) => program.education_levels.includes(selectedLevel)),
    );
</script>

<svelte:head>
    <title>Program Magang · Magang Jaya Intero</title>
    <meta name="description" content="Daftar program magang Jaya Intero untuk siswa SMK dan mahasiswa." />
</svelte:head>

<PageHero label="#ProgramMagang" title="Program magang" intro="Pilih program sesuai jurusan dan jenjang pendidikanmu." />

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 md:py-16">
    {#if programs.length === 0}
        <p class="rounded-card border border-dashed border-line p-10 text-center text-muted">
            Belum ada program yang dipublikasikan. Tanyakan info magang lewat WhatsApp di bagian kontak.
        </p>
    {:else}
        <div class="flex flex-wrap gap-2" role="group" aria-label="Saring jenjang">
            {#each levels as level (level)}
                <button
                    type="button"
                    class="rounded-full border px-4 py-2 text-sm font-bold transition {selectedLevel === level
                        ? 'border-primary bg-primary text-white'
                        : 'border-line text-ink hover:border-primary'}"
                    aria-pressed={selectedLevel === level}
                    onclick={() => (selectedLevel = level)}
                >
                    {level}
                </button>
            {/each}
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {#each visiblePrograms as program (program.slug)}
                <ProgramCard {program} />
            {/each}
        </div>
    {/if}
</section>
