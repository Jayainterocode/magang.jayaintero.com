<script>
    /** @type {{ status: string, label: string }} */
    let { status, label } = $props();

    const tones = {
        submitted: 'bg-accent text-primary-dark',
        screening: 'bg-secondary/15 text-primary-dark',
        interview: 'bg-primary/15 text-primary-dark',
        accepted: 'bg-primary text-white',
        rejected: 'bg-line text-muted',
    };
</script>

<span class="label-tag inline-flex rounded-md px-2 py-1 text-[10px] whitespace-nowrap {tones[status] ?? tones.rejected}">{label}</span>
