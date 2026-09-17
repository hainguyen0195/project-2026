import type { HomeData } from "./home-data";
import styles from "./website.module.css";

export default function SiteHeader({ data }: { data: HomeData }) {
  const name = data.company.name || "Website";
  const logo = data.media.logo?.[0];
  const phone = data.company.hotline || data.company.phone;
  const links = <><a href="#home">Trang chủ</a>{data.about && <a href="#about">Giới thiệu</a>}<a href="#products">Sản phẩm</a><a href="#news">Tin tức</a><a href="#contact">Liên hệ</a></>;
  return <><div className={styles.announcement}><span>{data.company.slogan || "Chào mừng bạn đến với " + name}</span>{phone && <a href={`tel:${phone.replace(/[^+\d]/g, "")}`}>Tư vấn: {phone}</a>}</div><header className={styles.header}><a className={styles.brand} href="/" aria-label={`${name} — Trang chủ`}>{logo ? <img src={logo.image.url} width={logo.image.width} height={logo.image.height} alt={logo.alt || name} /> : <><span className={styles.monogram}>{Array.from(name)[0]}</span><strong>{name}</strong></>}</a><nav className={styles.desktopNav} aria-label="Menu chính">{links}</nav><a className={styles.contactButton} href="#contact">Kết nối với chúng tôi <span aria-hidden="true">↗</span></a><details className={styles.mobileMenu}><summary>Menu ☰</summary><nav aria-label="Menu di động">{links}</nav></details></header></>;
}
