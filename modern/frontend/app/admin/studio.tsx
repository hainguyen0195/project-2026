"use client";
import { CmsLanguageSettings, useCmsLocale } from "./cms-locale";

import { useEffect, useRef, useState } from "react";
import { ArrowDownToLine, ArrowRight, ArrowUpRight, BarChart3, Check, ChevronDown, ChevronRight, CircleHelp, FileText, LayoutDashboard, PanelLeftClose, PanelLeftOpen, Menu, Monitor, Moon, Package, Plus, Search, Settings2, ShoppingBag, SlidersHorizontal, Sparkles, Sun, Users, X } from "lucide-react";
import { articles, demoOrders, initialProducts, money } from "./demo-data";

import { useCmsAuth } from "./cms-auth";
import AccessPanel from "./access-panel";
import PasswordPanel from "./password-panel";
import OrdersPanel from "./orders-panel";
import ProductsPanel from "./products-panel";
import StaticPagePanel from "./static-page-panel";
import MediaPanel, { type PhotoDefinition } from "./media-panel";
import GeneralSettingsPanel from "./general-settings-panel";
import { api, errorMessage } from "./cms-api";
import { type CatalogDefinition } from "./product-types";

type Section = "media" | "general" | "access" | "password" | "overview" | "products" | "orders" | "content" | "customers" | "settings";
type Theme = "light" | "dark" | "system";
const navigation = [
  { key: "overview", label: "Tổng quan", icon: LayoutDashboard },
  { key: "products", label: "Sản phẩm", icon: Package },
  { key: "orders", label: "Đơn hàng", icon: ShoppingBag },
  { key: "customers", label: "Khách hàng", icon: Users },
  { key: "content", label: "Nội dung", icon: FileText },
] as const;

