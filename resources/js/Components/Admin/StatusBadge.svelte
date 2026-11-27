<script>
    /** @type {{ tone?: 'success' | 'muted' | 'accent', children: import('svelte').Snippet }} */
    let { tone = 'muted', children } = $props();

    const tones = {
        success: 'bg-secondary/15 text-primary-dark',
        accent: 'bg-accent text-primary-dark',
        muted: 'bg-line/70 text-muted',
    };
</script>

<span class="label-tag inline-flex rounded-md px-2 py-1 text-[10px] {tones[tone]}">
    {@render children()}
</span>
