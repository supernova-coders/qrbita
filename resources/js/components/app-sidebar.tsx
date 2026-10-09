import { Link, usePage } from '@inertiajs/react';
import {
    ChartNoAxesCombined,
    ContactRound,
    LayoutGrid,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as usersIndex } from '@/routes/admin/users';
import {
    create as createVCard,
    edit as editVCard,
    metrics,
} from '@/routes/vcards';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const { auth, navVCard } = usePage().props;
    const mainNavItems: NavItem[] = [
        { title: 'Inicio', href: dashboard(), icon: LayoutGrid },
        {
            title: navVCard ? 'Editar mi vCard' : 'Crear mi vCard',
            href: navVCard ? editVCard(navVCard.slug) : createVCard(),
            icon: ContactRound,
        },
        ...(navVCard
            ? [
                  {
                      title: 'Métricas',
                      href: metrics(navVCard.slug),
                      icon: ChartNoAxesCombined,
                  },
              ]
            : []),
        ...(auth.user.role === 'administrator'
            ? [
                  {
                      title: 'Usuarios',
                      href: usersIndex(),
                      icon: Users,
                  },
              ]
            : []),
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
