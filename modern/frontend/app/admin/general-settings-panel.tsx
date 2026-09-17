"use client";

import { useEffect, useState } from "react";
import { api, errorMessage } from "./cms-api";
import { useCmsAuth } from "./cms-auth";
import { useCmsLocale } from "./cms-locale";
import { showNotification } from "./notifications";

type Settings = { translations: { vi: Record<string, string>; en: Record<string, string> }; options: Record<string, string>; analytics?: string; mastertool?: string; headjs?: string; bodyjs?: string };
const company = [["name", "Tên công ty / website", "Company / website name"], ["address", "Địa chỉ", "Address"], ["slogan", "Slogan", "Slogan"], ["copyright", "Bản quyền", "Copyright"], ["keysearch", "Từ khóa tìm kiếm", "Search keywords"]];
const contact = [["email", "Email", "Email", "email"], ["hotline", "Hotline", "Hotline", "tel"], ["phone", "Điện thoại", "Phone", "tel"], ["zalo", "Zalo", "Zalo", "text"], ["oaidzalo", "Zalo OA ID", "Zalo OA ID", "text"], ["website", "Website", "Website", "url"], ["worktime", "Giờ làm việc", "Working hours", "text"]];
const social = ["facebook", "fanpage", "twitter", "instagram", "youtube"].map(key => [key, key.charAt(0).toUpperCase() + key.slice(1), key.charAt(0).toUpperCase() + key.slice(1), "url"]);
const maps = [["coords", "Tọa độ bản đồ", "Map coordinates", "text"], ["link_googlemaps", "Liên kết Google Maps", "Google Maps URL", "url"]];

