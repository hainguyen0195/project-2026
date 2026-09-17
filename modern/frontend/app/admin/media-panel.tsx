"use client";

import { useEffect, useState } from "react";
import { api, errorMessage } from "./cms-api";
import { useCmsAuth } from "./cms-auth";
import { useCmsLocale } from "./cms-locale";
import { showNotification } from "./notifications";
import ImageCropDialog from "./image-crop-dialog";
import { type Media } from "./product-types";

export type PhotoDefinition = { key: string; label: { vi: string; en: string }; singleton: boolean; width: number; height: number; fields: string[] };
type Photo = { id?: number; image_id: number | null; image: Media | null; link: string; is_active: boolean; sort_order: number; translations: { vi?: Record<string, string>; en?: Record<string, string> } };
const emptyPhoto = (): Photo => ({ image_id: null, image: null, link: "", is_active: true, sort_order: 0, translations: { vi: {}, en: {} } });

export default function MediaPanel({ definition }: { definition: PhotoDefinition }) {
  const { locale } = useCmsLocale();
  const { user } = useCmsAuth();
  const editable = user.permissions.includes("media.manage");
  const text = (vi: string, en: string) => locale === "vi" ? vi : en;
  const endpoint = `/cms/photos/${encodeURIComponent(definition.key)}`;
  const [items, setItems] = useState<Photo[]>([]);
  const [draft, setDraft] = useState<Photo | null>(null);
  const [language, setLanguage] = useState<"vi" | "en">("vi");
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [retry, setRetry] = useState(0);
  const [crop, setCrop] = useState(false);
  useEffect(() => {
    let cancelled = false;
    setLoading(true); setError("");
    api<{ data: Photo[] }>(endpoint).then(result => {
      if (!cancelled) { setItems(result.data); if (definition.singleton) setDraft(result.data[0] || emptyPhoto()); }
    }).catch(failure => { if (!cancelled) setError(errorMessage(failure)); }).finally(() => { if (!cancelled) setLoading(false); });
    return () => { cancelled = true; };
  }, [endpoint, definition.singleton, retry]);
  async function upload(file: File) {
    if (!editable || busy) return;
    setBusy(true);
    try {
      if (file.size > 8 * 1024 * 1024) throw new Error(text("Ảnh tối đa 8 MB", "Maximum image size: 8 MB"));
      const body = new FormData(); body.append("image", file);
      const result = await api<{ data: Media }>("/cms/photo-media", "POST", body);
      setDraft(current => current ? { ...current, image: result.data, image_id: result.data.id } : current);
      setCrop(false);
    } catch (failure) { showNotification(errorMessage(failure), "error"); }
    finally { setBusy(false); }
  }
  async function save(value: Photo) {
    if (!editable || busy) return;
    setBusy(true); setError("");
    try {
      const result = await api<{ data: Photo }>(`${endpoint}${value.id ? `/${value.id}` : ""}`, value.id ? "PUT" : "POST", value);
      setItems(current => [...current.filter(item => item.id !== result.data.id), result.data].sort((left, right) => left.sort_order - right.sort_order));
      setDraft(definition.singleton ? result.data : null);
      showNotification(text("Đã lưu media.", "Media saved."));
    } catch (failure) { setError(errorMessage(failure)); showNotification(errorMessage(failure), "error"); }
    finally { setBusy(false); }
  }
  async function remove(item: Photo) {
    if (!editable || busy || !confirm(text("Xóa mục này? File ảnh gốc vẫn được giữ lại.", "Delete this entry? The image file will be retained."))) return;
    setBusy(true);
    try { await api(`${endpoint}/${item.id}`, "DELETE"); setItems(current => current.filter(value => value.id !== item.id)); showNotification(text("Đã xóa media.", "Media deleted.")); }
    catch (failure) { showNotification(errorMessage(failure), "error"); }
    finally { setBusy(false); }
  }
  if (loading) return <p role="status">{text("Đang tải media…", "Loading media…")}</p>;
  const labels: Record<string, string> = { name: text("Tên / tiêu đề", "Name / title"), text1: "Text 1", text2: "Text 2", texts: text("Danh sách text (mỗi dòng một mục)", "Text list (one item per line)"), alt: text("Mô tả ảnh (ALT)", "Image description (ALT)") };
  return <section className="product-edit">
    {error && <p role="alert" className="cms-error">{error} <button className="button secondary" onClick={() => setRetry(value => value + 1)}>{text("Tải lại", "Retry")}</button></p>}
    <div className="access-toolbar"><span className="subtle">{definition.singleton ? text("Ảnh đơn", "Single image") : `${items.length} media`} · {text("Kích thước gợi ý", "Suggested dimensions")}: {definition.width} × {definition.height}px</span>{!definition.singleton && !draft && editable && <button className="button primary" onClick={() => setDraft(emptyPhoto())}>{text("Thêm media", "Add media")}</button>}</div>
    {draft ? <form className="panel product-card cms-form" onSubmit={event => { event.preventDefault(); void save(draft); }}>
      <fieldset disabled={!editable || busy} className="media-fields">
        <div className="media-image-editor">{draft.image ? <img src={draft.image.url} alt={draft.translations[language]?.alt || ""} /> : <span>{text("Chưa có ảnh", "No image")}</span>}<label>{text("Tải ảnh", "Upload image")}<input type="file" accept="image/jpeg,image/png,image/webp,image/gif" onChange={event => { const file = event.target.files?.[0]; event.target.value = ""; if (file) void upload(file); }} /></label><small>JPG, PNG, WebP, GIF · 8 MB</small>{draft.image && <button type="button" className="button secondary" onClick={() => setCrop(true)}>{text("Chỉnh sửa / cắt ảnh", "Edit / crop image")}</button>}</div>
        <div className="access-tabs">{(["vi", "en"] as const).map(value => <button type="button" key={value} className={`button ${language === value ? "primary" : "secondary"}`} aria-pressed={language === value} onClick={() => setLanguage(value)}>{value === "vi" ? "Tiếng Việt" : "English"}</button>)}</div>
        {[...definition.fields.filter(field => field !== "link"), "alt"].map(field => <label key={field}>{labels[field] || field} ({language.toUpperCase()})<textarea rows={field === "texts" ? 4 : 2} maxLength={field === "alt" ? 255 : 5000} value={draft.translations[language]?.[field] || ""} onChange={event => setDraft({ ...draft, translations: { ...draft.translations, [language]: { ...draft.translations[language], [field]: event.target.value } } })} /></label>)}
        {definition.fields.includes("link") && <label>{text("Liên kết", "Link")}<input type="url" maxLength={2048} placeholder="https://" value={draft.link || ""} onChange={event => setDraft({ ...draft, link: event.target.value })} /></label>}
        {!definition.singleton && <label>{text("Thứ tự", "Sort order")}<input type="number" min={0} max={999999} value={draft.sort_order} onChange={event => setDraft({ ...draft, sort_order: Number(event.target.value) })} /></label>}
        <label className="media-status"><input type="checkbox" role="switch" checked={draft.is_active} onChange={event => setDraft({ ...draft, is_active: event.target.checked })} />{text("Hiển thị", "Visible")}</label>
      </fieldset>
      <div className="access-tabs">{editable && <button className="button primary" disabled={busy || !draft.image_id}>{busy ? text("Đang xử lý…", "Processing…") : text("Lưu media", "Save media")}</button>}{!definition.singleton && <button type="button" className="button secondary" disabled={busy} onClick={() => setDraft(null)}>{text("Đóng", "Close")}</button>}</div>
    </form> : <div className="media-grid">{items.map(item => <article className="panel product-card" key={item.id}>{item.image && <img className="media-thumbnail" src={item.image.url} alt={item.translations[locale]?.alt || ""} />}<strong>{item.translations[locale]?.name || item.translations.vi?.name || definition.label[locale]}</strong><small>#{item.sort_order}</small><label className="media-status"><input type="checkbox" role="switch" disabled={!editable || busy} checked={item.is_active} onChange={() => void save({ ...item, is_active: !item.is_active })} />{text("Hiển thị", "Visible")}</label><div className="access-tabs"><button className="button secondary" disabled={busy} onClick={() => setDraft(item)}>{text("Chi tiết", "Details")}</button>{editable && <button className="button secondary" disabled={busy} onClick={() => void remove(item)}>{text("Xóa", "Delete")}</button>}</div></article>)}{!items.length && <p>{text("Chưa có media. Thêm ảnh đầu tiên để bắt đầu.", "No media yet. Add your first image.")}</p>}</div>}
    {crop && draft?.image && <ImageCropDialog src={draft.image.url} onApply={upload} onClose={() => setCrop(false)} />}
  </section>;
}
