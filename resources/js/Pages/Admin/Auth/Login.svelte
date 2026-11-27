<script>
    import { Form, Link, page } from '@inertiajs/svelte';
    import BrandMark from '../../../Components/BrandMark.svelte';
</script>

<svelte:head>
    <title>Masuk Admin · Magang Jaya Intero</title>
    <meta name="robots" content="noindex" />
</svelte:head>

<div class="grid min-h-screen lg:grid-cols-2">
    <div class="bg-brand-gradient hidden flex-col justify-between p-12 text-white lg:flex">
        <BrandMark tone="light" subtitle="Admin Magang" />
        <div>
            <h1 class="text-5xl leading-[1.05] font-black uppercase">
                Kelola magang <span class="text-accent">rapi</span> dalam satu tempat
            </h1>
            <p class="mt-6 max-w-md text-white/85">Konten, program, dan lamaran peserta magang Jaya Intero.</p>
        </div>
        <Link href="/" class="text-sm font-semibold text-white/80 hover:text-white">← Kembali ke website</Link>
    </div>

    <div class="flex items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-sm">
            <div class="lg:hidden">
                <BrandMark subtitle="Admin Magang" />
            </div>

            <h2 class="mt-8 text-2xl font-extrabold text-primary uppercase lg:mt-0">Masuk</h2>
            <p class="mt-2 text-sm text-muted">Khusus tim Jaya Intero.</p>

            {#if page.props.flash.status}
                <p class="mt-6 rounded-xl bg-secondary/10 px-4 py-3 text-sm font-medium text-primary-dark" role="status">
                    {page.props.flash.status}
                </p>
            {/if}

            <Form action="/admin/login" method="post" resetOnError={['password']} class="mt-8 space-y-5">
                {#snippet children({ errors, processing })}
                    <div>
                        <label for="email" class="field-label">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="username"
                            required
                            class="field-input"
                            aria-invalid={errors.email ? 'true' : undefined}
                            aria-describedby={errors.email ? 'email-error' : undefined}
                        />
                        {#if errors.email}
                            <p id="email-error" class="field-error">{errors.email}</p>
                        {/if}
                    </div>

                    <div>
                        <label for="password" class="field-label">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="field-input"
                        />
                        {#if errors.password}
                            <p class="field-error">{errors.password}</p>
                        {/if}
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-line text-primary" />
                        Ingat saya
                    </label>

                    <button type="submit" class="btn-primary w-full" disabled={processing}>
                        {processing ? 'Memproses…' : 'Masuk'}
                    </button>
                {/snippet}
            </Form>
        </div>
    </div>
</div>
