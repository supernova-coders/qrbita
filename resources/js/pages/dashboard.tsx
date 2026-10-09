import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    Bot,
    ContactRound,
    Download,
    Eye,
    MousePointerClick,
    Plus,
    QrCode,
    ScanLine,
    Sparkles,
    UserPlus,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { create, edit, metrics as metricsRoute } from '@/routes/vcards';
import type { VCard, VCardMetrics } from '@/types';

type DashboardProps = {
    vCard: VCard | null;
    metrics: VCardMetrics;
    aiConfigured: boolean;
};

const statusLabels = {
    draft: 'Borrador',
    published: 'Publicada',
    unpublished: 'Sin publicar',
};

export default function Dashboard({
    vCard,
    metrics,
    aiConfigured,
}: DashboardProps) {
    const { auth } = usePage().props;
    const firstName = auth.user.name.split(' ')[0];
    const summary = [
        { label: 'Vistas', value: metrics.views, icon: Eye },
        { label: 'Escaneos', value: metrics.scans, icon: ScanLine },
        {
            label: 'Clics en enlaces',
            value: metrics.link_clicks,
            icon: MousePointerClick,
        },
        {
            label: 'Contactos guardados',
            value: metrics.contact_saves,
            icon: UserPlus,
        },
    ];

    return (
        <>
            <Head title="Inicio" />
            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-7 p-4 sm:p-6 lg:p-8">
                <section className="relative overflow-hidden rounded-3xl bg-black px-6 py-9 text-white sm:px-10 sm:py-12">
                    <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_20%,rgba(255,255,255,.18),transparent_26%)]" />
                    <div className="relative flex flex-col justify-between gap-8 md:flex-row md:items-end">
                        <div>
                            <div className="mb-4 inline-flex items-center gap-2 text-xs font-semibold tracking-[.18em] text-zinc-400 uppercase">
                                <Sparkles className="size-3.5" /> Espacio
                                personal
                            </div>
                            <h1 className="text-3xl font-semibold tracking-tight sm:text-5xl">
                                Hola, {firstName}.
                            </h1>
                            <p className="mt-3 max-w-xl text-zinc-400">
                                {vCard
                                    ? 'Tu identidad digital está lista para seguir creciendo.'
                                    : 'Crea tu primera vCard y empieza a compartir tu mundo con un escaneo.'}
                            </p>
                        </div>
                        <Button
                            asChild
                            className="h-11 rounded-full bg-white px-6 text-black hover:bg-zinc-200"
                        >
                            <Link href={vCard ? edit(vCard.slug) : create()}>
                                {vCard ? <ContactRound /> : <Plus />}
                                {vCard ? 'Editar mi vCard' : 'Crear mi vCard'}
                            </Link>
                        </Button>
                    </div>
                </section>

                {vCard ? (
                    <>
                        <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            {summary.map((item) => (
                                <Card
                                    key={item.label}
                                    className="gap-0 border-zinc-200 py-0 shadow-none"
                                >
                                    <CardContent className="p-5 sm:p-6">
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <p className="text-muted-foreground text-sm">
                                                    {item.label}
                                                </p>
                                                <p className="mt-3 text-3xl font-semibold tracking-tight">
                                                    {item.value.toLocaleString(
                                                        'es-GT',
                                                    )}
                                                </p>
                                            </div>
                                            <span className="rounded-xl bg-zinc-100 p-2.5 text-black">
                                                <item.icon
                                                    className="size-5"
                                                    strokeWidth={1.7}
                                                />
                                            </span>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </section>

                        <section className="grid gap-5 lg:grid-cols-[1.4fr_.6fr]">
                            <Card className="overflow-hidden border-zinc-200 py-0 shadow-none">
                                <div className="grid min-h-[330px] md:grid-cols-[.9fr_1.1fr]">
                                    <div className="relative min-h-52 overflow-hidden bg-zinc-950">
                                        {vCard.cover_url ? (
                                            <img
                                                src={vCard.cover_url}
                                                alt=""
                                                className="absolute inset-0 size-full object-cover opacity-65"
                                            />
                                        ) : (
                                            <div className="absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,#71717a,transparent_30%),linear-gradient(145deg,#09090b,#3f3f46)]" />
                                        )}
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent" />
                                        <div className="absolute inset-x-0 bottom-0 p-6 text-white">
                                            <div className="mb-4 flex size-16 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-black text-lg font-semibold">
                                                {vCard.photo_url ? (
                                                    <img
                                                        src={vCard.photo_url}
                                                        alt=""
                                                        className="size-full object-cover"
                                                    />
                                                ) : (
                                                    vCard.public_name
                                                        .slice(0, 2)
                                                        .toUpperCase()
                                                )}
                                            </div>
                                            <p className="text-xl font-semibold">
                                                {vCard.public_name}
                                            </p>
                                            <p className="mt-1 text-sm text-zinc-300">
                                                {[
                                                    vCard.job_title,
                                                    vCard.organization,
                                                ]
                                                    .filter(Boolean)
                                                    .join(' · ') ||
                                                    'Perfil profesional'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex flex-col justify-between p-6 sm:p-8">
                                        <div>
                                            <div className="flex flex-wrap items-center gap-2">
                                                <span
                                                    className={`rounded-full px-3 py-1 text-xs font-semibold ${vCard.status === 'published' ? 'bg-black text-white' : 'bg-zinc-100 text-zinc-600'}`}
                                                >
                                                    {statusLabels[vCard.status]}
                                                </span>
                                                <span className="text-xs text-zinc-500">
                                                    {vCard.title}
                                                </span>
                                            </div>
                                            <h2 className="mt-5 text-2xl font-semibold tracking-tight">
                                                Tu vCard
                                            </h2>
                                            <p className="mt-2 leading-7 text-zinc-500">
                                                Edita tus datos cuando quieras.
                                                El enlace y el QR se mantienen
                                                estables después de publicar.
                                            </p>
                                        </div>
                                        <div className="mt-8 flex flex-wrap gap-3">
                                            <Button
                                                asChild
                                                className="rounded-full"
                                            >
                                                <Link href={edit(vCard.slug)}>
                                                    Editar perfil <ArrowRight />
                                                </Link>
                                            </Button>
                                            <Button
                                                asChild
                                                variant="outline"
                                                className="rounded-full"
                                            >
                                                <Link
                                                    href={metricsRoute(
                                                        vCard.slug,
                                                    )}
                                                >
                                                    <BarChart3 /> Ver métricas
                                                </Link>
                                            </Button>
                                            {vCard.status === 'published' && (
                                                <Button
                                                    asChild
                                                    variant="outline"
                                                    className="rounded-full"
                                                >
                                                    <a
                                                        href={vCard.public_url}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                    >
                                                        <Eye /> Abrir
                                                    </a>
                                                </Button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </Card>

                            <Card className="border-zinc-200 py-0 shadow-none">
                                <CardContent className="flex h-full flex-col p-6 sm:p-8">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className="text-xs font-semibold tracking-[.16em] text-zinc-500 uppercase">
                                                Código dinámico
                                            </p>
                                            <h2 className="mt-2 text-2xl font-semibold">
                                                Tu QR
                                            </h2>
                                        </div>
                                        <QrCode className="size-6" />
                                    </div>
                                    {vCard.qr_image_url ? (
                                        <img
                                            src={vCard.qr_image_url}
                                            alt="Código QR de la vCard"
                                            className="mx-auto my-7 size-44 rounded-2xl border p-2"
                                        />
                                    ) : (
                                        <div className="my-7 flex min-h-44 flex-1 items-center justify-center rounded-2xl border border-dashed bg-zinc-50 px-5 text-center text-sm text-zinc-500">
                                            Publica tu vCard para generar el
                                            código QR.
                                        </div>
                                    )}
                                    {vCard.qr_download_url && (
                                        <Button
                                            asChild
                                            variant="outline"
                                            className="mt-auto rounded-full"
                                        >
                                            <a href={vCard.qr_download_url}>
                                                <Download /> Descargar PNG
                                            </a>
                                        </Button>
                                    )}
                                </CardContent>
                            </Card>
                        </section>
                    </>
                ) : (
                    <section className="grid gap-5 lg:grid-cols-[1.2fr_.8fr]">
                        <Card className="border-zinc-200 py-0 shadow-none">
                            <CardContent className="p-7 sm:p-10">
                                <span className="flex size-12 items-center justify-center rounded-2xl bg-black text-white">
                                    <ContactRound />
                                </span>
                                <h2 className="mt-8 text-3xl font-semibold tracking-tight">
                                    Construye una presentación que sí viaja
                                    contigo.
                                </h2>
                                <p className="mt-4 max-w-xl leading-7 text-zinc-500">
                                    En pocos pasos podrás agregar tus datos, ver
                                    el resultado en tiempo real y publicar un QR
                                    listo para compartir.
                                </p>
                                <Button
                                    asChild
                                    className="mt-8 h-11 rounded-full px-6"
                                >
                                    <Link href={create()}>
                                        <Plus /> Crear ahora
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                        <Card className="border-zinc-200 bg-zinc-50 py-0 text-black shadow-none">
                            <CardContent className="p-7 sm:p-10">
                                <Bot className="size-7" />
                                <h2 className="mt-8 text-2xl font-semibold">
                                    Dos formas de empezar
                                </h2>
                                <p className="mt-3 leading-7 text-zinc-500">
                                    Completa cada campo o pide una propuesta a
                                    la IA. Nada se publica hasta que tú lo
                                    confirmes.
                                </p>
                                <p className="mt-8 flex items-center gap-2 text-sm font-medium">
                                    <span
                                        className={`size-2 rounded-full ${aiConfigured ? 'bg-emerald-500' : 'bg-amber-500'}`}
                                    />
                                    {aiConfigured
                                        ? 'Asistente IA disponible'
                                        : 'Formulario manual disponible'}
                                </p>
                            </CardContent>
                        </Card>
                    </section>
                )}
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Inicio', href: dashboard() }],
};
