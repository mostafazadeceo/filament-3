# -*- coding: utf-8 -*-
"""
وبکم مجازی از روی عکس (ROI دقیق + زنده + فارسی + چرخش/قرینه + بهبود متن)

کنترل‌ها (همه زنده):
- روی تصویر کادر بکش (ROI آزاد)
- داخل کادر بگیر و بکش: جابه‌جایی
- گوشه‌های کادر را بگیر و بکش: تغییر اندازه
- اسکرول موس: زوم از مرکز کادر (واقعی)
- کلیدهای جهت: حرکت دقیق | Shift+جهت: سریع‌تر
- کلید R: بازنشانی کادر طبیعی (وسط)

نکته:
اگر بعد از عکس گرفتن خروجی وارونه شد → «چرخش خروجی = ۱۸۰» را انتخاب کن.
"""

import tkinter as tk
from tkinter import ttk, filedialog, messagebox
from PIL import Image, ImageTk, ImageOps, ImageFilter, ImageEnhance
import numpy as np
from typing import Optional, Tuple, List

try:
    import pyvirtualcam
except Exception as e:
    pyvirtualcam = None
    _pyvirtualcam_err = e


def clamp(v: float, lo: float, hi: float) -> float:
    return lo if v < lo else hi if v > hi else v


class ROI:
    def __init__(self, l: float, t: float, r: float, b: float):
        self.l, self.t, self.r, self.b = l, t, r, b

    def norm(self):
        l = min(self.l, self.r)
        r = max(self.l, self.r)
        t = min(self.t, self.b)
        b = max(self.t, self.b)
        self.l, self.t, self.r, self.b = l, t, r, b

    def w(self) -> float:
        return max(0.0, self.r - self.l)

    def h(self) -> float:
        return max(0.0, self.b - self.t)

    def cx(self) -> float:
        return (self.l + self.r) / 2.0

    def cy(self) -> float:
        return (self.t + self.b) / 2.0

    def set_center_size(self, cx: float, cy: float, w: float, h: float):
        w = max(1.0, w)
        h = max(1.0, h)
        self.l = cx - w / 2.0
        self.r = cx + w / 2.0
        self.t = cy - h / 2.0
        self.b = cy + h / 2.0


class MaskRect:
    def __init__(self, l: float, t: float, r: float, b: float):
        self.l, self.t, self.r, self.b = l, t, r, b

    def norm(self):
        l = min(self.l, self.r)
        r = max(self.l, self.r)
        t = min(self.t, self.b)
        b = max(self.t, self.b)
        self.l, self.t, self.r, self.b = l, t, r, b

    def w(self) -> float:
        return max(0.0, self.r - self.l)

    def h(self) -> float:
        return max(0.0, self.b - self.t)


