import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowLeft,
    BarChart3,
    CalendarDays,
    Eye,
    MousePointerClick,
    ScanLine,
    Smartphone,
    UserPlus,
} from 'lucide-react';
import { FormEvent, useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import { edit, metrics as metricsRoute } from '@/routes/vcards';
import type { VCard, VCardMetrics } from '@/types';

type MetricsProps = {
    vCard: VCard;
    filters: { from: string; to: string };
    metrics: Required<VCardMetrics>;
};

export default function Metrics({ vCard, filters, metrics }: MetricsProps) {
    const [from, setFrom] = useState(filters.from);
    const [to, setTo] = useState(filters.to);
    const maxDaily = useMemo(
        () => Math.max(...metrics.daily.map((item) => item.count), 1),
        [metrics.daily],
    );
    const maxDevice = useMemo(
        () => Math.max(...metrics.devices.map((item) => item.count), 1),
        [metrics.devices],
    );
    const summary = [
        { label: 'Vistas', value: metrics.views, icon: Eye },
        { label: 'Escaneos', value: metrics.scans, icon: ScanLine },
        { label: 'Clics', value: metrics.link_clicks, icon: MousePointerClick },
        {
            label: 'Contactos guardados',
            value: metrics.contact_saves,
            icon: UserPlus,
        },
    ];

    const applyFilters = (event: FormEvent) => {
        event.preventDefault();
        router.get(
            metricsRoute.url(vCard.slug),
            { from, to },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Métricas" />
            <div className="mx-auto w-full max-w-7xl space-y-7 p-4 sm:p-6 lg:p-8">
                <div className="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
                    <div>
                        <Link
                            href={edit(vCard.slug)}
                            className="mb-4 inline-flex items-center gap-2 text-sm font-medium text-zinc-500 transition hover:text-black"
                        >
                            <ArrowLeft className="size-4" /> Volver a la vCard
                        </Link>
                        <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                            Métricas de tu vCard
                        </h1>
                        <p className="mt-2 text-zinc-500">
                            Una lectura simple del alcance de{' '}
                            {vCard.public_name}.
                        </p>
                    </div>
                    <form
                        onSubmit={applyFilters}
                        className="flex flex-col gap-3 rounded-2xl border bg-white p-4 text-black sm:flex-row sm:items-end"
                    >
                        <div className="space-y-1.5">
                            <Label htmlFor="from">Desde</Label>
                            <Input
                                id="from"
                                type="date"
                                value={from}
                                onChange={(event) =>
                                    setFrom(event.target.value)
                                }
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="to">Hasta</Label>
                            <Input
                                id="to"
                                type="date"
                                value={to}
                                onChange={(event) => setTo(event.target.value)}
                            />
                        </div>
                        <Button className="rounded-full">
                            <CalendarDays /> Aplicar
                        </Button>
                    </form>
                </div>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {summary.map((item) => (
                        <Card
                            key={item.label}
                            className="gap-0 border-zinc-200 py-0 shadow-none"
                        >
                            <CardContent className="p-6">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <p className="text-sm text-zinc-500">
                                            {item.label}
                                        </p>
                                        <p className="mt-3 text-3xl font-semibold">
                                            {item.value.toLocaleString('es-GT')}
                                        </p>
                                    </div>
                                    <span className="rounded-xl bg-zinc-100 p-2.5 text-black">
                                        <item.icon className="size-5" />
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </section>

                <section className="grid gap-5 lg:grid-cols-[1.35fr_.65fr]">
                    <Card className="border-zinc-200 shadow-none">
                        <CardContent>
                            <div className="flex items-center gap-3">
                                <BarChart3 className="size-5" />
                                <div>
                                    <h2 className="font-semibold">
                                        Actividad diaria
                                    </h2>
                                    <p className="text-sm text-zinc-500">
                                        Todas las interacciones del período
                                    </p>
                                </div>
                            </div>
                            {metrics.daily.length ? (
                                <div className="mt-8 flex h-64 items-end gap-2 overflow-x-auto border-b border-zinc-200 pb-px">
                                    {metrics.daily.map((item) => (
                                        <div
                                            key={item.date}
                                            className="group flex min-w-7 flex-1 flex-col items-center justify-end gap-2"
                                            title={`${item.date}: ${item.count}`}
                                        >
                                            <span className="text-[10px] font-semibold opacity-0 transition group-hover:opacity-100">
                                                {item.count}
                                            </span>
                                            <div
                                                className="w-full min-w-6 rounded-t-md bg-black transition hover:bg-zinc-600"
                                                style={{
                                                    height: `${Math.max((item.count / maxDaily) * 190, 5)}px`,
                                                }}
                                            />
                                            <span className="rotate-[-45deg] pb-2 text-[9px] whitespace-nowrap text-zinc-400">
                                                {item.date.slice(5)}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="mt-8 flex h-64 items-center justify-center rounded-2xl border border-dashed bg-zinc-50 text-sm text-zinc-500">
                                    Aún no hay interacciones en este período.
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="border-zinc-200 shadow-none">
                        <CardContent>
                            <div className="flex items-center gap-3">
                                <Smartphone className="size-5" />
                                <div>
                                    <h2 className="font-semibold">
                                        Dispositivos
                                    </h2>
                                    <p className="text-sm text-zinc-500">
                                        Distribución aproximada
                                    </p>
                                </div>
                            </div>
                            <div className="mt-8 space-y-5">
                                {metrics.devices.length ? (
                                    metrics.devices.map((item) => (
                                        <div key={item.label}>
                                            <div className="mb-2 flex justify-between text-sm">
                                                <span className="capitalize">
                                                    {item.label}
                                                </span>
                                                <span className="font-semibold">
                                                    {item.count}
                                                </span>
                                            </div>
                                            <div className="h-2 overflow-hidden rounded-full bg-zinc-100">
                                                <div
                                                    className="h-full rounded-full bg-black"
                                                    style={{
                                                        width: `${(item.count / maxDevice) * 100}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-sm text-zinc-500">
                                        Sin datos todavía.
                                    </p>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <Card className="border-zinc-200 shadow-none">
                    <CardContent>
                        <div className="flex items-center gap-3">
                            <MousePointerClick className="size-5" />
                            <div>
                                <h2 className="font-semibold">
                                    Enlaces más visitados
                                </h2>
                                <p className="text-sm text-zinc-500">
                                    Canales que generan más interés
                                </p>
                            </div>
                        </div>
                        <div className="mt-6 divide-y divide-zinc-100">
                            {metrics.top_links.length ? (
                                metrics.top_links.map((item, index) => (
                                    <div
                                        key={`${item.type}-${index}`}
                                        className="flex items-center gap-4 py-4"
                                    >
                                        <span className="flex size-9 items-center justify-center rounded-full bg-zinc-100 text-sm font-semibold">
                                            {index + 1}
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate font-medium">
                                                {item.label}
                                            </p>
                                            <p className="text-xs text-zinc-500 capitalize">
                                                {item.type}
                                            </p>
                                        </div>
                                        <span className="text-sm font-semibold">
                                            {item.count} clics
                                        </span>
                                    </div>
                                ))
                            ) : (
                                <p className="py-8 text-center text-sm text-zinc-500">
                                    Aún no hay clics registrados.
                                </p>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Metrics.layout = ({ vCard }: { vCard: VCard }) => ({
    breadcrumbs: [
        { title: 'Inicio', href: dashboard() },
        { title: 'Editar mi vCard', href: edit(vCard.slug) },
        { title: 'Métricas', href: metricsRoute(vCard.slug) },
    ],
});
