import { Head, router, useForm, useHttp } from '@inertiajs/react';
import {
    Bot,
    ChevronRight,
    Download,
    Eye,
    Globe2,
    ImageIcon,
    Mail,
    Phone,
    QrCode,
    Save,
    Send,
    Sparkles,
    UserRound,
} from 'lucide-react';
import { FormEvent, useEffect, useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import { store as generateProposal } from '@/routes/vcard-ai/proposals';
import { create, edit, metrics, store, update } from '@/routes/vcards';
import {
    destroy as unpublish,
    store as publish,
} from '@/routes/vcards/publication';
import type { VCard, VCardLinkType } from '@/types';

type LinkField = {
    type: VCardLinkType;
    label: string;
    url: string;
    is_active: boolean;
};

type VCardForm = {
    title: string;
    public_name: string;
    organization: string;
    job_title: string;
    phone: string;
    public_email: string;
    website: string;
    description: string;
    accent_color: '#000000';
    photo: File | null;
    cover: File | null;
    logo: File | null;
    remove_photo: boolean;
    remove_cover: boolean;
    remove_logo: boolean;
    links: LinkField[];
    _method?: 'put';
};

type AiForm = {
    prompt: string;
    current: {
        title: string;
        public_name: string;
        organization: string;
        job_title: string;
        phone: string;
        public_email: string;
        website: string;
        description: string;
    };
};

type AiResponse = {
    proposal: Omit<
        VCardForm,
        | 'photo'
        | 'cover'
        | 'logo'
        | 'remove_photo'
        | 'remove_cover'
        | 'remove_logo'
        | 'accent_color'
        | '_method'
    >;
};

const linkOptions: Array<{ type: VCardLinkType; label: string }> = [
    { type: 'facebook', label: 'Facebook' },
    { type: 'instagram', label: 'Instagram' },
    { type: 'linkedin', label: 'LinkedIn' },
    { type: 'whatsapp', label: 'WhatsApp' },
    { type: 'tiktok', label: 'TikTok' },
    { type: 'youtube', label: 'YouTube' },
    { type: 'x', label: 'X' },
    { type: 'github', label: 'GitHub' },
];

function initialLinks(vCard: VCard | null): LinkField[] {
    return linkOptions.map((option) => {
        const existing = vCard?.links.find((link) => link.type === option.type);

        return {
            ...option,
            url: existing?.url ?? '',
            is_active: existing?.is_active ?? true,
        };
    });
}

function useObjectUrl(file: File | null): string | null {
    const [url, setUrl] = useState<string | null>(null);

    useEffect(() => {
        if (!file) {
            setUrl(null);
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        setUrl(objectUrl);

        return () => URL.revokeObjectURL(objectUrl);
    }, [file]);

    return url;
}

function Field({
    id,
    label,
    error,
    hint,
    children,
}: {
    id: string;
    label: string;
    error?: string;
    hint?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="space-y-2">
            <div className="flex items-end justify-between gap-3">
                <Label htmlFor={id}>{label}</Label>
                {hint && <span className="text-xs text-zinc-400">{hint}</span>}
            </div>
            {children}
            <InputError message={error} />
        </div>
    );
}

function Preview({
    data,
    photoUrl,
    coverUrl,
    logoUrl,
}: {
    data: VCardForm;
    photoUrl: string | null;
    coverUrl: string | null;
    logoUrl: string | null;
}) {
    const activeLinks = data.links.filter((link) => link.url && link.is_active);

    return (
        <div className="mx-auto w-full max-w-[390px] overflow-hidden rounded-[2.25rem] border border-zinc-200 bg-white shadow-[0_24px_80px_rgba(0,0,0,.14)]">
            <div className="relative h-40 overflow-hidden bg-zinc-950">
                {coverUrl ? (
                    <img
                        src={coverUrl}
                        alt=""
                        className="size-full object-cover"
                    />
                ) : (
                    <div className="size-full bg-[radial-gradient(circle_at_22%_18%,#71717a,transparent_28%),linear-gradient(140deg,#09090b,#3f3f46)]" />
                )}
                <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />
                <img
                    src="/images/qrbita-logo.png"
                    alt=""
                    className="absolute top-4 right-4 size-10 rounded-lg object-contain"
                />
            </div>
            <div className="relative px-6 pb-7 text-center">
                <div className="mx-auto -mt-16 flex size-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-black text-3xl font-semibold text-white">
                    {photoUrl ? (
                        <img
                            src={photoUrl}
                            alt=""
                            className="size-full object-cover"
                        />
                    ) : (
                        (data.public_name || 'TU').slice(0, 2).toUpperCase()
                    )}
                </div>
                {logoUrl && (
                    <img
                        src={logoUrl}
                        alt=""
                        className="mx-auto mt-4 max-h-10 max-w-28 object-contain"
                    />
                )}
                <h2 className="mt-5 text-2xl font-semibold tracking-tight text-black">
                    {data.public_name || 'Tu nombre'}
                </h2>
                <p className="mt-1 text-sm text-zinc-500">
                    {[data.job_title, data.organization]
                        .filter(Boolean)
                        .join(' · ') || 'Tu especialidad · Tu organización'}
                </p>
                <p className="mx-auto mt-4 min-h-12 max-w-xs text-sm leading-6 text-zinc-600">
                    {data.description ||
                        'Una descripción breve ayuda a que las personas recuerden lo que haces.'}
                </p>
                <div className="mt-6 grid gap-2">
                    <div className="rounded-full bg-black py-3.5 text-sm font-semibold text-white">
                        Guardar contacto
                    </div>
                    <div className="grid grid-cols-3 gap-2">
                        {data.phone && (
                            <span className="flex flex-col items-center gap-1 rounded-2xl bg-zinc-100 p-3 text-[11px] font-medium">
                                <Phone className="size-4" /> Llamar
                            </span>
                        )}
                        {data.public_email && (
                            <span className="flex flex-col items-center gap-1 rounded-2xl bg-zinc-100 p-3 text-[11px] font-medium">
                                <Mail className="size-4" /> Correo
                            </span>
                        )}
                        {data.website && (
                            <span className="flex flex-col items-center gap-1 rounded-2xl bg-zinc-100 p-3 text-[11px] font-medium">
                                <Globe2 className="size-4" /> Web
                            </span>
                        )}
                    </div>
                    {activeLinks.length > 0 && (
                        <div className="mt-2 flex flex-wrap justify-center gap-2">
                            {activeLinks.map((link) => (
                                <span
                                    key={link.type}
                                    className="rounded-full border border-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-700"
                                >
                                    {link.label}
                                </span>
                            ))}
                        </div>
                    )}
                </div>
                <p className="mt-6 text-[10px] font-semibold tracking-[.18em] text-zinc-400 uppercase">
                    Hecho en Qrbita
                </p>
            </div>
        </div>
    );
}

export default function VCardEditor({
    vCard,
    aiConfigured,
}: {
    vCard: VCard | null;
    aiConfigured: boolean;
}) {
    const form = useForm<VCardForm>({
        title: vCard?.title ?? 'Mi vCard',
        public_name: vCard?.public_name ?? '',
        organization: vCard?.organization ?? '',
        job_title: vCard?.job_title ?? '',
        phone: vCard?.phone ?? '',
        public_email: vCard?.public_email ?? '',
        website: vCard?.website ?? '',
        description: vCard?.description ?? '',
        accent_color: '#000000',
        photo: null,
        cover: null,
        logo: null,
        remove_photo: false,
        remove_cover: false,
        remove_logo: false,
        links: initialLinks(vCard),
    });
    const aiForm = useHttp<AiForm, AiResponse>({
        prompt: '',
        current: {
            title: form.data.title,
            public_name: form.data.public_name,
            organization: form.data.organization,
            job_title: form.data.job_title,
            phone: form.data.phone,
            public_email: form.data.public_email,
            website: form.data.website,
            description: form.data.description,
        },
    });
    const [aiError, setAiError] = useState<string | null>(null);
    const photoPreview = useObjectUrl(form.data.photo);
    const coverPreview = useObjectUrl(form.data.cover);
    const logoPreview = useObjectUrl(form.data.logo);
    const visiblePhoto = form.data.remove_photo
        ? photoPreview
        : (photoPreview ?? vCard?.photo_url ?? null);
    const visibleCover = form.data.remove_cover
        ? coverPreview
        : (coverPreview ?? vCard?.cover_url ?? null);
    const visibleLogo = form.data.remove_logo
        ? logoPreview
        : (logoPreview ?? vCard?.logo_url ?? null);
    const errorBag = form.errors as Record<string, string | undefined>;
    const descriptionLength = form.data.description.length;

    const aiCurrent = useMemo(
        () => ({
            title: form.data.title,
            public_name: form.data.public_name,
            organization: form.data.organization,
            job_title: form.data.job_title,
            phone: form.data.phone,
            public_email: form.data.public_email,
            website: form.data.website,
            description: form.data.description,
        }),
        [form.data],
    );

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (vCard) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(update.url(vCard.slug), {
                forceFormData: true,
                preserveScroll: true,
            });
            return;
        }

        form.post(store.url(), { forceFormData: true, preserveScroll: true });
    };

    const askAi = async () => {
        setAiError(null);
        aiForm.transform((data) => ({ ...data, current: aiCurrent }));

        try {
            await aiForm.post(generateProposal.url(), {
                onSuccess: ({ proposal }) => {
                    const proposedLinks = initialLinks(null).map((link) => {
                        const proposalLink = proposal.links.find(
                            (item) => item.type === link.type,
                        );

                        return proposalLink
                            ? { ...link, ...proposalLink, is_active: true }
                            : link;
                    });

                    form.setData({
                        ...form.data,
                        title: proposal.title,
                        public_name: proposal.public_name,
                        organization: proposal.organization ?? '',
                        job_title: proposal.job_title ?? '',
                        phone: proposal.phone ?? '',
                        public_email: proposal.public_email ?? '',
                        website: proposal.website ?? '',
                        description: proposal.description ?? '',
                        links: proposedLinks,
                    });
                },
                onHttpException: (response) => {
                    const payload =
                        typeof response.data === 'string'
                            ? JSON.parse(response.data)
                            : response.data;
                    setAiError(
                        typeof payload.message === 'string'
                            ? payload.message
                            : 'No fue posible generar la propuesta.',
                    );
                },
            });
        } catch {
            setAiError(
                'La asistencia de IA no está disponible. Puedes continuar con el formulario manual.',
            );
        }
    };

    const updateLink = (
        index: number,
        key: keyof LinkField,
        value: string | boolean,
    ) => {
        form.setData(
            'links',
            form.data.links.map((link, itemIndex) =>
                itemIndex === index ? { ...link, [key]: value } : link,
            ),
        );
    };

    const togglePublication = () => {
        if (!vCard) return;

        if (vCard.status === 'published') {
            router.delete(unpublish.url(vCard.slug), { preserveScroll: true });
        } else {
            router.post(publish.url(vCard.slug), {}, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title={vCard ? 'Editar vCard' : 'Crear vCard'} />
            <form
                onSubmit={submit}
                className="mx-auto w-full max-w-[1500px] p-4 sm:p-6 lg:p-8"
            >
                <div className="mb-7 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <div className="flex items-center gap-2 text-xs font-semibold tracking-[.15em] text-zinc-500 uppercase">
                            <span>1. Contenido</span>
                            <ChevronRight className="size-3" />
                            <span>2. Revisión</span>
                            <ChevronRight className="size-3" />
                            <span>3. Publicación</span>
                        </div>
                        <h1 className="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                            {vCard ? 'Edita tu vCard' : 'Crea tu vCard'}
                        </h1>
                        <p className="mt-2 text-zinc-500">
                            Todo lo que escribas aparecerá en la vista previa.
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        {vCard?.status === 'published' && (
                            <Button
                                asChild
                                type="button"
                                variant="outline"
                                className="rounded-full"
                            >
                                <a
                                    href={vCard.public_url}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <Eye /> Ver pública
                                </a>
                            </Button>
                        )}
                        {vCard && (
                            <Button
                                type="button"
                                variant="outline"
                                className="rounded-full"
                                onClick={togglePublication}
                            >
                                <Send />
                                {vCard.status === 'published'
                                    ? 'Despublicar'
                                    : 'Publicar'}
                            </Button>
                        )}
                        <Button
                            disabled={form.processing}
                            className="rounded-full px-6"
                        >
                            <Save />{' '}
                            {form.processing ? 'Guardando…' : 'Guardar cambios'}
                        </Button>
                    </div>
                </div>

                <div className="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_430px]">
                    <div className="space-y-6">
                        <Card className="border-zinc-200 shadow-none">
                            <CardContent className="space-y-5">
                                <div className="flex items-start gap-4">
                                    <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-black text-white">
                                        <Bot className="size-5" />
                                    </span>
                                    <div>
                                        <h2 className="font-semibold">
                                            Crear o ajustar con IA
                                        </h2>
                                        <p className="mt-1 text-sm leading-6 text-zinc-500">
                                            Describe tu perfil o pide un cambio.
                                            La propuesta se aplica al formulario
                                            para que puedas revisarla.
                                        </p>
                                    </div>
                                </div>
                                <textarea
                                    value={aiForm.data.prompt}
                                    onChange={(event) =>
                                        aiForm.setData(
                                            'prompt',
                                            event.target.value,
                                        )
                                    }
                                    disabled={
                                        !aiConfigured || aiForm.processing
                                    }
                                    maxLength={2000}
                                    rows={4}
                                    placeholder="Ejemplo: Mi nombre es Sara López, soy fotógrafa profesional, trabajo en mi propio estudio de cine y quiero un tono profesional. Mi correo es…"
                                    className="w-full resize-y rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm leading-6 text-black transition outline-none focus:border-black focus:ring-2 focus:ring-black/10 disabled:bg-zinc-50"
                                />
                                <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                    <div>
                                        <InputError
                                            message={aiForm.errors.prompt}
                                        />
                                        {aiError && (
                                            <p className="text-sm text-amber-700">
                                                {aiError}
                                            </p>
                                        )}
                                        {!aiConfigured && (
                                            <p className="text-sm text-amber-700">
                                                La IA aún no está configurada;
                                                el formulario manual funciona
                                                con normalidad.
                                            </p>
                                        )}
                                    </div>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        disabled={
                                            !aiConfigured ||
                                            aiForm.processing ||
                                            aiForm.data.prompt.length < 20
                                        }
                                        onClick={askAi}
                                        className="rounded-full"
                                    >
                                        <Sparkles />{' '}
                                        {aiForm.processing
                                            ? 'Preparando…'
                                            : 'Generar propuesta'}
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-zinc-200 shadow-none">
                            <CardContent>
                                <div className="mb-6 flex items-center gap-3">
                                    <UserRound className="size-5" />
                                    <h2 className="text-lg font-semibold">
                                        Información principal
                                    </h2>
                                </div>
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field
                                        id="title"
                                        label="Nombre de mi vCard"
                                        error={form.errors.title}
                                        hint="Solo tú lo ves"
                                    >
                                        <Input
                                            id="title"
                                            value={form.data.title}
                                            onChange={(e) =>
                                                form.setData(
                                                    'title',
                                                    e.target.value,
                                                )
                                            }
                                            maxLength={120}
                                        />
                                    </Field>
                                    <Field
                                        id="public_name"
                                        label="Nombre completo *"
                                        error={form.errors.public_name}
                                    >
                                        <Input
                                            id="public_name"
                                            value={form.data.public_name}
                                            onChange={(e) =>
                                                form.setData(
                                                    'public_name',
                                                    e.target.value,
                                                )
                                            }
                                            maxLength={120}
                                            placeholder="Nombre y apellido"
                                        />
                                    </Field>
                                    <Field
                                        id="job_title"
                                        label="Puesto o especialidad"
                                        error={form.errors.job_title}
                                    >
                                        <Input
                                            id="job_title"
                                            value={form.data.job_title}
                                            onChange={(e) =>
                                                form.setData(
                                                    'job_title',
                                                    e.target.value,
                                                )
                                            }
                                            maxLength={120}
                                            placeholder="Desarrollador Full Stack"
                                        />
                                    </Field>
                                    <Field
                                        id="organization"
                                        label="Empresa u organización"
                                        error={form.errors.organization}
                                    >
                                        <Input
                                            id="organization"
                                            value={form.data.organization}
                                            onChange={(e) =>
                                                form.setData(
                                                    'organization',
                                                    e.target.value,
                                                )
                                            }
                                            maxLength={120}
                                            placeholder="Nombre de tu organización"
                                        />
                                    </Field>
                                    <div className="sm:col-span-2">
                                        <Field
                                            id="description"
                                            label="Descripción"
                                            error={form.errors.description}
                                            hint={`${descriptionLength}/280`}
                                        >
                                            <textarea
                                                id="description"
                                                value={form.data.description}
                                                onChange={(e) =>
                                                    form.setData(
                                                        'description',
                                                        e.target.value,
                                                    )
                                                }
                                                maxLength={280}
                                                rows={4}
                                                placeholder="En pocas palabras, ¿qué haces y qué servicios ofreces?"
                                                className="border-input focus-visible:border-ring focus-visible:ring-ring/50 w-full resize-y rounded-md border bg-transparent px-3 py-2 text-sm leading-6 shadow-xs outline-none focus-visible:ring-3"
                                            />
                                        </Field>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-zinc-200 shadow-none">
                            <CardContent>
                                <div className="mb-6 flex items-center gap-3">
                                    <Mail className="size-5" />
                                    <h2 className="text-lg font-semibold">
                                        Datos de contacto
                                    </h2>
                                </div>
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field
                                        id="phone"
                                        label="Teléfono"
                                        error={form.errors.phone}
                                    >
                                        <Input
                                            id="phone"
                                            type="tel"
                                            value={form.data.phone}
                                            onChange={(e) =>
                                                form.setData(
                                                    'phone',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="+502 5555 5555"
                                        />
                                    </Field>
                                    <Field
                                        id="public_email"
                                        label="Correo público"
                                        error={form.errors.public_email}
                                    >
                                        <Input
                                            id="public_email"
                                            type="email"
                                            value={form.data.public_email}
                                            onChange={(e) =>
                                                form.setData(
                                                    'public_email',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="hola@ejemplo.com"
                                        />
                                    </Field>
                                    <div className="sm:col-span-2">
                                        <Field
                                            id="website"
                                            label="Sitio web"
                                            error={form.errors.website}
                                        >
                                            <Input
                                                id="website"
                                                type="url"
                                                value={form.data.website}
                                                onChange={(e) =>
                                                    form.setData(
                                                        'website',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="https://tusitio.com"
                                            />
                                        </Field>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-zinc-200 shadow-none">
                            <CardContent>
                                <div className="mb-6 flex items-center gap-3">
                                    <ImageIcon className="size-5" />
                                    <h2 className="text-lg font-semibold">
                                        Imágenes
                                    </h2>
                                </div>
                                <div className="grid gap-5 md:grid-cols-3">
                                    {(['photo', 'cover', 'logo'] as const).map(
                                        (image) => {
                                            const labels = {
                                                photo: 'Fotografía',
                                                cover: 'Portada',
                                                logo: 'Logotipo',
                                            };
                                            const existing =
                                                vCard?.[`${image}_url`] ?? null;
                                            return (
                                                <div
                                                    key={image}
                                                    className="space-y-2"
                                                >
                                                    <Label htmlFor={image}>
                                                        {labels[image]}
                                                    </Label>
                                                    <Input
                                                        id={image}
                                                        type="file"
                                                        accept="image/png,image/jpeg,image/webp"
                                                        onChange={(event) =>
                                                            form.setData(
                                                                image,
                                                                event.target
                                                                    .files?.[0] ??
                                                                    null,
                                                            )
                                                        }
                                                        className="h-auto py-2"
                                                    />
                                                    <InputError
                                                        message={
                                                            form.errors[image]
                                                        }
                                                    />
                                                    {existing && (
                                                        <label className="flex items-center gap-2 text-xs text-zinc-500">
                                                            <Checkbox
                                                                checked={
                                                                    form.data[
                                                                        `remove_${image}`
                                                                    ]
                                                                }
                                                                onCheckedChange={(
                                                                    checked,
                                                                ) =>
                                                                    form.setData(
                                                                        `remove_${image}`,
                                                                        checked ===
                                                                            true,
                                                                    )
                                                                }
                                                            />{' '}
                                                            Quitar imagen actual
                                                        </label>
                                                    )}
                                                </div>
                                            );
                                        },
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-zinc-200 shadow-none">
                            <CardContent>
                                <div className="mb-6 flex items-center gap-3">
                                    <Globe2 className="size-5" />
                                    <div>
                                        <h2 className="text-lg font-semibold">
                                            Canales y redes
                                        </h2>
                                        <p className="mt-1 text-sm text-zinc-500">
                                            Agrega solo los enlaces que quieras
                                            mostrar.
                                        </p>
                                    </div>
                                </div>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {form.data.links.map((link, index) => (
                                        <div
                                            key={link.type}
                                            className="rounded-xl border border-zinc-200 p-4"
                                        >
                                            <div className="mb-3 flex items-center justify-between gap-3">
                                                <Label
                                                    htmlFor={`link-${link.type}`}
                                                >
                                                    {link.label}
                                                </Label>
                                                <label className="flex items-center gap-2 text-xs text-zinc-500">
                                                    <Checkbox
                                                        checked={link.is_active}
                                                        onCheckedChange={(
                                                            checked,
                                                        ) =>
                                                            updateLink(
                                                                index,
                                                                'is_active',
                                                                checked ===
                                                                    true,
                                                            )
                                                        }
                                                    />{' '}
                                                    Mostrar
                                                </label>
                                            </div>
                                            <Input
                                                id={`link-${link.type}`}
                                                type="url"
                                                value={link.url}
                                                onChange={(event) =>
                                                    updateLink(
                                                        index,
                                                        'url',
                                                        event.target.value,
                                                    )
                                                }
                                                placeholder={`https://${link.type}.com/...`}
                                            />
                                            <InputError
                                                message={
                                                    errorBag[
                                                        `links.${index}.url`
                                                    ]
                                                }
                                                className="mt-2"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>

                        <div className="flex justify-end pb-6">
                            <Button
                                disabled={form.processing}
                                className="h-11 rounded-full px-7"
                            >
                                <Save />{' '}
                                {form.processing
                                    ? 'Guardando…'
                                    : 'Guardar vCard'}
                            </Button>
                        </div>
                    </div>

                    <aside className="space-y-5 xl:sticky xl:top-6">
                        <div className="flex items-center justify-between px-1">
                            <div>
                                <p className="text-sm font-semibold">
                                    Vista previa
                                </p>
                                <p className="text-xs text-zinc-500">
                                    Así se verá en un teléfono
                                </p>
                            </div>
                            <span className="flex items-center gap-1.5 rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium">
                                <span className="size-2 rounded-full bg-emerald-500" />{' '}
                                En vivo
                            </span>
                        </div>
                        <div className="rounded-[2.75rem] bg-zinc-100 p-4 sm:p-6">
                            <Preview
                                data={form.data}
                                photoUrl={visiblePhoto}
                                coverUrl={visibleCover}
                                logoUrl={visibleLogo}
                            />
                        </div>
                        {vCard?.qr_image_url && (
                            <Card className="border-zinc-200 shadow-none">
                                <CardContent className="flex items-center gap-4">
                                    <img
                                        src={vCard.qr_image_url}
                                        alt="QR de la vCard"
                                        className="size-24 rounded-xl border p-1"
                                    />
                                    <div className="min-w-0 flex-1">
                                        <p className="flex items-center gap-2 font-semibold">
                                            <QrCode className="size-4" /> QR
                                            listo
                                        </p>
                                        <p className="mt-1 text-sm text-zinc-500">
                                            Apunta siempre a tu perfil
                                            actualizado.
                                        </p>
                                        <a
                                            href={vCard.qr_download_url ?? '#'}
                                            className="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold hover:underline"
                                        >
                                            <Download className="size-4" />{' '}
                                            Descargar
                                        </a>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </aside>
                </div>
            </form>
        </>
    );
}

VCardEditor.layout = ({ vCard }: { vCard: VCard | null }) => ({
    breadcrumbs: [
        { title: 'Inicio', href: dashboard() },
        {
            title: vCard ? 'Editar mi vCard' : 'Crear mi vCard',
            href: vCard ? edit(vCard.slug) : create(),
        },
        ...(vCard ? [{ title: 'Métricas', href: metrics(vCard.slug) }] : []),
    ],
});
