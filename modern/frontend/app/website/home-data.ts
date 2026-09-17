import { cache } from "react";

export type SiteImage = { url: string; width: number; height: number };
export type SitePhoto = { id: number; image: SiteImage; name: string; alt: string; text1: string; text2: string; link: string };
export type SiteCard = { id: number; name: string; description: string; image: SiteImage | null; image_alt: string; regular_price?: string; sale_price?: string | null; is_new?: boolean };
export type HomeData = {
  locale: "vi" | "en";
  company: { name: string; slogan: string; address: string; copyright: string; email?: string; phone?: string; hotline?: string; website?: string; worktime?: string };
  media: Record<string, SitePhoto[]>;
  seo: { title: string; description: string; keywords: string; canonical: string; noindex: boolean; image: SiteImage | null };
  about: (SiteCard & { content: string }) | null;
  products: SiteCard[];
  news: SiteCard[];
};
export const getHomeData = cache(async (): Promise<HomeData | null> => {
  const base = (process.env.API_URL || process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000").replace(/\/$/, "").replace(/\/api\/v1$/, "");
  try {
    const response = await fetch(`${base}/api/v1/website/home?locale=vi`, { cache: "no-store", headers: { Accept: "application/json" }, signal: AbortSignal.timeout(8000) });
    if (!response.ok) return null;
    return (await response.json()).data;
  } catch { return null; }
});