export default function GeneralSettingsPanel() {
  const { locale } = useCmsLocale();
  const { user } = useCmsAuth();
  const editable = user.permissions.includes("general.manage");
  const text = (vi: string, en: string) => locale === "vi" ? vi : en;
  const [data, setData] = useState<Settings | null>(null);
  const [saved, setSaved] = useState<Settings | null>(null);
  const [language, setLanguage] = useState<"vi" | "en">("vi");
  const [tab, setTab] = useState("company");
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [retry, setRetry] = useState(0);
  const dirty = JSON.stringify(data) !== JSON.stringify(saved);
  useEffect(() => {
    let cancelled = false;
    setError("");
    api<{ data: Settings }>("/cms/general-settings").then(result => {
      if (!cancelled) { const value = { ...result.data, translations: { vi: result.data.translations?.vi || {}, en: result.data.translations?.en || {} } }; setData(value); setSaved(value); }
    }).catch(failure => { if (!cancelled) setError(errorMessage(failure)); });
    return () => { cancelled = true; };
  }, [retry]);
  useEffect(() => {
    const warn = (event: BeforeUnloadEvent) => { if (dirty) event.preventDefault(); };
    window.addEventListener("beforeunload", warn);
    return () => window.removeEventListener("beforeunload", warn);
  }, [dirty]);
  async function save() {
    if (!data || busy || !editable) return;
    setBusy(true); setError("");
    try {
      const result = await api<{ data: Settings }>("/cms/general-settings", "PUT", data);
      setData(result.data); setSaved(result.data);
      window.dispatchEvent(new Event("cms:branding-updated"));
      showNotification(text("Đã lưu thông tin chung.", "General information saved."));
    } catch (failure) { setError(errorMessage(failure)); }
    finally { setBusy(false); }
  }
  if (!data) return error ? <div className="cms-error" role="alert">{error}<button className="button secondary" onClick={() => setRetry(value => value + 1)}>{text("Thử lại", "Retry")}</button></div> : <p role="status">{text("Đang tải thông tin…", "Loading…")}</p>;
  const option = (key: string, value: string) => setData({ ...data, options: { ...data.options, [key]: value } });
  return <section className="product-edit">
    <div className="access-toolbar"><span className="subtle">{dirty ? text("Có thay đổi chưa lưu", "Unsaved changes") : text("Thông tin chung của website", "Website information")}</span><div className="access-tabs"><button type="button" className="button secondary" disabled={busy || !dirty} onClick={() => { if (confirm(text("Bỏ các thay đổi chưa lưu?", "Discard unsaved changes?"))) setData(saved); }}>{text("Khôi phục", "Reset")}</button>{editable && <button type="button" className="button primary" disabled={busy} onClick={() => void save()}>{busy ? text("Đang lưu…", "Saving…") : text("Lưu thông tin", "Save information")}</button>}</div></div>
    {error && <p className="cms-error" role="alert">{error}</p>}
    <div className="product-tabs">{[["company", "Công ty & liên hệ", "Company & contact"], ["social", "Mạng xã hội", "Social media"], ["maps", "Bản đồ", "Maps"], ["integration", "Mã tích hợp", "Integrations"]].map(([key, vi, en]) => <button type="button" key={key} className={tab === key ? "active" : ""} onClick={() => setTab(key)}>{text(vi, en)}</button>)}</div>
    <fieldset disabled={busy} className="general-settings-fields">
      {tab === "company" && <div className="product-content-stack"><section className="panel product-card cms-form"><div className="access-tabs">{(["vi", "en"] as const).map(value => <button type="button" key={value} className={`button ${language === value ? "primary" : "secondary"}`} aria-pressed={language === value} onClick={() => setLanguage(value)}>{value === "vi" ? "Tiếng Việt" : "English"}</button>)}</div>{company.map(([key, vi, en]) => <label key={key}>{text(vi, en)} ({language.toUpperCase()}){key === "name" && language === "vi" ? " *" : ""}<input readOnly={!editable} maxLength={key === "name" ? 255 : 1000} value={data.translations[language]?.[key] || ""} onChange={event => setData({ ...data, translations: { ...data.translations, [language]: { ...data.translations[language], [key]: event.target.value } } })} /></label>)}</section><section className="panel product-card cms-form"><label>{text("Ngôn ngữ mặc định của website", "Default website language")}<select disabled={!editable} value={data.options.lang_default || "vi"} onChange={event => option("lang_default", event.target.value)}><option value="vi">Tiếng Việt</option><option value="en">English</option></select></label>{contact.map(([key, vi, en, type]) => <label key={key}>{text(vi, en)}<input readOnly={!editable} type={type} maxLength={type === "url" ? 2048 : 255} value={data.options[key] || ""} onChange={event => option(key, event.target.value)} /></label>)}</section></div>}
      {(tab === "social" || tab === "maps") && <section className="panel product-card cms-form">{(tab === "social" ? social : maps).map(([key, vi, en, type]) => <label key={key}>{text(vi, en)}<input readOnly={!editable} type={type} maxLength={2048} value={data.options[key] || ""} onChange={event => option(key, event.target.value)} /></label>)}{tab === "maps" && <label>{text("Mã nhúng Google Maps", "Google Maps embed code")}<textarea readOnly={!editable} rows={6} maxLength={10000} value={data.options.coords_iframe || ""} onChange={event => option("coords_iframe", event.target.value)} /><small>{text("Chỉ lưu mã, không thực thi trong admin.", "Stored as text; never executed in admin.")}</small></label>}</section>}
      {tab === "integration" && <section className="panel product-card cms-form"><p className="subtle">{text("Chỉ nhập mã từ nguồn tin cậy. Các đoạn mã được lưu dạng văn bản, không chạy trong admin; chưa được gắn vào website công khai.", "Only use trusted code. Stored as text, never executed in admin; not yet connected to the public website.")}</p>{([["analytics", "Google Analytics"], ["mastertool", "Google Search Console / Webmaster Tool"], ["headjs", "Head JS"], ["bodyjs", "Body JS"]] as const).map(([key, label]) => <label key={key}>{label}<textarea readOnly={!editable} className="code-input" rows={5} maxLength={30000} value={data[key] || ""} onChange={event => setData({ ...data, [key]: event.target.value })} /></label>)}</section>}
    </fieldset>
  </section>;
}
