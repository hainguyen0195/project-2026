"use client";

import { useCallback, useEffect, useState } from "react";
import { useCmsLocale } from "./cms-locale";
import { useCmsAuth } from "./cms-auth";
import { errorMessage } from "./cms-api";
import { catalogApi, newProduct, type CatalogDefinition, type CatalogProduct } from "./product-types";
import ProductForm from "./product-form";
import { showNotification } from "./notifications";

export default function StaticPagePanel({ definition }: { definition: CatalogDefinition }) {
  const { locale } = useCmsLocale();
  const { user } = useCmsAuth();
  const editable = user.permissions.includes("content.manage");
  const [page, setPage] = useState<CatalogProduct | null>(null);
  const [error, setError] = useState("");
  const [version, setVersion] = useState(0);
  const [loading, setLoading] = useState(true);
  const load = useCallback(async () => {
    setLoading(true); setError("");
    try {
      const result = await catalogApi<{ data: CatalogProduct[] }>(definition, "/cms/products");
      setPage(result.data[0] || { ...newProduct(definition.key), name: definition.label.vi, slug: definition.key, translations: { en: { name: definition.label.en } } });
      setVersion(value => value + 1);
    } catch (failure) { setError(errorMessage(failure)); }
    finally { setLoading(false); }
  }, [definition]);
  useEffect(() => { void load(); }, [load]);
  if (loading) return <p role="status">{locale === "vi" ? "Đang tải trang tĩnh…" : "Loading page…"}</p>;
  if (error) return <div role="alert" className="cms-error">{error}<button className="button secondary" onClick={() => void load()}>{locale === "vi" ? "Thử lại" : "Retry"}</button></div>;
  if (!page) return null;
  if (!editable && !page.id) return <p>{locale === "vi" ? "Trang này chưa có nội dung." : "This page has no content yet."}</p>;
  return <ProductForm key={version} definition={definition} initial={page} categories={[]} readOnly={!editable} onClose={() => void load()} onSaved={saved => {
    setPage(saved); setVersion(value => value + 1);
    showNotification(locale === "vi" ? (definition.kind === "seopage" ? "Đã lưu SEO Page." : "Đã lưu trang tĩnh.") : "Page saved.");
  }} />;
}
