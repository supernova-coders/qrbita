export type VCardStatus = 'draft' | 'published' | 'unpublished';

export type VCardLinkType =
    | 'facebook'
    | 'instagram'
    | 'linkedin'
    | 'whatsapp'
    | 'tiktok'
    | 'youtube'
    | 'x'
    | 'github';

export type VCardLink = {
    id?: number;
    type: VCardLinkType;
    label: string;
    url?: string;
    redirect_url?: string;
    is_active?: boolean;
};

export type VCard = {
    id: number;
    slug: string;
    title: string;
    public_name: string;
    organization: string | null;
    job_title: string | null;
    phone: string | null;
    public_email: string | null;
    website: string | null;
    description: string | null;
    accent_color: string;
    status: VCardStatus;
    published_at: string | null;
    photo_url: string | null;
    cover_url: string | null;
    logo_url: string | null;
    public_url: string;
    qr_image_url: string | null;
    qr_download_url: string | null;
    contact_url?: string;
    links: VCardLink[];
};

export type VCardMetrics = {
    views: number;
    scans: number;
    link_clicks: number;
    contact_saves: number;
    devices?: Array<{ label: string; count: number }>;
    daily?: Array<{ date: string; count: number }>;
    top_links?: Array<{ label: string; type: string; count: number }>;
};