export default function AdminStudio() {
  const { t, locale } = useCmsLocale();
  const { user, logout } = useCmsAuth();
  const [photoDefinitions, setPhotoDefinitions] = useState<PhotoDefinition[]>([]);
  const [mediaMenuCollapsed, setMediaMenuCollapsed] = useState(false);
  useEffect(() => {
    if (!user.permissions.includes("media.view")) return;
    let cancelled = false;
    api<{ data: PhotoDefinition[] }>("/cms/photo-types").then(result => { if (!cancelled) setPhotoDefinitions(result.data); }).catch(() => { });
    return () => { cancelled = true; };
  }, [user.permissions]);
  const [companyNames, setCompanyNames] = useState({ vi: "", en: "" });
  const companyName = companyNames[locale]?.trim() || companyNames.vi?.trim() || companyNames.en?.trim() || "Cris";
  const companyInitial = Array.from(companyName)[0]?.toLocaleUpperCase(locale) || "C";
  useEffect(() => {
    let cancelled = false;
    let sequence = 0;
    const loadBranding = async () => {
      const current = ++sequence;
      try {
        const result = await api<{ data: { vi: string; en: string } }>("/cms/branding");
        if (!cancelled && current === sequence) setCompanyNames(result.data);
      } catch { }
    };
    void loadBranding();
    window.addEventListener("cms:branding-updated", loadBranding);
    return () => { cancelled = true; window.removeEventListener("cms:branding-updated", loadBranding); };
  }, []);
  const canView = (value: string) => value === "password" || value === "settings" || user.permissions.includes(value === "access" ? "access.manage" : `${value}.view`);
  const [section, setSection] = useState<Section>(() => (user.permissions.find(permission => permission.endsWith(".view"))?.split(".")[0] || "password") as Section);
  const [catalogType, setCatalogType] = useState("");
  const [definitions, setDefinitions] = useState<CatalogDefinition[]>([]);
  const [catalogReady, setCatalogReady] = useState(false);
  const [catalogError, setCatalogError] = useState("");
  const [catalogRetry, setCatalogRetry] = useState(0);
  const canViewProducts = user.permissions.includes("products.view");
  const canViewNews = user.permissions.includes("content.view");
  const moduleDefinitions = definitions.filter(item => item.module === (section === "content" ? "news" : "products"));
  const definition = catalogType ? moduleDefinitions.find(item => item.key === catalogType) : moduleDefinitions[0];

  useEffect(() => {
    if (!canViewProducts && !canViewNews) return;
    let cancelled = false;
    setCatalogReady(false);
    setCatalogError("");
    Promise.all([...(canViewProducts ? [api<{ data: CatalogDefinition[] }>("/cms/product-types")] : []), ...(canViewNews ? [api<{ data: CatalogDefinition[] }>("/cms/news-types"), api<{ data: CatalogDefinition[] }>("/cms/static-types"), api<{ data: CatalogDefinition[] }>("/cms/seopage-types")] : [])])
      .then(result => { if (!cancelled) setDefinitions(result.flatMap(item => item.data)); })
      .catch(error => { if (!cancelled) setCatalogError(errorMessage(error)); })
      .finally(() => { if (!cancelled) setCatalogReady(true); });
    return () => { cancelled = true; };
  }, [canViewProducts, canViewNews, catalogRetry]);

  const [theme, setTheme] = useState<Theme>("system");
  const [mobileMenu, setMobileMenu] = useState(false);
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
  const [seoMenuCollapsed, setSeoMenuCollapsed] = useState(false);
  const [query, setQuery] = useState("");
  const [status, setStatus] = useState("all");
  const products = initialProducts;
  const [notice, setNotice] = useState("");
  const [period, setPeriod] = useState("week");
  const [selectedOrder, setSelectedOrder] = useState<(typeof demoOrders)[number] | null>(null);
  const [selectedArticle, setSelectedArticle] = useState<(typeof articles)[number] | null>(null);
  const detailRef = useRef<HTMLDialogElement>(null);
  const searchRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    let saved: string | null = null;
    try { saved = localStorage.getItem("comi-theme"); setSidebarCollapsed(localStorage.getItem("comi-sidebar-collapsed") === "true"); setSeoMenuCollapsed(localStorage.getItem("comi-seo-menu-collapsed") === "true"); } catch { }
    if (saved === "dark" || saved === "light" || saved === "system") setTheme(saved);
    const readHash = () => {
      const [value, params = ""] = window.location.hash.slice(1).split("?");
      setCatalogType((value === "products" || value === "content" || value === "media") ? new URLSearchParams(params).get("type") || "" : "");
      if (["media", "overview", "products", "orders", "content", "customers", "settings", "general", "access", "password"].includes(value)) setSection(value as Section);
    };
    readHash();
    window.addEventListener("hashchange", readHash);
    return () => window.removeEventListener("hashchange", readHash);
  }, []);

  useEffect(() => {
    const media = window.matchMedia("(prefers-color-scheme: dark)");
    const apply = () => { document.documentElement.dataset.theme = theme === "system" ? (media.matches ? "dark" : "light") : theme; };
    apply();
    media.addEventListener("change", apply);
    return () => media.removeEventListener("change", apply);
  }, [theme]);

  useEffect(() => {
    if (!notice) return;
    const timer = setTimeout(() => setNotice(""), 4500);
    return () => clearTimeout(timer);
  }, [notice]);

  useEffect(() => {
    const shortcut = (event: KeyboardEvent) => {
      if ((event.ctrlKey || event.metaKey) && event.key === "k") {
        event.preventDefault();
        searchRef.current?.focus();
      }
    };
    window.addEventListener("keydown", shortcut);
    return () => window.removeEventListener("keydown", shortcut);
  }, []);

  useEffect(() => { if (selectedOrder || selectedArticle) detailRef.current?.showModal(); }, [selectedOrder, selectedArticle]);

  const chooseTheme = (value: Theme) => {
    setTheme(value);
    try { localStorage.setItem("comi-theme", value); } catch { setNotice("Trình duyệt không cho phép lưu giao diện. Lựa chọn chỉ áp dụng cho phiên này."); }
  };
  const go = (value: Section, type = "") => { if (!canView(value)) { setNotice("Bạn chưa được cấp quyền cho mục này."); return; } setSection(value); setCatalogType((value === "products" || value === "content" || value === "media") ? type : ""); setQuery(""); setStatus("all"); setMobileMenu(false); window.location.hash = (value === "products" || value === "content" || value === "media") && type ? `${value}?type=${encodeURIComponent(type)}` : value; };
  const normalizedQuery = query.trim().toLocaleLowerCase("vi");
  const matches = (text: string) => text.toLocaleLowerCase("vi").includes(normalizedQuery);
  const filteredProducts = products.filter(product => matches(`${product.name} ${product.code} ${product.category}`) && (status === "all" || product.status === status));
  const filteredOrders = demoOrders.filter(order => matches(`${order.code} ${order.customer}`) && (status === "all" || order.status === status));
  const filteredArticles = articles.filter(article => matches(`${article.title} ${article.category}`));
  const photoDefinition = photoDefinitions.find(item => item.key === catalogType) || (!catalogType ? photoDefinitions[0] : undefined);
  const title = section === "media" ? photoDefinition?.label[locale] || "Media" : section === "general" ? (locale === "vi" ? "Thông tin chung" : "General information") : (section === "products" || section === "content") ? (definition ? `${definition.kind === "seopage" ? "SEO Page · " : ""}${definition.label[locale]}` : "") || t(section === "content" ? "Nội dung" : "Sản phẩm") : section === "access" ? "Tài khoản & phân quyền" : section === "password" ? "Bảo mật tài khoản" : section === "settings" ? "Cài đặt giao diện" : navigation.find(item => item.key === section)?.label;

  return (
    <div className={`studio ${sidebarCollapsed ? "sidebar-collapsed" : ""}`}>
      <a className="skip-link" href="#main-content">{t("Bỏ qua menu")}</a>
      {mobileMenu && <button className="sidebar-scrim" aria-label={t("Đóng menu")} onClick={() => setMobileMenu(false)} />}
      <aside id="admin-sidebar" className={`sidebar ${mobileMenu ? "is-open" : ""}`} aria-label={t("Điều hướng quản trị")}>
        <a href="#overview" className="brand" onClick={() => go("overview")}><span className="brand-symbol company-brand-symbol">{companyInitial}</span><span className="company-brand-name" title={companyName}>{companyName}<span className="brand-caption">STUDIO</span></span></a>
        <div className="workspace"><span className="workspace-icon">{companyInitial}</span><div className="company-workspace-name"><strong title={companyName}>{companyName}</strong><small>{t("Không gian quản trị")}</small></div><span className="online-dot" /></div>
        <p className="nav-label">{t("KHÔNG GIAN LÀM VIỆC")}</p>
        <nav>{navigation.filter(item => canView(item.key)).map(item => (item.key === "products" || item.key === "content") && definitions.some(catalog => catalog.module === (item.key === "content" ? "news" : "products")) ? definitions.filter(catalog => catalog.kind !== "seopage" && catalog.module === (item.key === "content" ? "news" : "products")).map(catalog => <button key={`products:${catalog.key}`} className={`nav-item ${section === item.key && definition?.key === catalog.key ? "active" : ""}`} onClick={() => go(item.key, catalog.key)} aria-current={section === item.key && definition?.key === catalog.key ? "page" : undefined}><item.icon size={19} /><span>{catalog.label[locale]}</span></button>) : <button key={item.key} className={`nav-item ${section === item.key ? "active" : ""}`} onClick={() => go(item.key)} aria-current={section === item.key ? "page" : undefined}><item.icon size={19} /><span>{t(item.label)}</span></button>)}</nav>
        {canView("media") && <><div className="nav-divider" /><button className="nav-label nav-group-toggle" aria-expanded={!mediaMenuCollapsed} aria-controls="media-navigation" onClick={() => setMediaMenuCollapsed(!mediaMenuCollapsed)}><span>MEDIA</span>{mediaMenuCollapsed ? <ChevronRight size={15} /> : <ChevronDown size={15} />}</button><nav id="media-navigation" hidden={mediaMenuCollapsed}>{photoDefinitions.map(item => <button key={item.key} className={`nav-item ${section === "media" && photoDefinition?.key === item.key ? "active" : ""}`} onClick={() => go("media", item.key)}><FileText size={19} /><span>{item.label[locale]}</span></button>)}{!photoDefinitions.length && <button className="nav-item" onClick={() => go("media")}>Media</button>}</nav></>}
        {canView("content") && definitions.some(item => item.kind === "seopage") && <><div className="nav-divider" /><button type="button" className={`nav-label nav-group-toggle ${section === "content" && definition?.kind === "seopage" ? "is-active" : ""}`} aria-expanded={!seoMenuCollapsed} aria-controls="seo-page-navigation" onClick={() => {
          const collapsed = !seoMenuCollapsed;
          setSeoMenuCollapsed(collapsed);
          try { localStorage.setItem("comi-seo-menu-collapsed", String(collapsed)); } catch { }
        }}><span>SEO PAGE</span>{seoMenuCollapsed ? <ChevronRight size={15} /> : <ChevronDown size={15} />}</button><nav id="seo-page-navigation" aria-label="SEO Page" hidden={seoMenuCollapsed}>{definitions.filter(item => item.kind === "seopage").map(item => <button key={item.key} className={`nav-item ${section === "content" && definition?.key === item.key ? "active" : ""}`} aria-current={section === "content" && definition?.key === item.key ? "page" : undefined} onClick={() => go("content", item.key)}><Search size={19} /><span>{item.label[locale]}</span></button>)}</nav></>}
        <div className="nav-divider" /><p className="nav-label">{t("HỆ THỐNG")}</p>
        {canView("general") && <button className={`nav-item ${section === "general" ? "active" : ""}`} aria-current={section === "general" ? "page" : undefined} onClick={() => go("general")}><Settings2 size={19} /><span>{locale === "vi" ? "Thông tin chung" : "General information"}</span></button>}
        {canView("access") && <button className={`nav-item ${section === "access" ? "active" : ""}`} onClick={() => go("access")}><Users size={19} /><span>{t("Tài khoản & phân quyền")}</span></button>}
        <button className={`nav-item ${section === "password" ? "active" : ""}`} onClick={() => go("password")}><Settings2 size={19} /><span>{t("Đổi mật khẩu")}</span></button>
        <button className="nav-item" onClick={logout}><ArrowRight size={19} /><span>{t("Đăng xuất")}</span></button>
        {canView("settings") && <button className={`nav-item ${section === "settings" ? "active" : ""}`} onClick={() => go("settings")} aria-current={section === "settings" ? "page" : undefined}><Settings2 size={19} /><span>{t("Cài đặt giao diện")}</span></button>}
        <div className="sidebar-bottom"><div className="help-card"><Sparkles size={21} /><strong>{t("Một khởi đầu mới")}</strong><p>{t("Gọn gàng hơn. Tập trung hơn.")}<br />{t("Quản lý theo cách của bạn.")}</p><button onClick={() => go("settings")}>{t("Cá nhân hóa giao diện")} <ArrowRight size={14} /></button></div><div className="theme-switch" aria-label={t("Chế độ giao diện")}>{([{ value: "light", label: "Sáng", icon: Sun }, { value: "dark", label: "Tối", icon: Moon }, { value: "system", label: "Hệ thống", icon: Monitor }] as const).map(item => <button key={item.value} aria-label={t("Giao diện {0}", { "0": t(item.label).toLowerCase() })} aria-pressed={theme === item.value} className={theme === item.value ? "selected" : ""} onClick={() => chooseTheme(item.value)}><item.icon size={15} /><span>{t(item.label)}</span></button>)}</div><div className="profile"><span className="avatar sage">AD</span><div><strong>{user.name}</strong><small>{user.role?.name}</small></div><span className="online-dot" /></div></div>
      </aside>

      <div className="main-shell">
        <header className="topbar"><div className="breadcrumb"><button type="button" className="icon-button sidebar-collapse-toggle" aria-controls="admin-sidebar" aria-expanded={!sidebarCollapsed} aria-label={locale === "vi" ? (sidebarCollapsed ? "Mở rộng menu" : "Thu gọn menu") : (sidebarCollapsed ? "Expand sidebar" : "Collapse sidebar")} title={locale === "vi" ? (sidebarCollapsed ? "Mở rộng menu" : "Thu gọn menu") : (sidebarCollapsed ? "Expand sidebar" : "Collapse sidebar")} onClick={() => {
          const collapsed = !sidebarCollapsed;
          setSidebarCollapsed(collapsed);
          try { localStorage.setItem("comi-sidebar-collapsed", String(collapsed)); } catch { }
        }}>{sidebarCollapsed ? <PanelLeftOpen size={20} /> : <PanelLeftClose size={20} />}</button><button className="icon-button mobile-toggle" aria-label={t("Mở menu")} aria-expanded={mobileMenu} onClick={() => setMobileMenu(!mobileMenu)}><Menu size={20} /></button><span>Workspace</span><ChevronRight size={14} /><strong>{title ? t(title) : ""}</strong></div><div className="topbar-actions"><span className="demo-pill"><span /> {section === "orders" || section === "media" || section === "general" || section === "products" || section === "content" || section === "access" || section === "password" ? t("Đã kết nối Laravel") : t("Dữ liệu minh họa")}</span><button className="icon-button" aria-label={t("Thông tin bản demo")} onClick={() => setNotice("Đây là bản thiết kế tương tác. Tài khoản, phân quyền và sản phẩm đã kết nối Laravel. Dashboard, đơn hàng, nội dung và khách hàng vẫn là demo.")}><CircleHelp size={19} /></button><span className="avatar mini">AD</span></div></header>
        <main id="main-content">
          {!canView(section) ? <section className="panel password-panel"><h1>{t("Chưa có quyền truy cập")}</h1><p>{t("Chọn mục được cấp quyền ở menu hoặc liên hệ quản trị viên.")}</p></section> : <>
            <div className="page-heading"><div><div className="eyebrow">COMI / ADMINISTRATION</div><h1>{section === "overview" ? t("Mọi thứ trong tầm tay.") : title}</h1><p>{section === "overview" ? t("Chào bạn, cùng nhìn lại hoạt động cửa hàng hôm nay.") : section === "products" ? t("Tổ chức sản phẩm rõ ràng. Cập nhật chỉ trong vài thao tác.") : section === "settings" ? t("Một không gian làm việc phù hợp với bạn.") : t("Tập trung vào thông tin quan trọng, giảm bớt thao tác mỗi ngày.")}</p></div><div className="heading-actions">{section === "overview" ? <span className="date-chip">{t("15 tháng 9, 2026")} <span>{t("· Demo")}</span></span> : null}</div></div>

            {section === "media" && (photoDefinition ? <MediaPanel key={photoDefinition.key} definition={photoDefinition} /> : <p role="alert">Không tải được cấu hình media. Vui lòng tải lại trang.</p>)}
            {section === "orders" && <OrdersPanel />}
            {section === "general" && <GeneralSettingsPanel />}
            {section === "access" && <AccessPanel />}
            {section === "password" && <PasswordPanel />}
            {section === "overview" && <>
              <div className="welcome-banner"><div><span className="banner-kicker"><span className="online-dot" /> {t("KHÔNG GIAN QUẢN TRỊ MỚI")}</span><h2>{t("Ít thao tác hơn.")}<br />{t("Nhiều điều được hoàn thành hơn.")}</h2><p>{t("Sản phẩm, đơn hàng và nội dung — cùng một nơi.")}</p><button className="button banner-button" onClick={() => go("products")}>{t("Quản lý sản phẩm")} <ArrowRight size={16} /></button></div><div className="banner-art" aria-hidden="true"><div className="orbit orbit-one" /><div className="orbit orbit-two" /><div className="art-tile tile-back"><BarChart3 size={52} strokeWidth={1.2} /></div><div className="art-tile tile-front"><Package size={61} strokeWidth={1.2} /><span>MADE FOR YOUR FLOW</span></div><span className="art-spark">✳</span></div></div>
              <div className="section-heading"><h2>{t("Nhịp hoạt động")}</h2><span className="subtle">{t("Số liệu mô phỏng · không phải doanh thu thực")}</span></div>
              <div className="stats-grid">{[{ label: "Doanh thu hôm nay", value: "12.450.000 ₫", trend: "+12,8%", icon: BarChart3, note: "so với hôm qua" }, { label: "Đơn hàng mới", value: "24", trend: "+8,2%", icon: ShoppingBag, note: "so với hôm qua" }, { label: "Sản phẩm hiển thị", value: String(products.filter(product => product.status === "published").length).padStart(2, "0"), trend: t("{0} tổng", { "0": products.length }), icon: Package, note: "trong bản demo" }, { label: "Khách hàng mới", value: "18", trend: "+5,6%", icon: Users, note: "so với hôm qua" }].map((stat, index) => <article className="stat-card" key={t(stat.label)}><div className="stat-top"><span>{t(stat.label)}</span><stat.icon size={18} /></div><strong>{stat.value}</strong><div className="stat-bottom"><span className="trend">{index !== 2 && <ArrowUpRight size={13} />}{stat.trend}</span><small>{t(stat.note)}</small></div></article>)}</div>
              <div className="dashboard-grid"><section className="panel chart-panel"><div className="panel-heading"><div><h2>{t("Tổng quan doanh thu")}</h2><p>{t("Xu hướng kinh doanh theo thời gian")}</p></div><label className="select-wrap"><select aria-label={t("Khoảng thời gian doanh thu")} value={period} onChange={event => setPeriod(event.target.value)}><option value="week">{t("7 ngày qua")}</option><option value="month">{t("4 tuần qua")}</option></select><ChevronDown size={14} /></label></div><div className="chart-summary"><strong>{period === "week" ? "86.420.000" : "324.800.000"}<span> ₫</span></strong><span className="trend"><ArrowUpRight size={13} />{period === "week" ? "16,2%" : "11,4%"}</span><span className="subtle">{t("so với kỳ trước · mô phỏng")}</span></div><div className="revenue-chart" role="img" aria-label={period === "week" ? t("Biểu đồ minh họa 7 ngày, tổng doanh thu 86.420.000 đồng, tăng 16,2% so với kỳ trước") : t("Biểu đồ minh họa 4 tuần, tổng doanh thu 324.800.000 đồng, tăng 11,4% so với kỳ trước")}><div className="chart-axis"><span>{period === "week" ? "20tr" : "100tr"}</span><span>{period === "week" ? "15tr" : "75tr"}</span><span>{period === "week" ? "10tr" : "50tr"}</span><span>{period === "week" ? "5tr" : "25tr"}</span><span>0</span></div><div className="chart-plot"><div className="chart-gridlines"><i /><i /><i /><i /><i /></div><svg viewBox="0 0 660 170" preserveAspectRatio="none" aria-hidden="true"><defs><linearGradient id="area" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stopColor="var(--accent)" stopOpacity="0.23" /><stop offset="100%" stopColor="var(--accent)" stopOpacity="0.01" /></linearGradient></defs><path d={period === "week" ? "M0 138 C40 138 65 72 110 80 S175 135 220 98 S290 113 330 62 S405 113 440 69 S495 101 550 32 S615 55 660 14 L660 170 L0 170Z" : "M0 150 C90 150 125 75 220 94 S360 135 440 57 S570 45 660 8 L660 170 L0 170Z"} fill="url(#area)" /><path d="M0 151 C60 136 95 149 145 120 S215 142 280 121 S350 122 410 107 S485 123 550 90 S625 95 660 76" fill="none" stroke="var(--chart-muted)" strokeWidth="2" strokeDasharray="5 6" /><path d={period === "week" ? "M0 138 C40 138 65 72 110 80 S175 135 220 98 S290 113 330 62 S405 113 440 69 S495 101 550 32 S615 55 660 14" : "M0 150 C90 150 125 75 220 94 S360 135 440 57 S570 45 660 8"} fill="none" stroke="var(--accent)" strokeWidth="3" /></svg><div className="chart-labels">{(period === "week" ? ["09/09", "10/09", "11/09", "12/09", "13/09", "14/09", "15/09"] : ["Tuần 1", "Tuần 2", "Tuần 3", "Tuần 4"]).map(label => <span key={t(label)}>{t(label)}</span>)}</div></div></div><div className="chart-legend"><span><i />{t("Kỳ này")}</span><span><i />{t("Kỳ trước")}</span></div></section><section className="panel quick-panel"><div className="panel-heading"><div><h2>{t("Bắt đầu từ đây")}</h2><p>{t("Lối tắt cho công việc thường ngày")}</p></div><Sparkles size={19} /></div>{[{ title: "Thêm sản phẩm", text: "Đưa ý tưởng mới lên cửa hàng", icon: Plus, action: () => { if (user.permissions.includes("products.manage")) { go("products"); } else { setNotice("Bạn chưa có quyền quản lý sản phẩm."); } } }, { title: "Quản lý nội dung", text: "Kể câu chuyện thương hiệu", icon: FileText, action: () => go("content") }, { title: "Xử lý đơn hàng", text: "Có 1 đơn demo chờ xác nhận", icon: ShoppingBag, action: () => go("orders") }].map(item => <button className="quick-action" key={t(item.title)} onClick={item.action}><span className="quick-icon"><item.icon size={20} /></span><span><strong>{t(item.title)}</strong><small>{t(item.text)}</small></span><ChevronRight size={16} /></button>)}<div className="quick-note"><span className="online-dot" /><span>{t("Mọi thay đổi tại đây là bản xem thử.")}</span></div></section></div>
            </>}

            {section !== "orders" && section !== "media" && section !== "general" && section !== "products" && section !== "content" && section !== "access" && section !== "password" && section !== "settings" && section !== "overview" && <div className="toolbar"><label className="search-field"><Search size={18} /><input ref={searchRef} aria-label={t("Tìm kiếm {0}", { "0": title ? t(title).toLowerCase() : "" })} placeholder={t("Tìm kiếm {0}...", { "0": title ? t(title).toLowerCase() : "" })} value={query} onChange={event => setQuery(event.target.value)} /><kbd>{t("⌘ K")}</kbd></label><span className="subtle">{t("Dữ liệu mẫu để trải nghiệm giao diện")}</span></div>}

            {section === "overview" && <section className="panel orders-panel"><div className="panel-heading"><div className="title-with-count"><h2>{section === "overview" ? t("Đơn hàng gần đây") : t("Danh sách đơn hàng")}</h2><span className="count-badge">{filteredOrders.length}</span></div>{section === "overview" && <button className="text-button" onClick={() => go("orders")}>{t("Xem tất cả")} <ArrowRight size={15} /></button>}</div><div className="table-scroll"><table><thead><tr><th>{t("Mã đơn hàng")}</th><th>{t("Khách hàng")}</th><th>{t("Sản phẩm")}</th><th>{t("Tổng tiền")}</th><th>{t("Trạng thái")}</th><th>{t("Thời gian")}</th><th><span className="sr-only">{t("Thao tác")}</span></th></tr></thead><tbody>{filteredOrders.map(order => <tr key={order.code}><td><button className="order-link" onClick={() => setSelectedOrder(order)}>{order.code}</button></td><td><div className="customer-cell"><span className={`avatar ${order.color}`}>{order.initials}</span><strong>{order.customer}</strong></div></td><td className="subtle">{order.items} {t("sản phẩm")}</td><td className="amount">{money(order.total)}</td><td><span className={`badge ${order.status === "Hoàn tất" ? "success" : order.status === "Đang giao" ? "info" : "warning"}`}><i />{t(order.status)}</span></td><td className="subtle">{order.time}</td><td><button className="icon-button" aria-label={t("Xem đơn {0}", { "0": order.code })} onClick={() => setSelectedOrder(order)}><ArrowUpRight size={16} /></button></td></tr>)}</tbody></table>{!filteredOrders.length && <div className="empty-state">{t("Không tìm thấy đơn hàng phù hợp.")}</div>}</div><div className="table-footer"><span>{t("Hiển thị")} {filteredOrders.length} {t("đơn hàng minh họa")}</span><span>{t("Đồng tiền: VND")}</span></div></section>}

            {(section === "products" || section === "content") && (catalogError ? <div className="cms-error" role="alert">{t(catalogError)}<button className="button secondary" onClick={() => setCatalogRetry(value => value + 1)}>{t("Thử lại")}</button></div> : !catalogReady ? <p role="status">{t("Đang tải cấu hình type…")}</p> : definition ? (definition.singleton ? <StaticPagePanel key={definition.key} definition={definition} /> : <ProductsPanel key={definition.key} definition={definition} />) : <p role="status">{catalogType ? (locale === "vi" ? "Loại dữ liệu không tồn tại. Vui lòng chọn lại ở menu bên trái." : "This data type does not exist. Please choose one from the sidebar.") : t("Chưa khai báo type trong cấu hình catalog.")}</p>)}


            {section === "customers" && <section className="panel"><div className="panel-heading"><h2>{t("Khách hàng minh họa")}</h2><span className="subtle">{t("Không phải dữ liệu khách hàng thật")}</span></div><div className="table-scroll"><table><thead><tr><th>{t("Khách hàng")}</th><th>{t("Đơn hàng demo")}</th><th>{t("Giá trị đơn")}</th></tr></thead><tbody>{demoOrders.filter(order => matches(order.customer)).map(order => <tr key={order.code}><td><div className="customer-cell"><span className={`avatar ${order.color}`}>{order.initials}</span><strong>{order.customer}</strong></div></td><td><button className="order-link" onClick={() => setSelectedOrder(order)}>{order.code}</button></td><td>{money(order.total)}</td></tr>)}</tbody></table>{!demoOrders.some(order => matches(order.customer)) && <div className="empty-state">{t("Không tìm thấy khách hàng phù hợp.")}</div>}</div></section>}

            {section === "settings" && <CmsLanguageSettings />}
            {section === "settings" && <section className="panel settings-panel"><div className="panel-heading"><div><h2>{t("Giao diện của bạn")}</h2><p>{t("Lựa chọn được lưu trên trình duyệt này và áp dụng cho toàn bộ bản demo.")}</p></div><Sun size={22} /></div><div className="theme-cards">{([{ value: "light", title: "Sáng", description: "Sạch sẽ, nhẹ nhàng và rõ ràng.", icon: Sun }, { value: "dark", title: "Tối", description: "Tông trầm cho không gian ít ánh sáng.", icon: Moon }, { value: "system", title: "Theo hệ thống", description: "Tự động theo cài đặt thiết bị.", icon: Monitor }] as const).map(option => <button key={option.value} className={`theme-card ${theme === option.value ? "chosen" : ""}`} aria-pressed={theme === option.value} onClick={() => chooseTheme(option.value)}><div className={`theme-preview preview-${option.value}`}><div /><section><i /><span /><span /><span /></section></div><div className="theme-card-title"><option.icon size={18} /><strong>{t(option.title)}</strong>{theme === option.value && <Check size={17} />}</div><p>{t(option.description)}</p></button>)}</div><div className="settings-note"><Monitor size={20} /><p><strong>{t("Thống nhất trên mọi màn hình")}</strong><br />{t("Menu, bảng dữ liệu, biểu mẫu và hộp thoại đều sử dụng cùng bộ màu sáng/tối.")}</p></div></section>}
            <footer className="page-footer"><span>{t("© 2026 COMI Studio")} <span>·</span> {t("Không gian làm việc của bạn.")}</span><span><span className="online-dot" /> {t("Sản phẩm & tài khoản: Laravel · Mục khác: Demo")}</span></footer>
          </>}</main>
      </div>


      <dialog ref={detailRef} className="edit-dialog" onClose={() => { setSelectedOrder(null); setSelectedArticle(null); }}><div className="dialog-heading"><h2>{selectedOrder ? t("Đơn hàng {0}", { "0": selectedOrder.code }) : t("Xem trước nội dung")}</h2><button className="icon-button" aria-label={t("Đóng chi tiết")} onClick={() => detailRef.current?.close()}><X size={20} /></button></div>{selectedOrder && <dl className="order-detail"><div><dt>{t("Khách hàng")}</dt><dd>{selectedOrder.customer}</dd></div><div><dt>{t("Số sản phẩm")}</dt><dd>{selectedOrder.items}</dd></div><div><dt>{t("Tổng tiền")}</dt><dd>{money(selectedOrder.total)}</dd></div><div><dt>{t("Trạng thái")}</dt><dd>{t(selectedOrder.status)}</dd></div></dl>}{selectedArticle && <div className="article-preview"><span className="eyebrow">{selectedArticle.category}</span><h2>{selectedArticle.title}</h2><p>{selectedArticle.date} · {selectedArticle.status}</p><p>{t("Đây là thẻ nội dung mẫu để duyệt bố cục CMS mới. Trình soạn thảo, nội dung thật và quy trình xuất bản sẽ được tích hợp với backend ở giai đoạn tiếp theo.")}</p></div>}<p className="form-note">{t("Dữ liệu minh họa, chưa kết nối hệ thống hiện tại.")}</p><div className="dialog-actions"><button className="button secondary" onClick={() => detailRef.current?.close()}>{t("Đóng")}</button></div></dialog>
      {notice && <div className="toast" role="status"><CircleHelp size={19} /><span>{t(notice)}</span><button aria-label={t("Đóng thông báo")} onClick={() => setNotice("")}><X size={16} /></button></div>}
    </div>
  );
}
