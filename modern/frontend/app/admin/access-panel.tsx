"use client";
import { useCmsLocale } from "./cms-locale";

import { useEffect, useRef, useState } from "react";
import { Plus, ShieldCheck, Users, X } from "lucide-react";
import { api, errorMessage, type CmsRole, type CmsUser } from "./cms-api";
import { useCmsAuth } from "./cms-auth";

export default function AccessPanel() {
  const { t, locale } = useCmsLocale();
  const { user, refresh } = useCmsAuth();
  const [users, setUsers] = useState<CmsUser[]>([]);
  const [roles, setRoles] = useState<CmsRole[]>([]);
  const [catalogue, setCatalogue] = useState<Record<string, string>>({});
  const [tab, setTab] = useState<"users" | "roles">("users");
  const [query, setQuery] = useState("");
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  const [account, setAccount] = useState<CmsUser | "new" | null>(null);
  const [role, setRole] = useState<CmsRole | "new" | null>(null);
  const [permissions, setPermissions] = useState<string[]>([]);
  const dialogRef = useRef<HTMLDialogElement>(null);
  useEffect(() => { if (account || role) dialogRef.current?.showModal(); }, [account, role]);
  async function load() {
    setLoading(true); setError("");
    try {
      const [accounts, groups] = await Promise.all([
        api<{ data: CmsUser[]; meta: { last_page: number; total: number } }>(`/cms/users?page=${page}&search=${encodeURIComponent(query)}`),
        api<{ data: CmsRole[]; permissions: Record<string, string> }>("/cms/roles"),
      ]);
      setUsers(accounts.data); setLastPage(accounts.meta.last_page); setTotal(accounts.meta.total); setRoles(groups.data); setCatalogue(groups.permissions);
    } catch (error) { setUsers([]); setRoles([]); setError(errorMessage(error)); }
    finally { setLoading(false); }
  }
  useEffect(() => { const timer = setTimeout(() => void load(), 250); return () => clearTimeout(timer); }, [page, query]);
  const self = account !== null && account !== "new" && account.id === user.id;
  return <section className="access-panel"><div className="access-toolbar"><div className="access-tabs"><button className={`button ${tab === "users" ? "primary" : "secondary"}`} onClick={() => setTab("users")}><Users size={16} />{t("Tài khoản")}</button><button className={`button ${tab === "roles" ? "primary" : "secondary"}`} onClick={() => setTab("roles")}><ShieldCheck size={16} />{t("Vai trò & quyền")}</button></div><button className="button primary" disabled={loading} onClick={() => { setError(""); if (tab === "users") setAccount("new"); else { setRole("new"); setPermissions([]); } }}><Plus size={16} />{tab === "users" ? t("Tạo tài khoản") : t("Tạo vai trò")}</button></div>
    <p className="subtle">{t("Tài khoản và phân quyền được lưu tại Laravel. Chỉ quản trị hệ thống có thể thay đổi quyền truy cập.")}</p>
    {notice && <p className="cms-success" role="status">{t(notice)}</p>}{error && !account && !role && <div className="cms-error" role="alert">{t(error)} <button className="button secondary" onClick={load}>{t("Thử lại")}</button></div>}
    {tab === "users" ? <div className="panel"><div className="panel-heading"><h2>{t("Đội ngũ của bạn")} <small>({total})</small></h2><input className="cms-search" aria-label={t("Tìm tài khoản")} placeholder={t("Tìm tên hoặc email…")} value={query} onChange={event => { setQuery(event.target.value); setPage(1); }} /></div><div className="table-scroll"><table><thead><tr><th>{t("Tài khoản")}</th><th>{t("Vai trò")}</th><th>{t("Trạng thái")}</th><th>{t("Đăng nhập gần nhất")}</th><th>{t("Thao tác")}</th></tr></thead><tbody>{!loading && users.map(account => <tr key={account.id}><td><strong>{account.name}{account.id === user.id ? t(" (Bạn)") : ""}</strong><div className="subtle">{account.email}</div></td><td>{account.role?.name || t("Chưa phân quyền")}</td><td><span className={`badge ${account.is_active ? "published" : "draft"}`}>{account.is_active ? t("Hoạt động") : t("Đã khóa")}</span>{account.must_change_password && <small className="subtle"> {t("· Cần đổi mật khẩu")}</small>}</td><td>{account.last_login_at ? new Date(account.last_login_at).toLocaleString(locale === "en" ? "en-US" : "vi-VN") : t("Chưa đăng nhập")}</td><td><button className="button secondary" onClick={() => { setError(""); setAccount(account); }}>{t("Chỉnh sửa")}</button></td></tr>)}</tbody></table></div>{loading ? <p className="access-empty" role="status">{t("Đang tải tài khoản…")}</p> : users.length === 0 && <p className="access-empty">{t("Không có tài khoản phù hợp.")}</p>}<div className="access-pagination"><button className="button secondary" disabled={page <= 1 || loading} onClick={() => setPage(page - 1)}>{t("Trước")}</button><span>{t("Trang")} {page} / {lastPage}</span><button className="button secondary" disabled={page >= lastPage || loading} onClick={() => setPage(page + 1)}>{t("Sau")}</button></div></div> : <div className="role-grid">{roles.map(group => <article className="panel role-card" key={group.id}><ShieldCheck size={24} /><h2>{group.name}</h2><p>{group.users_count} {t("tài khoản ·")} {group.is_system ? t("Toàn quyền hệ thống") : t("{0} quyền truy cập", { "0": group.permissions.length })}</p><div className="permission-tags">{group.is_system ? <span className="badge published">{t("Vai trò được bảo vệ")}</span> : group.permissions.map(permission => <span className="badge" key={permission}>{t(catalogue[permission])}</span>)}</div><button className="button secondary" disabled={group.is_system} onClick={() => { setError(""); setRole(group); setPermissions(group.permissions); }}>{group.is_system ? t("Không thể chỉnh sửa") : t("Chỉnh sửa quyền")}</button></article>)}</div>}
    {(account || role) && <dialog ref={dialogRef} className="cms-modal" aria-modal="true" aria-labelledby="access-dialog-title" onCancel={event => { event.preventDefault(); if (!busy) { setAccount(null); setRole(null); } }}><div className="cms-modal-body"><div className="panel-heading"><h2 id="access-dialog-title">{account ? account === "new" ? t("Tạo tài khoản") : t("Chỉnh sửa tài khoản") : role === "new" ? t("Tạo vai trò") : t("Chỉnh sửa vai trò")}</h2><button className="icon-button" aria-label={t("Đóng")} disabled={busy} onClick={() => { setAccount(null); setRole(null); setError(""); }}><X size={20} /></button></div>
      {account ? <form className="cms-form" onSubmit={async event => {
        event.preventDefault(); setBusy(true); setError(""); const form = new FormData(event.currentTarget);
        const data = { name: form.get("name"), email: form.get("email"), role_id: self ? user.role?.id : Number(form.get("role_id")), is_active: self || form.get("is_active") === "on", password: form.get("password") || undefined };
        try { await api(account === "new" ? "/cms/users" : `/cms/users/${account.id}`, account === "new" ? "POST" : "PUT", data); setAccount(null); setNotice("Đã lưu tài khoản. Hãy gửi mật khẩu tạm thời qua kênh riêng an toàn nếu vừa tạo hoặc đặt lại."); await load(); await refresh(); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); }
      }}><label>{t("Họ và tên")}<input name="name" autoFocus required maxLength={120} defaultValue={account === "new" ? "" : account.name} /></label><label>Email<input name="email" type="email" required maxLength={255} autoComplete="off" defaultValue={account === "new" ? "" : account.email} /></label><label htmlFor="cms-role">{t("Vai trò")}<select id="cms-role" name="role_id" required disabled={self} defaultValue={account === "new" ? "" : account.role?.id || ""}><option value="" disabled>{t("Chọn vai trò")}</option>{roles.map(group => <option value={group.id} key={group.id}>{group.name}</option>)}</select></label>{!self && <label>{account === "new" ? t("Mật khẩu tạm thời") : t("Đặt lại mật khẩu (để trống nếu giữ nguyên)")}<input name="password" type="password" autoComplete="new-password" required={account === "new"} minLength={8} maxLength={72} /><small>{t("8–72 ký tự, gồm chữ hoa, chữ thường, số, ký tự đặc biệt. Người dùng phải đổi ở lần đăng nhập tiếp theo.")}</small></label>}<label className="cms-checkbox"><input name="is_active" type="checkbox" disabled={self} defaultChecked={account === "new" || account.is_active} />{t("Cho phép đăng nhập")}</label><small>{t("Khóa tài khoản, đổi email, vai trò hoặc đặt lại mật khẩu sẽ vô hiệu hóa các phiên cũ.")}</small>{error && <p className="cms-error" role="alert">{t(error)}</p>}<button className="button primary" disabled={busy}>{busy ? t("Đang lưu…") : t("Lưu tài khoản")}</button></form> : <form className="cms-form" onSubmit={async event => {
        event.preventDefault(); setBusy(true); setError(""); const form = new FormData(event.currentTarget);
        try { await api(role === "new" ? "/cms/roles" : `/cms/roles/${role?.id}`, role === "new" ? "POST" : "PUT", { name: form.get("name"), permissions }); setRole(null); setNotice("Đã cập nhật vai trò và quyền truy cập."); await load(); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); }
      }}><label>{t("Tên vai trò")}<input name="name" autoFocus required maxLength={100} defaultValue={role === "new" ? "" : role?.name} /></label><fieldset className="permission-list"><legend>{t("Quyền được cấp")}</legend>{Object.entries(catalogue).map(([permission, label]) => <label className="cms-checkbox" key={permission}><input type="checkbox" checked={permissions.includes(permission)} onChange={event => {
        if (event.target.checked) setPermissions(Array.from(new Set([...permissions, permission, ...(permission.endsWith(".manage") ? [permission.replace(".manage", ".view")] : [])])));
        else setPermissions(permissions.filter(value => value !== permission && !(permission.endsWith(".view") && value === permission.replace(".view", ".manage"))));
      }} />{t(label)}</label>)}</fieldset><small>{t("Vai trò tùy chỉnh không được quản lý tài khoản hoặc cấp quyền hệ thống.")}</small>{error && <p className="cms-error" role="alert">{t(error)}</p>}<button className="button primary" disabled={busy}>{busy ? t("Đang lưu…") : t("Lưu vai trò")}</button></form>}
    </div></dialog>}
  </section>;
}
