import type { Auth } from '@/types/auth';
import type { VCardStatus } from '@/types/vcard';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            navVCard: {
                id: number;
                slug: string;
                status: VCardStatus;
            } | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
