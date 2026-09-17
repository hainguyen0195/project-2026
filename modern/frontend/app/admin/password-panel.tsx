"use client";
import { useCmsLocale } from "./cms-locale";
import { useState } from "react";
import { api, errorMessage } from "./cms-api";
import { useCmsAuth } from "./cms-auth";
export default function PasswordPanel() {
  const { t } = useCmsLocale();
  const { refresh } = useCmsAuth();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  return <section className="panel password-panel"><h2>{t("Đổi mật khẩu")}</h2><p className="subtle">{t("Các phiên đăng nhập khác sẽ bị thu hồi sau khi đổi mật khẩu.")}</p><form className="cms-form" onSubmit={async event => {
    event.preventDefault(); const form = event.currentTarget; setBusy(true); setError(""); setNotice("");
    try { await api("/auth/password", "PUT", Object.fromEntries(new FormData(form))); form.reset(); setNotice("Đã đổi mật khẩu thành công."); await refresh(); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); }
  }}><label>{t("Mật khẩu hiện tại")}<input name="current_password" type="password" autoComplete="current-password" required /></label><label>{t("Mật khẩu mới")}<input name="password" type="password" autoComplete="new-password" minLength={8} maxLength={72} required /></label><label>{t("Nhập lại mật khẩu mới")}<input name="password_confirmation" type="password" autoComplete="new-password" required /></label><small>{t("Ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.")}</small>{error && <p role="alert" className="cms-error">{t(error)}</p>}{notice && <p role="status" className="cms-success">{t(notice)}</p>}<button className="button primary" disabled={busy}>{busy ? t("Đang lưu…") : t("Cập nhật mật khẩu")}</button></form></section>;
}
