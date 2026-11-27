<script module>
    export { default as layout } from '../../Layouts/PublicLayout.svelte';
</script>

<script>
    import PageHero from '../../Components/Public/PageHero.svelte';
    import FaqList from '../../Components/Public/FaqList.svelte';

    let { faqs } = $props();

    let search = $state('');

    const filteredFaqs = $derived.by(() => {
        const term = search.trim().toLowerCase();

        return term ? faqs.filter((faq) => faq.question.toLowerCase().includes(term)) : faqs;
    });

    const groups = $derived.by(() => {
        const byCategory = new Map();

        for (const faq of filteredFaqs) {
            byCategory.set(faq.category, [...(byCategory.get(faq.category) ?? []), faq]);
        }

        return [...byCategory.entries()];
    });
</script>

<svelte:head>
    <title>FAQ · Magang Jaya Intero</title>
    <meta name="description" content="Pertanyaan yang sering diajukan tentang magang di Jaya Intero." />
</svelte:head>

<PageHero label="#FAQ" title="Yang sering ditanyakan" />

<section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 md:py-16">
    {#if faqs.length === 0}
        <p class="rounded-card border border-dashed border-line p-10 text-center text-muted">Belum ada FAQ.</p>
    {:else}
        <label for="faq-search" class="sr-only">Cari pertanyaan</label>
        <input id="faq-search" type="search" bind:value={search} placeholder="Cari pertanyaan…" class="field-input" />

        {#each groups as [category, items] (category)}
            <h2 class="label-tag mt-10 mb-4 text-primary">{category}</h2>
            <FaqList faqs={items} />
        {:else}
            <p class="mt-8 text-center text-muted">Tidak ada pertanyaan yang cocok.</p>
        {/each}
    {/if}
</section>
