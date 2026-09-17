"use client";
import { useCmsLocale } from "./cms-locale";

import { useEditor, EditorContent } from "@tiptap/react";
import StarterKit from "@tiptap/starter-kit";
import Image from "@tiptap/extension-image";
import { TableKit } from "@tiptap/extension-table";
import TextAlign from "@tiptap/extension-text-align";
import { useEffect, useRef, useState } from "react";
import { uploadProductImage } from "./product-types";
import { errorMessage } from "./cms-api";

export default function ProductEditor({ module = "products", label, value, onChange, disabled = false, onBusy }: { module?: "products" | "news"; label: string; value: string; onChange: (html: string) => void; disabled?: boolean; onBusy?: (busy: boolean) => void }) {
  const { t } = useCmsLocale();
  const fileInput = useRef<HTMLInputElement>(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const editor = useEditor({
    extensions: [StarterKit.configure({ heading: { levels: [2, 3, 4] }, link: { openOnClick: false, protocols: ["http", "https", "mailto"] } }), Image, TableKit, TextAlign.configure({ types: ["heading", "paragraph"] })],
    content: value || "", immediatelyRender: false, editable: !disabled,
    editorProps: { attributes: { "aria-label": label, class: "product-prose", role: "textbox", "aria-multiline": "true" } },
    onUpdate: ({ editor }) => onChange(editor.getHTML()),
  });
  useEffect(() => { editor?.setEditable(!disabled); }, [editor, disabled]);
  if (!editor) return <div className="rich-editor"><p>{t("Đang tải trình soạn thảo…")}</p></div>;
  const controls = [
    { text: "B", title: "In đậm", active: editor.isActive("bold"), action: () => editor.chain().focus().toggleBold().run() },
    { text: "I", title: "In nghiêng", active: editor.isActive("italic"), action: () => editor.chain().focus().toggleItalic().run() },
    { text: "U", title: "Gạch chân", active: editor.isActive("underline"), action: () => editor.chain().focus().toggleUnderline().run() },
    { text: "H2", title: "Tiêu đề 2", active: editor.isActive("heading", { level: 2 }), action: () => editor.chain().focus().toggleHeading({ level: 2 }).run() },
    { text: "H3", title: "Tiêu đề 3", active: editor.isActive("heading", { level: 3 }), action: () => editor.chain().focus().toggleHeading({ level: 3 }).run() },
    { text: "• Danh sách", title: "Danh sách", active: editor.isActive("bulletList"), action: () => editor.chain().focus().toggleBulletList().run() },
    { text: "1. Danh sách", title: "Danh sách số", active: editor.isActive("orderedList"), action: () => editor.chain().focus().toggleOrderedList().run() },
    { text: "Trích dẫn", title: "Trích dẫn", active: editor.isActive("blockquote"), action: () => editor.chain().focus().toggleBlockquote().run() },
    { text: "Căn trái", title: "Căn trái", action: () => editor.chain().focus().setTextAlign("left").run() },
    { text: "Căn giữa", title: "Căn giữa", action: () => editor.chain().focus().setTextAlign("center").run() },
    { text: "Liên kết", title: "Chèn liên kết", action: () => { const url = window.prompt("URL https:// hoặc mailto:", editor.getAttributes("link").href || "https://"); if (url === null) return; if (!url) editor.chain().focus().unsetLink().run(); else if (/^(https?:\/\/|mailto:)/i.test(url)) editor.chain().focus().extendMarkRange("link").setLink({ href: url }).run(); else setError("Chỉ nhận liên kết http, https hoặc mailto."); } },
    { text: "Ảnh", title: "Chèn ảnh vào nội dung", action: () => fileInput.current?.click() },
    { text: "Bảng", title: "Chèn bảng", action: () => editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run() },
    { text: "+ Hàng", title: "Thêm hàng", action: () => editor.chain().focus().addRowAfter().run() },
    { text: "+ Cột", title: "Thêm cột", action: () => editor.chain().focus().addColumnAfter().run() },
    { text: "Xóa bảng", title: "Xóa bảng", action: () => editor.chain().focus().deleteTable().run() },
    { text: "↶", title: "Hoàn tác", action: () => editor.chain().focus().undo().run() },
    { text: "↷", title: "Làm lại", action: () => editor.chain().focus().redo().run() },
  ];
  return <div className="rich-editor"><div className="editor-toolbar" role="toolbar" aria-label={t("Công cụ {0}", { "0": label })}>{controls.map(control => <button type="button" key={t(control.title === "Danh sách" ? "• Danh sách" : control.title)} title={t(control.title === "Danh sách" ? "• Danh sách" : control.title)} aria-label={t(control.title === "Danh sách" ? "• Danh sách" : control.title)} aria-pressed={control.active} className={control.active ? "active" : ""} disabled={disabled || busy} onClick={control.action}>{t(control.text)}</button>)}</div><EditorContent editor={editor} /><input ref={fileInput} hidden type="file" accept="image/jpeg,image/png,image/webp,image/gif" onChange={async event => { const file = event.target.files?.[0]; event.target.value = ""; if (!file) return; setBusy(true); onBusy?.(true); setError(""); try { const media = await uploadProductImage(file, module); editor.chain().focus().setImage({ src: media.url, alt: file.name }).run(); } catch (error) { setError(error instanceof Error && !("status" in error) ? error.message : errorMessage(error)); } finally { setBusy(false); onBusy?.(false); } }} />{busy && <p role="status">{t("Đang tải ảnh…")}</p>}{error && <p role="alert" className="cms-error">{t(error)}</p>}<div className="editor-caption">{t("Soạn thảo trực quan · HTML được lọc an toàn khi lưu")}</div></div>;
}
