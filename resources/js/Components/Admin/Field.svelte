<script>
    /** @type {{ label: string, id: string, error?: string, hint?: string, children: import('svelte').Snippet }} */
    let { label, id, error = '', hint = '', children } = $props();
</script>

<div>
    <label for={id} class="field-label">{label}</label>
    {@render children()}
    {#if hint && !error}
        <p class="mt-1.5 text-xs text-muted">{hint}</p>
    {/if}
    {#if error}
        <p id="{id}-error" class="field-error">{error}</p>
    {/if}
</div>
