<script>
    import { router } from '@inertiajs/svelte';

    /** @type {{ href: string, confirmMessage: string }} */
    let { href, confirmMessage } = $props();

    let isDeleting = $state(false);

    function destroy() {
        if (!window.confirm(confirmMessage)) {
            return;
        }

        router.delete(href, {
            preserveScroll: true,
            onStart: () => (isDeleting = true),
            onFinish: () => (isDeleting = false),
        });
    }
</script>

<button
    type="button"
    class="rounded-lg px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-50 disabled:opacity-50"
    disabled={isDeleting}
    onclick={destroy}
>
    Hapus
</button>
