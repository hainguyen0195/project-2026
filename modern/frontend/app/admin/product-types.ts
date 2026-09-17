import { api } from "./cms-api";

export type CatalogDefinition = {
  key: string;
  module: "products" | "news";
  singleton: boolean;
  kind: "catalog" | "news" | "static" | "seopage";
  label: { vi: string; en: string };
  singular: { vi: string; en: string };
  category_depth: number;
  features: Record<"code" | "pricing" | "rating" | "brand" | "size" | "description" | "content" | "specifications" | "images" | "seo" | "schema" | "ai_seo" | "copy", boolean>;
  statuses: Partial<Record<"is_active" | "is_featured" | "is_new" | "is_bestseller", { vi: string; en: string }>>;
};
export function catalogApi<T>(definition: CatalogDefinition, path: string, method = "GET", data?: unknown) {
  if (definition.module === "news") path = path.replace("/cms/product-categories", "/cms/news-categories").replace("/cms/products", "/cms/news");
  return api<T>(`${path}${path.includes("?") ? "&" : "?"}type=${encodeURIComponent(definition.key)}`, method, data);
}

export type Media = { id: number; url: string; original_name: string; width: number; height: number };
export type Category = { type: string; id: number; kind: "category" | "brand" | "size"; parent_id: number | null; name: string; slug: string; image_id: number | null; image: Media | null; description: string; seo_title: string; seo_description: string; seo_keywords: string; is_active: boolean; is_featured: boolean; sort_order: number; translations: { en?: Record<string, string> } | null };
export type AlbumImage = { media_id: number; url: string; alt: string; caption: string; is_active: boolean };
export type AiSeoContent = { summary: string; audience: string; use_cases: string; alternate_names: string; faqs: { question: string; answer: string }[]; sources: { title: string; url: string }[] };
export type CatalogProduct = {
  type: string;
  ai_seo: { vi?: AiSeoContent; en?: AiSeoContent } | null;
  id: number; name: string; slug: string; code: string; category_id: number | null; brand_id: number | null; size_ids: number[];
  main_image_id: number | null; main_image: Media | null; image_alt: string; images: AlbumImage[];
  regular_price: string; sale_price: string | null; availability: string; rating: string | null;
  description: string; content: string; specifications: string;
  seo_title: string; seo_description: string; seo_keywords: string; canonical_url: string; noindex: boolean;
  schema_mode: "auto" | "custom" | "disabled"; schema_data: Record<string, unknown> | null; resolved_schema?: Record<string, unknown> | null;
  translations: { en?: Record<string, string> } | null;
  is_active: boolean; is_featured: boolean; is_new: boolean; is_bestseller: boolean; sort_order: number;
  category?: Category; brand?: Category; deleted_at?: string | null; discount_percent?: number;
};
export function newProduct(type = "san-pham"): CatalogProduct {
  return { type, ai_seo: {}, id: 0, name: "", slug: "", code: "", category_id: null, brand_id: null, size_ids: [], main_image_id: null, main_image: null, image_alt: "", images: [], regular_price: "0", sale_price: null, availability: "in_stock", rating: null, description: "", content: "", specifications: "", seo_title: "", seo_description: "", seo_keywords: "", canonical_url: "", noindex: false, schema_mode: "auto", schema_data: null, translations: {}, is_active: false, is_featured: false, is_new: false, is_bestseller: false, sort_order: 0 };
}
export function slugify(value: string) { return value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, ""); }
export function categoryPath(category: Category, categories: Category[]): string {
  const parts = [category.name]; let parent = category.parent_id; const seen = new Set([category.id]);
  while (parent && !seen.has(parent)) { seen.add(parent); const node = categories.find(item => item.id === parent); if (!node) break; parts.unshift(node.name); parent = node.parent_id; }
  return parts.join(" / ");
}
export async function uploadProductImage(file: File, module: "products" | "news" = "products"): Promise<Media> {
  if (file.size > 8 * 1024 * 1024) throw new Error("Ảnh tối đa 8 MB.");
  const data = new FormData(); data.append("image", file);
  return (await api<{ data: Media }>(module === "news" ? "/cms/news-media" : "/cms/product-media", "POST", data)).data;
}
