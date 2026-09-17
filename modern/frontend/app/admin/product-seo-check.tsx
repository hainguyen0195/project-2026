"use client";

import { useMemo } from "react";
import { CheckCircle2, CircleAlert, Info, Search, XCircle } from "lucide-react";
import { useCmsLocale } from "./cms-locale";
import { type CatalogProduct, slugify } from "./product-types";

type Check = { label: string; pass: boolean; warning?: boolean; info?: boolean; detail?: string };

export default function ProductSeoCheck({ product, language, keyword, onKeywordChange }: { product: CatalogProduct; language: "vi" | "en"; keyword: string; onKeywordChange: (value: string) => void }) {
  const { locale } = useCmsLocale();
  const text = (vi: string, en: string) => locale === "en" ? en : vi;
  const localized = (key: "name" | "slug" | "seo_title" | "seo_description" | "content" | "description") => language === "vi" ? product[key] || "" : product.translations?.en?.[key] || "";
  const content = localized("content");
  const description = localized("description");
  const parsed = useMemo(() => {
    if (typeof document === "undefined") return { body: "", headings: [] as string[], alts: [] as string[], images: 0 };
    const parse = (html: string) => {
      const template = document.createElement("template");
      template.innerHTML = html;
      template.content.querySelectorAll("script,style,noscript").forEach(element => element.remove());
      return template.content;
    };
    const plain = (node: Node) => {
      const copy = node.cloneNode(true) as DocumentFragment;
      copy.querySelectorAll("p,div,h1,h2,h3,h4,h5,h6,li,br,tr,td,th").forEach(element => element.append(document.createTextNode(" ")));
      return (copy.textContent || "").replace(/\s+/g, " ").trim();
    };
    const full = parse(content);
    const source = plain(full) ? full : parse(description);
    return { body: plain(source), headings: Array.from(source.querySelectorAll("h2,h3,h4"), heading => plain(heading)), alts: Array.from(source.querySelectorAll("img"), image => image.getAttribute("alt") || ""), images: source.querySelectorAll("img").length };
  }, [content, description]);
  const normalize = (value: string) => value.normalize("NFC").toLocaleLowerCase(language).replace(/\s+/g, " ").trim();
  const focus = normalize(keyword);
  const body = normalize(parsed.body);
  const contains = (value: string) => Boolean(focus) && normalize(value).includes(focus);
  const title = localized("seo_title").trim();
  const meta = localized("seo_description").trim();
  const slug = localized("slug") || slugify(localized("name"));
  const words = body.split(/\s+/).filter(Boolean).length;
  const occurrences = focus ? body.split(focus).length - 1 : 0;
  const density = words ? occurrences / words * 100 : 0;
  const titleLength = Array.from(title).length;
  const metaLength = Array.from(meta).length;
  const titlePosition = normalize(title).indexOf(focus);
  const bodyPosition = body.indexOf(focus);
  const alts = [...parsed.alts, ...(product.main_image ? [product.image_alt] : []), ...product.images.filter(image => image.is_active).map(image => image.alt)];
  const checks: Check[] = [
    { label: text("Từ khóa chính có trong tên sản phẩm.", "Focus keyword appears in the product name."), pass: contains(localized("name")) },
    { label: text("Từ khóa chính có trong tiêu đề SEO.", "Focus keyword appears in the SEO title."), pass: contains(title) },
    { label: text("Từ khóa nằm gần đầu tiêu đề SEO (20 ký tự đầu).", "Focus keyword appears near the beginning of the SEO title (first 20 characters)."), pass: titlePosition >= 0 && titlePosition <= 20 },
    { label: text("Tiêu đề SEO: gợi ý 40–70 ký tự.", "SEO title: suggested 40–70 characters."), pass: titleLength >= 40 && titleLength <= 70, warning: titleLength > 0, detail: `${titleLength}/70` },
    { label: text("Từ khóa chính có trong mô tả meta.", "Focus keyword appears in the meta description."), pass: contains(meta) },
    { label: text("Mô tả meta: gợi ý 100–160 ký tự.", "Meta description: suggested 100–160 characters."), pass: metaLength >= 100 && metaLength <= 160, warning: metaLength > 0, detail: `${metaLength}/160` },
    { label: text("Từ khóa chính có trong slug (đã bỏ dấu).", "Focus keyword appears in the normalized slug."), pass: Boolean(slugify(focus)) && slug.includes(slugify(focus)) },
    { label: text("Slug: gợi ý 10–75 ký tự.", "Slug: suggested 10–75 characters."), pass: slug.length >= 10 && slug.length <= 75, warning: slug.length > 0, detail: `${slug.length}/75` },
    { label: text("Từ khóa nằm trong 200 ký tự đầu nội dung.", "Focus keyword appears in the first 200 characters of content."), pass: bodyPosition >= 0 && bodyPosition < 200 },
    { label: text("Từ khóa chính có trong nội dung.", "Focus keyword appears in the content."), pass: occurrences > 0 },
    { label: text("Độ dài nội dung: gợi ý 600–2.500 từ theo bộ kiểm tra cũ.", "Content length: suggested 600–2,500 words from the legacy checker."), pass: words >= 600 && words <= 2500, warning: words > 0, detail: `${words} ${text("từ", "words")}` },
    { label: text("Từ khóa có trong tiêu đề phụ H2, H3 hoặc H4.", "Focus keyword appears in an H2, H3 or H4 heading."), pass: parsed.headings.some(contains), warning: parsed.headings.length > 0 },
    { label: text("Từ khóa có trong alt của ít nhất một ảnh.", "Focus keyword appears in at least one image alt text."), pass: alts.some(contains) },
    { label: text("Mật độ từ khóa: gợi ý 0,5–2,5% (ước tính theo số lần xuất hiện / số từ).", "Keyword density: suggested 0.5–2.5% (estimated occurrences / word count)."), pass: density >= 0.5 && density <= 2.5, warning: density > 2.5, detail: `${density.toFixed(1)}%` },
    { label: text("Có hình ảnh trong nội dung, ảnh đại diện hoặc album hiển thị.", "Images are present in content, the main image or the visible gallery."), pass: parsed.images > 0 || Boolean(product.main_image) || product.images.some(image => image.is_active) },
    { label: text("Kiểm tra thủ công liên kết nội bộ: đúng trang và hữu ích cho người đọc.", "Manually review internal links for relevance and correct destinations."), pass: false, info: true },
    { label: text("Chia đoạn ngắn, dễ đọc; không kéo dài nội dung hoặc nhồi từ khóa chỉ để đạt điểm.", "Keep paragraphs readable; do not pad content or stuff keywords to raise the score."), pass: false, info: true },
  ];
  const total = checks.filter(check => !check.info).length;
  const passed = checks.filter(check => !check.info && check.pass).length;
  const score = focus ? Math.round(passed / total * 100) : 0;
  const grade = score >= 70 ? "pass" : score >= 40 ? "warn" : "fail";
  return <section className="panel product-card product-seo-check">
    <div className="seo-check-heading"><div><h2><Search size={20} aria-hidden="true" /> SEO Analyzer ({language.toUpperCase()})</h2><p className="subtle">{text("Phân tích nội dung theo từ khóa chính", "Analyze content against a focus keyword")}</p></div><div className={`seo-check-score ${focus ? grade : "info"}`} role="status"><strong>{focus ? `${score}/100` : "—"}</strong><span>{!focus ? text("Chưa phân tích", "Not analyzed") : score >= 70 ? text("Tốt", "Good") : score >= 40 ? text("Cần cải thiện", "Needs improvement") : text("Yếu", "Poor")}</span></div></div>
    <div className="cms-form"><label>{text("Từ khóa chính", "Focus keyword")} ({language.toUpperCase()})<input maxLength={100} value={keyword} onChange={event => onKeywordChange(event.target.value)} placeholder={text("Nhập từ khóa để bắt đầu phân tích", "Enter a keyword to start analyzing")} /><small>{Array.from(keyword).length}/100 · {text("Từ khóa kiểm tra chỉ giữ trong lần chỉnh sửa này, không lưu vào sản phẩm.", "The analysis keyword is kept for this editing session only, not saved to the product.")}</small></label></div>
    {focus ? <><p className="subtle">{passed}/{total} {text("tiêu chí đạt; mục gợi ý không tính điểm.", "checks passed; advisory items are not scored.")}</p><ul className="seo-check-list">{checks.map((check, index) => {
      const status = check.info ? "info" : check.pass ? "pass" : check.warning ? "warn" : "fail";
      const Icon = check.info ? Info : check.pass ? CheckCircle2 : check.warning ? CircleAlert : XCircle;
      return <li key={index} className={`seo-check-row ${status}`}><Icon size={18} aria-label={check.info ? text("Gợi ý", "Advice") : check.pass ? text("Đạt", "Passed") : check.warning ? text("Cần cải thiện", "Needs improvement") : text("Chưa đạt", "Not passed")} /><span>{check.label}</span>{check.detail && <small>{check.detail}</small>}</li>;
    })}</ul></> : <p className="access-empty">{text("Nhập từ khóa chính để xem điểm và checklist SEO.", "Enter a focus keyword to view the SEO score and checklist.")}</p>}
    <p className="subtle seo-check-note">{text("Điểm tham khảo theo bộ kiểm tra nội bộ, không phải điểm xếp hạng Google. Phân tích nội dung chi tiết, hoặc mô tả ngắn nếu nội dung trống; dùng slug dự kiến nếu chưa nhập slug.", "This internal checklist score is not a Google ranking score. It analyzes detailed content, falling back to the short description, and uses a generated slug when the slug is empty.")}</p>
    {product.noindex && <p className="cms-error">{text("Sản phẩm đang bật noindex: điểm checklist không thay đổi thiết lập không lập chỉ mục.", "This product has noindex enabled: the checklist score does not change the indexing setting.")}</p>}
  </section>;
}
