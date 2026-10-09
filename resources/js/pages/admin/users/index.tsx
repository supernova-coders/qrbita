import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    CheckCircle2,
    ShieldCheck,
    UserRound,
    Users,
    XCircle,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index } from '@/routes/admin/users';
import { update } from '@/routes/admin/users/status';

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role: 'user' | 'administrator';
    status: 'active' | 'inactive';
    created_at: string;
    vcard: { slug: string; status: string } | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type UsersPage = {
    data: ManagedUser[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

export default function UsersIndex({ users }: { users: UsersPage }) {
    const currentUser = usePage().props.auth.user;

    const changeStatus = (user: ManagedUser) => {
        router.patch(
            update.url(user.id),
            { status: user.status === 'active' ? 'inactive' : 'active' },
            { preserveScroll: true },
        );
    };

    return (
        <>
            <Head title="Usuarios" />
            <div className="mx-auto w-full max-w-7xl space-y-7 p-4 sm:p-6 lg:p-8">
                <section className="flex flex-col justify-between gap-5 rounded-3xl bg-black px-6 py-9 text-white sm:px-10 md:flex-row md:items-end">
                    <div>
                        <p className="flex items-center gap-2 text-xs font-semibold tracking-[.17em] text-zinc-400 uppercase">
                            <ShieldCheck className="size-4" /> Administración
                        </p>
                        <h1 className="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                            Gestión de usuarios
                        </h1>
                        <p className="mt-2 text-zinc-400">
                            Consulta cuentas y controla su acceso a Qrbita.
                        </p>
                    </div>
                    <div className="flex items-center gap-3 rounded-2xl border border-white/15 px-5 py-3">
                        <Users className="size-5" />
                        <div>
                            <p className="text-xs text-zinc-400">
                                Usuarios registrados
                            </p>
                            <p className="text-xl font-semibold">
                                {users.total}
                            </p>
                        </div>
                    </div>
                </section>

                <Card className="overflow-hidden border-zinc-200 py-0 shadow-none">
                    <CardContent className="p-0">
                        <div className="hidden grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_140px_150px] gap-4 border-b bg-zinc-50 px-6 py-3 text-xs font-semibold tracking-wide text-zinc-500 uppercase md:grid">
                            <span>Usuario</span>
                            <span>vCard</span>
                            <span>Estado</span>
                            <span className="text-right">Acción</span>
                        </div>
                        <div className="divide-y divide-zinc-100">
                            {users.data.map((user) => (
                                <div
                                    key={user.id}
                                    className="grid gap-5 px-5 py-5 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_140px_150px] md:items-center md:px-6"
                                >
                                    <div className="flex min-w-0 items-center gap-3">
                                        <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-zinc-100">
                                            <UserRound className="size-4" />
                                        </span>
                                        <div className="min-w-0">
                                            <p className="truncate font-semibold">
                                                {user.name}
                                            </p>
                                            <p className="truncate text-sm text-zinc-500">
                                                {user.email}
                                            </p>
                                            {user.role === 'administrator' && (
                                                <span className="mt-1 inline-block text-[10px] font-semibold tracking-[.12em] uppercase">
                                                    Administrador
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="text-sm">
                                        <p className="text-xs text-zinc-400 md:hidden">
                                            vCard
                                        </p>
                                        {user.vcard ? (
                                            <>
                                                <p className="font-medium">
                                                    /{user.vcard.slug}
                                                </p>
                                                <p className="text-xs text-zinc-500 capitalize">
                                                    {user.vcard.status}
                                                </p>
                                            </>
                                        ) : (
                                            <span className="text-zinc-400">
                                                Sin crear
                                            </span>
                                        )}
                                    </div>
                                    <div>
                                        <span
                                            className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ${user.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-600'}`}
                                        >
                                            {user.status === 'active' ? (
                                                <CheckCircle2 className="size-3.5" />
                                            ) : (
                                                <XCircle className="size-3.5" />
                                            )}
                                            {user.status === 'active'
                                                ? 'Activo'
                                                : 'Inactivo'}
                                        </span>
                                    </div>
                                    <div className="md:text-right">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            disabled={
                                                user.id === currentUser.id
                                            }
                                            onClick={() => changeStatus(user)}
                                            className="rounded-full"
                                        >
                                            {user.status === 'active'
                                                ? 'Desactivar'
                                                : 'Activar'}
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                {users.last_page > 1 && (
                    <nav
                        aria-label="Paginación"
                        className="flex flex-wrap items-center justify-between gap-4"
                    >
                        <p className="text-sm text-zinc-500">
                            Mostrando {users.from}–{users.to} de {users.total}
                        </p>
                        <div className="flex flex-wrap gap-1">
                            {users.links.map((link, linkIndex) =>
                                link.url ? (
                                    <Link
                                        key={`${link.label}-${linkIndex}`}
                                        href={link.url}
                                        preserveScroll
                                        className={`rounded-lg border px-3 py-2 text-sm ${link.active ? 'border-black bg-black text-white' : 'bg-white hover:bg-zinc-50'}`}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ) : (
                                    <span
                                        key={`${link.label}-${linkIndex}`}
                                        className="rounded-lg border px-3 py-2 text-sm text-zinc-300"
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ),
                            )}
                        </div>
                    </nav>
                )}
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [
        { title: 'Inicio', href: dashboard() },
        { title: 'Usuarios', href: index() },
    ],
};