class App:
    def __init__(self, root: tk.Tk):
        self.root = root
        self.root.title("وبکم مجازی (ROI دقیق و زنده)")

        self.RESAMPLE = Image.Resampling.LANCZOS if hasattr(Image, "Resampling") else Image.LANCZOS

        # تصویر
        self.img: Optional[Image.Image] = None
        self.img_path: Optional[str] = None

        # خروجی
        self.out_w, self.out_h = 1280, 720
        self.fps = 30

        # ROI
        self.roi: Optional[ROI] = None
        # ماسک‌های حریم خصوصی (مختصات نسبی به ROI)
        self.masks: List[MaskRect] = []
        self._mask_preview: Optional[MaskRect] = None

        # نقشه نمایش (مقیاس و آفست)
        self._view_map = None  # (scale, ox, oy, draw_w, draw_h)
        self._view_img_tk = None
        self._view_cache_key = None
        self._preview_tk = None

        # تعامل موس
        self._mode = None  # None | 'new' | 'move' | 'resize_tl' ...
        self._drag_start_img = (0.0, 0.0)
        self._drag_start_roi = None

        # کش فریم خروجی
        self._frame_cache_key = None
        self._frame_cache_np = None

        # وبکم مجازی
        self.cam = None
        self.streaming = False

        # گزینه‌ها (همه زنده)
        self.fill_mode = tk.StringVar(value="مشکی")          # مشکی/خاکستری/بلور
        self.enhance_mode = tk.StringVar(value="متن-سیاه‌سفید")  # خاموش/متن-رنگی/متن-سیاه‌سفید
        self.rotate_deg = tk.StringVar(value="0")            # 0/90/180/270
        self.mirror = tk.BooleanVar(value=False)             # قرینه افقی
        self.no_upscale = tk.BooleanVar(value=True)          # جلوگیری از بزرگ‌نمایی مصنوعی
        self.mask_mode = tk.StringVar(value="بلور")          # خاموش/بلور/سیاه
        self.auto_mask = tk.BooleanVar(value=True)           # ماسک پیش‌فرض پاسپورت روی بارگذاری
        self.auto_start = tk.BooleanVar(value=True)          # شروع خودکار ارسال

        self._build_ui()
        self._bind_events()

        if pyvirtualcam is None:
            messagebox.showerror(
                "خطا",
                f"pyvirtualcam نصب/لود نشد.\n"
                f"نصب:\n"
                f"pip install pyvirtualcam numpy pillow\n\n"
                f"جزئیات:\n{_pyvirtualcam_err}"
            )

        self._tick()

    # ---------- UI ----------
    def _build_ui(self):
        self.root.geometry("1120x720")

        top = ttk.Frame(self.root, padding=(8, 8, 8, 6))
        top.pack(fill="x")

        ttk.Button(top, text="انتخاب عکس", command=self.load_image).pack(side="left")

        self.btn_start = ttk.Button(top, text="شروع", command=self.start_stream)
        self.btn_start.pack(side="left", padx=(8, 0))
        self.btn_stop = ttk.Button(top, text="توقف", command=self.stop_stream, state="disabled")
        self.btn_stop.pack(side="left", padx=(6, 0))
        ttk.Checkbutton(top, text="شروع خودکار", variable=self.auto_start).pack(side="left", padx=(8, 0))

        ttk.Label(top, text="خروجی:").pack(side="left", padx=(12, 4))
        self.res_var = tk.StringVar(value="1280x720")
        res_box = ttk.Combobox(top, textvariable=self.res_var, state="readonly",
                               values=["640x360", "1280x720", "1920x1080"], width=10)
        res_box.pack(side="left")
        res_box.bind("<<ComboboxSelected>>", lambda e: self._on_res_change())

        ttk.Label(top, text="FPS:").pack(side="left", padx=(10, 4))
        self.fps_var = tk.IntVar(value=self.fps)
        fps_spin = ttk.Spinbox(top, from_=5, to=60, textvariable=self.fps_var, width=5, command=self._on_fps_change)
        fps_spin.pack(side="left")

        ttk.Label(top, text="پس‌زمینه:").pack(side="left", padx=(10, 4))
        fill_box = ttk.Combobox(top, textvariable=self.fill_mode, state="readonly",
                                values=["مشکی", "خاکستری", "بلور"], width=8)
        fill_box.pack(side="left")
        fill_box.bind("<<ComboboxSelected>>", lambda e: self.invalidate())

        ttk.Label(top, text="بهبود:").pack(side="left", padx=(10, 4))
        enh_box = ttk.Combobox(top, textvariable=self.enhance_mode, state="readonly",
                               values=["خاموش", "متن-رنگی", "متن-سیاه‌سفید"], width=12)
        enh_box.pack(side="left")
        enh_box.bind("<<ComboboxSelected>>", lambda e: self.invalidate())

        ttk.Label(top, text="چرخش:").pack(side="left", padx=(10, 4))
        rot_box = ttk.Combobox(top, textvariable=self.rotate_deg, state="readonly",
                               values=["0", "90", "180", "270"], width=5)
        rot_box.pack(side="left")
        rot_box.bind("<<ComboboxSelected>>", lambda e: self.invalidate())

        ttk.Checkbutton(top, text="قرینه", variable=self.mirror, command=self.invalidate).pack(side="left", padx=(10, 0))
        ttk.Checkbutton(top, text="بدون بزرگ‌نمایی مصنوعی", variable=self.no_upscale, command=self.invalidate)\
            .pack(side="left", padx=(10, 0))

        ttk.Button(top, text="بازنشانی (R)", command=self.reset_roi_natural).pack(side="right")

        main = ttk.Frame(self.root, padding=(8, 4, 8, 8))
        main.pack(fill="both", expand=True)

        left = ttk.Frame(main)
        left.pack(side="left", fill="both", expand=True)

        self.canvas = tk.Canvas(left, bg="gray90", highlightthickness=0)
        self.canvas.pack(fill="both", expand=True)

        right = ttk.Frame(main, width=340)
        right.pack(side="right", fill="y", padx=(10, 0))

        ttk.Label(right, text="پیش‌نمایش خروجی").pack(anchor="w")
        self.preview = tk.Canvas(right, bg="black", width=320, height=180, highlightthickness=0)
        self.preview.pack(pady=(6, 10))

        ttk.Label(right, text="حریم خصوصی").pack(anchor="w")
        mask_box = ttk.Combobox(right, textvariable=self.mask_mode, state="readonly",
                                values=["خاموش", "بلور", "سیاه"], width=10)
        mask_box.pack(anchor="w", pady=(4, 4))
        mask_box.bind("<<ComboboxSelected>>", lambda e: self.invalidate())

        ttk.Checkbutton(right, text="ماسک خودکار پاسپورت", variable=self.auto_mask,
                        command=self._on_auto_mask_toggle).pack(anchor="w", pady=(0, 6))

        mask_btns = ttk.Frame(right)
        mask_btns.pack(anchor="w", pady=(0, 6))
        ttk.Button(mask_btns, text="MRZ", command=lambda: self.add_mask_preset("mrz")).pack(side="left")
        ttk.Button(mask_btns, text="شماره", command=lambda: self.add_mask_preset("num")).pack(side="left", padx=(6, 0))
        ttk.Button(mask_btns, text="QR", command=lambda: self.add_mask_preset("qr")).pack(side="left", padx=(6, 0))
        ttk.Button(right, text="پاک‌کردن ماسک‌ها", command=self.clear_masks).pack(anchor="w", pady=(0, 6))

        self.info = tk.StringVar(value="—")
        ttk.Label(right, textvariable=self.info, justify="left").pack(anchor="w")

        help_txt = (
            "راهنمای کلیدها:\n"
            "• R : بازنشانی کادر (وسط)\n"
            "• جهت‌ها : حرکت دقیق کادر\n"
            "• Shift+جهت‌ها : حرکت سریع‌تر\n"
            "• اسکرول موس : زوم از مرکز کادر\n"
            "• Shift+درگ یا کلیک راست : ماسک‌کردن بخش‌های حساس (MRZ/شماره/QR)\n\n"
            "نکته:\n"
            "اگر بعد از عکس گرفتن وارونه شد:\n"
            "«چرخش = 180» را انتخاب کن.\n"
            "اگر تصویر حساس است، قبل از ارسال ماسک‌ها را تنظیم کن."
        )
        ttk.Label(right, text=help_txt, justify="left").pack(anchor="w", pady=(8, 0))

    def _bind_events(self):
        self.canvas.bind("<ButtonPress-1>", self.on_mouse_down)
        self.canvas.bind("<B1-Motion>", self.on_mouse_drag)
        self.canvas.bind("<ButtonRelease-1>", self.on_mouse_up)
        self.canvas.bind("<Shift-ButtonPress-1>", self.on_mask_down)
        self.canvas.bind("<Shift-B1-Motion>", self.on_mask_drag)
        self.canvas.bind("<Shift-ButtonRelease-1>", self.on_mask_up)
        self.canvas.bind("<ButtonPress-3>", self.on_mask_down)
        self.canvas.bind("<B3-Motion>", self.on_mask_drag)
        self.canvas.bind("<ButtonRelease-3>", self.on_mask_up)

        self.canvas.bind("<MouseWheel>", self.on_wheel)        # ویندوز
        self.canvas.bind("<Button-4>", self.on_wheel_linux)    # لینوکس بالا
        self.canvas.bind("<Button-5>", self.on_wheel_linux)    # لینوکس پایین

        self.root.bind("<KeyPress>", self.on_key)
        self.canvas.bind("<Configure>", lambda e: self.invalidate(full=True))

    # ---------- تغییر رزولوشن/FPS (زنده) ----------
    def _on_res_change(self):
        try:
            w, h = self.res_var.get().split("x")
            self.out_w, self.out_h = int(w), int(h)
        except Exception:
            return
        # اگر در حال استریم هستیم، ریستارت کنیم تا سایز جدید اعمال شود
        if self.streaming:
            self.stop_stream()
            self.start_stream()
        self.invalidate()

    def _on_fps_change(self):
        # برای اعمال دقیق fps روی pyvirtualcam بهتره ریستارت شود
        if self.streaming:
            self.stop_stream()
            self.start_stream()

    # ---------- تبدیل مختصات ----------
    def _compute_view_map(self):
        if self.img is None:
            return None
        cw = max(1, self.canvas.winfo_width())
        ch = max(1, self.canvas.winfo_height())
        iw, ih = self.img.size
        scale = min(cw / iw, ch / ih)
        draw_w = int(round(iw * scale))
        draw_h = int(round(ih * scale))
        ox = (cw - draw_w) // 2
        oy = (ch - draw_h) // 2
        return (scale, ox, oy, draw_w, draw_h)

    def _canvas_to_img(self, x: float, y: float) -> Tuple[float, float]:
        if self.img is None or self._view_map is None:
            return (0.0, 0.0)
        scale, ox, oy, _, _ = self._view_map
        ix = (x - ox) / scale
        iy = (y - oy) / scale
        iw, ih = self.img.size
        return (clamp(ix, 0.0, iw), clamp(iy, 0.0, ih))

    def _img_to_canvas(self, ix: float, iy: float) -> Tuple[float, float]:
        if self.img is None or self._view_map is None:
            return (0.0, 0.0)
        scale, ox, oy, _, _ = self._view_map
        return (ox + ix * scale, oy + iy * scale)

    # ---------- ROI ----------
    def _roi_exists(self) -> bool:
        return self.roi is not None and self.roi.w() >= 4 and self.roi.h() >= 4

    def _clamp_roi(self):
        if self.img is None or self.roi is None:
            return
        iw, ih = self.img.size
        self.roi.norm()

        w, h = self.roi.w(), self.roi.h()
        w = max(4.0, min(w, float(iw)))
        h = max(4.0, min(h, float(ih)))

        cx, cy = self.roi.cx(), self.roi.cy()
        cx = clamp(cx, w/2.0, iw - w/2.0)
        cy = clamp(cy, h/2.0, ih - h/2.0)

        self.roi.set_center_size(cx, cy, w, h)
        self.roi.norm()

    def reset_roi_natural(self):
        """کادر طبیعی وسط (به نسبت خروجی) ولی بعدش آزاد می‌تونی تغییرش بدی."""
        if self.img is None:
            return
        iw, ih = self.img.size
        out_a = self.out_w / self.out_h
        img_a = iw / ih

        if img_a >= out_a:
            h = float(ih)
            w = h * out_a
        else:
            w = float(iw)
            h = w / out_a

        # کمی حاشیه برای طبیعی‌تر
        w *= 0.96
        h *= 0.96

        cx, cy = iw / 2.0, ih / 2.0
        self.roi = ROI(cx - w/2, cy - h/2, cx + w/2, cy + h/2)
        self._clamp_roi()
        self.invalidate()

    def _hit_corner(self, x: float, y: float) -> Optional[str]:
        if not self._roi_exists():
            return None
        l, t, r, b = self.roi.l, self.roi.t, self.roi.r, self.roi.b
        x0, y0 = self._img_to_canvas(l, t)
        x1, y1 = self._img_to_canvas(r, b)
        R = 12
        corners = {
            "resize_tl": (x0, y0),
            "resize_tr": (x1, y0),
            "resize_bl": (x0, y1),
            "resize_br": (x1, y1),
        }
        for k, (cx, cy) in corners.items():
            if (x - cx) ** 2 + (y - cy) ** 2 <= R ** 2:
                return k
        return None

    def _in_roi(self, x: float, y: float) -> bool:
        if not self._roi_exists():
            return False
        l, t, r, b = self.roi.l, self.roi.t, self.roi.r, self.roi.b
        x0, y0 = self._img_to_canvas(l, t)
        x1, y1 = self._img_to_canvas(r, b)
        return (min(x0, x1) <= x <= max(x0, x1)) and (min(y0, y1) <= y <= max(y0, y1))

    # ---------- Mouse handlers ----------
    def on_mouse_down(self, event):
        if self.img is None:
            return
        if (event.state & 0x0001) != 0:  # Shift -> ماسک
            return
        self._view_map = self._compute_view_map()
        if self._view_map is None:
            return

        corner = self._hit_corner(event.x, event.y)
        if corner:
            self._mode = corner
            self._drag_start_img = self._canvas_to_img(event.x, event.y)
            self._drag_start_roi = ROI(self.roi.l, self.roi.t, self.roi.r, self.roi.b)
            return

        if self._in_roi(event.x, event.y):
            self._mode = "move"
            self._drag_start_img = self._canvas_to_img(event.x, event.y)
            self._drag_start_roi = ROI(self.roi.l, self.roi.t, self.roi.r, self.roi.b)
            return

        # new ROI
        self._mode = "new"
        ix, iy = self._canvas_to_img(event.x, event.y)
        self.roi = ROI(ix, iy, ix, iy)
        self.invalidate()

    def on_mouse_drag(self, event):
        if self.img is None or self._mode is None:
            return
        if self._mode == "mask":
            return
        self._view_map = self._compute_view_map()
        ix, iy = self._canvas_to_img(event.x, event.y)

        if self._mode == "new":
            self.roi.r, self.roi.b = ix, iy
            self.roi.norm()
            self._clamp_roi()
            self.invalidate()
            return

        if self._drag_start_roi is None:
            return

        base = self._drag_start_roi
        if self._mode == "move":
            sx, sy = self._drag_start_img
            dx, dy = ix - sx, iy - sy
            self.roi = ROI(base.l + dx, base.t + dy, base.r + dx, base.b + dy)
            self._clamp_roi()
            self.invalidate()
            return

        if self._mode == "resize_tl":
            self.roi = ROI(ix, iy, base.r, base.b)
        elif self._mode == "resize_tr":
            self.roi = ROI(base.l, iy, ix, base.b)
        elif self._mode == "resize_bl":
            self.roi = ROI(ix, base.t, base.r, iy)
        elif self._mode == "resize_br":
            self.roi = ROI(base.l, base.t, ix, iy)

        self.roi.norm()
        if self.roi.w() < 8:
            cx = self.roi.cx()
            self.roi.l, self.roi.r = cx - 4, cx + 4
        if self.roi.h() < 8:
            cy = self.roi.cy()
            self.roi.t, self.roi.b = cy - 4, cy + 4

        self._clamp_roi()
        self.invalidate()

    def on_mouse_up(self, event):
        if self._mode == "mask" or (event.state & 0x0001) != 0:
            return
        if self._mode == "new" and not self._roi_exists():
            self.roi = None
        self._mode = None
        self._drag_start_roi = None
        self.invalidate()

    # ---------- Mask handlers ----------
    def _img_to_rel(self, ix: float, iy: float) -> Tuple[float, float]:
        if self.roi is None:
            return (0.0, 0.0)
        rl = (ix - self.roi.l) / max(1.0, self.roi.w())
        rt = (iy - self.roi.t) / max(1.0, self.roi.h())
        return (clamp(rl, 0.0, 1.0), clamp(rt, 0.0, 1.0))

    def _rel_to_img(self, rl: float, rt: float) -> Tuple[float, float]:
        if self.roi is None:
            return (0.0, 0.0)
        ix = self.roi.l + rl * self.roi.w()
        iy = self.roi.t + rt * self.roi.h()
        return (ix, iy)

    def on_mask_down(self, event):
        if self.img is None or not self._roi_exists():
            return "break"
        self._view_map = self._compute_view_map()
        if self._view_map is None:
            return "break"
        ix, iy = self._canvas_to_img(event.x, event.y)
        rl, rt = self._img_to_rel(ix, iy)
        self._mode = "mask"
        self._mask_preview = MaskRect(rl, rt, rl, rt)
        return "break"

    def on_mask_drag(self, event):
        if self.img is None or self._mode != "mask" or self._mask_preview is None:
            return "break"
        ix, iy = self._canvas_to_img(event.x, event.y)
        rl, rt = self._img_to_rel(ix, iy)
        self._mask_preview.r = rl
        self._mask_preview.b = rt
        self._mask_preview.norm()
        return "break"

    def on_mask_up(self, _event):
        if self._mode != "mask" or self._mask_preview is None:
            return "break"
        self._mask_preview.norm()
        if self._mask_preview.w() >= 0.02 and self._mask_preview.h() >= 0.02:
            self.masks.append(self._mask_preview)
            self.invalidate()
        self._mask_preview = None
        self._mode = None
        return "break"

    # ---------- Wheel zoom ----------
    def on_wheel(self, event):
        if self.img is None or not self._roi_exists():
            return
        self._zoom_roi(1 if event.delta > 0 else -1)

    def on_wheel_linux(self, event):
        if self.img is None or not self._roi_exists():
            return
        self._zoom_roi(1 if event.num == 4 else -1)

    def _zoom_roi(self, step: int):
        # step>0: زوم داخل => ROI کوچکتر
        factor = 0.92 if step > 0 else (1.0 / 0.92)
        cx, cy = self.roi.cx(), self.roi.cy()
        w, h = self.roi.w() * factor, self.roi.h() * factor
        w, h = max(8.0, w), max(8.0, h)

        # اگر no_upscale روشن باشد، اجازه نمی‌دهیم ROI خیلی کوچک شود که مجبور به بزرگ‌نمایی مصنوعی شویم
        if self.no_upscale.get() and self.img is not None:
            iw, ih = self.img.size
            w = max(w, min(float(iw), float(self.out_w)))
            h = max(h, min(float(ih), float(self.out_h)))

        self.roi.set_center_size(cx, cy, w, h)
        self._clamp_roi()
        self.invalidate()

    # ---------- Keyboard ----------
    def on_key(self, event):
        if event.keysym.lower() == "r":
            self.reset_roi_natural()
            return
        if self.img is None or not self._roi_exists():
            return

        step = 2.0
        if (event.state & 0x0001) != 0:  # Shift
            step = 12.0

        if event.keysym == "Left":
            self.roi.l -= step; self.roi.r -= step
        elif event.keysym == "Right":
            self.roi.l += step; self.roi.r += step
        elif event.keysym == "Up":
            self.roi.t -= step; self.roi.b -= step
        elif event.keysym == "Down":
            self.roi.t += step; self.roi.b += step
        else:
            return

        self._clamp_roi()
        self.invalidate()

    # ---------- پردازش تصویر ----------
    def _enhance(self, im: Image.Image) -> Image.Image:
        mode = self.enhance_mode.get()
        if mode == "خاموش":
            return im
        if mode == "متن-سیاه‌سفید":
            g = im.convert("L")
            g = ImageOps.autocontrast(g)
            g = ImageEnhance.Contrast(g).enhance(1.30)
            g = g.filter(ImageFilter.UnsharpMask(radius=1.6, percent=150, threshold=2))
            return g.convert("RGB")
        # متن-رنگی
        c = ImageOps.autocontrast(im)
        c = ImageEnhance.Contrast(c).enhance(1.18)
        c = c.filter(ImageFilter.UnsharpMask(radius=1.4, percent=130, threshold=2))
        return c

    def _apply_masks(self, im: Image.Image) -> Image.Image:
        mode = self.mask_mode.get()
        if mode == "خاموش" or not self.masks:
            return im
        cw, ch = im.size
        for m in self.masks:
            l = int(round(clamp(m.l, 0.0, 1.0) * cw))
            t = int(round(clamp(m.t, 0.0, 1.0) * ch))
            r = int(round(clamp(m.r, 0.0, 1.0) * cw))
            b = int(round(clamp(m.b, 0.0, 1.0) * ch))
            if r - l < 2 or b - t < 2:
                continue
            region = im.crop((l, t, r, b))
            if mode == "سیاه":
                region = Image.new("RGB", (r - l, b - t), (0, 0, 0))
            else:
                region = region.filter(ImageFilter.GaussianBlur(radius=14))
            im.paste(region, (l, t))
        return im

    def _rotate(self, im: Image.Image) -> Image.Image:
        try:
            deg = int(self.rotate_deg.get()) % 360
        except Exception:
            deg = 0
        if deg == 0:
            return im
        return im.rotate(deg, expand=False)

    def _apply_mirror(self, im: Image.Image) -> Image.Image:
        return ImageOps.mirror(im) if self.mirror.get() else im

    def _build_background(self, crop: Image.Image, cw: int, ch: int) -> Image.Image:
        mode = self.fill_mode.get()
        if mode == "مشکی":
            return Image.new("RGB", (self.out_w, self.out_h), (0, 0, 0))
        if mode == "خاکستری":
            return Image.new("RGB", (self.out_w, self.out_h), (22, 22, 22))

        # بلور
        cover_scale = max(self.out_w / cw, self.out_h / ch)
        if self.no_upscale.get() and cover_scale > 1.0:
            cover_scale = 1.0
        cover_w = max(1, int(round(cw * cover_scale)))
        cover_h = max(1, int(round(ch * cover_scale)))
        bg = crop.resize((cover_w, cover_h), resample=self.RESAMPLE)
        left = (cover_w - self.out_w) // 2
        top = (cover_h - self.out_h) // 2
        bg = bg.crop((left, top, left + self.out_w, top + self.out_h))
        return bg.filter(ImageFilter.GaussianBlur(radius=18))

    def render_frame_np(self) -> Optional[np.ndarray]:
        if self.img is None or not self._roi_exists():
            return None

        key = (
            self.img_path,
            self.out_w, self.out_h, int(self.fps_var.get()),
            self.fill_mode.get(), self.enhance_mode.get(),
            self.rotate_deg.get(), int(self.mirror.get()), int(self.no_upscale.get()),
            int(round(self.roi.l)), int(round(self.roi.t)), int(round(self.roi.r)), int(round(self.roi.b)),
        )
        if self._frame_cache_key == key and self._frame_cache_np is not None:
            return self._frame_cache_np

        img = self.img if self.img.mode == "RGB" else self.img.convert("RGB")
        iw, ih = img.size

        l = int(round(clamp(self.roi.l, 0.0, iw)))
        t = int(round(clamp(self.roi.t, 0.0, ih)))
        r = int(round(clamp(self.roi.r, 0.0, iw)))
        b = int(round(clamp(self.roi.b, 0.0, ih)))

        crop = img.crop((l, t, r, b))
        cw, ch = crop.size
        if cw < 2 or ch < 2:
            return None

        crop = self._enhance(crop)
        crop = self._apply_masks(crop)

        sx = self.out_w / cw
        sy = self.out_h / ch
        scale = min(sx, sy)
        if self.no_upscale.get():
            scale = min(scale, 1.0)

        fit_w = max(1, int(round(cw * scale)))
        fit_h = max(1, int(round(ch * scale)))
        roi_fit = crop.resize((fit_w, fit_h), resample=self.RESAMPLE)

        bg = self._build_background(crop, cw, ch)
        x = (self.out_w - fit_w) // 2
        y = (self.out_h - fit_h) // 2
        bg.paste(roi_fit, (x, y))

        bg = self._rotate(bg)
        bg = self._apply_mirror(bg)

        frame = np.asarray(bg, dtype=np.uint8)
        self._frame_cache_key = key
        self._frame_cache_np = frame
        return frame

    # ---------- نمایش ----------
    def invalidate(self, full: bool = False):
        if full:
            self._view_cache_key = None
        self._frame_cache_key = None
        self._frame_cache_np = None

    def draw(self):
        self.canvas.delete("all")
        if self.img is None:
            self.info.set("یک عکس انتخاب کن.")
            return

        self._view_map = self._compute_view_map()
        if self._view_map is None:
            return
        scale, ox, oy, draw_w, draw_h = self._view_map
        ck = (self.img_path, draw_w, draw_h)
        if self._view_cache_key != ck:
            disp = self.img.resize((draw_w, draw_h), resample=self.RESAMPLE)
            self._view_img_tk = ImageTk.PhotoImage(disp)
            self._view_cache_key = ck

        self.canvas.create_image(ox, oy, anchor="nw", image=self._view_img_tk)

        if self._roi_exists():
            x0, y0 = self._img_to_canvas(self.roi.l, self.roi.t)
            x1, y1 = self._img_to_canvas(self.roi.r, self.roi.b)
            self.canvas.create_rectangle(x0, y0, x1, y1, outline="#00cc66", width=3)
            for (hx, hy) in [(x0, y0), (x1, y0), (x0, y1), (x1, y1)]:
                self.canvas.create_oval(hx-6, hy-6, hx+6, hy+6, fill="#00cc66", outline="white", width=1)

        # نمایش ماسک‌ها
        if self._roi_exists() and self.mask_mode.get() != "خاموش":
            masks = list(self.masks)
            if self._mask_preview is not None:
                masks.append(self._mask_preview)
            for m in masks:
                ix0, iy0 = self._rel_to_img(m.l, m.t)
                ix1, iy1 = self._rel_to_img(m.r, m.b)
                x0, y0 = self._img_to_canvas(ix0, iy0)
                x1, y1 = self._img_to_canvas(ix1, iy1)
                self.canvas.create_rectangle(x0, y0, x1, y1, outline="#ff4444", width=2, dash=(4, 2))

        # preview
        self.preview.delete("all")
        frame = self.render_frame_np()
        if frame is not None:
            im = Image.fromarray(frame, mode="RGB")
            im = im.resize((320, 180), resample=self.RESAMPLE)
            self._preview_tk = ImageTk.PhotoImage(im)
            self.preview.create_image(0, 0, anchor="nw", image=self._preview_tk)

        # info
        iw, ih = self.img.size
        if self._roi_exists():
            self.info.set(
                f"تصویر: {iw}×{ih}\n"
                f"کادر: {int(self.roi.w())}×{int(self.roi.h())}\n"
                f"خروجی: {self.out_w}×{self.out_h}  |  FPS: {int(self.fps_var.get())}\n"
                f"ماسک: {len(self.masks)} ناحیه\n"
                f"نکته: اگر بعد از عکس گرفتن وارونه شد → چرخش = 180"
            )
        else:
            self.info.set(f"تصویر: {iw}×{ih}\nکادر: هنوز انتخاب نشده (بکش)")

    # ---------- وبکم ----------
    def start_stream(self):
        if pyvirtualcam is None:
            return
        if self.img is None or not self._roi_exists():
            messagebox.showwarning("هشدار", "اول عکس را انتخاب کن و کادر (ROI) را مشخص کن.")
            return
        if self.streaming:
            return
        try:
            self.fps = int(self.fps_var.get())
            self.cam = pyvirtualcam.Camera(
                width=self.out_w,
                height=self.out_h,
                fps=self.fps,
                fmt=pyvirtualcam.PixelFormat.RGB
            )
        except Exception as e:
            messagebox.showerror("خطا", f"وبکم مجازی باز نشد:\n{e}")
            self.cam = None
            return

        self.streaming = True
        self.btn_start.config(state="disabled")
        self.btn_stop.config(state="normal")

    def stop_stream(self):
        self.streaming = False
        self.btn_start.config(state="normal")
        self.btn_stop.config(state="disabled")
        if self.cam is not None:
            try:
                self.cam.close()
            except Exception:
                pass
            self.cam = None

    # ---------- فایل ----------
    def load_image(self):
        path = filedialog.askopenfilename(
            title="انتخاب تصویر",
            filetypes=[("تصاویر", "*.jpg *.jpeg *.png *.bmp *.gif *.webp")]
        )
        if not path:
            return
        try:
            img = Image.open(path)
            img = ImageOps.exif_transpose(img)  # اصلاح چرخش EXIF
            img.load()
            if img.mode != "RGB":
                img = img.convert("RGB")
        except Exception as e:
            messagebox.showerror("خطا", f"باز کردن تصویر ناموفق بود:\n{e}")
            return

        self.img_path = path
        self.img = img
        self.roi = None
        self.masks = []
        self._mask_preview = None
        self._view_cache_key = None
        self.invalidate(full=True)
        self.reset_roi_natural()
        if self.auto_mask.get():
            self.apply_passport_masks()
        if self.auto_start.get():
            self.start_stream()

    # ---------- ماسک‌های پیش‌فرض ----------
    def add_mask_preset(self, kind: str):
        if not self._roi_exists():
            return
        presets = {
            "mrz": (0.0, 0.78, 1.0, 1.0),
            "num": (0.60, 0.05, 1.0, 0.24),
            "qr": (0.70, 0.68, 1.0, 1.0),
        }
        rect = presets.get(kind)
        if rect is None:
            return
        m = MaskRect(*rect)
        m.norm()
        self.masks.append(m)
        self.invalidate()

    def apply_passport_masks(self):
        if not self._roi_exists():
            return
        if self.mask_mode.get() == "خاموش":
            self.mask_mode.set("بلور")
        self.masks = []
        for key in ("mrz", "num", "qr"):
            self.add_mask_preset(key)

    def clear_masks(self):
        if not self.masks:
            return
        self.masks = []
        self._mask_preview = None
        self.invalidate()

    def _on_auto_mask_toggle(self):
        if self.auto_mask.get():
            self.apply_passport_masks()


    # ---------- حلقه ----------
    def _tick(self):
        try:
            self.draw()
            if self.streaming and self.cam is not None:
                frame = self.render_frame_np()
                if frame is not None:
                    self.cam.send(frame)
                    self.cam.sleep_until_next_frame()
        finally:
            self.root.after(16, self._tick)


def _run_gui() -> None:
    root = tk.Tk()
    try:
        style = ttk.Style()
        if "clam" in style.theme_names():
            style.theme_use("clam")
    except Exception:
        pass

    app = App(root)
    root.mainloop()


def _maybe_run_cli() -> Optional[int]:
    import sys

    if len(sys.argv) > 1 and "--gui" not in sys.argv:
        try:
            from kyc_defense.cli import main
        except Exception as exc:
            print(f"Failed to start CLI: {exc}")
            return 1
        return int(main())
    return None


if __name__ == "__main__":
    exit_code = _maybe_run_cli()
    if exit_code is None:
        _run_gui()

