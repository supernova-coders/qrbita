import { Head } from '@inertiajs/react';
import {
    Download,
    Facebook,
    Github,
    Globe2,
    Instagram,
    Linkedin,
    Mail,
    MessageCircle,
    Music2,
    Phone,
    Share2,
    Twitter,
    Youtube,
} from 'lucide-react';
import { useState } from 'react';
import type { VCardLink, VCardLinkType } from '@/types';

type PublicCard = {
    slug: string;
    title: string;
    public_name: string;
    organization: string | null;
    job_title: string | null;
    phone: string | null;
    public_email: string | null;
    website: string | null;
    description: string | null;
    photo_url: string | null;
    cover_url: string | null;
    logo_url: string | null;
    contact_url: string;
    links: VCardLink[];
};

const linkIcons = {
    facebook: Facebook,
    instagram: Instagram,
    linkedin: Linkedin,
    whatsapp: MessageCircle,
    tiktok: Music2,
    youtube: Youtube,
    x: Twitter,
    github: Github,
} satisfies Record<VCardLinkType, typeof Facebook>;

export default function PublicVCard({ vCard }: { vCard: PublicCard }) {
    const [copied, setCopied] = useState(false);
    const share = async () => {
        if (navigator.share) {
            await navigator.share({
                title: vCard.public_name,
                text: vCard.description ?? undefined,
                url: window.location.href,
            });
            return;
        }

        await navigator.clipboard.writeText(window.location.href);
        setCopied(true);
        window.setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className="min-h-screen bg-zinc-950 px-0 py-0 text-black sm:px-5 sm:py-10">
            <Head title={vCard.public_name}>
                <meta
                    name="description"
                    content={
                        vCard.description ?? `vCard de ${vCard.public_name}`
                    }
                />
            </Head>
            <main className="mx-auto min-h-screen w-full max-w-[470px] overflow-hidden bg-white shadow-2xl sm:min-h-0 sm:rounded-[2.75rem]">
                <section className="relative h-52 overflow-hidden bg-black">
                    {vCard.cover_url ? (
                        <img
                            src={vCard.cover_url}
                            alt=""
                            className="size-full object-cover"
                        />
                    ) : (
                        <div className="size-full bg-[radial-gradient(circle_at_20%_20%,#71717a,transparent_28%),linear-gradient(135deg,#09090b,#3f3f46)]" />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/55 to-transparent" />
                    <button
                        type="button"
                        onClick={share}
                        aria-label="Compartir perfil"
                        className="absolute top-5 right-5 flex size-11 items-center justify-center rounded-full border border-white/20 bg-black/40 text-white backdrop-blur transition hover:bg-black/70"
                    >
                        <Share2 className="size-4" />
                    </button>
                    {copied && (
                        <span className="absolute top-17 right-5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-black shadow-lg">
                            Enlace copiado
                        </span>
                    )}
                </section>

                <section className="relative px-6 pb-9 text-center sm:px-8">
                    <div className="mx-auto -mt-20 flex size-40 items-center justify-center overflow-hidden rounded-full border-[5px] border-white bg-black text-4xl font-semibold text-white shadow-xl">
                        {vCard.photo_url ? (
                            <img
                                src={vCard.photo_url}
                                alt={`Fotografía de ${vCard.public_name}`}
                                className="size-full object-cover"
                            />
                        ) : (
                            vCard.public_name.slice(0, 2).toUpperCase()
                        )}
                    </div>

                    {vCard.logo_url && (
                        <img
                            src={vCard.logo_url}
                            alt={`Logotipo de ${vCard.organization ?? vCard.public_name}`}
                            className="mx-auto mt-5 max-h-12 max-w-32 object-contain"
                        />
                    )}
                    <h1 className="mt-6 text-3xl font-semibold tracking-[-.035em]">
                        {vCard.public_name}
                    </h1>
                    {(vCard.job_title || vCard.organization) && (
                        <p className="mt-2 text-sm font-medium text-zinc-500">
                            {[vCard.job_title, vCard.organization]
                                .filter(Boolean)
                                .join(' · ')}
                        </p>
                    )}
                    {vCard.description && (
                        <p className="mx-auto mt-5 max-w-sm leading-7 text-zinc-600">
                            {vCard.description}
                        </p>
                    )}

                    <a
                        href={vCard.contact_url}
                        download
                        className="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-full bg-black px-5 py-4 font-semibold text-white transition hover:bg-zinc-800"
                    >
                        <Download className="size-4" /> Guardar contacto
                    </a>

                    <div className="mt-6 grid gap-3">
                        {vCard.phone && (
                            <a
                                href={`tel:${vCard.phone}`}
                                className="flex items-center gap-4 rounded-2xl border border-zinc-200 p-4 text-left transition hover:border-black hover:bg-zinc-50"
                            >
                                <span className="flex size-11 items-center justify-center rounded-xl bg-zinc-100">
                                    <Phone className="size-5" />
                                </span>
                                <span className="min-w-0">
                                    <span className="block text-xs text-zinc-500">
                                        Teléfono
                                    </span>
                                    <span className="block truncate text-sm font-semibold">
                                        {vCard.phone}
                                    </span>
                                </span>
                            </a>
                        )}
                        {vCard.public_email && (
                            <a
                                href={`mailto:${vCard.public_email}`}
                                className="flex items-center gap-4 rounded-2xl border border-zinc-200 p-4 text-left transition hover:border-black hover:bg-zinc-50"
                            >
                                <span className="flex size-11 items-center justify-center rounded-xl bg-zinc-100">
                                    <Mail className="size-5" />
                                </span>
                                <span className="min-w-0">
                                    <span className="block text-xs text-zinc-500">
                                        Correo
                                    </span>
                                    <span className="block truncate text-sm font-semibold">
                                        {vCard.public_email}
                                    </span>
                                </span>
                            </a>
                        )}
                        {vCard.website && (
                            <a
                                href={vCard.website}
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center gap-4 rounded-2xl border border-zinc-200 p-4 text-left transition hover:border-black hover:bg-zinc-50"
                            >
                                <span className="flex size-11 items-center justify-center rounded-xl bg-zinc-100">
                                    <Globe2 className="size-5" />
                                </span>
                                <span className="min-w-0">
                                    <span className="block text-xs text-zinc-500">
                                        Sitio web
                                    </span>
                                    <span className="block truncate text-sm font-semibold">
                                        Visitar sitio
                                    </span>
                                </span>
                            </a>
                        )}
                    </div>

                    {vCard.links.length > 0 && (
                        <div className="mt-8 border-t border-zinc-200 pt-7">
                            <p className="mb-4 text-xs font-semibold tracking-[.17em] text-zinc-400 uppercase">
                                Encuéntrame también
                            </p>
                            <div className="flex flex-wrap justify-center gap-3">
                                {vCard.links.map((link) => {
                                    const Icon = linkIcons[link.type];
                                    return (
                                        <a
                                            key={link.type}
                                            href={link.redirect_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            aria-label={link.label}
                                            className="flex size-12 items-center justify-center rounded-full border border-zinc-200 transition hover:-translate-y-1 hover:border-black hover:bg-black hover:text-white"
                                        >
                                            <Icon className="size-5" />
                                        </a>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    <a
                        href="/"
                        className="mt-10 inline-flex items-center gap-2 text-[11px] font-semibold tracking-[.18em] text-zinc-400 uppercase hover:text-black"
                    >
                        <img
                            src="/images/qrbita-logo.png"
                            alt=""
                            className="size-6 rounded-md"
                        />{' '}
                        Creado con Qrbita
                    </a>
                </section>
            </main>
        </div>
    );
}
