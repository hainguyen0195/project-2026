"use client";
import { useCmsLocale } from "./cms-locale";

import { type AiSeoContent } from "./product-types";

const emptyContent: AiSeoContent = { summary: "", audience: "", use_cases: "", alternate_names: "", faqs: [], sources: [] };

export default function ProductAiSeo({ value, language, noindex, onChange }: { value?: AiSeoContent; language: "vi" | "en"; noindex: boolean; onChange: (value: AiSeoContent) => void }) {
  const { t } = useCmsLocale();
  const content = { ...emptyContent, ...value };
  function change<Key extends keyof AiSeoContent>(key: Key, next: AiSeoContent[Key]) { onChange({ ...content, [key]: next }); }
  return <div className="product-content-stack">
    <section className="panel product-card cms-form">
      <h2>{t("SEO cho AI ·")} {language.toUpperCase()}</h2>
      <p className="subtle">{t("Chuẩn bị nội dung rõ ràng để trả lời câu hỏi của người mua. Đây là dữ liệu biên tập, không tự viết bằng AI và không bảo đảm được AI trích dẫn. Chỉ nhập thông tin có thể kiểm chứng, không nhồi từ khóa.")}</p>
      <p className="form-note">{t("Hiện lưu trong CMS/API. Khi tích hợp website bán hàng, cần hiển thị tóm tắt, FAQ và nguồn tham khảo trên trang sản phẩm công khai; dữ liệu chỉ nằm trong CMS không được công cụ tìm kiếm đọc.")}</p>
      {noindex && <p role="status" className="cms-error">{t("Sản phẩm đang bật noindex. Kiểm tra lại tại tab SEO & Schema nếu muốn trang được lập chỉ mục.")}</p>}
      <label>{t("Tóm tắt trả lời nhanh")}<textarea rows={4} maxLength={1500} value={content.summary || ""} placeholder={t("Sản phẩm là gì, giải quyết nhu cầu nào và có điểm khác biệt gì? Viết ngắn gọn dựa trên thông tin thật.")} onChange={event => change("summary", event.target.value)} /><small>{(content.summary || "").length}{t("/1500 ký tự")}</small></label>
      <label>{t("Đối tượng phù hợp")}<textarea rows={3} maxLength={1000} value={content.audience || ""} placeholder={t("Ai phù hợp, ai không phù hợp hoặc cần lưu ý trước khi mua?")} onChange={event => change("audience", event.target.value)} /></label>
      <label>{t("Tình huống sử dụng")}<textarea rows={3} maxLength={2000} value={content.use_cases || ""} placeholder={t("Mỗi dòng một nhu cầu hoặc tình huống sử dụng thực tế.")} onChange={event => change("use_cases", event.target.value)} /></label>
      <label>{t("Tên gọi khác")}<textarea rows={3} maxLength={1000} value={content.alternate_names || ""} placeholder={t("Mỗi dòng một tên gọi khác chính xác của sản phẩm, không nhập danh sách từ khóa chung.")} onChange={event => change("alternate_names", event.target.value)} /><small>{t("Tên gọi tiếng Việt được đưa vào alternateName khi dùng schema Product tự động.")}</small></label>
    </section>
    <section className="panel product-card cms-form">
      <h2>{t("Câu hỏi thường gặp ·")} {content.faqs.length}/20</h2>
      <p className="subtle">{t("Trả lời trực tiếp về cách dùng, tương thích, bảo quản hoặc hạn chế. Không tự tạo FAQPage schema hay cam kết rich result.")}</p>
      {content.faqs.map((faq, index) => <div className="seo-preview" key={index}>
        <label>{t("Câu hỏi")} {index + 1}<input maxLength={300} value={faq.question} onChange={event => change("faqs", content.faqs.map((item, position) => position === index ? { ...item, question: event.target.value } : item))} /></label>
        <label>{t("Trả lời")} {index + 1}<textarea rows={3} maxLength={3000} value={faq.answer} onChange={event => change("faqs", content.faqs.map((item, position) => position === index ? { ...item, answer: event.target.value } : item))} /></label>
        <button type="button" className="button secondary" aria-label={t("Xóa câu hỏi {0}", { "0": index + 1 })} onClick={() => change("faqs", content.faqs.filter((_, position) => position !== index))}>{t("Xóa câu hỏi")}</button>
      </div>)}
      <button type="button" className="button secondary" disabled={content.faqs.length >= 20} onClick={() => change("faqs", [...content.faqs, { question: "", answer: "" }])}>{t("Thêm câu hỏi")}</button>
    </section>
    <section className="panel product-card cms-form">
      <h2>{t("Nguồn tham khảo ·")} {content.sources.length}/10</h2>
      <p className="subtle">{t("Dẫn tài liệu nhà sản xuất hoặc nguồn xác thực cho thông tin đã nhập. Hệ thống không tự truy cập hay xác minh các URL này.")}</p>
      {content.sources.map((source, index) => <div className="seo-preview" key={index}>
        <label>{t("Tên nguồn")} {index + 1}<input maxLength={255} value={source.title} onChange={event => change("sources", content.sources.map((item, position) => position === index ? { ...item, title: event.target.value } : item))} /></label>
        <label>{t("URL nguồn")} {index + 1}<input type="url" maxLength={2048} placeholder="https://..." value={source.url} onChange={event => change("sources", content.sources.map((item, position) => position === index ? { ...item, url: event.target.value } : item))} /></label>
        <button type="button" className="button secondary" aria-label={t("Xóa nguồn {0}", { "0": index + 1 })} onClick={() => change("sources", content.sources.filter((_, position) => position !== index))}>{t("Xóa nguồn")}</button>
      </div>)}
      <button type="button" className="button secondary" disabled={content.sources.length >= 10} onClick={() => change("sources", [...content.sources, { title: "", url: "" }])}>{t("Thêm nguồn tham khảo")}</button>
    </section>
    <section className="panel product-card"><h2>{t("Xem trước nội dung ·")} {language.toUpperCase()}</h2><div className="seo-preview" style={{ whiteSpace: "pre-wrap" }}>
      <p>{content.summary || t("Chưa có tóm tắt trả lời nhanh.")}</p>
      {content.audience && <><h3>{t("Phù hợp với ai?")}</h3><p>{content.audience}</p></>}
      {content.use_cases && <><h3>{t("Tình huống sử dụng")}</h3><p>{content.use_cases}</p></>}
      {content.faqs.map((faq, index) => <div key={index}><h3>{faq.question}</h3><p>{faq.answer}</p></div>)}
      {content.sources.map((source, index) => <p key={index}>{source.title}{source.url ? t(" — {0}", { "0": source.url }) : ""}</p>)}
    </div></section>
  </div>;
}
