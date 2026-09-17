import type { Metadata } from "next";
import { getHomeData } from "./website/home-data";
import { safeLink } from "./website/site-links";
import SiteHeader from "./website/site-header";
import HeroSlider from "./website/hero-slider";
import HomeSections from "./website/home-sections";
import SiteFooter from "./website/site-footer";
import styles from "./website/website.module.css";

export async function generateMetadata(): Promise<Metadata> {
  const data = await getHomeData();
  if (!data) return { title: "Website đang cập nhật", description: "Vui lòng quay lại sau.", robots: { index: false, follow: false } };
  const title = data.seo.title || data.company.name || "Trang chủ";
  const description = data.seo.description || data.company.slogan || `Chào mừng bạn đến với ${data.company.name || "website của chúng tôi"}.`;
  const canonical = safeLink(data.seo.canonical) || safeLink(data.company.website);
  const image = data.seo.image?.url || data.media.logo?.[0]?.image.url;
  return { title, description, keywords: data.seo.keywords || undefined, robots: { index: !data.seo.noindex, follow: !data.seo.noindex }, alternates: canonical ? { canonical } : undefined, icons: data.media.favicon?.[0] ? { icon: data.media.favicon[0].image.url } : undefined, openGraph: { title, description, type: "website", locale: "vi_VN", siteName: data.company.name, ...(canonical ? { url: canonical } : {}), images: image ? [{ url: image }] : [] }, twitter: { card: image ? "summary_large_image" : "summary", title, description, images: image ? [image] : [] } };
}

export default async function Home() {
  const data = await getHomeData();
  if (!data) return <div className={styles.site}><main className={styles.unavailable}><span className={styles.eyebrow}>HẸN GẶP BẠN SỚM</span><h1>Website đang được cập nhật.</h1><p>Chúng tôi chưa thể tải nội dung lúc này. Vui lòng thử lại sau ít phút.</p><a className={styles.primaryButton} href="/">Tải lại trang ↗</a></main></div>;
  return <div className={styles.site}><a className={styles.skipLink} href="#main-content">Bỏ qua menu, đến nội dung</a><SiteHeader data={data} /><main id="main-content" className={styles.main}><HeroSlider slides={data.media.slide || []} name={data.company.name || "Website"} slogan={data.company.slogan} /><HomeSections data={data} /></main><SiteFooter data={data} /></div>;
}
