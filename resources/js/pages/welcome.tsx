import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    Bot,
    Check,
    ContactRound,
    QrCode,
    ScanLine,
    ShieldCheck,
    Sparkles,
} from 'lucide-react';
import { dashboard, login, register } from '@/routes';

const features = [
    {
        icon: ContactRound,
        title: 'Tu identidad, en un solo lugar',
        text: 'Comparte teléfono, correo, sitio web y redes desde una experiencia móvil clara.',
    },
    {
        icon: QrCode,
        title: 'Un QR que permanece',
        text: 'Actualiza tus datos sin reimprimir el código que ya entregaste.',
    },
    {
        icon: Bot,
        title: 'Creación asistida por IA',
        text: 'Describe tu perfil o completa el formulario. Tú siempre revisas antes de publicar.',
    },
    {
        icon: BarChart3,
        title: 'Métricas útiles y privadas',
        text: 'Conoce vistas, escaneos y acciones sin recopilar datos sensibles del visitante.',
    },
];

function MiniQr() {
    const darkCells = [
        0, 1, 2, 6, 7, 8, 9, 11, 15, 17, 18, 20, 22, 24, 26, 27, 29, 31, 33, 35,
        36, 37, 38, 40, 42, 44, 45, 47, 49, 51, 53, 54, 55, 56, 60, 62, 63, 65,
        67, 69, 71, 72, 73, 74, 78, 80,
    ];

    return (
        <div className="grid grid-cols-9 gap-[2px] rounded-2xl bg-white p-3 shadow-2xl ring-1 ring-black/10">
            {Array.from({ length: 81 }, (_, index) => (
                <span
                    key={index}
                    className={`aspect-square rounded-[1px] ${darkCells.includes(index) ? 'bg-black' : 'bg-white'}`}
                />
            ))}
        </div>
    );
}

