<script module>
    export { default as layout } from '../../../Layouts/PublicLayout.svelte';
</script>

<script>
    import { Link, page, useForm } from '@inertiajs/svelte';
    import PageHero from '../../../Components/Public/PageHero.svelte';
    import Field from '../../../Components/Admin/Field.svelte';

    let { programs, selectedProgramId, educationLevels, genders } = $props();

    const steps = [
        { title: 'Program', fields: ['program_id', 'start_date', 'end_date', 'motivation'] },
        { title: 'Data diri', fields: ['full_name', 'email', 'phone', 'birth_date', 'gender', 'address'] },
        {
            title: 'Pendidikan',
            fields: ['education_level', 'institution', 'major', 'semester', 'student_number', 'supervisor_name', 'supervisor_phone'],
        },
        { title: 'Berkas', fields: ['cv', 'cover_letter', 'photo', 'portfolio', 'consent', 'website'] },
    ];

    const documents = [
        { field: 'cv', label: 'CV', accept: 'application/pdf', hint: 'PDF, maks 2 MB.', required: true },
        { field: 'cover_letter', label: 'Surat pengantar sekolah/kampus', accept: 'application/pdf', hint: 'PDF, maks 2 MB.', required: true },
        { field: 'photo', label: 'Pas foto', accept: 'image/jpeg,image/png', hint: 'JPG atau PNG, maks 1 MB.', required: true },
        { field: 'portfolio', label: 'Portofolio (opsional)', accept: 'application/pdf', hint: 'PDF, maks 5 MB.', required: false },
    ];

    const form = useForm({
        program_id: selectedProgramId ?? (programs.length === 1 ? programs[0].id : null),
        start_date: '',
        end_date: '',
        motivation: '',
        full_name: '',
        email: '',
        phone: '',
        birth_date: '',
        gender: '',
        address: '',
        education_level: '',
        institution: '',
        major: '',
        semester: '',
        student_number: '',
        supervisor_name: '',
        supervisor_phone: '',
        cv: null,
        cover_letter: null,
        photo: null,
        portfolio: null,
        consent: false,
        website: '',
    });

    let currentStep = $state(0);

    /** @type {HTMLFieldSetElement[]} */
    const stepElements = [];

    const today = new Date().toISOString().slice(0, 10);

    const selectedProgram = $derived(programs.find((program) => program.id === Number(form.program_id)) ?? null);

    const allowedLevels = $derived(
        selectedProgram ? educationLevels.filter((level) => selectedProgram.education_levels.includes(level.value)) : educationLevels,
    );

    const formError = $derived(page.props.errors?.form ?? form.errors.form);

    /** Uses the browser's own validation for the fields of one step. */
    function isStepValid(stepIndex) {
        const fields = stepElements[stepIndex]?.querySelectorAll('input, select, textarea') ?? [];

        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();

                return false;
            }
        }

        return true;
    }

    function goToStep(stepIndex) {
        currentStep = stepIndex;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function next() {
        if (isStepValid(currentStep)) {
            goToStep(currentStep + 1);
        }
    }

    function submit(event) {
        event.preventDefault();

        if (!isStepValid(currentStep)) {
            return;
        }

        form.post('/daftar', {
            preserveScroll: (page) => Object.keys(page.props.errors ?? {}).length > 0,
            onError: (errors) => {
                const firstStepWithError = steps.findIndex((step) => step.fields.some((field) => errors[field]));

                if (firstStepWithError !== -1) {
                    goToStep(firstStepWithError);
                }
            },
        });
    }

    function stepHasError(stepIndex) {
        return steps[stepIndex].fields.some((field) => form.errors[field]);
    }
</script>

<svelte:head>
    <title>Daftar Magang · Magang Jaya Intero</title>
    <meta name="description" content="Form lamaran magang Jaya Intero untuk siswa SMK dan mahasiswa." />
</svelte:head>

<PageHero label="#DaftarMagang" title="Form lamaran magang" intro="Isi empat langkah singkat. Siapkan CV, surat pengantar, dan pas foto." />

<section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 md:py-16">
    {#if programs.length === 0}
        <div class="rounded-card border border-dashed border-line p-10 text-center">
            <p class="text-lg font-bold text-primary">Pendaftaran sedang tutup</p>
            <p class="mt-2 text-sm text-muted">Belum ada program yang menerima lamaran saat ini. Pantau halaman program atau tanyakan lewat WhatsApp.</p>
            <Link href="/program" class="btn-primary mt-6">Lihat program</Link>
        </div>
    {:else}
        <ol class="grid grid-cols-4 gap-2" aria-label="Langkah pendaftaran">
            {#each steps as step, index (step.title)}
                <li>
                    <button
                        type="button"
                        class="w-full text-left disabled:cursor-default"
                        disabled={index > currentStep}
                        onclick={() => goToStep(index)}
                        aria-current={index === currentStep ? 'step' : undefined}
                    >
                        <span
                            class="block h-1.5 rounded-full {stepHasError(index)
                                ? 'bg-red-500'
                                : index <= currentStep
                                  ? 'bg-primary'
                                  : 'bg-line'}"
                        ></span>
                        <span class="label-tag mt-2 block text-[11px] {index === currentStep ? 'text-primary' : 'text-muted'}">
                            {index + 1}. {step.title}
                        </span>
                    </button>
                </li>
            {/each}
        </ol>

        {#if formError}
            <p class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700" role="alert">{formError}</p>
        {/if}

        <form onsubmit={submit} novalidate class="mt-8 rounded-card border border-line bg-white p-6 sm:p-8">
            <fieldset bind:this={stepElements[0]} class="space-y-6" hidden={currentStep !== 0}>
                <legend class="text-xl font-extrabold text-primary uppercase">Pilih program</legend>

                <div class="space-y-3" role="radiogroup" aria-label="Program magang">
                    {#each programs as program (program.id)}
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition {Number(form.program_id) === program.id
                                ? 'border-primary bg-primary/5'
                                : 'border-line hover:border-primary/50'}"
                        >
                            <input type="radio" name="program_id" value={program.id} bind:group={form.program_id} required class="mt-1" />
                            <span>
                                <span class="block font-bold text-ink">{program.title}</span>
                                <span class="text-sm text-muted">{program.division} · {program.duration_months} bulan</span>
                            </span>
                        </label>
                    {/each}
                </div>
                {#if form.errors.program_id}<p class="field-error">{form.errors.program_id}</p>{/if}

                <div class="grid gap-6 sm:grid-cols-2">
                    <Field label="Rencana mulai" id="start_date" error={form.errors.start_date}>
                        <input id="start_date" type="date" min={today} bind:value={form.start_date} class="field-input" required />
                    </Field>
                    <Field label="Rencana selesai" id="end_date" error={form.errors.end_date}>
                        <input id="end_date" type="date" min={form.start_date || today} bind:value={form.end_date} class="field-input" required />
                    </Field>
                </div>

                <Field label="Kenapa kamu ingin magang di program ini?" id="motivation" error={form.errors.motivation} hint="Minimal 30 karakter.">
                    <textarea id="motivation" rows="5" bind:value={form.motivation} class="field-input" required minlength="30" maxlength="2000"></textarea>
                </Field>
            </fieldset>

            <fieldset bind:this={stepElements[1]} class="space-y-6" hidden={currentStep !== 1}>
                <legend class="text-xl font-extrabold text-primary uppercase">Data diri</legend>

                <Field label="Nama lengkap" id="full_name" error={form.errors.full_name}>
                    <input id="full_name" autocomplete="name" bind:value={form.full_name} class="field-input" required maxlength="150" />
                </Field>
                <div class="grid gap-6 sm:grid-cols-2">
                    <Field label="Email" id="email" error={form.errors.email} hint="Kode lamaran dan kabar seleksi dikirim ke sini.">
                        <input id="email" type="email" autocomplete="email" bind:value={form.email} class="field-input" required maxlength="150" />
                    </Field>
                    <Field label="Nomor WhatsApp" id="phone" error={form.errors.phone}>
                        <input id="phone" type="tel" autocomplete="tel" bind:value={form.phone} class="field-input" required maxlength="30" />
                    </Field>
                    <Field label="Tanggal lahir" id="birth_date" error={form.errors.birth_date}>
                        <input id="birth_date" type="date" max={today} autocomplete="bday" bind:value={form.birth_date} class="field-input" required />
                    </Field>
                    <Field label="Jenis kelamin" id="gender" error={form.errors.gender}>
                        <select id="gender" bind:value={form.gender} class="field-input" required>
                            <option value="" disabled>Pilih…</option>
                            {#each genders as gender (gender.value)}
                                <option value={gender.value}>{gender.label}</option>
                            {/each}
                        </select>
                    </Field>
                </div>
                <Field label="Alamat domisili" id="address" error={form.errors.address}>
                    <textarea id="address" rows="2" autocomplete="street-address" bind:value={form.address} class="field-input" required maxlength="500"></textarea>
                </Field>
            </fieldset>

            <fieldset bind:this={stepElements[2]} class="space-y-6" hidden={currentStep !== 2}>
                <legend class="text-xl font-extrabold text-primary uppercase">Pendidikan</legend>

                <div class="grid gap-6 sm:grid-cols-2">
                    <Field label="Jenjang" id="education_level" error={form.errors.education_level}>
                        <select id="education_level" bind:value={form.education_level} class="field-input" required>
                            <option value="" disabled>Pilih…</option>
                            {#each allowedLevels as level (level.value)}
                                <option value={level.value}>{level.label}</option>
                            {/each}
                        </select>
                    </Field>
                    <Field label="Nama sekolah/kampus" id="institution" error={form.errors.institution}>
                        <input id="institution" bind:value={form.institution} class="field-input" required maxlength="150" />
                    </Field>
                    <Field label="Jurusan" id="major" error={form.errors.major}>
                        <input id="major" bind:value={form.major} class="field-input" required maxlength="150" />
                    </Field>
                    <Field label="Kelas/semester" id="semester" error={form.errors.semester} hint="Contoh: Kelas XI atau Semester 5">
                        <input id="semester" bind:value={form.semester} class="field-input" required maxlength="30" />
                    </Field>
                    <Field label="NIS/NIM" id="student_number" error={form.errors.student_number}>
                        <input id="student_number" bind:value={form.student_number} class="field-input" required maxlength="50" />
                    </Field>
                </div>

                <div class="rounded-xl bg-surface p-5">
                    <p class="text-sm font-bold text-ink">Guru/dosen pembimbing (opsional)</p>
                    <div class="mt-4 grid gap-6 sm:grid-cols-2">
                        <Field label="Nama pembimbing" id="supervisor_name" error={form.errors.supervisor_name}>
                            <input id="supervisor_name" bind:value={form.supervisor_name} class="field-input" maxlength="150" />
                        </Field>
                        <Field label="Kontak pembimbing" id="supervisor_phone" error={form.errors.supervisor_phone}>
                            <input id="supervisor_phone" type="tel" bind:value={form.supervisor_phone} class="field-input" maxlength="30" />
                        </Field>
                    </div>
                </div>
            </fieldset>

            <fieldset bind:this={stepElements[3]} class="space-y-6" hidden={currentStep !== 3}>
                <legend class="text-xl font-extrabold text-primary uppercase">Berkas</legend>

                {#each documents as document (document.field)}
                    <Field label={document.label} id={document.field} error={form.errors[document.field]} hint={document.hint}>
                        <input
                            id={document.field}
                            type="file"
                            accept={document.accept}
                            required={document.required}
                            class="field-input py-2.5"
                            onchange={(event) => (form[document.field] = event.currentTarget.files?.[0] ?? null)}
                        />
                    </Field>
                {/each}

                <!-- Honeypot for spam bots; hidden from people and screen readers. -->
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="website">Website</label>
                    <input id="website" tabindex="-1" autocomplete="off" bind:value={form.website} />
                </div>

                <label class="flex items-start gap-3 rounded-xl bg-surface px-4 py-3 text-sm">
                    <input type="checkbox" bind:checked={form.consent} required class="mt-0.5 size-4 rounded border-line" />
                    <span>
                        Saya setuju data dan berkas ini dipakai Jaya Intero untuk keperluan seleksi magang, dan menyatakan data yang saya isi benar.
                    </span>
                </label>
                {#if form.errors.consent}<p class="field-error">{form.errors.consent}</p>{/if}

                {#if form.progress}
                    <progress value={form.progress.percentage} max="100" class="w-full">{form.progress.percentage}%</progress>
                {/if}
            </fieldset>

            <div class="mt-8 flex items-center justify-between gap-3 border-t border-line pt-6">
                {#if currentStep > 0}
                    <button type="button" class="btn border border-line text-ink hover:border-primary" onclick={() => goToStep(currentStep - 1)}>
                        Kembali
                    </button>
                {:else}
                    <span></span>
                {/if}

                {#if currentStep < steps.length - 1}
                    <button type="button" class="btn-primary" onclick={next}>Lanjut</button>
                {:else}
                    <button type="submit" class="btn-primary" disabled={form.processing}>
                        {form.processing ? 'Mengirim…' : 'Kirim lamaran'}
                    </button>
                {/if}
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-muted">
            Sudah pernah melamar? <Link href="/cek-status" class="font-semibold text-primary hover:underline">Cek status lamaran</Link>
        </p>
    {/if}
</section>
