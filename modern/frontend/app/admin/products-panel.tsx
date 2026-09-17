"use client";
import { useCmsLocale } from "./cms-locale";

import { useCallback, useEffect, useRef, useState } from "react";
import { Copy, FolderTree, Package, Plus, Search, Trash2 } from "lucide-react";
import { errorMessage } from "./cms-api";
import { useCmsAuth } from "./cms-auth";
import { type CatalogProduct, type Category, type CatalogDefinition, catalogApi, categoryPath, newProduct } from "./product-types";
import ProductForm from "./product-form";
import ProductCategories from "./product-categories";
import { showNotification } from "./notifications";

export default function ProductsPanel({ definition, startNew = false }: { definition: CatalogDefinition; startNew?: boolean }) {
  const { t: translate, locale } = useCmsLocale();
  const t: typeof translate = (message, values) => {
    const translated = translate(message, values);
    if (definition.module !== "news") return translated;
    return translated.replace(/Sản phẩm/g, "Bài viết").replace(/sản phẩm/g, "bài viết").replace(/Products/g, "Articles").replace(/Product/g, "Article").replace(/products/g, "articles").replace(/product/g, "article");
  };
  const request = useCallback(<T,>(path: string, method = "GET", data?: unknown) => catalogApi<T>(definition, path, method, data), [definition]);
  const hasCategories = definition.category_depth > 0 || definition.features.brand || definition.features.size;
  const { user } = useCmsAuth();
  const editable = user.permissions.includes(definition.module === "news" ? "content.manage" : "products.manage");
  const [tab, setTab] = useState("products");
  const [products, setProducts] = useState<CatalogProduct[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [editing, setEditing] = useState<CatalogProduct | null>(startNew && editable ? newProduct(definition.key) : null);
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("all");
  const [category, setCategory] = useState("");
  const [page, setPage] = useState(1);
  const [meta, setMeta] = useState({ total: 0, last_page: 1 });
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const sequence = useRef(0);
  const statusSaving = useRef(false);
  const loadCategories = useCallback(async () => { setCategories((await request<{ data: Category[] }>("/cms/product-categories")).data); }, [request]);
  const load = useCallback(async () => {
    const current = ++sequence.current; setLoading(true); setError("");
    try {
      const result = await request<{ data: CatalogProduct[]; meta: { total: number; last_page: number } }>(`/cms/products?search=${encodeURIComponent(search)}&status=${status}&page=${page}${category ? `&category_id=${category}` : ""}`);
      if (current === sequence.current) { setProducts(result.data); setMeta(result.meta); if (page > result.meta.last_page) setPage(result.meta.last_page); }
    } catch (error) { if (current === sequence.current) { setProducts([]); setError(errorMessage(error)); } }
    finally { if (current === sequence.current) setLoading(false); }
  }, [search, status, page, category, request]);
  useEffect(() => { void loadCategories().catch(error => setError(errorMessage(error))); }, [loadCategories]);
  useEffect(() => { const timer = setTimeout(() => void load(), 200); return () => { clearTimeout(timer); sequence.current++; }; }, [load]);
  async function action(product: CatalogProduct, operation: "delete" | "restore" | "duplicate") {
    if (operation === "delete" && !confirm(t("Chuyển “{0}” vào thùng rác? Có thể khôi phục sau.", { "0": product.name }))) return;
    setBusy(true); setError("");
    try {
      const path = `/cms/products/${product.id}${operation === "delete" ? "" : `/${operation}`}`;
      const result = await request<{ data: CatalogProduct }>(path, operation === "delete" ? "DELETE" : "POST");
      if (operation === "duplicate") setEditing(result.data);
      showNotification(t(operation === "delete" ? "Đã chuyển vào thùng rác." : operation === "restore" ? "Đã khôi phục sản phẩm." : "Đã tạo bản sao ở trạng thái nháp.")); await load();
    } catch (error) { showNotification(t(errorMessage(error)), "error"); } finally { setBusy(false); }
  }
  async function quickStatus(product: CatalogProduct, field: "is_active" | "is_featured" | "is_new" | "is_bestseller", value: boolean) {
    if (!editable || busy || loading || product.deleted_at || statusSaving.current) return;
    statusSaving.current = true; setBusy(true); setError("");
    try {
      const result = await request<{ data: CatalogProduct }>(`/cms/products/${product.id}/status`, "PATCH", { field, value });
      setProducts(current => current.map(item => item.id === product.id ? result.data : item));
      showNotification(t("Đã cập nhật trạng thái sản phẩm."));
      if (status === field || (field === "is_active" && ["published", "draft"].includes(status))) await load();
    } catch (error) { showNotification(t(errorMessage(error)), "error"); } finally { statusSaving.current = false; setBusy(false); }
  }
  if (editing) return <ProductForm definition={definition} key={editing.id} initial={editing} categories={categories} readOnly={!editable} onClose={() => { setEditing(null); void load(); }} onSaved={() => { setEditing(null); showNotification(t("Đã lưu sản phẩm vào Laravel.")); void load(); }} />;
  return <section className="products-panel"><div className="access-toolbar"><div className="access-tabs"><button className={`button ${tab === "products" ? "primary" : "secondary"}`} onClick={() => setTab("products")}><Package size={16} />{definition.label[locale]}</button>{hasCategories && <button className={`button ${tab === "categories" ? "primary" : "secondary"}`} onClick={() => setTab("categories")}><FolderTree size={16} />{t("Phân loại")}</button>}</div>{tab === "products" && editable && <button className="button primary" disabled={busy} onClick={() => setEditing(newProduct(definition.key))}><Plus size={17} />{t("Thêm")} {definition.singular[locale]}</button>}</div>
    {error && <div className="cms-error" role="alert">{t(error)}<button className="button secondary" onClick={() => { void load(); void loadCategories().catch(error => setError(errorMessage(error))); }}>{t("Thử lại")}</button></div>}
    {tab === "categories" ? <ProductCategories definition={definition} categories={categories} reload={loadCategories} editable={editable} /> : <><div className="toolbar product-filter"><label className="search-field"><Search size={17} /><input disabled={busy} aria-label={t("Tìm sản phẩm")} placeholder={t("Tìm tên hoặc mã sản phẩm…")} value={search} onChange={event => { setSearch(event.target.value); setPage(1); }} /></label>{definition.category_depth > 0 && <select disabled={busy} aria-label={t("Lọc danh mục sản phẩm")} value={category} onChange={event => { setCategory(event.target.value); setPage(1); }}><option value="">{t("Tất cả danh mục")}</option>{categories.filter(item => item.kind === "category").map(item => <option key={item.id} value={item.id}>{categoryPath(item, categories)}</option>)}</select>}<select disabled={busy} aria-label={t("Lọc trạng thái sản phẩm")} value={status} onChange={event => { setStatus(event.target.value); setPage(1); }}><option value="all">{t("Tất cả đang quản lý")}</option>{Object.entries(definition.statuses).map(([field, label]) => <option key={field} value={field}>{label[locale]}</option>)}{definition.statuses.is_active && <option value="draft">{t("Bản nháp / ẩn")}</option>}<option value="trash">{t("Thùng rác")}</option></select></div><div className="panel"><div className="panel-heading"><h2>{status === "trash" ? t("Thùng rác") : definition.label[locale]} <span className="count-badge">{meta.total}</span></h2><span className="subtle">{t("Dữ liệu thật · Laravel")}</span></div><div className="table-scroll"><table className="product-list-table"><thead><tr><th>{definition.label[locale]}</th><th>{t("Danh mục")}</th><th>{t("Trạng thái")}</th><th>{t("Thao tác")}</th></tr></thead><tbody>{!loading && products.map(product => <tr key={product.id}><td><div className="customer-cell">{product.main_image ? <img className="catalog-thumb" src={product.main_image.url} alt={product.image_alt || product.name} /> : <span className="product-thumb"><Package size={23} /></span>}<div className="product-list-details"><strong>{product.name}</strong>{definition.features.code && <div className="subtle product-list-code">{t("Mã")}: {product.code}</div>}{definition.features.pricing && <div className="product-list-price"><strong>{Number(product.sale_price ?? product.regular_price).toLocaleString("vi-VN")} ₫</strong>{product.sale_price !== null && <del className="subtle">{Number(product.regular_price).toLocaleString("vi-VN")} ₫</del>}</div>}</div></div></td><td>{product.category ? categoryPath(product.category, categories) : t("Chưa phân loại")}</td><td>{product.deleted_at ? <span className="badge neutral">{t("Thùng rác")}</span> : <div className="product-quick-status">{(Object.entries(definition.statuses) as [keyof CatalogDefinition["statuses"], { vi: string; en: string }][]).map(([field, label]) => <label className="product-quick-toggle" key={field}><span>{label[locale]}</span><input type="checkbox" role="switch" aria-label={`${label[locale]}: ${product.name}`} checked={product[field]} disabled={!editable || busy || loading} onChange={event => void quickStatus(product, field, event.target.checked)} /><span className="product-toggle-track" aria-hidden="true" /></label>)}</div>}</td><td><div className="catalog-actions">{!product.deleted_at ? <><button className="text-button" disabled={busy} onClick={async () => { setBusy(true); setError(""); try { setEditing((await request<{ data: CatalogProduct }>(`/cms/products/${product.id}`)).data); } catch (error) { setError(errorMessage(error)); } finally { setBusy(false); } }}>{editable ? t("Chỉnh sửa") : t("Xem")}</button>{editable && <>{definition.features.copy && <button className="icon-button" aria-label={t("Sao chép {0}", { "0": product.name })} disabled={busy} onClick={() => action(product, "duplicate")}><Copy size={16} /></button>}<button className="icon-button" aria-label={t("Xóa {0}", { "0": product.name })} disabled={busy} onClick={() => action(product, "delete")}><Trash2 size={16} /></button></>}</> : editable && <button className="text-button" disabled={busy} onClick={() => action(product, "restore")}>{t("Khôi phục")}</button>}</div></td></tr>)}</tbody></table></div>{loading ? <p className="access-empty" role="status">{t("Đang tải sản phẩm…")}</p> : products.length === 0 && <div className="empty-state"><Package size={32} /><h3>{status === "trash" ? t("Thùng rác trống") : t("Chưa có sản phẩm phù hợp")}</h3><p>{meta.total === 0 && !search && !category ? t("Bạn có thể tạo danh mục, rồi thêm sản phẩm và hình ảnh đầu tiên.") : t("Thử thay đổi từ khóa hoặc bộ lọc.")}</p></div>}<div className="access-pagination"><span>{meta.total} {t("sản phẩm")}</span><button className="button secondary" disabled={page <= 1 || loading || busy} onClick={() => setPage(page - 1)}>{t("Trước")}</button><span>{page} / {meta.last_page}</span><button className="button secondary" disabled={page >= meta.last_page || loading || busy} onClick={() => setPage(page + 1)}>{t("Sau")}</button></div></div></>}
  </section>;
}