export default function Welcome() {
    const { auth } = usePage().props;
    const primaryHref = auth.user ? dashboard() : register();

    return (
        <div className="min-h-screen bg-black text-white selection:bg-white selection:text-black">
            <Head title="Tu identidad digital en un QR">
                <meta
                    name="description"
                    content="Crea una vCard profesional, compártela con un QR dinámico y entiende su alcance con Qrbita."
                />
            </Head>

            <header className="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-black/80 backdrop-blur-xl">
                <nav className="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 lg:px-8">
                    <Link href="/" className="flex items-center gap-3">
                        <img
                            src="/images/qrbita-logo.png"
                            alt="Qrbita"
                            className="size-12 rounded-xl object-contain"
                        />
                        <span className="text-xl font-semibold tracking-tight">
                            qrbita
                        </span>
                    </Link>
                    <div className="flex items-center gap-2">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-zinc-200"
                            >
                                Ir a mi espacio
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="hidden rounded-full px-5 py-2.5 text-sm font-medium text-zinc-300 transition hover:text-white sm:inline-flex"
                                >
                                    Iniciar sesión
                                </Link>
                                <Link
                                    href={register()}
                                    className="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-zinc-200"
                                >
                                    Crear cuenta
                                </Link>
                            </>
                        )}
                    </div>
                </nav>
            </header>

            <main>
                <section className="relative overflow-hidden pt-36 pb-24 sm:pt-44 lg:min-h-[860px] lg:pt-52">
                    <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(255,255,255,0.12),transparent_28%),radial-gradient(circle_at_15%_80%,rgba(255,255,255,0.06),transparent_22%)]" />
                    <div className="pointer-events-none absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,.05)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.05)_1px,transparent_1px)] [background-size:72px_72px] opacity-30" />
                    <div className="relative mx-auto grid max-w-7xl items-center gap-16 px-5 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
                        <div className="animate-qrbita-rise">
                            <div className="mb-7 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-medium tracking-[.18em] text-zinc-300 uppercase">
                                <Sparkles className="size-3.5" />
                                Tu presencia profesional evoluciona
                            </div>
                            <h1 className="max-w-4xl text-5xl leading-[.96] font-semibold tracking-[-.055em] sm:text-7xl lg:text-[6.5rem]">
                                Una conexión.
                                <span className="mt-2 block text-zinc-500">
                                    Todo tu mundo.
                                </span>
                            </h1>
                            <p className="mt-8 max-w-xl text-lg leading-8 text-zinc-400 sm:text-xl">
                                Crea una vCard profesional, compártela con un QR
                                y mantén tu información actualizada sin volver a
                                imprimir.
                            </p>
                            <div className="mt-10 flex flex-col gap-3 sm:flex-row">
                                <Link
                                    href={primaryHref}
                                    className="group inline-flex items-center justify-center gap-2 rounded-full bg-white px-7 py-4 font-semibold text-black transition hover:scale-[1.02] hover:bg-zinc-200"
                                >
                                    {auth.user
                                        ? 'Continuar en Qrbita'
                                        : 'Crear mi vCard'}
                                    <ArrowRight className="size-4 transition group-hover:translate-x-1" />
                                </Link>
                                <a
                                    href="#como-funciona"
                                    className="inline-flex items-center justify-center rounded-full border border-white/15 px-7 py-4 font-medium text-white transition hover:bg-white/10"
                                >
                                    Ver cómo funciona
                                </a>
                            </div>
                            <div className="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm text-zinc-500">
                                {[
                                    'Sin tarjeta física',
                                    'Editable en cualquier momento',
                                    'Diseñada para móvil',
                                ].map((item) => (
                                    <span
                                        key={item}
                                        className="flex items-center gap-2"
                                    >
                                        <Check className="size-4 text-white" />{' '}
                                        {item}
                                    </span>
                                ))}
                            </div>
                        </div>

                        <div className="animate-qrbita-rise relative mx-auto w-full max-w-[520px] [animation-delay:180ms]">
                            <div className="absolute -inset-8 rounded-full bg-white/10 blur-3xl" />
                            <div className="animate-qrbita-float relative mx-auto w-[82%] overflow-hidden rounded-[2.5rem] border border-white/20 bg-white text-black shadow-[0_40px_120px_rgba(255,255,255,.14)]">
                                <div className="h-36 bg-[radial-gradient(circle_at_20%_20%,#52525b,transparent_30%),linear-gradient(135deg,#09090b,#52525b)]" />
                                <div className="relative px-7 pb-8 text-center">
                                    <div className="mx-auto -mt-14 flex size-28 items-center justify-center rounded-full border-4 border-white bg-black text-3xl font-semibold text-white">
                                        ND
                                    </div>
                                    <p className="mt-5 text-2xl font-semibold tracking-tight">
                                        Nicolás Del Valle
                                    </p>
                                    <p className="mt-1 text-sm text-zinc-500">
                                        Diseñador & desarrollador
                                    </p>
                                    <p className="mx-auto mt-4 max-w-xs text-sm leading-6 text-zinc-600">
                                        Creo experiencias digitales claras,
                                        funcionales y memorables.
                                    </p>
                                    <button className="mt-6 w-full rounded-full bg-black py-3.5 text-sm font-semibold text-white">
                                        Guardar contacto
                                    </button>
                                    <div className="mt-5 grid grid-cols-3 gap-2">
                                        {[
                                            'Correo',
                                            'Sitio web',
                                            'LinkedIn',
                                        ].map((item) => (
                                            <div
                                                key={item}
                                                className="rounded-xl bg-zinc-100 px-2 py-3 text-[11px] font-medium"
                                            >
                                                {item}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                            <div className="absolute -right-1 bottom-8 w-32 rotate-6 sm:-right-7 sm:w-40">
                                <MiniQr />
                            </div>
                            <div className="absolute top-12 -left-3 flex size-16 items-center justify-center rounded-2xl border border-white/15 bg-black/80 backdrop-blur sm:-left-10">
                                <ScanLine className="size-7" />
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="como-funciona"
                    className="border-y border-white/10 bg-zinc-950 py-24 sm:py-32"
                >
                    <div className="mx-auto max-w-7xl px-5 lg:px-8">
                        <div className="max-w-2xl">
                            <p className="text-sm font-semibold tracking-[.2em] text-zinc-500 uppercase">
                                Simple por diseño
                            </p>
                            <h2 className="mt-5 text-4xl font-semibold tracking-tight sm:text-6xl">
                                De tus datos a una conexión real.
                            </h2>
                        </div>
                        <div className="mt-16 grid gap-px overflow-hidden rounded-3xl border border-white/10 bg-white/10 md:grid-cols-3">
                            {[
                                [
                                    '01',
                                    'Crea',
                                    'Completa el formulario o cuéntale a la IA cómo quieres presentarte.',
                                ],
                                [
                                    '02',
                                    'Publica',
                                    'Revisa tu vista previa y activa una dirección pública protegida.',
                                ],
                                [
                                    '03',
                                    'Comparte',
                                    'Descarga tu QR estable y consulta cómo interactúan con tu perfil.',
                                ],
                            ].map(([number, title, text]) => (
                                <article
                                    key={number}
                                    className="bg-zinc-950 p-8 sm:p-10"
                                >
                                    <span className="text-xs font-semibold tracking-[.2em] text-zinc-600">
                                        {number}
                                    </span>
                                    <h3 className="mt-12 text-2xl font-semibold">
                                        {title}
                                    </h3>
                                    <p className="mt-3 leading-7 text-zinc-400">
                                        {text}
                                    </p>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="py-24 sm:py-32">
                    <div className="mx-auto max-w-7xl px-5 lg:px-8">
                        <div className="grid gap-5 md:grid-cols-2">
                            {features.map((feature) => (
                                <article
                                    key={feature.title}
                                    className="group rounded-3xl border border-white/10 bg-white/[.035] p-8 transition hover:-translate-y-1 hover:border-white/25 sm:p-10"
                                >
                                    <feature.icon
                                        className="size-7 text-white"
                                        strokeWidth={1.5}
                                    />
                                    <h3 className="mt-10 text-2xl font-semibold tracking-tight">
                                        {feature.title}
                                    </h3>
                                    <p className="mt-3 max-w-md leading-7 text-zinc-400">
                                        {feature.text}
                                    </p>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="px-5 pb-24 sm:pb-32 lg:px-8">
                    <div className="relative mx-auto max-w-7xl overflow-hidden rounded-[2.5rem] bg-white px-6 py-20 text-center text-black sm:px-12 sm:py-28">
                        <div className="absolute inset-0 [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:18px_18px] opacity-10" />
                        <div className="relative">
                            <ShieldCheck className="mx-auto size-9" />
                            <h2 className="mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-[-.04em] sm:text-6xl">
                                Tu próxima conexión empieza con un escaneo.
                            </h2>
                            <p className="mx-auto mt-6 max-w-xl text-lg text-zinc-600">
                                Una identidad digital profesional, fácil de
                                actualizar y lista para compartir.
                            </p>
                            <Link
                                href={primaryHref}
                                className="mt-9 inline-flex items-center gap-2 rounded-full bg-black px-7 py-4 font-semibold text-white transition hover:bg-zinc-800"
                            >
                                Comenzar ahora <ArrowRight className="size-4" />
                            </Link>
                        </div>
                    </div>
                </section>
            </main>

            <footer className="border-t border-white/10 py-10">
                <div className="mx-auto flex max-w-7xl flex-col gap-4 px-5 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                    <span>
                        © {new Date().getFullYear()} Qrbita. Tu identidad en
                        movimiento.
                    </span>
                    <span>Diseñado con privacidad, claridad y propósito.</span>
                </div>
            </footer>
        </div>
    );
}
