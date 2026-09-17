"use client";

import { useEffect, useState } from "react";
import { api, errorMessage } from "./cms-api";
import { useCmsAuth } from "./cms-auth";
import { useCmsLocale } from "./cms-locale";
import { showNotification } from "./notifications";

type Labels = Record<string, { vi: string; en: string }>;
type Config = { currency: string; statuses: Labels; payment_methods: Labels; payment_statuses: Labels; transitions: Record<string, string[]>; payment_transitions: Record<string, string[]> };
type Line = { name: string; sku: string; quantity: number; unit_price: number };
type Draft = { customer_name: string; phone: string; email: string; address: string; customer_note: string; internal_note: string; payment_method: string; shipping_fee: number; discount: number; items: Line[] };
type Order = Draft & { id: number; code: string; status: string; payment_status: string; currency: string; subtotal: number; total: number; version: number; created_at: string; history: { action: string; user_id: number; at: string; from?: { status: string; payment_status: string }; to?: { status: string; payment_status: string } }[] };
type Page = { data: Order[]; total: number; current_page: number; last_page: number };
const newLine = (): Line => ({ name: "", sku: "", quantity: 1, unit_price: 0 });

export default function OrdersPanel() {
  const { locale } = useCmsLocale();
  const { user } = useCmsAuth();
  const editable = user.permissions.includes("orders.manage");
  const text = (vi: string, en: string) => locale === "vi" ? vi : en;
  const [config, setConfig] = useState<Config | null>(null);
  const [result, setResult] = useState<Page | null>(null);
  const [filters, setFilters] = useState({ search: "", status: "", payment_status: "", from: "", to: "" });
  const [page, setPage] = useState(1);
  const [retry, setRetry] = useState(0);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [draft, setDraft] = useState<Draft | null>(null);
  const [selected, setSelected] = useState<Order | null>(null);
  const [changes, setChanges] = useState({ status: "", payment_status: "", internal_note: "" });
  useEffect(() => {
    let cancelled = false;
    setLoading(true); setError("");
    const timer = setTimeout(() => {
      const params = new URLSearchParams({ page: String(page) });
      Object.entries(filters).forEach(([key, value]) => { if (value) params.set(key, value); });
      Promise.all([api<{ data: Config }>("/cms/order-config"), api<Page>(`/cms/orders?${params}`)]).then(([settings, rows]) => { if (!cancelled) { setConfig(settings.data); setResult(rows); } }).catch(failure => { if (!cancelled) setError(errorMessage(failure)); }).finally(() => { if (!cancelled) setLoading(false); });
    }, 250);
    return () => { cancelled = true; clearTimeout(timer); };
  }, [filters, page, retry]);
  const money = (value: number, currency = config?.currency || "VND") => new Intl.NumberFormat(locale === "vi" ? "vi-VN" : "en-US", { style: "currency", currency, maximumFractionDigits: 0 }).format(value);
  const label = (values: Labels, key: string) => values[key]?.[locale] || key;
  const choose = (order: Order) => { setSelected(order); setChanges({ status: order.status, payment_status: order.payment_status, internal_note: order.internal_note || "" }); setDraft(null); setError(""); };
  async function save() {
    if (busy || !editable) return;
    setBusy(true); setError("");
    try {
      const response = await api<{ data: Order }>(selected ? `/cms/orders/${selected.id}` : "/cms/orders", selected ? "PUT" : "POST", selected ? { ...changes, version: selected.version } : draft);
      choose(response.data); setRetry(value => value + 1); showNotification(text("Đã lưu đơn hàng.", "Order saved."));
    } catch (failure) { setError(errorMessage(failure)); showNotification(errorMessage(failure), "error"); }
    finally { setBusy(false); }
  }
  async function reloadSelected() {
    if (!selected || busy) return;
    setBusy(true);
    try { choose((await api<{ data: Order }>(`/cms/orders/${selected.id}`)).data); }
    catch (failure) { setError(errorMessage(failure)); }
    finally { setBusy(false); }
  }
  const updateFilter = (key: keyof typeof filters, value: string) => { setFilters(current => ({ ...current, [key]: value })); setPage(1); };
  const updateLine = (index: number, data: Partial<Line>) => setDraft(current => current ? { ...current, items: current.items.map((item, position) => position === index ? { ...item, ...data } : item) } : null);
  return <section className="product-edit">
    {error && <p className="cms-error" role="alert">{error} <button className="button secondary" onClick={() => setRetry(value => value + 1)}>{text("Thử lại", "Retry")}</button></p>}
    {config && (draft || selected) ? <form className="panel product-card cms-form" onSubmit={event => { event.preventDefault(); void save(); }}>
      <div className="access-toolbar"><h2>{selected?.code || text("Tạo đơn hàng", "Create order")}</h2><button type="button" className="button secondary" disabled={busy} onClick={() => { if (confirm(text("Đóng và bỏ thay đổi chưa lưu?", "Close and discard unsaved changes?"))) { setDraft(null); setSelected(null); } }}>{text("Quay lại", "Back")}</button></div>
      {draft && <fieldset disabled={busy} className="order-fields"><div className="order-form-grid">{([['customer_name', 'Khách hàng', 'Customer'], ['phone', 'Số điện thoại', 'Phone'], ['email', 'Email', 'Email'], ['address', 'Địa chỉ giao hàng', 'Shipping address']] as const).map(([key, vi, en]) => <label key={key}>{text(vi, en)}<input required={key !== "email"} type={key === "email" ? "email" : "text"} maxLength={key === "address" ? 2000 : key === "phone" ? 50 : 255} value={draft[key]} onChange={event => setDraft({ ...draft, [key]: event.target.value })} /></label>)}</div>
        <h3>{text("Sản phẩm trong đơn", "Order items")}</h3><p className="subtle">{text("Nhập tên, mã và giá chốt của sản phẩm. Chưa đồng bộ tồn kho.", "Enter item name, SKU and agreed price. Inventory is not synchronized.")}</p>
        {draft.items.map((item, index) => <div className="order-line" key={index}><label>{text("Tên sản phẩm", "Item name")}<input required maxLength={255} value={item.name} onChange={event => updateLine(index, { name: event.target.value })} /></label><label>SKU<input maxLength={100} value={item.sku} onChange={event => updateLine(index, { sku: event.target.value })} /></label><label>{text("Số lượng", "Quantity")}<input type="number" required min={1} max={10000} step={1} value={item.quantity} onChange={event => updateLine(index, { quantity: Number(event.target.value) })} /></label><label>{text("Đơn giá", "Unit price")}<input type="number" required min={0} max={1000000000} step={1} value={item.unit_price} onChange={event => updateLine(index, { unit_price: Number(event.target.value) })} /></label><button type="button" className="button secondary" disabled={draft.items.length === 1} onClick={() => setDraft({ ...draft, items: draft.items.filter((_, position) => position !== index) })}>{text("Xóa", "Remove")}</button></div>)}
        <button type="button" className="button secondary" disabled={draft.items.length >= 100} onClick={() => setDraft({ ...draft, items: [...draft.items, newLine()] })}>{text("Thêm dòng", "Add item")}</button>
        <div className="order-form-grid">{([['shipping_fee', 'Phí vận chuyển', 'Shipping fee'], ['discount', 'Giảm giá', 'Discount']] as const).map(([key, vi, en]) => <label key={key}>{text(vi, en)}<input type="number" required min={0} max={1000000000} step={1} value={draft[key]} onChange={event => setDraft({ ...draft, [key]: Number(event.target.value) })} /></label>)}<label>{text("Thanh toán", "Payment method")}<select value={draft.payment_method} onChange={event => setDraft({ ...draft, payment_method: event.target.value })}>{Object.keys(config.payment_methods).map(key => <option key={key} value={key}>{label(config.payment_methods, key)}</option>)}</select></label></div>
        <label>{text("Ghi chú khách hàng", "Customer note")}<textarea maxLength={5000} value={draft.customer_note} onChange={event => setDraft({ ...draft, customer_note: event.target.value })} /></label><label>{text("Ghi chú nội bộ", "Internal note")}<textarea maxLength={5000} value={draft.internal_note} onChange={event => setDraft({ ...draft, internal_note: event.target.value })} /></label><strong>{text("Tổng tiền dự kiến", "Estimated total")}: {money(draft.items.reduce((total, item) => total + item.quantity * item.unit_price, 0) + draft.shipping_fee - draft.discount)}</strong>
      </fieldset>}
      {selected && <><div className="order-form-grid"><section><h3>{selected.customer_name}</h3><p>{selected.phone} · {selected.email}</p><p>{selected.address}</p><p>{selected.customer_note}</p><small>{new Date(selected.created_at).toLocaleString(locale)} · {label(config.payment_methods, selected.payment_method)}</small></section><section><p>{text("Tiền hàng", "Subtotal")}: {money(selected.subtotal, selected.currency)}</p><p>{text("Phí vận chuyển", "Shipping")}: {money(selected.shipping_fee, selected.currency)}</p><p>{text("Giảm giá", "Discount")}: {money(selected.discount, selected.currency)}</p><h3>{text("Tổng tiền", "Total")}: {money(selected.total, selected.currency)}</h3></section></div>
        <div className="table-scroll"><table><thead><tr>{[text("Sản phẩm", "Product"), "SKU", text("Số lượng", "Quantity"), text("Đơn giá", "Unit price"), text("Thành tiền", "Amount")].map(value => <th key={value}>{value}</th>)}</tr></thead><tbody>{selected.items.map((item, index) => <tr key={index}><td>{item.name}</td><td>{item.sku}</td><td>{item.quantity}</td><td>{money(item.unit_price, selected.currency)}</td><td>{money(item.quantity * item.unit_price, selected.currency)}</td></tr>)}</tbody></table></div>
        <fieldset disabled={!editable || busy} className="order-fields"><div className="order-form-grid"><label>{text("Trạng thái đơn", "Order status")}<select value={changes.status} onChange={event => setChanges({ ...changes, status: event.target.value })}>{[selected.status, ...(config.transitions[selected.status] || [])].map(key => <option key={key} value={key}>{label(config.statuses, key)}</option>)}</select></label><label>{text("Trạng thái thanh toán", "Payment status")}<select value={changes.payment_status} onChange={event => setChanges({ ...changes, payment_status: event.target.value })}>{[selected.payment_status, ...(config.payment_transitions[selected.payment_status] || [])].map(key => <option key={key} value={key}>{label(config.payment_statuses, key)}</option>)}</select></label></div><label>{text("Ghi chú nội bộ", "Internal note")}<textarea maxLength={5000} value={changes.internal_note} onChange={event => setChanges({ ...changes, internal_note: event.target.value })} /></label></fieldset>
        <details><summary>{text("Lịch sử xử lý", "Activity history")} ({selected.history.length})</summary>{selected.history.map((entry, index) => <p key={index}>{new Date(entry.at).toLocaleString(locale)} · User #{entry.user_id} · {entry.to ? `${label(config.statuses, entry.from?.status || "")} → ${label(config.statuses, entry.to.status)} / ${label(config.payment_statuses, entry.to.payment_status)}` : text("Tạo đơn", "Created")}</p>)}</details><button type="button" className="button secondary" disabled={busy} onClick={() => void reloadSelected()}>{text("Tải lại chi tiết", "Reload details")}</button></>}
      {editable && <button className="button primary" disabled={busy}>{busy ? text("Đang lưu…", "Saving…") : text("Lưu đơn hàng", "Save order")}</button>}
    </form> : <>
      <div className="access-toolbar"><span>{text("Đơn hàng", "Orders")} ({result?.total || 0})</span>{editable && config && <button className="button primary" onClick={() => { setError(""); setDraft({ customer_name: "", phone: "", email: "", address: "", customer_note: "", internal_note: "", payment_method: Object.keys(config.payment_methods)[0] || "", shipping_fee: 0, discount: 0, items: [newLine()] }); }}>{text("Tạo đơn hàng", "Create order")}</button>}</div>
      <div className="panel product-card cms-form order-filters"><label>{text("Tìm kiếm", "Search")}<input placeholder={text("Mã đơn, tên, SĐT, email", "Code, name, phone, email")} value={filters.search} onChange={event => updateFilter("search", event.target.value)} /></label>{config && ([['status', config.statuses, text('Trạng thái đơn', 'Order status')], ['payment_status', config.payment_statuses, text('Thanh toán', 'Payment')]] as const).map(([key, values, title]) => <label key={key}>{title}<select value={filters[key]} onChange={event => updateFilter(key, event.target.value)}><option value="">{text("Tất cả", "All")}</option>{Object.keys(values).map(value => <option key={value} value={value}>{label(values, value)}</option>)}</select></label>)}{([['from', 'Từ ngày', 'From'], ['to', 'Đến ngày', 'To']] as const).map(([key, vi, en]) => <label key={key}>{text(vi, en)}<input type="date" value={filters[key]} onChange={event => updateFilter(key, event.target.value)} /></label>)}</div>
      {loading ? <p role="status">{text("Đang tải…", "Loading…")}</p> : config && result && <section className="panel"><div className="table-scroll"><table><thead><tr>{[text("Mã đơn", "Order"), text("Khách hàng", "Customer"), text("Tổng tiền", "Total"), text("Trạng thái", "Status"), text("Thanh toán", "Payment"), text("Ngày tạo", "Created")].map(value => <th key={value}>{value}</th>)}</tr></thead><tbody>{result.data.map(order => <tr key={order.id}><td><button className="order-link" onClick={() => choose(order)}>{order.code}</button></td><td><strong>{order.customer_name}</strong><p className="subtle">{order.phone}</p></td><td>{money(order.total, order.currency)}</td><td><span className={`badge ${order.status === "completed" ? "success" : order.status === "cancelled" ? "" : "info"}`}>{label(config.statuses, order.status)}</span></td><td>{label(config.payment_statuses, order.payment_status)}</td><td>{new Date(order.created_at).toLocaleDateString(locale)}</td></tr>)}</tbody></table>{!result.data.length && <div className="empty-state">{text("Chưa có đơn hàng phù hợp.", "No matching orders.")}</div>}</div><div className="table-footer"><button className="button secondary" disabled={page <= 1} onClick={() => setPage(value => value - 1)}>{text("Trước", "Previous")}</button><span>{page} / {result.last_page}</span><button className="button secondary" disabled={page >= result.last_page} onClick={() => setPage(value => value + 1)}>{text("Sau", "Next")}</button></div></section>}
    </>}
  </section>;
}
