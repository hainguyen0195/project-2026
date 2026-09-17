"use client";

import { useState } from "react";
import type { SitePhoto } from "./home-data";
import { safeLink } from "./site-links";
import styles from "./website.module.css";

export default function HeroSlider({ slides, name, slogan }: { slides: SitePhoto[]; name: string; slogan: string }) {
  const [index, setIndex] = useState(0);
  const slide = slides[index];
  return <section id="home" className={styles.hero} aria-label="Giới thiệu website" aria-roledescription="carousel">
    <div className={styles.heroCopy} aria-live="polite"><span className={styles.eyebrow}>{slide?.text1 || "CHÀO MỪNG ĐẾN VỚI " + name}</span><h1>{slide?.name || slogan || "Khám phá những điều phù hợp với bạn."}</h1><p>{slide?.text2 || `Tìm hiểu sản phẩm, câu chuyện và những cập nhật mới nhất từ ${name}.`}</p><div className={styles.heroActions}><a className={styles.primaryButton} href={safeLink(slide?.link) || "#products"}>{slide?.link ? "Khám phá ngay" : "Khám phá sản phẩm"} <span aria-hidden="true">↗</span></a><a className={styles.textLink} href="#contact">Liên hệ tư vấn <span aria-hidden="true">→</span></a></div>{slides.length > 1 && <div className={styles.sliderControls}><button aria-label="Slide trước" onClick={() => setIndex(value => (value - 1 + slides.length) % slides.length)}>←</button><span>{String(index + 1).padStart(2, "0")} / {String(slides.length).padStart(2, "0")}</span><button aria-label="Slide tiếp theo" onClick={() => setIndex(value => (value + 1) % slides.length)}>→</button></div>}</div>
    <div className={styles.heroVisual}>{slide ? <img key={slide.id} src={slide.image.url} alt={slide.alt || slide.name} width={slide.image.width} height={slide.image.height} fetchPriority="high" /> : <div className={styles.abstractArt} aria-hidden="true"><div /><div /><span>{name}</span><small>A NEW PERSPECTIVE</small></div>}<span className={styles.visualCaption}>{name} <span>01 — KHÁM PHÁ</span></span></div>
  </section>;
}
