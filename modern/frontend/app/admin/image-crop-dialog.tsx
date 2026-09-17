"use client";

import { useEffect, useRef, useState } from "react";
import { useCmsLocale } from "./cms-locale";
import { errorMessage } from "./cms-api";

type Crop = { x: number; y: number; width: number; height: number };

export default function ImageCropDialog({ src, onApply, onClose }: { src: string; onApply: (file: File) => Promise<void>; onClose: () => void }) {
  const { locale } = useCmsLocale();
  const text = (vi: string, en: string) => locale === "vi" ? vi : en;
  const dialog = useRef<HTMLDialogElement>(null);
  const canvas = useRef<HTMLCanvasElement>(null);
  const image = useRef<HTMLImageElement | null>(null);
  const origin = useRef<{ x: number; y: number } | null>(null);
  const [ready, setReady] = useState(false);
  const [rotation, setRotation] = useState(0);
  const [zoom, setZoom] = useState(1);
  const [size, setSize] = useState({ width: 1, height: 1 });
  const [crop, setCrop] = useState<Crop>({ x: 0, y: 0, width: 1, height: 1 });
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    dialog.current?.showModal();
    const controller = new AbortController();
    let objectUrl = "";
    let cancelled = false;
    async function load() {
      try {
        const response = await fetch(src, { signal: controller.signal });
        if (!response.ok) throw new Error("Cannot load image");
        objectUrl = URL.createObjectURL(await response.blob());
        const source = new Image();
        source.src = objectUrl;
        await source.decode();
        if (cancelled) return;
        image.current = source;
        setReady(true);
      } catch {
        if (!cancelled) setError(locale === "vi" ? "Không tải được ảnh. Hãy đóng và thử lại." : "Unable to load image. Close and try again.");
      }
    }
    void load();
    return () => { cancelled = true; controller.abort(); if (objectUrl) URL.revokeObjectURL(objectUrl); };
  }, [src, locale]);

  useEffect(() => {
    const source = image.current;
    const target = canvas.current;
    if (!ready || !source || !target) return;
    const radians = rotation * Math.PI / 180;
    const width = Math.round(Math.abs(source.naturalWidth * Math.cos(radians)) + Math.abs(source.naturalHeight * Math.sin(radians)));
    const height = Math.round(Math.abs(source.naturalWidth * Math.sin(radians)) + Math.abs(source.naturalHeight * Math.cos(radians)));
    target.width = width;
    target.height = height;
    const context = target.getContext("2d");
    if (!context) return;
    context.translate(width / 2, height / 2);
    context.rotate(radians);
    context.drawImage(source, -source.naturalWidth / 2, -source.naturalHeight / 2);
    setSize({ width, height });
    setCrop({ x: 0, y: 0, width: Math.min(width, 5000), height: Math.min(height, 5000) });
  }, [ready, rotation]);

  function updateCrop(key: keyof Crop, value: number) {
    if (!Number.isFinite(value)) return;
    setCrop(current => {
      const next = { ...current, [key]: Math.round(value) };
      next.x = Math.max(0, Math.min(next.x, size.width - 1));
      next.y = Math.max(0, Math.min(next.y, size.height - 1));
      next.width = Math.max(1, Math.min(next.width, size.width - next.x, 5000));
      next.height = Math.max(1, Math.min(next.height, size.height - next.y, 5000));
      return next;
    });
  }

  async function apply() {
    if (!canvas.current || busy || !ready) return;
    setBusy(true); setError("");
    try {
      const output = document.createElement("canvas");
      output.width = crop.width; output.height = crop.height;
      const context = output.getContext("2d");
      if (!context) throw new Error(text("Không thể xử lý ảnh.", "Cannot process image."));
      context.drawImage(canvas.current, crop.x, crop.y, crop.width, crop.height, 0, 0, crop.width, crop.height);
      const blob = await new Promise<Blob>((resolve, reject) => output.toBlob(value => value ? resolve(value) : reject(new Error(text("Không thể xuất ảnh.", "Cannot export image."))), "image/webp", 0.92));
      await onApply(new File([blob], "cropped-image.webp", { type: "image/webp" }));
      onClose();
    } catch (failure) {
      setError(failure instanceof Error && !("status" in failure) ? failure.message : errorMessage(failure));
    } finally { setBusy(false); }
  }

  return <dialog ref={dialog} className="image-crop-dialog" aria-labelledby="image-crop-title" onCancel={event => { event.preventDefault(); if (!busy) onClose(); }}>
    <div className="dialog-heading"><h2 id="image-crop-title">{text("Chỉnh sửa ảnh đại diện", "Edit main image")}</h2><button type="button" className="button secondary" disabled={busy} onClick={onClose}>{text("Đóng", "Close")}</button></div>
    <div className="image-crop-layout">
      <fieldset className="cms-form image-crop-tools" disabled={busy || !ready}>
        <p className="subtle">{text("Kéo trên ảnh để chọn vùng cắt, hoặc nhập vị trí và kích thước bên dưới. Xoay ảnh sẽ đặt lại vùng cắt.", "Drag on the image to select a crop, or enter its position and dimensions below. Rotating resets the crop.")}</p>
        <div className="album-actions"><button type="button" className="button secondary" onClick={() => setRotation(value => value - 45)}>{text("Xoay trái", "Rotate left")}</button><button type="button" className="button secondary" onClick={() => setRotation(value => value + 45)}>{text("Xoay phải", "Rotate right")}</button></div>
        <label>{text("Phóng to", "Zoom")} · {Math.round(zoom * 100)}%<input type="range" min="1" max="3" step="0.1" value={zoom} onChange={event => setZoom(Number(event.target.value))} /></label>
        {(["x", "y", "width", "height"] as const).map(key => <label key={key}>{key === "width" ? text("Rộng", "Width") : key === "height" ? text("Cao", "Height") : key.toUpperCase()} (px)<input type="number" min={key === "x" || key === "y" ? 0 : 1} max={key === "x" ? size.width - 1 : key === "y" ? size.height - 1 : Math.min(5000, key === "width" ? size.width - crop.x : size.height - crop.y)} value={crop[key]} onChange={event => updateCrop(key, Number(event.target.value))} /></label>)}
        <button type="button" className="button secondary" onClick={() => { setRotation(0); setZoom(1); if (image.current) setCrop({ x: 0, y: 0, width: Math.min(5000, image.current.naturalWidth), height: Math.min(5000, image.current.naturalHeight) }); }}>{text("Khôi phục ảnh gốc", "Reset image")}</button>
      </fieldset>
      <div className="image-crop-viewport"><div className="image-crop-stage" style={{ width: `${zoom * 100}%` }} onPointerDown={event => {
        if (!ready || busy) return;
        const bounds = event.currentTarget.getBoundingClientRect();
        origin.current = { x: Math.min(size.width - 1, Math.max(0, Math.round((event.clientX - bounds.left) / bounds.width * size.width))), y: Math.min(size.height - 1, Math.max(0, Math.round((event.clientY - bounds.top) / bounds.height * size.height))) };
        event.currentTarget.setPointerCapture(event.pointerId);
      }} onPointerMove={event => {
        if (!origin.current || busy) return;
        const bounds = event.currentTarget.getBoundingClientRect();
        const point = { x: Math.max(0, Math.min(size.width, Math.round((event.clientX - bounds.left) / bounds.width * size.width))), y: Math.max(0, Math.min(size.height, Math.round((event.clientY - bounds.top) / bounds.height * size.height))) };
        setCrop({ x: Math.min(origin.current.x, point.x), y: Math.min(origin.current.y, point.y), width: Math.min(5000, Math.max(1, Math.abs(point.x - origin.current.x))), height: Math.min(5000, Math.max(1, Math.abs(point.y - origin.current.y))) });
      }} onPointerUp={() => { origin.current = null; }} onPointerCancel={() => { origin.current = null; }}>
        <canvas ref={canvas} aria-label={text("Ảnh xem trước; dùng ô nhập để chọn vùng cắt bằng bàn phím", "Image preview; use number inputs for keyboard cropping")} />
        {ready && <div className="image-crop-selection" style={{ left: `${crop.x / size.width * 100}%`, top: `${crop.y / size.height * 100}%`, width: `${crop.width / size.width * 100}%`, height: `${crop.height / size.height * 100}%` }} />}
      </div></div>
    </div>
    {!ready && !error && <p role="status">{text("Đang tải ảnh…", "Loading image…")}</p>}
    {error && <p className="cms-error" role="alert">{error}</p>}
    <div className="dialog-actions"><span className="subtle">{text("Áp dụng rồi lưu sản phẩm để giữ thay đổi.", "Apply, then save the product to keep changes.")}</span><button type="button" className="button primary" disabled={!ready || busy} onClick={() => void apply()}>{busy ? text("Đang xử lý…", "Processing…") : text("Áp dụng hình ảnh", "Apply image")}</button></div>
  </dialog>;
}
