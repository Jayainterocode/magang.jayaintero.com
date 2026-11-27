<script module>
    export { default as layout } from '../../Layouts/AdminLayout.svelte';
</script>

<script>
    import { page } from '@inertiajs/svelte';

    const user = $derived(page.props.auth.user);
</script>

<svelte:head>
    <title>Dashboard · Admin Magang Jaya Intero</title>
</svelte:head>

<p class="label-tag text-primary">Dashboard</p>
<h1 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">Halo, {user.name}</h1>

<div class="mt-8 rounded-card border border-dashed border-line bg-white p-8 text-center sm:p-12">
    <p class="text-lg font-bold text-primary">Belum ada lamaran masuk</p>
    <p class="mx-auto mt-2 max-w-md text-sm text-muted">
        Statistik lamaran akan tampil di sini setelah form pendaftaran online dibuka.
    </p>
</div>
