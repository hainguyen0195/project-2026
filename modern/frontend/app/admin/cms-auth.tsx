"use client";
import { useCmsLocale } from "./cms-locale";

import { createContext, useContext, useEffect, useState, type ReactNode } from "react";
import { useRouter } from "next/navigation";
import { api, ApiError, errorMessage, type CmsUser } from "./cms-api";

const AuthContext = createContext<{ user: CmsUser; refresh: () => Promise<void>; logout: () => Promise<void> } | null>(null);
export function useCmsAuth() {
  const context = useContext(AuthContext);
  if (!context) throw new Error("CMS authentication required");
  return context;
}
export default function CmsAuth({ children }: { children: ReactNode }) {
  const { t } = useCmsLocale();
  const router = useRouter();
  const [user, setUser] = useState<CmsUser | null>(null);
  const [error, setError] = useState("");
  const [busy, setBusy] = useState(false);
  async function refresh() {
    try { const result = await api<{ data: CmsUser }>("/auth/me"); setUser(result.data); setError(""); }
    catch (error) { setUser(null); if (error instanceof ApiError && error.status === 401) router.replace("/login"); else setError(errorMessage(error)); }
  }
  async function logout() {
    try { await api("/auth/logout", "POST"); setUser(null); router.replace("/login"); }
    catch (error) { setError(errorMessage(error)); }
  }
  useEffect(() => {
    let live = true;
    const check = () => { if (live) void refresh(); };
    const expired = () => { setUser(null); router.replace("/login"); };
    check(); const interval = setInterval(check, 60000);
    window.addEventListener("focus", check); window.addEventListener("cms:unauthorized", expired);
    return () => { live = false; clearInterval(interval); window.removeEventListener("focus", check); window.removeEventListener("cms:unauthorized", expired); };
  }, []);
  if (!user) return <div className="auth-page"><section className="auth-card"><h1>COMI Studio</h1><p role="status">{error || t("Đang kiểm tra phiên đăng nhập…")}</p>{error && <button className="button primary" onClick={refresh}>{t("Thử lại")}</button>}</section></div>;
  if (user.must_change_password) return <div className="auth-page"><section className="auth-card"><span className="eyebrow">{t("BẢO MẬT TÀI KHOẢN")}</span><h1>{t("Đặt mật khẩu của bạn")}</h1><p>{t("Xin chào")} {user.name}{t(". Hãy thay mật khẩu tạm thời trước khi vào CMS.")}</p><form className="cms-form" onSubmit={async event => {
    event.preventDefault(); setBusy(true); setError(""); const data = Object.fromEntries(new FormData(event.currentTarget));
    try { const result = await api<{ data: CmsUser }>("/auth/password", "PUT", data); setUser(result.data); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); }
  }}><label>{t("Mật khẩu tạm thời")}<input name="current_password" type="password" autoComplete="current-password" required /></label><label>{t("Mật khẩu mới")}<input name="password" type="password" autoComplete="new-password" minLength={8} maxLength={72} required /></label><label>{t("Nhập lại mật khẩu mới")}<input name="password_confirmation" type="password" autoComplete="new-password" required /></label><small>{t("Ít nhất 8 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt.")}</small>{error && <p className="cms-error" role="alert">{t(error)}</p>}<button className="button primary" disabled={busy}>{busy ? t("Đang lưu…") : t("Đổi mật khẩu & tiếp tục")}</button><button type="button" className="button secondary" onClick={logout}>{t("Đăng xuất")}</button></form></section></div>;
  return <AuthContext.Provider value={{ user, refresh, logout }}>{error && <div role="alert" className="cms-error">{t(error)}</div>}{children}</AuthContext.Provider>;
}
