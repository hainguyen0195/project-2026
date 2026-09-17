"use client";

import { createContext, useContext, useEffect, useState, type ReactNode } from "react";
import { englishMessages } from "./cms-messages";

export type CmsLocale = "vi" | "en";
type Translate = (message: string, values?: Record<string, string | number>) => string;
const LocaleContext = createContext<{ locale: CmsLocale; setLocale: (locale: CmsLocale) => void; t: Translate; storageError: boolean } | null>(null);
const storageKey = "comi-cms-language";

export function CmsLocaleProvider({ children }: { children: ReactNode }) {
  const [locale, updateLocale] = useState<CmsLocale>("vi");
  const [storageError, setStorageError] = useState(false);
  useEffect(() => {
    try { updateLocale(localStorage.getItem(storageKey) === "en" ? "en" : "vi"); } catch { setStorageError(true); }
    const sync = (event: StorageEvent) => { if (event.key === storageKey || event.key === null) updateLocale(event.newValue === "en" ? "en" : "vi"); };
    window.addEventListener("storage", sync);
    return () => window.removeEventListener("storage", sync);
  }, []);
  useEffect(() => { document.documentElement.lang = locale; document.title = locale === "en" ? "COMI Studio · Administration" : "COMI Studio · Quản trị"; }, [locale]);
  function setLocale(next: CmsLocale) {
    updateLocale(next);
    try { localStorage.setItem(storageKey, next); setStorageError(false); } catch { setStorageError(true); }
  }
  const t: Translate = (message, values) => {
    const translated = locale === "en" ? englishMessages[message] ?? message : message;
    return translated.replace(/\{(\w+)\}/g, (match, key: string) => values?.[key] === undefined ? match : String(values[key]));
  };
  return <LocaleContext.Provider value={{ locale, setLocale, t, storageError }}>{children}</LocaleContext.Provider>;
}

export function useCmsLocale() {
  const context = useContext(LocaleContext);
  if (!context) throw new Error("CMS locale provider required");
  return context;
}

export function CmsLanguageSettings() {
  const { locale, setLocale, t, storageError } = useCmsLocale();
  return <section className="panel settings-panel cms-language-settings">
    <div className="panel-heading"><div><h2>{t("Ngôn ngữ CMS")}</h2><p>{t("Chọn ngôn ngữ giao diện quản trị của bạn.")}</p></div><span aria-hidden="true">VI / EN</span></div>
    <div className="cms-language-options" role="group" aria-label={t("Ngôn ngữ CMS")}>
      {([{ value: "vi", name: "Tiếng Việt", note: "Vietnamese" }, { value: "en", name: "English", note: "Tiếng Anh" }] as const).map(option => <button key={option.value} type="button" className={`theme-card ${locale === option.value ? "chosen" : ""}`} aria-pressed={locale === option.value} onClick={() => setLocale(option.value)}><strong lang={option.value}>{option.name}</strong><p>{option.note}</p></button>)}
    </div>
    <div className="settings-note"><p>{t("Áp dụng ngay và lưu trên trình duyệt này. Không thay đổi ngôn ngữ nội dung sản phẩm, SEO, tiền tệ hay phân quyền tài khoản.")}</p></div>
    {storageError && <p className="cms-error" role="status">{t("Trình duyệt không cho phép lưu lựa chọn. Ngôn ngữ chỉ áp dụng cho phiên này.")}</p>}
  </section>;
}
