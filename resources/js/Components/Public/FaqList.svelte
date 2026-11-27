<script>
    let { faqs } = $props();
</script>

<div class="divide-y divide-line rounded-card border border-line bg-white">
    {#each faqs as faq (faq.id)}
        <details class="group px-6 py-5">
            <summary class="flex cursor-pointer list-none items-start justify-between gap-4 font-bold text-ink marker:hidden">
                {faq.question}
                <span class="mt-0.5 text-xl leading-none text-primary transition group-open:rotate-45" aria-hidden="true">+</span>
            </summary>
            <div class="rich-text mt-3 text-sm text-muted">{@html faq.answer_html}</div>
        </details>
    {/each}
</div>
