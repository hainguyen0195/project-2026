export type CmsUser = { id: number; name: string; email: string; is_active: boolean; must_change_password: boolean; role: { id: number; name: string; is_system: boolean } | null; permissions: string[]; last_login_at: string | null };
export type CmsRole = { id: number; name: string; is_system: boolean; permissions: string[]; users_count: number };
export class ApiError extends Error {
  constructor(public status: number, message: string) { super(message); }
}
const base = (process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000").replace(/\/$/, "").replace(/\/api\/v1$/, "");
export async function api<T>(path: string, method = "GET", data?: unknown): Promise<T> {
  const headers: Record<string, string> = { Accept: "application/json" };
  if (method !== "GET") {
    const csrf = await fetch(`${base}/sanctum/csrf-cookie`, { credentials: "include", headers, cache: "no-store" });
    if (!csrf.ok) throw new ApiError(csrf.status, "Không thể khởi tạo phiên bảo mật.");
    const token = document.cookie.split("; ").find(cookie => cookie.startsWith("XSRF-TOKEN="))?.slice(11);
    if (token) headers["X-XSRF-TOKEN"] = decodeURIComponent(token);
    if (!(data instanceof FormData)) headers["Content-Type"] = "application/json";
  }
  const response = await fetch(`${base}/api/v1${path}`, { method, headers, credentials: "include", cache: "no-store", body: data === undefined ? undefined : data instanceof FormData ? data : JSON.stringify(data) });
  const result = response.status === 204 ? null : await response.json().catch(() => ({ message: "Máy chủ không xử lý được yêu cầu. Kiểm tra dung lượng file hoặc thử lại." }));
  if (!response.ok) {
    if (response.status === 401 && !path.includes("login")) window.dispatchEvent(new Event("cms:unauthorized"));
    throw new ApiError(response.status, result?.errors ? (Object.values(result.errors).flat() as string[]).join(" ") : result?.message || "Không thể thực hiện yêu cầu.");
  }
  return result as T;
}
export function errorMessage(error: unknown) { return error instanceof ApiError ? error.message : "Không kết nối được máy chủ. Vui lòng thử lại."; }
