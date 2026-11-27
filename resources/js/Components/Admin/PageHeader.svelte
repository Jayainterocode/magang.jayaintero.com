<script>
    /** @type {{ label: string, title: string, description?: string, actions?: import('svelte').Snippet }} */
    let { label, title, description = '', actions } = $props();
</script>

<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="label-tag text-primary">{label}</p>
        <h1 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">{title}</h1>
        {#if description}
            <p class="mt-1 text-sm text-muted">{description}</p>
        {/if}
    </div>
    {#if actions}
        <div class="flex flex-wrap gap-2">{@render actions()}</div>
    {/if}
</div>
