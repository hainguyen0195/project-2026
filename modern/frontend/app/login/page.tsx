"use client";
import { useCmsLocale } from "../admin/cms-locale";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { ArrowRight, Eye, EyeOff, LockKeyhole, Moon, Sun, ShieldCheck } from "lucide-react";
import { api, ApiError, errorMessage } from "../admin/cms-api";

export default function LoginPage() {
  const { t } = useCmsLocale();
  const router = useRouter();
  const [visible, setVisible] = useState(false);
  const [dark, setDark] = useState(false);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  useEffect(() => {
    setDark(document.documentElement.dataset.theme === "dark");
    api("/auth/me").then(() => router.replace("/admin")).catch(error => { if (!(error instanceof ApiError && error.status === 401)) setError(errorMessage(error)); });
  }, [router]);
  return <main className="auth-page"><div className="login-shell"><section className="login-story"><a className="brand" href="/">comi<span className="brand-caption">STUDIO</span></a><div><span className="banner-kicker">{t("KHÔNG GIAN QUẢN TRỊ CỦA BẠN")}</span><h1>{t("Một nơi.")}<br />{t("Mọi công việc.")}</h1><p>{t("Quản lý cửa hàng, cộng tác cùng đội ngũ và dành nhiều thời gian hơn cho những điều quan trọng.")}</p><div className="login-orbit" aria-hidden="true"><ShieldCheck size={76} strokeWidth={1} /></div></div><small>{t("Gọn gàng hơn. Tập trung hơn. Cùng COMI.")}</small></section><section className="auth-card login-card"><button className="icon-button login-theme" aria-label={t("Đổi giao diện sáng tối")} onClick={() => { const value = !dark; setDark(value); document.documentElement.dataset.theme = value ? "dark" : "light"; try { localStorage.setItem("comi-theme", value ? "dark" : "light"); } catch {} }}>{dark ? <Sun size={20} /> : <Moon size={20} />}</button><span className="auth-icon"><LockKeyhole size={24} /></span><span className="eyebrow">{t("CHÀO MỪNG TRỞ LẠI")}</span><h2>{t("Đăng nhập CMS")}</h2><p>{t("Sử dụng tài khoản được quản trị viên cấp để tiếp tục.")}</p><form className="cms-form" onSubmit={async event => {
    event.preventDefault(); setBusy(true); setError(""); const data = Object.fromEntries(new FormData(event.currentTarget));
    try { await api("/auth/login", "POST", data); router.replace("/admin"); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); }
  }}><label>{t("Email công việc")}<input name="email" type="email" autoComplete="username" placeholder="ban@congty.vn" required /></label><label>{t("Mật khẩu")}<div className="password-field"><input name="password" type={visible ? "text" : "password"} autoComplete="current-password" placeholder={t("Nhập mật khẩu của bạn")} required maxLength={72} /><button type="button" className="icon-button" aria-label={visible ? t("Ẩn mật khẩu") : t("Hiện mật khẩu")} onClick={() => setVisible(!visible)}>{visible ? <EyeOff size={18} /> : <Eye size={18} />}</button></div></label>{error && <div className="cms-error" role="alert">{t(error)}</div>}<button disabled={busy} className="button primary">{busy ? t("Đang đăng nhập…") : t("Đăng nhập")}<ArrowRight size={17} /></button></form><p className="login-help">{t("Chưa có tài khoản hoặc quên mật khẩu?")}<br />{t("Liên hệ quản trị viên hệ thống để được hỗ trợ.")}</p><div className="login-security"><ShieldCheck size={15} /> {t("Phiên đăng nhập được bảo vệ")}</div></section></div></main>;
}
