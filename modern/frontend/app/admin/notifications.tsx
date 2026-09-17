"use client";

import { Toaster, toast } from "sonner";
import { useCmsLocale } from "./cms-locale";

export function showNotification(message: string, type: "success" | "error" = "success") {
  return toast[type](message, { duration: type === "error" ? 6000 : 3000 });
}

export default function Notifications() {
  const { t } = useCmsLocale();
  return <Toaster position="top-right" closeButton visibleToasts={3} offset={24} toastOptions={{
    closeButtonAriaLabel: t("Đóng thông báo"),
    style: { background: "var(--surface)", color: "var(--text)", border: "1px solid var(--border)", borderRadius: "12px", boxShadow: "0 8px 30px #0002", fontSize: "13px" },
    classNames: { success: "cms-toast-success", error: "cms-toast-error" },
  }} />;
}
