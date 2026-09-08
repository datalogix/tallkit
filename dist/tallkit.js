(function(global, factory) {
	typeof exports === "object" && typeof module !== "undefined" ? factory(exports) : typeof define === "function" && define.amd ? define(["exports"], factory) : (global = typeof globalThis !== "undefined" ? globalThis : global || self, factory(global.TALLKit = {}));
})(this, function(exports) {
	Object.defineProperty(exports, Symbol.toStringTag, { value: "Module" });
	//#region \0rolldown/runtime.js
	var __defProp = Object.defineProperty;
	var __exportAll = (all, no_symbols) => {
		let target = {};
		for (var name in all) __defProp(target, name, {
			get: all[name],
			enumerable: true
		});
		if (!no_symbols) __defProp(target, Symbol.toStringTag, { value: "Module" });
		return target;
	};
	//#endregion
	//#region resources/js/utils/animation.js
	function parseTimeToMilliseconds(value) {
		const parsed = Number.parseFloat(value);
		if (Number.isNaN(parsed)) return 0;
		return value.trim().endsWith("ms") ? parsed : parsed * 1e3;
	}
	function getTransitionTimeout(element) {
		const style = window.getComputedStyle(element);
		const durations = style.transitionDuration.split(",");
		const delays = style.transitionDelay.split(",");
		return durations.reduce((max, duration, index) => {
			const delay = delays[index] ?? delays[delays.length - 1] ?? "0s";
			return Math.max(max, parseTimeToMilliseconds(duration) + parseTimeToMilliseconds(delay));
		}, 0);
	}
	function animation(el, options = {}) {
		let fallbackId = null;
		let onTransitionEnd = null;
		let finished = false;
		const cleanup = () => {
			if (fallbackId !== null) {
				clearTimeout(fallbackId);
				fallbackId = null;
			}
			if (onTransitionEnd) {
				el.removeEventListener("transitionend", onTransitionEnd);
				onTransitionEnd = null;
			}
		};
		const finish = () => {
			if (finished) return;
			finished = true;
			cleanup();
			if (options.remove && el.isConnected) el.remove();
			options.onDone?.();
		};
		const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
		const applyClasses = (remove = [], add = []) => {
			if (remove.length) el.classList.remove(...remove);
			if (add.length) el.classList.add(...add);
		};
		if (reduceMotion) {
			applyClasses(options.from, options.to);
			options.start?.();
			options.finish?.();
			finish();
			return () => {};
		}
		applyClasses(options.to, options.from);
		options.start?.();
		requestAnimationFrame(() => {
			el.offsetHeight;
			applyClasses(options.from, options.to);
			options.finish?.();
		});
		onTransitionEnd = (event) => {
			if (event.target !== el) return;
			finish();
		};
		el.addEventListener("transitionend", onTransitionEnd);
		const timeout = getTransitionTimeout(el);
		if (timeout === 0) finish();
		else {
			const fallbackDelay = Math.max(timeout * .1, 50);
			fallbackId = window.setTimeout(finish, timeout + fallbackDelay);
		}
		return cleanup;
	}
	function fadeOut(el, options = {}) {
		return animation(el, {
			from: ["opacity-100"],
			to: ["opacity-0"],
			remove: true,
			...options
		});
	}
	function collapse(el, options = {}) {
		const style = window.getComputedStyle(el);
		const height = el.offsetHeight;
		const marginTop = style.marginTop;
		const marginBottom = style.marginBottom;
		const paddingTop = style.paddingTop;
		const paddingBottom = style.paddingBottom;
		el.style.height = `${height}px`;
		el.style.overflow = "hidden";
		el.style.marginTop = marginTop;
		el.style.marginBottom = marginBottom;
		el.style.paddingTop = paddingTop;
		el.style.paddingBottom = paddingBottom;
		el.style.opacity = "1";
		el.offsetHeight;
		return animation(el, {
			...options,
			start() {
				el.style.willChange = "height, margin, padding, opacity";
				options.start?.();
			},
			finish() {
				el.style.height = "0px";
				el.style.marginTop = "0px";
				el.style.marginBottom = "0px";
				el.style.paddingTop = "0px";
				el.style.paddingBottom = "0px";
				el.style.opacity = "0";
				options.finish?.();
			},
			onDone() {
				el.style.removeProperty("height");
				el.style.removeProperty("overflow");
				el.style.removeProperty("margin-top");
				el.style.removeProperty("margin-bottom");
				el.style.removeProperty("padding-top");
				el.style.removeProperty("padding-bottom");
				el.style.removeProperty("opacity");
				el.style.removeProperty("will-change");
				options.onDone?.();
			}
		});
	}
	//#endregion
	//#region resources/js/utils/assets.js
	var scripts = /* @__PURE__ */ new Map();
	async function loadScript(src, { integrity, crossorigin } = {}) {
		if (Array.isArray(src)) return src.reduce((p, s) => p.then(async (events) => [...events, await loadScript(s, {
			integrity,
			crossorigin
		})]), Promise.resolve([]));
		if (scripts.has(src)) return scripts.get(src);
		const promise = new Promise((resolve, reject) => {
			if (document.querySelector(`script[src="${src}"]`)) {
				resolve(new Event("load"));
				return;
			}
			const script = document.createElement("script");
			script.src = src;
			script.defer = true;
			if (integrity) script.integrity = integrity;
			if (integrity || crossorigin) script.crossOrigin = crossorigin ?? "anonymous";
			script.onload = resolve;
			script.onerror = (e) => {
				scripts.delete(src);
				reject(e);
			};
			document.head.appendChild(script);
		});
		scripts.set(src, promise);
		return promise;
	}
	async function loadRemoteAssets(check, scriptSrc, styleHref) {
		if (check()) return;
		await loadScript(scriptSrc);
		if (styleHref) await loadStyle(styleHref);
	}
	var modules = /* @__PURE__ */ new Map();
	async function loadRemoteModule(src) {
		if (Array.isArray(src)) return Promise.all(src.map((s) => loadRemoteModule(s)));
		if (modules.has(src)) return modules.get(src);
		const promise = import(
			/* @vite-ignore */
			src
).catch((e) => {
			modules.delete(src);
			throw e;
		});
		modules.set(src, promise);
		return promise;
	}
	var styles = /* @__PURE__ */ new Map();
	function loadStyle(href, { integrity, crossorigin } = {}) {
		if (Array.isArray(href)) return href.reduce((p, s) => p.then(async (events) => [...events, await loadStyle(s, {
			integrity,
			crossorigin
		})]), Promise.resolve([]));
		if (styles.has(href)) return styles.get(href);
		const promise = new Promise((resolve, reject) => {
			if (document.querySelector(`link[rel="stylesheet"][href="${href}"]`)) {
				resolve(new Event("load"));
				return;
			}
			const link = document.createElement("link");
			link.rel = "stylesheet";
			link.href = href;
			if (integrity) link.integrity = integrity;
			if (integrity || crossorigin) link.crossOrigin = crossorigin ?? "anonymous";
			link.onload = resolve;
			link.onerror = (e) => {
				styles.delete(href);
				reject(e);
			};
			document.head.appendChild(link);
		});
		styles.set(href, promise);
		return promise;
	}
	//#endregion
	//#region resources/js/utils/bind.js
	function bind(el, bindings) {
		const elements = el instanceof Element ? [el] : el;
		Array.from(elements ?? []).filter((element) => element instanceof Element).forEach((element, index) => {
			window.Alpine.bind(element, typeof bindings === "function" ? bindings(element, index) : bindings);
		});
	}
	function bindShortcut(el, shortcut, callback) {
		bind(el, { [`@keydown.${shortcut}.document`](event) {
			event.preventDefault();
			callback(event);
		} });
	}
	//#endregion
	//#region resources/js/utils/cache.js
	function cache(name, { ttl = 36e5, persist = true } = {}) {
		const memory = /* @__PURE__ */ new Map();
		return {
			getStorageKey(key) {
				return [
					"tallkit",
					"cache",
					name,
					key
				].filter(Boolean).join(":");
			},
			get(key) {
				const mem = memory.get(key);
				if (mem) {
					if (Date.now() < mem.exp) return mem.data;
					memory.delete(key);
				}
				if (persist) try {
					const raw = localStorage.getItem(this.getStorageKey(key));
					if (!raw) return null;
					const parsed = JSON.parse(raw);
					if (Date.now() > parsed.exp) {
						localStorage.removeItem(this.getStorageKey(key));
						return null;
					}
					memory.set(key, parsed);
					return parsed.data;
				} catch (e) {
					return null;
				}
				return null;
			},
			set(key, data) {
				const entry = {
					data,
					exp: Date.now() + ttl
				};
				memory.set(key, entry);
				if (persist) try {
					localStorage.setItem(this.getStorageKey(key), JSON.stringify(entry));
				} catch (e) {}
			}
		};
	}
	//#endregion
	//#region resources/js/utils/color.js
	var HEX_RE = /^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i;
	var RGB_RE = /^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+%?)\s*)?\)$/i;
	var HSL_RE = /^hsla?\(\s*([\d.]+)\s*,\s*([\d.]+)%\s*,\s*([\d.]+)%\s*(?:,\s*([\d.]+%?)\s*)?\)$/i;
	function clamp255(value) {
		return Math.max(0, Math.min(255, Math.round(Number(value))));
	}
	function clampAlpha(value) {
		return Math.max(0, Math.min(1, Number.isFinite(value) ? value : 1));
	}
	function roundAlpha(value) {
		return parseFloat(clampAlpha(value).toFixed(2));
	}
	function parseAlpha(value) {
		if (value === void 0) return 1;
		return clampAlpha(value.endsWith("%") ? parseFloat(value) / 100 : parseFloat(value));
	}
	function hexToRgba(hex) {
		const short = hex.length === 3 || hex.length === 4;
		const r = short ? hex[0] + hex[0] : hex.slice(0, 2);
		const g = short ? hex[1] + hex[1] : hex.slice(2, 4);
		const b = short ? hex[2] + hex[2] : hex.slice(4, 6);
		const a = short ? hex.length === 4 ? hex[3] + hex[3] : null : hex.length === 8 ? hex.slice(6, 8) : null;
		return {
			r: parseInt(r, 16),
			g: parseInt(g, 16),
			b: parseInt(b, 16),
			a: a === null ? 1 : parseInt(a, 16) / 255
		};
	}
	function hslToRgb(h, s, l) {
		h = (h % 360 + 360) % 360;
		s = clampAlpha(s / 100);
		l = clampAlpha(l / 100);
		const c = (1 - Math.abs(2 * l - 1)) * s;
		const x = c * (1 - Math.abs(h / 60 % 2 - 1));
		const m = l - c / 2;
		const [r, g, b] = h < 60 ? [
			c,
			x,
			0
		] : h < 120 ? [
			x,
			c,
			0
		] : h < 180 ? [
			0,
			c,
			x
		] : h < 240 ? [
			0,
			x,
			c
		] : h < 300 ? [
			x,
			0,
			c
		] : [
			c,
			0,
			x
		];
		return {
			r: Math.round((r + m) * 255),
			g: Math.round((g + m) * 255),
			b: Math.round((b + m) * 255)
		};
	}
	function rgbToHsl(r, g, b) {
		r /= 255;
		g /= 255;
		b /= 255;
		const max = Math.max(r, g, b);
		const min = Math.min(r, g, b);
		const l = (max + min) / 2;
		const d = max - min;
		let h = 0;
		let s = 0;
		if (d !== 0) {
			s = d / (1 - Math.abs(2 * l - 1));
			switch (max) {
				case r:
					h = (g - b) / d % 6;
					break;
				case g:
					h = (b - r) / d + 2;
					break;
				default: h = (r - g) / d + 4;
			}
			h = Math.round(h * 60);
			if (h < 0) h += 360;
		}
		return [
			h,
			Math.round(s * 100),
			Math.round(l * 100)
		];
	}
	function toHex({ r, g, b, a }, includeAlpha) {
		const hex = [
			r,
			g,
			b
		].map((v) => clamp255(v).toString(16).padStart(2, "0")).join("");
		if (!includeAlpha) return `#${hex}`;
		return `#${hex}${Math.round(clampAlpha(a) * 255).toString(16).padStart(2, "0")}`;
	}
	function parseColor(input) {
		if (typeof input !== "string") return null;
		const value = input.trim();
		if (!value) return null;
		let m;
		if (m = value.match(HEX_RE)) return hexToRgba(m[1].toLowerCase());
		if (m = value.match(RGB_RE)) return {
			r: clamp255(m[1]),
			g: clamp255(m[2]),
			b: clamp255(m[3]),
			a: parseAlpha(m[4])
		};
		if (m = value.match(HSL_RE)) {
			const { r, g, b } = hslToRgb(parseFloat(m[1]), parseFloat(m[2]), parseFloat(m[3]));
			return {
				r,
				g,
				b,
				a: parseAlpha(m[4])
			};
		}
		return null;
	}
	function formatColor({ r, g, b, a = 1 }, format = "hex") {
		const alpha = clampAlpha(a);
		switch (format) {
			case "hexa": return toHex({
				r,
				g,
				b,
				a: alpha
			}, true);
			case "rgb": return `rgb(${clamp255(r)}, ${clamp255(g)}, ${clamp255(b)})`;
			case "rgba": return `rgba(${clamp255(r)}, ${clamp255(g)}, ${clamp255(b)}, ${roundAlpha(alpha)})`;
			case "hsl": {
				const [h, s, l] = rgbToHsl(r, g, b);
				return `hsl(${h}, ${s}%, ${l}%)`;
			}
			case "hsla": {
				const [h, s, l] = rgbToHsl(r, g, b);
				return `hsla(${h}, ${s}%, ${l}%, ${roundAlpha(alpha)})`;
			}
			default: return toHex({
				r,
				g,
				b
			}, false);
		}
	}
	function normalizeColor(input, format = "hex") {
		const parsed = parseColor(input);
		return parsed ? formatColor(parsed, format) : null;
	}
	//#endregion
	//#region resources/js/utils/datetime.js
	function padDatePart(n) {
		return String(n).padStart(2, "0");
	}
	function isoOf(date) {
		return `${date.getFullYear()}-${padDatePart(date.getMonth() + 1)}-${padDatePart(date.getDate())}`;
	}
	function parseIso(iso) {
		if (!iso) return null;
		const [y, m, d] = iso.split("-").map(Number);
		return new Date(y, m - 1, d);
	}
	function startOfMonth(date) {
		return new Date(date.getFullYear(), date.getMonth(), 1);
	}
	function addMonths(date, n) {
		return new Date(date.getFullYear(), date.getMonth() + n, 1);
	}
	function endOfMonth(date) {
		return new Date(date.getFullYear(), date.getMonth() + 1, 0);
	}
	function startOfWeek(date, startDay = 0) {
		const offset = (date.getDay() - startDay + 7) % 7;
		return addDays(isoOf(date), -offset);
	}
	function addDays(iso, n) {
		const date = parseIso(iso);
		date.setDate(date.getDate() + n);
		return isoOf(date);
	}
	function sameMonth(a, b) {
		return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth();
	}
	function diffDays(isoA, isoB) {
		return Math.round((parseIso(isoB) - parseIso(isoA)) / 864e5);
	}
	function isoWeekNumber(date) {
		const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
		const dayNum = d.getUTCDay() || 7;
		d.setUTCDate(d.getUTCDate() + 4 - dayNum);
		const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
		return Math.ceil(((d - yearStart) / 864e5 + 1) / 7);
	}
	function resolveLocaleFirstDay(locale) {
		try {
			const info = new Intl.Locale(locale).weekInfo ?? new Intl.Locale(locale).getWeekInfo?.();
			if (info?.firstDay) return info.firstDay % 7;
		} catch {}
		return 0;
	}
	function localeDateOrder(locale) {
		try {
			const order = new Intl.DateTimeFormat(locale, {
				year: "numeric",
				month: "2-digit",
				day: "2-digit"
			}).formatToParts(new Date(2e3, 0, 2)).filter((part) => [
				"day",
				"month",
				"year"
			].includes(part.type)).map((part) => part.type);
			if (order.length === 3) return order;
		} catch {}
		return [
			"month",
			"day",
			"year"
		];
	}
	function formatEditable(iso, locale) {
		const date = parseIso(iso);
		if (!date) return "";
		return new Intl.DateTimeFormat(locale, {
			year: "numeric",
			month: "2-digit",
			day: "2-digit"
		}).format(date);
	}
	function parseTypedDate(text, locale) {
		if (!text) return null;
		const digits = String(text).match(/\d+/g);
		if (!digits || digits.length < 3) return null;
		const order = localeDateOrder(locale);
		const values = {};
		order.forEach((type, index) => {
			values[type] = digits[index];
		});
		if (!values.day || !values.month || !values.year) return null;
		const day = Number(values.day);
		const month = Number(values.month);
		let year = Number(values.year);
		if (values.year.length === 2) year += year < 70 ? 2e3 : 1900;
		const date = new Date(year, month - 1, day);
		if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) return null;
		return isoOf(date);
	}
	function toMinutes(hhmm) {
		const [h, m] = hhmm.split(":").map(Number);
		return h * 60 + m;
	}
	function parseTimeToken(token) {
		const match = /^(\d{1,2}):(\d{2})$/.exec(String(token).trim());
		if (!match) return null;
		const h = Number(match[1]);
		const m = Number(match[2]);
		if (h > 23 || m > 59) return null;
		return `${padDatePart(h)}:${padDatePart(m)}`;
	}
	//#endregion
	//#region resources/js/utils/direction.js
	function isRtl(el = document.documentElement) {
		return el.dir === "rtl" || getComputedStyle(el).direction === "rtl";
	}
	//#endregion
	//#region resources/js/utils/fetch.js
	async function fetchWithRetry(fn, retries = 2) {
		try {
			return await fn();
		} catch (e) {
			if (retries <= 0 || e.name === "AbortError" || e.name === "NotFoundError") throw e;
			return fetchWithRetry(fn, retries - 1);
		}
	}
	//#endregion
	//#region resources/js/utils/file.js
	function formatBytes(bytes, decimals = 1) {
		if (!bytes) return "0 B";
		const units = [
			"B",
			"KB",
			"MB",
			"GB",
			"TB"
		];
		const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
		const value = bytes / Math.pow(1024, exponent);
		return `${exponent === 0 ? value : value.toFixed(decimals)} ${units[exponent]}`;
	}
	function detectFileType(type, name) {
		if (type.startsWith("image/")) return "image";
		if (type.startsWith("video/")) return "video";
		if (type.startsWith("audio/")) return "audio";
		switch (name.split(".").pop()?.toLowerCase() ?? "") {
			case "jpg":
			case "jpeg":
			case "png":
			case "gif": return "image";
			case "mp4": return "video";
			case "mp3": return "audio";
			case "pdf": return "pdf";
			case "doc":
			case "docx": return "doc";
			case "xls":
			case "xlsx": return "xls";
			case "ppt":
			case "pptx": return "ppt";
			case "rar":
			case "zip":
			case "7z": return "archive";
			case "txt":
			case "md": return "text";
			case "csv": return "csv";
			case "json":
			case "js":
			case "ts":
			case "html":
			case "css": return "code";
			default: return "unknown";
		}
	}
	//#endregion
	//#region resources/js/utils/string.js
	function parseCommaList(value) {
		if (!value) return [];
		if (Array.isArray(value)) return value.filter(Boolean);
		return String(value).split(",").map((v) => v.trim()).filter(Boolean);
	}
	function dataKey(name, value) {
		return value ? `[data-tallkit-${name}="${value}"]` : `[data-tallkit-${name}]`;
	}
	function escapeHtml(str) {
		if (str == null) return str;
		return str.replace(/[&<>"']/g, (char) => ({
			"&": "&amp;",
			"<": "&lt;",
			">": "&gt;",
			"\"": "&quot;",
			"'": "&#39;"
		})[char]);
	}
	function generateId(prefix, name, suffix) {
		return slug([
			"tallkit",
			prefix,
			name ?? Math.random().toString(36).slice(2, 9),
			suffix
		].filter(Boolean).join("-")) ?? "";
	}
	function slug(str) {
		return normalize(str, {
			replaceAccents: true,
			removeSpaces: true,
			replaceSpaces: "-",
			lowercase: true,
			mode: "alphanumeric"
		});
	}
	function normalize(str, options) {
		if (!options || !str) return str;
		const opts = {
			replaceAccents: false,
			removeSpaces: false,
			lowercase: false,
			uppercase: false,
			mode: void 0,
			...options
		};
		if (opts?.replaceAccents) str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
		switch (opts.mode) {
			case "alpha":
				str = str.replace(/[^a-z\s-]/gi, "");
				break;
			case "alphanumeric":
				str = str.replace(/[^a-z0-9\s-]/gi, "");
				break;
			case "numeric": str = str.replace(/[^0-9\s-]/g, "");
		}
		if (opts?.removeSpaces) str = str.replace(/\s+/g, " ").trim();
		if (opts?.replaceSpaces) str = str.replace(/\s+/g, opts.replaceSpaces).trim();
		if (opts.uppercase && !opts.lowercase) str = str.toUpperCase();
		else if (opts.lowercase && !opts.uppercase) str = str.toLowerCase();
		return str;
	}
	//#endregion
	//#region resources/js/utils/field.js
	function setFieldValue(el, value) {
		if (!el) return;
		el.value = value?.toString() ?? "";
		el.dispatchEvent(new Event("input", { bubbles: true }));
		el.dispatchEvent(new Event("change", { bubbles: true }));
	}
	function setFieldChecked(el, checked) {
		if (!el || el.checked === checked) return;
		el.checked = checked;
		el.dispatchEvent(new Event("input", { bubbles: true }));
		el.dispatchEvent(new Event("change", { bubbles: true }));
	}
	function findInField(el, childKey, ancestorKey = "field") {
		return el?.closest(dataKey(ancestorKey))?.querySelector(dataKey(childKey)) ?? null;
	}
	function findFieldInput(el) {
		return findInField(el, "input", "field-control");
	}
	function allChecked(items, getChecked) {
		return items.length > 0 && items.every(getChecked);
	}
	//#endregion
	//#region resources/js/utils/livewire.js
	function hasLivewire() {
		return !!window.Livewire;
	}
	function onLivewireCommit(handler) {
		const off = window.Livewire?.hook("commit", handler);
		return typeof off === "function" ? off : () => {};
	}
	//#endregion
	//#region resources/js/utils/model.js
	function getWireModelInfo(element) {
		if (!element) return null;
		for (const attr of element.attributes) if (attr.name.startsWith("wire:model")) {
			const modifier = attr.name.includes(".") ? attr.name.split(".").slice(1).join(".") : "";
			return {
				name: attr.value,
				modifier
			};
		}
		return null;
	}
	//#endregion
	//#region resources/js/utils/timer.js
	function timeout(callback, milliseconds, defaultMilliseconds = 500) {
		const ms = !milliseconds || isNaN(parseInt(milliseconds.toString())) ? defaultMilliseconds : parseInt(milliseconds.toString());
		return setTimeout(callback, ms);
	}
	function debounce(callback, delay = 300) {
		let timeout = void 0;
		const debounced = (...args) => {
			clearTimeout(timeout);
			timeout = setTimeout(() => callback(...args), delay);
		};
		debounced.cancel = () => clearTimeout(timeout);
		return debounced;
	}
	//#endregion
	//#region resources/js/components/address-form.js
	var address_form_exports = /* @__PURE__ */ __exportAll({ addressForm: () => addressForm });
	function addressForm(options = {}) {
		const _cache = cache("zipcode", options);
		return {
			abortController: null,
			$els: {},
			init() {
				this.$els = {
					loading: this.$root.querySelector(dataKey("loading")),
					zipcode: this.$root.querySelector(dataKey("address-form-zipcode")),
					address: this.$root.querySelector(dataKey("address-form-address")),
					number: this.$root.querySelector(dataKey("address-form-number")),
					complement: this.$root.querySelector(dataKey("address-form-complement")),
					neighborhood: this.$root.querySelector(dataKey("address-form-neighborhood")),
					city: this.$root.querySelector(dataKey("address-form-city")),
					state: this.$root.querySelector(dataKey("address-form-state"))
				};
				const debouncedSearch = debounce(this.search.bind(this));
				bind(this.$els.zipcode, { ["@input"]() {
					debouncedSearch(this.$el.value);
				} });
			},
			setLoading(state) {
				this.$els.loading?.classList.toggle("hidden", !state);
				[
					"address",
					"neighborhood",
					"city",
					"state"
				].map((k) => this.$els[k]).filter(Boolean).forEach((el) => el.disabled = state);
			},
			resolveState(data) {
				const el = this.$els.state;
				if (!el) return "";
				const value = data.estado ?? data.uf;
				if (el.tagName.toLowerCase() === "input") return value ?? "";
				return value != null && Array.from(el.options ?? []).some((option) => option.value === value) ? value : data.uf ?? "";
			},
			normalizeZipcode(value) {
				return value.replace(/\D/g, "");
			},
			async viaCep(zipcode, signal) {
				const data = await (await fetch(`https://viacep.com.br/ws/${zipcode}/json/`, { signal })).json();
				if (data.erro) {
					const error = /* @__PURE__ */ new Error("ViaCEP not found");
					error.name = "NotFoundError";
					throw error;
				}
				return data;
			},
			async brasilApi(zipcode, signal) {
				const res = await fetch(`https://brasilapi.com.br/api/cep/v1/${zipcode}`, { signal });
				if (!res.ok) {
					const error = /* @__PURE__ */ new Error("BrasilAPI error");
					if (res.status === 404) error.name = "NotFoundError";
					throw error;
				}
				const data = await res.json();
				return {
					logradouro: data.street,
					bairro: data.neighborhood,
					localidade: data.city,
					uf: data.state
				};
			},
			async resolveAddress(zipcode, signal) {
				const providers = [this.viaCep.bind(this), this.brasilApi.bind(this)];
				for (const provider of providers) try {
					return await fetchWithRetry(() => provider(zipcode, signal));
				} catch (e) {
					if (e.name === "AbortError") throw e;
				}
				throw new Error("All providers failed");
			},
			fill(data) {
				setFieldValue(this.$els.address, data.logradouro);
				setFieldValue(this.$els.neighborhood, data.bairro);
				setFieldValue(this.$els.city, data.localidade);
				setFieldValue(this.$els.state, this.resolveState(data));
				this.$els.number?.focus();
			},
			async search(value) {
				const zipcode = this.normalizeZipcode(value);
				this.abortController?.abort();
				if (zipcode.length !== 8) return;
				const controller = new AbortController();
				this.abortController = controller;
				const { signal } = controller;
				const cached = _cache.get(zipcode);
				if (cached) {
					this.setLoading(true);
					await new Promise((r) => setTimeout(r, 120));
					if (signal.aborted) return;
					this.fill(cached);
					this.$dispatch("loaded", {
						zipcode,
						data: cached,
						cached: true
					});
					this.setLoading(false);
					return;
				}
				this.setLoading(true);
				this.$dispatch("loading", { zipcode });
				try {
					const data = await this.resolveAddress(zipcode, signal);
					if (signal.aborted) return;
					_cache.set(zipcode, data);
					this.fill(data);
					this.$dispatch("loaded", {
						zipcode,
						data,
						cached: false
					});
				} catch (e) {
					if (e.name === "AbortError" || signal.aborted) return;
					this.$dispatch("error", {
						zipcode,
						error: e
					});
					this.$els.zipcode?.focus();
				} finally {
					if (!signal.aborted) this.setLoading(false);
				}
			},
			destroy() {
				this.abortController?.abort();
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/dismissible.js
	function dismissible(animation) {
		return {
			cancelDismiss: null,
			isDismissing: false,
			_dismissTimeout: null,
			init() {
				bind(this.$root.querySelectorAll(dataKey("dismissible")), { ["@click.stop"]: () => {
					this.dismiss("manual");
				} });
				bind(this.$root, { ["@dismiss"]: (e) => {
					const detail = e.detail || {};
					this.dismiss(detail.reason || "programmatic");
				} });
			},
			beforeDismiss() {},
			dismiss(reason = "programmatic") {
				if (this.isDismissing) return;
				const event = new CustomEvent("before-dismiss", {
					detail: { reason },
					cancelable: true
				});
				this.$root.dispatchEvent(event);
				if (event.defaultPrevented) return;
				this.isDismissing = true;
				this.beforeDismiss();
				this.cancelDismiss?.();
				this.cancelDismiss = null;
				const onDone = () => {
					this.isDismissing = false;
					this.cancelDismiss = null;
					this.$dispatch("dismissed", { reason });
					if (this.$root.isConnected) this.$root.remove();
				};
				if (animation === "fade") this.cancelDismiss = fadeOut(this.$root, { onDone });
				else if (animation === "collapse") this.cancelDismiss = collapse(this.$root, { onDone });
				else onDone();
				if (this._dismissTimeout) clearTimeout(this._dismissTimeout);
				this._dismissTimeout = setTimeout(() => {
					this.isDismissing = false;
					this._dismissTimeout = null;
				}, Math.max(getTransitionTimeout(this.$root) * 1.5, 500));
			},
			destroy() {
				this.cancelDismiss?.();
				this.cancelDismiss = null;
				this.isDismissing = false;
				if (this._dismissTimeout) {
					clearTimeout(this._dismissTimeout);
					this._dismissTimeout = null;
				}
			}
		};
	}
	//#endregion
	//#region resources/js/components/alert-component.js
	var alert_component_exports = /* @__PURE__ */ __exportAll({ alertComponent: () => alertComponent });
	function alertComponent({ timeout: timeout$1 = 0, pauseOnHover = false } = {}) {
		const _dismissible = dismissible("collapse");
		return {
			..._dismissible,
			timeoutId: null,
			remaining: timeout$1,
			startedAt: 0,
			pauseReasons: /* @__PURE__ */ new Set(),
			progressEl: null,
			visibilityHandler: null,
			state: "idle",
			init() {
				_dismissible.init.call(this);
				this.progressEl = this.$root.querySelector(dataKey("alert-progress"));
				this.startTimer();
				this.initProgress();
				this.visibilityHandler = this.handleVisibility.bind(this);
				document.addEventListener("visibilitychange", this.visibilityHandler);
				bind(this.$root, {
					...pauseOnHover ? {
						["@mouseenter"]: () => this.pause("hover"),
						["@mouseleave"]: () => this.resume("hover")
					} : {},
					["@pause"]: () => this.pause("external"),
					["@resume"]: () => this.resume("external")
				});
			},
			startTimer() {
				if (!timeout$1 || this.remaining <= 0) return;
				this.state = "running";
				this.startedAt = Date.now();
				this.timeoutId = timeout(() => this.dismiss("timeout"), this.remaining, 7e3);
			},
			pause(reason = "manual") {
				this.pauseReasons.add(reason);
				if (this.pauseReasons.size > 1) return;
				if (!this.timeoutId) return;
				const elapsed = Date.now() - this.startedAt;
				this.remaining = Math.max(this.remaining - elapsed, 0);
				clearTimeout(this.timeoutId);
				this.timeoutId = null;
				this.state = "paused";
				this.freezeProgress();
			},
			resume(reason = "manual") {
				this.pauseReasons.delete(reason);
				if (this.pauseReasons.size > 0) return;
				if (this.remaining <= 0) return;
				this.state = "running";
				if (this.progressEl) {
					this.progressEl.style.transitionDuration = "150ms";
					requestAnimationFrame(() => {
						requestAnimationFrame(() => {
							if (!this.progressEl) return;
							this.progressEl.style.transitionTimingFunction = "linear";
							this.progressEl.style.transitionDuration = `${this.remaining}ms`;
							this.applyProgress(0);
						});
					});
				}
				this.startTimer();
			},
			handleVisibility() {
				if (document.hidden) this.pause("visibility");
				else this.resume("visibility");
			},
			initProgress() {
				if (!this.progressEl || !this.remaining) return;
				this.progressEl.style.transitionTimingFunction = "linear";
				this.applyProgress(100);
				requestAnimationFrame(() => {
					if (!this.progressEl) return;
					this.progressEl.style.transitionDuration = `${this.remaining}ms`;
					this.applyProgress(0);
				});
			},
			applyProgress(percent) {
				if (!this.progressEl) return;
				this.progressEl.style.backgroundSize = `${percent}% 100%`;
			},
			freezeProgress() {
				if (!this.progressEl) return;
				const size = getComputedStyle(this.progressEl).backgroundSize;
				this.progressEl.style.transitionDuration = "0ms";
				this.progressEl.style.backgroundSize = size;
			},
			beforeDismiss() {
				this.state = "dismissing";
				if (this.timeoutId) {
					clearTimeout(this.timeoutId);
					this.timeoutId = null;
				}
			},
			destroy() {
				if (this.timeoutId) {
					clearTimeout(this.timeoutId);
					this.timeoutId = null;
				}
				if (this.visibilityHandler) {
					document.removeEventListener("visibilitychange", this.visibilityHandler);
					this.visibilityHandler = null;
				}
				_dismissible.destroy.call(this);
				this.pauseReasons.clear();
				this.state = "idle";
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/data-options.js
	function dataOptions() {
		return { getDataOptions(el = this.$el) {
			return window.Alpine.evaluate(el, el.getAttribute("data-options") || "{}");
		} };
	}
	//#endregion
	//#region resources/js/components/loadable.js
	var loadable_exports = /* @__PURE__ */ __exportAll({ loadable: () => loadable });
	function loadable() {
		return {
			empty: null,
			loaded: null,
			error: null,
			_loadToken: 0,
			_pendingLoad: null,
			async load(cb, silent = false) {
				if (!silent && !this.$el.hasAttribute("data-silent")) this.start();
				const token = this._loadToken;
				try {
					const result = await cb();
					this.complete(0, token);
					if (typeof result === "function") this.$nextTick(result);
				} catch (e) {
					this.fail(e, 0, token);
				}
			},
			reset() {
				this.empty = null;
				this.loaded = null;
				this.error = null;
			},
			clear() {
				this.reset();
				this.empty = true;
			},
			start() {
				this._loadToken++;
				this._clearPendingLoad();
				this.reset();
				this.loaded = false;
				this.$dispatch("started");
			},
			complete(milliseconds = 0, token) {
				token ??= this._loadToken;
				this._clearPendingLoad();
				this._pendingLoad = setTimeout(() => {
					this._pendingLoad = null;
					if (token !== this._loadToken) return;
					this.reset();
					this.loaded = true;
					this.$dispatch("completed");
				}, milliseconds);
			},
			fail(error, milliseconds = 0, token) {
				token ??= this._loadToken;
				this._clearPendingLoad();
				this._pendingLoad = setTimeout(() => {
					this._pendingLoad = null;
					if (token !== this._loadToken) return;
					this.reset();
					this.error = error;
					this.$dispatch("failed");
				}, milliseconds);
			},
			_clearPendingLoad() {
				if (this._pendingLoad) {
					clearTimeout(this._pendingLoad);
					this._pendingLoad = null;
				}
			},
			destroy() {
				this._clearPendingLoad();
			},
			startAndComplete(completeOnNextTick = false) {
				this.start();
				if (completeOnNextTick) this.$nextTick(() => this.complete());
			},
			isEmpty() {
				return this.empty === true;
			},
			isLoading() {
				return this.loaded === false;
			},
			isCompleted() {
				return this.loaded === true;
			},
			isError() {
				return this.error !== null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/apexcharts.js
	var apexcharts_exports = /* @__PURE__ */ __exportAll({ apexcharts: () => apexcharts });
	function apexcharts() {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			chart: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.ApexCharts, "https://cdn.jsdelivr.net/npm/apexcharts@5"));
			},
			render(options = {}) {
				try {
					const merged = {
						...options,
						...this.getDataOptions(this.$refs.target)
					};
					if (this.chart) this.chart.updateOptions(merged);
					else {
						this.chart = new window.ApexCharts(this.$refs.target, merged);
						this.chart.render();
					}
					this.$dispatch("rendered", { chart: this.chart });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.chart?.destroy();
				this.chart = null;
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/sticky.js
	function sticky() {
		return {
			_onResize: null,
			_resizeObserver: null,
			init() {
				this.updateOffset();
				this._onResize = () => this.updateOffset();
				window.addEventListener("resize", this._onResize);
				this._resizeObserver = new ResizeObserver(() => this.updateOffset());
				this._resizeObserver.observe(document.body);
			},
			updateOffset() {
				const top = this.$el.offsetTop;
				this.$el.style.position = "sticky";
				this.$el.style.top = `${top}px`;
				this.$el.style.maxHeight = `calc(100dvh - ${top}px)`;
			},
			destroy() {
				window.removeEventListener("resize", this._onResize);
				this._resizeObserver?.disconnect();
			}
		};
	}
	//#endregion
	//#region resources/js/components/aside.js
	var aside_exports = /* @__PURE__ */ __exportAll({ aside: () => aside });
	function aside() {
		return { ...sticky() };
	}
	//#endregion
	//#region resources/js/mixins/toggleable.js
	function toggleable() {
		return {
			opened: false,
			lastOpened: null,
			init(opened = false) {
				if (Number.isInteger(opened)) return timeout(() => this.open(), opened);
				this.opened = Boolean(opened);
			},
			open(storage = true) {
				this.opened = true;
				if (storage) this.lastOpened = this.opened;
			},
			close(storage = true) {
				this.opened = false;
				if (storage) this.lastOpened = this.opened;
			},
			toggle(storage = true) {
				if (this.isOpened()) this.close(storage);
				else this.open(storage);
			},
			isOpened() {
				return this.opened === true;
			},
			isClosed() {
				return this.opened === false;
			}
		};
	}
	//#endregion
	//#region resources/js/components/popover.js
	var popover_exports = /* @__PURE__ */ __exportAll({ popover: () => popover });
	function popover({ mode = "hover", position = "bottom", align = "end", matchTriggerWidth = false } = {}) {
		const _toggleable = toggleable();
		return {
			..._toggleable,
			popoverElement: null,
			trigger: null,
			ariaTrigger: null,
			resizeObserver: null,
			mutationObserver: null,
			livewireCommitCleanup: null,
			_rAF: null,
			_cancelPendingClose: null,
			mouseX: 0,
			mouseY: 0,
			_hasPointerPosition: false,
			init() {
				_toggleable.init.call(this);
				this.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
				if (!this.popoverElement) return;
				this.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root;
				if (this.trigger?.matches(dataKey("tooltip"))) this.trigger = this.trigger.firstElementChild;
				this.ariaTrigger = this.trigger?.matches(dataKey("control")) ? this.trigger : this.trigger?.querySelector(dataKey("control")) ?? this.trigger;
				const role = this.popoverElement.getAttribute("role");
				if (!this.ariaTrigger.hasAttribute("aria-haspopup") && role !== "tooltip") this.ariaTrigger.setAttribute("aria-haspopup", role === "listbox" || role === "dialog" ? role : "true");
				if (!this.ariaTrigger.hasAttribute("aria-expanded")) this.ariaTrigger.setAttribute("aria-expanded", "false");
				if (!this.popoverElement.id) this.popoverElement.id = generateId("popover");
				if (!this.ariaTrigger.hasAttribute("aria-controls")) this.ariaTrigger.setAttribute("aria-controls", this.popoverElement.id);
				if (role === "tooltip") {
					const ids = new Set((this.ariaTrigger.getAttribute("aria-describedby") ?? "").split(" ").filter(Boolean));
					ids.add(this.popoverElement.id);
					this.ariaTrigger.setAttribute("aria-describedby", Array.from(ids).join(" "));
				}
				this.popoverElement.addEventListener("beforetoggle", (e) => {
					queueMicrotask(() => {
						if (e.newState === "open") this.onOpen();
						else this.onClose();
					});
				});
				this.livewireCommitCleanup = onLivewireCommit(({ succeed }) => {
					succeed(() => {
						if (!this.popoverElement?.matches(":popover-open")) return;
						if (!this.$root?.isConnected) return;
						this.boundSetPosition();
					});
				});
				if (mode !== "manual" && (window.matchMedia("(hover: none)").matches || mode === "dropdown")) bind(this.trigger, {
					["@click"]() {
						this.toggle(!["menu"].includes(role));
					},
					["@click.outside"](e) {
						if ((this.popoverElement.hasAttribute("data-keep-open") || e.target?.hasAttribute("data-keep-open") || e.target?.closest("[data-keep-open]")) && this.popoverElement.contains(e.target)) return;
						this.close();
					}
				});
				else if (mode === "hover") bind(this.trigger, {
					["@mouseenter"]() {
						this.open(false);
					},
					["@mouseleave"]() {
						this.close();
					},
					["@focus"]() {
						this.open();
					},
					["@blur"]() {
						this.close();
					}
				});
				else if (mode === "context") {
					if (!this.trigger.hasAttribute("tabindex") && ![
						"A",
						"BUTTON",
						"INPUT",
						"SELECT",
						"TEXTAREA"
					].includes(this.trigger.tagName)) this.trigger.setAttribute("tabindex", "0");
					bind(this.trigger, {
						["@contextmenu.prevent"](event) {
							this.close();
							this.mouseX = event.clientX;
							this.mouseY = event.clientY;
							this._hasPointerPosition = true;
							this.open();
						},
						["@keydown"](event) {
							if (event.key !== "ContextMenu" && !(event.shiftKey && event.key === "F10")) return;
							event.preventDefault();
							this.close();
							this._hasPointerPosition = false;
							this.open();
						}
					});
					bind(this.popoverElement, { ["@click.outside"]() {
						this.close();
					} });
				}
				bind(this.trigger, {
					["@open"]() {
						this.open();
					},
					["@close"]() {
						this.close();
					},
					["@keydown.escape.window"]() {
						this.close();
					}
				});
			},
			destroy() {
				this.onClose();
				this.livewireCommitCleanup?.();
			},
			open(focus = true) {
				requestAnimationFrame(() => {
					if (!this.popoverElement?.isConnected) return;
					if (this._cancelPendingClose) {
						this._cancelPendingClose();
						this._cancelPendingClose = null;
						this.onOpen();
					} else {
						if (this.popoverElement.matches(":popover-open")) return;
						this.popoverElement.showPopover();
					}
					if (focus) (this.popoverElement.querySelector("[role=menuitem], [role=option], [role=tab]") ?? this.popoverElement).focus();
				});
			},
			close() {
				requestAnimationFrame(() => {
					if (!this.popoverElement?.isConnected) return;
					if (!this.popoverElement.matches(":popover-open")) return;
					if (this._cancelPendingClose) return;
					this.onClose();
					const target = this.popoverElement.firstElementChild ?? this.popoverElement;
					let fallback;
					const hide = () => {
						target.removeEventListener("transitionend", hide);
						clearTimeout(fallback);
						this._cancelPendingClose = null;
						if (this.popoverElement?.isConnected && this.popoverElement.matches(":popover-open")) this.popoverElement.hidePopover();
					};
					this._cancelPendingClose = () => {
						target.removeEventListener("transitionend", hide);
						clearTimeout(fallback);
						this._cancelPendingClose = null;
					};
					requestAnimationFrame(() => {
						const timeout = getTransitionTimeout(target);
						if (timeout === 0) {
							hide();
							return;
						}
						target.addEventListener("transitionend", hide, { once: true });
						fallback = setTimeout(hide, timeout + 50);
					});
				});
			},
			onOpen() {
				_toggleable.open.call(this);
				this.ariaTrigger.setAttribute("aria-expanded", "true");
				this._onScroll ??= () => this.boundSetPosition();
				this._onResize ??= () => this.boundSetPosition();
				window.addEventListener("scroll", this._onScroll, true);
				window.addEventListener("resize", this._onResize, true);
				this.resizeObserver = new ResizeObserver(() => this.boundSetPosition());
				this.resizeObserver.observe(this.trigger);
				this.resizeObserver.observe(this.popoverElement);
				this.mutationObserver = new MutationObserver(() => this.boundSetPosition());
				this.mutationObserver.observe(this.trigger, { childList: true });
				this.mutationObserver.observe(this.popoverElement, { childList: true });
				this.setPosition();
			},
			onClose() {
				if (this.isClosed()) return;
				_toggleable.close.call(this);
				this.ariaTrigger.setAttribute("aria-expanded", "false");
				window.removeEventListener("scroll", this._onScroll, true);
				window.removeEventListener("resize", this._onResize, true);
				this.resizeObserver?.disconnect();
				this.resizeObserver = null;
				this.mutationObserver?.disconnect();
				this.mutationObserver = null;
				if (this._rAF) {
					cancelAnimationFrame(this._rAF);
					this._rAF = null;
				}
			},
			setPosition() {
				if (!this.popoverElement?.isConnected) return;
				if (!this.popoverElement.matches(":popover-open")) return;
				if ((mode !== "context" || !this._hasPointerPosition) && !this.trigger?.isConnected) return;
				let triggerRect;
				if (mode === "context" && this._hasPointerPosition) triggerRect = {
					top: this.mouseY,
					bottom: this.mouseY,
					left: this.mouseX,
					right: this.mouseX,
					height: 0,
					width: 0
				};
				else triggerRect = this.trigger.getBoundingClientRect();
				const triggerHeight = triggerRect.height;
				const triggerWidth = triggerRect.width;
				if (matchTriggerWidth) this.popoverElement.style.width = `${triggerWidth}px`;
				const scrollTop = window.scrollY;
				const scrollLeft = window.scrollX;
				const tooltipHeight = this.popoverElement.offsetHeight;
				const tooltipWidth = this.popoverElement.offsetWidth;
				const isRTL = isRtl(this.trigger);
				const margin = 4;
				const resolveAlign = (align) => {
					if (align === "start") return isRTL ? "right" : "left";
					if (align === "end") return isRTL ? "left" : "right";
					return align;
				};
				const getCenterOffset = (pos, align) => {
					align = resolveAlign(align);
					if (align === "left") return 0;
					if (align === "right") return pos === "left" || pos === "right" ? triggerHeight - tooltipHeight : triggerWidth - tooltipWidth;
					return pos === "left" || pos === "right" ? (triggerHeight - tooltipHeight) / 2 : (triggerWidth - tooltipWidth) / 2;
				};
				const getCoords = (pos, align) => {
					const center = getCenterOffset(pos, align);
					let top = 0, left = 0;
					switch (pos) {
						case "right":
							left = triggerRect.right + margin + scrollLeft;
							top = triggerRect.top + center + scrollTop;
							break;
						case "left":
							left = triggerRect.left - tooltipWidth - margin + scrollLeft;
							top = triggerRect.top + center + scrollTop;
							break;
						case "bottom":
							top = triggerRect.bottom + margin + scrollTop;
							left = triggerRect.left + center + scrollLeft;
							break;
						case "top":
							top = triggerRect.top - tooltipHeight - margin + scrollTop;
							left = triggerRect.left + center + scrollLeft;
					}
					return {
						top,
						left
					};
				};
				const isVisible = ({ top, left }) => {
					return top >= scrollTop && left >= scrollLeft && top + tooltipHeight <= scrollTop + window.innerHeight && left + tooltipWidth <= scrollLeft + window.innerWidth;
				};
				const positions = [
					"top",
					"bottom",
					"left",
					"right"
				];
				const aligns = [
					"start",
					"left",
					"end",
					"right",
					"center"
				];
				let computedPosition = position || "bottom";
				let computedAlign = align || "end";
				let coords = getCoords(computedPosition, computedAlign);
				if (!isVisible(coords)) {
					let found = false;
					for (const pos of [computedPosition, ...positions.filter((p) => p !== computedPosition)]) {
						for (const al of [computedAlign, ...aligns.filter((a) => a !== computedAlign)]) {
							const testCoords = getCoords(pos, al);
							if (isVisible(testCoords)) {
								computedPosition = pos;
								computedAlign = al;
								coords = testCoords;
								found = true;
								break;
							}
						}
						if (found) break;
					}
				}
				this.popoverElement.style.position = "absolute";
				this.popoverElement.style.inset = "auto";
				this.popoverElement.style.top = `${coords.top}px`;
				this.popoverElement.style.left = `${coords.left}px`;
				this.popoverElement.dataset.position = computedPosition;
				this.popoverElement.dataset.align = computedAlign === "center" ? "center" : resolveAlign(computedAlign);
			},
			boundSetPosition() {
				if (this._rAF) return;
				this._rAF = requestAnimationFrame(() => {
					this.setPosition();
					this._rAF = null;
				});
			}
		};
	}
	//#endregion
	//#region node_modules/.pnpm/fuse.js@7.5.0/node_modules/fuse.js/dist/fuse.mjs
	/**
	* Fuse.js v7.5.0 - Lightweight fuzzy-search (http://fusejs.io)
	*
	* Copyright (c) 2026 Kiro Risk (http://kiro.me)
	* All Rights Reserved. Apache Software License 2.0
	*
	* http://www.apache.org/licenses/LICENSE-2.0
	*/
	function isArray(value) {
		return !Array.isArray ? getTag(value) === "[object Array]" : Array.isArray(value);
	}
	function baseToString(value) {
		if (typeof value == "string") return value;
		if (typeof value === "bigint") return value.toString();
		const result = value + "";
		return result == "0" && 1 / value == -Infinity ? "-0" : result;
	}
	function toString(value) {
		return value == null ? "" : baseToString(value);
	}
	function isString(value) {
		return typeof value === "string";
	}
	function isNumber(value) {
		return typeof value === "number";
	}
	function isBoolean(value) {
		return value === true || value === false || isObjectLike(value) && getTag(value) == "[object Boolean]";
	}
	function isObject(value) {
		return typeof value === "object";
	}
	function isObjectLike(value) {
		return isObject(value) && value !== null;
	}
	function isDefined(value) {
		return value !== void 0 && value !== null;
	}
	function isBlank(value) {
		return !value.trim().length;
	}
	function getTag(value) {
		return value == null ? value === void 0 ? "[object Undefined]" : "[object Null]" : Object.prototype.toString.call(value);
	}
	var INCORRECT_INDEX_TYPE = "Incorrect 'index' type";
	var INVALID_DOC_INDEX = "Invalid doc index: must be a non-negative integer within the bounds of the docs array";
	var LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY = (key) => `Invalid value for key ${key}`;
	var PATTERN_LENGTH_TOO_LARGE = (max) => `Pattern length exceeds max of ${max}.`;
	var MISSING_KEY_PROPERTY = (name) => `Missing ${name} property in key`;
	var INVALID_KEY_WEIGHT_VALUE = (key) => `Property 'weight' in key '${key}' must be a positive integer`;
	var FUSE_MATCH_TOKEN_SEARCH_UNSUPPORTED = "Fuse.match does not support useTokenSearch: token search requires corpus-level statistics (df, fieldCount) that a one-off string comparison does not have. Use new Fuse(...).search(...) instead.";
	var hasOwn = Object.prototype.hasOwnProperty;
	var KeyStore = class {
		constructor(keys) {
			this._keys = [];
			this._keyMap = {};
			let totalWeight = 0;
			keys.forEach((key) => {
				const obj = createKey(key);
				this._keys.push(obj);
				this._keyMap[obj.id] = obj;
				totalWeight += obj.weight;
			});
			this._keys.forEach((key) => {
				key.weight /= totalWeight;
			});
		}
		get(keyId) {
			return this._keyMap[keyId];
		}
		keys() {
			return this._keys;
		}
		toJSON() {
			return JSON.stringify(this._keys);
		}
	};
	function createKey(key) {
		let path = null;
		let id = null;
		let src = null;
		let weight = 1;
		let getFn = null;
		if (isString(key) || isArray(key)) {
			src = key;
			path = createKeyPath(key);
			id = createKeyId(key);
		} else {
			if (!hasOwn.call(key, "name")) throw new Error(MISSING_KEY_PROPERTY("name"));
			const name = key.name;
			src = name;
			if (hasOwn.call(key, "weight") && key.weight !== void 0) {
				weight = key.weight;
				if (weight <= 0) throw new Error(INVALID_KEY_WEIGHT_VALUE(createKeyId(name)));
			}
			path = createKeyPath(name);
			id = createKeyId(name);
			getFn = key.getFn ?? null;
		}
		return {
			path,
			id,
			weight,
			src,
			getFn
		};
	}
	function createKeyPath(key) {
		return isArray(key) ? key : key.split(".");
	}
	function createKeyId(key) {
		return isArray(key) ? key.join(".") : key;
	}
	function get(obj, path) {
		const list = [];
		let arr = false;
		const deepGet = (obj, path, index, arrayIndex) => {
			if (!isDefined(obj)) return;
			if (!path[index]) list.push(arrayIndex !== void 0 ? {
				v: obj,
				i: arrayIndex
			} : obj);
			else {
				const value = obj[path[index]];
				if (!isDefined(value)) return;
				if (index === path.length - 1 && (isString(value) || isNumber(value) || isBoolean(value) || typeof value === "bigint")) list.push(arrayIndex !== void 0 ? {
					v: toString(value),
					i: arrayIndex
				} : toString(value));
				else if (isArray(value)) {
					arr = true;
					for (let i = 0, len = value.length; i < len; i += 1) deepGet(value[i], path, index + 1, i);
				} else if (path.length) deepGet(value, path, index + 1, arrayIndex);
			}
		};
		deepGet(obj, isString(path) ? path.split(".") : path, 0);
		return arr ? list : list[0];
	}
	var MatchOptions = {
		includeMatches: false,
		findAllMatches: false,
		minMatchCharLength: 1
	};
	var BasicOptions = {
		isCaseSensitive: false,
		ignoreDiacritics: false,
		includeScore: false,
		keys: [],
		shouldSort: true,
		sortFn: (a, b) => a.score === b.score ? a.idx < b.idx ? -1 : 1 : a.score < b.score ? -1 : 1
	};
	var FuzzyOptions = {
		location: 0,
		threshold: .6,
		distance: 100
	};
	var AdvancedOptions = {
		useExtendedSearch: false,
		useTokenSearch: false,
		tokenize: void 0,
		tokenMatch: "any",
		getFn: get,
		ignoreLocation: false,
		ignoreFieldNorm: false,
		fieldNormWeight: 1
	};
	var Config = Object.freeze({
		...BasicOptions,
		...MatchOptions,
		...FuzzyOptions,
		...AdvancedOptions
	});
	function isWordSeparator(code) {
		return code >= 9 && code <= 13 || code === 32 || code === 160;
	}
	function norm(weight = 1, mantissa = 3) {
		const cache = /* @__PURE__ */ new Map();
		const m = Math.pow(10, mantissa);
		return {
			get(value) {
				let numTokens = 0;
				let inWord = false;
				for (let i = 0; i < value.length; i++) if (!isWordSeparator(value.charCodeAt(i))) {
					if (!inWord) {
						numTokens++;
						inWord = true;
					}
				} else inWord = false;
				if (numTokens === 0) numTokens = 1;
				if (cache.has(numTokens)) return cache.get(numTokens);
				const n = Math.round(m / Math.pow(numTokens, .5 * weight)) / m;
				cache.set(numTokens, n);
				return n;
			},
			clear() {
				cache.clear();
			}
		};
	}
	var FuseIndex = class {
		constructor({ getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
			this.norm = norm(fieldNormWeight, 3);
			this.getFn = getFn;
			this.isCreated = false;
			this.docs = [];
			this.keys = [];
			this._keysMap = {};
			this.setIndexRecords();
		}
		setSources(docs = []) {
			this.docs = docs;
		}
		setIndexRecords(records = []) {
			this.records = records;
		}
		setKeys(keys = []) {
			this.keys = keys;
			this._keysMap = {};
			keys.forEach((key, idx) => {
				this._keysMap[key.id] = idx;
			});
		}
		create() {
			if (this.isCreated || !this.docs.length) return;
			this.isCreated = true;
			const len = this.docs.length;
			this.records = new Array(len);
			let recordCount = 0;
			if (isString(this.docs[0])) for (let i = 0; i < len; i++) {
				const record = this._createStringRecord(this.docs[i], i);
				if (record) this.records[recordCount++] = record;
			}
			else for (let i = 0; i < len; i++) this.records[recordCount++] = this._createObjectRecord(this.docs[i], i);
			this.records.length = recordCount;
			this.norm.clear();
		}
		add(doc, docIndex) {
			if (!Number.isInteger(docIndex) || docIndex < 0) throw new Error(INVALID_DOC_INDEX);
			if (isString(doc)) {
				const record = this._createStringRecord(doc, docIndex);
				if (record) this.records.push(record);
				return record;
			}
			const record = this._createObjectRecord(doc, docIndex);
			this.records.push(record);
			return record;
		}
		removeAt(idx) {
			if (!Number.isInteger(idx) || idx < 0) throw new Error(INVALID_DOC_INDEX);
			for (let i = 0, len = this.records.length; i < len; i += 1) if (this.records[i].i === idx) {
				this.records.splice(i, 1);
				break;
			}
			for (let i = 0, len = this.records.length; i < len; i += 1) if (this.records[i].i > idx) this.records[i].i -= 1;
		}
		removeAll(indices) {
			const toRemove = /* @__PURE__ */ new Set();
			for (const v of indices) if (Number.isInteger(v) && v >= 0) toRemove.add(v);
			if (toRemove.size === 0) return;
			this.records = this.records.filter((r) => !toRemove.has(r.i));
			const sorted = Array.from(toRemove).sort((a, b) => a - b);
			for (const record of this.records) {
				let lo = 0;
				let hi = sorted.length;
				while (lo < hi) {
					const mid = lo + hi >>> 1;
					if (sorted[mid] < record.i) lo = mid + 1;
					else hi = mid;
				}
				record.i -= lo;
			}
		}
		getValueForItemAtKeyId(item, keyId) {
			return item[this._keysMap[keyId]];
		}
		size() {
			return this.records.length;
		}
		_createStringRecord(doc, docIndex) {
			if (!isDefined(doc) || isBlank(doc)) return null;
			return {
				v: doc,
				i: docIndex,
				n: this.norm.get(doc)
			};
		}
		_createObjectRecord(doc, docIndex) {
			const record = {
				i: docIndex,
				$: {}
			};
			for (let keyIndex = 0, keyLen = this.keys.length; keyIndex < keyLen; keyIndex++) {
				const key = this.keys[keyIndex];
				const value = key.getFn ? key.getFn(doc) : this.getFn(doc, key.path);
				if (!isDefined(value)) continue;
				if (isArray(value)) {
					const subRecords = [];
					for (let i = 0, len = value.length; i < len; i += 1) {
						const item = value[i];
						if (!isDefined(item)) continue;
						if (isString(item)) {
							if (!isBlank(item)) {
								const subRecord = {
									v: item,
									i,
									n: this.norm.get(item)
								};
								subRecords.push(subRecord);
							}
						} else if (isDefined(item.v)) {
							const text = isString(item.v) ? item.v : toString(item.v);
							if (!isBlank(text)) {
								const subRecord = {
									v: text,
									i: item.i,
									n: this.norm.get(text)
								};
								subRecords.push(subRecord);
							}
						}
					}
					record.$[keyIndex] = subRecords;
				} else if (isString(value) && !isBlank(value)) {
					const subRecord = {
						v: value,
						n: this.norm.get(value)
					};
					record.$[keyIndex] = subRecord;
				}
			}
			return record;
		}
		toJSON() {
			return {
				keys: this.keys.map(({ getFn, ...key }) => key),
				records: this.records
			};
		}
	};
	function createIndex(keys, docs, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
		const myIndex = new FuseIndex({
			getFn,
			fieldNormWeight
		});
		myIndex.setKeys(keys.map(createKey));
		myIndex.setSources(docs);
		myIndex.create();
		return myIndex;
	}
	function parseIndex(data, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
		const { keys, records } = data;
		const myIndex = new FuseIndex({
			getFn,
			fieldNormWeight
		});
		myIndex.setKeys(keys);
		myIndex.setIndexRecords(records);
		return myIndex;
	}
	function convertMaskToIndices(matchmask = [], minMatchCharLength = Config.minMatchCharLength) {
		const indices = [];
		let start = -1;
		let end = -1;
		let i = 0;
		for (let len = matchmask.length; i < len; i += 1) {
			const match = matchmask[i];
			if (match && start === -1) start = i;
			else if (!match && start !== -1) {
				end = i - 1;
				if (end - start + 1 >= minMatchCharLength) indices.push([start, end]);
				start = -1;
			}
		}
		if (matchmask[i - 1] && i - start >= minMatchCharLength) indices.push([start, i - 1]);
		return indices;
	}
	function search(text, pattern, patternAlphabet, { location = Config.location, distance = Config.distance, threshold = Config.threshold, findAllMatches = Config.findAllMatches, minMatchCharLength = Config.minMatchCharLength, includeMatches = Config.includeMatches, ignoreLocation = Config.ignoreLocation } = {}) {
		if (pattern.length > 32) throw new Error(PATTERN_LENGTH_TOO_LARGE(32));
		const patternLen = pattern.length;
		const textLen = text.length;
		const expectedLocation = Math.max(0, Math.min(location, textLen));
		let currentThreshold = threshold;
		let bestLocation = expectedLocation;
		const calcScore = (errors, currentLocation) => {
			const accuracy = errors / patternLen;
			if (ignoreLocation) return accuracy;
			const proximity = Math.abs(expectedLocation - currentLocation);
			if (!distance) return proximity ? 1 : accuracy;
			return accuracy + proximity / distance;
		};
		const computeMatches = minMatchCharLength > 1 || includeMatches;
		const matchMask = computeMatches ? Array(textLen) : [];
		let index;
		while ((index = text.indexOf(pattern, bestLocation)) > -1) {
			const score = calcScore(0, index);
			currentThreshold = Math.min(score, currentThreshold);
			bestLocation = index + patternLen;
			if (computeMatches) {
				let i = 0;
				while (i < patternLen) {
					matchMask[index + i] = 1;
					i += 1;
				}
			}
		}
		bestLocation = -1;
		let lastBitArr = [];
		let finalScore = 1;
		let bestErrors = 0;
		let binMax = patternLen + textLen;
		const mask = 1 << patternLen - 1;
		for (let i = 0; i < patternLen; i += 1) {
			let binMin = 0;
			let binMid = binMax;
			while (binMin < binMid) {
				if (calcScore(i, expectedLocation + binMid) <= currentThreshold) binMin = binMid;
				else binMax = binMid;
				binMid = Math.floor((binMax - binMin) / 2 + binMin);
			}
			binMax = binMid;
			let start = Math.max(1, expectedLocation - binMid + 1);
			const finish = findAllMatches ? textLen : Math.min(expectedLocation + binMid, textLen) + patternLen;
			const bitArr = Array(finish + 2);
			bitArr[finish + 1] = (1 << i) - 1;
			for (let j = finish; j >= start; j -= 1) {
				const currentLocation = j - 1;
				const charMatch = patternAlphabet[text[currentLocation]];
				bitArr[j] = (bitArr[j + 1] << 1 | 1) & charMatch;
				if (i) bitArr[j] |= (lastBitArr[j + 1] | lastBitArr[j]) << 1 | 1 | lastBitArr[j + 1];
				if (bitArr[j] & mask) {
					finalScore = calcScore(i, currentLocation);
					if (finalScore <= currentThreshold) {
						currentThreshold = finalScore;
						bestLocation = currentLocation;
						bestErrors = i;
						if (bestLocation <= expectedLocation) break;
						start = Math.max(1, 2 * expectedLocation - bestLocation);
					}
				}
			}
			if (calcScore(i + 1, expectedLocation) > currentThreshold) break;
			lastBitArr = bitArr;
		}
		if (computeMatches && bestLocation >= 0) {
			const matchEnd = Math.min(textLen - 1, bestLocation + patternLen - 1 + bestErrors);
			for (let k = bestLocation; k <= matchEnd; k += 1) if (patternAlphabet[text[k]]) matchMask[k] = 1;
		}
		const result = {
			isMatch: bestLocation >= 0,
			score: Math.max(.001, finalScore)
		};
		if (computeMatches) {
			const indices = convertMaskToIndices(matchMask, minMatchCharLength);
			if (!indices.length) result.isMatch = false;
			else if (includeMatches) result.indices = indices;
		}
		return result;
	}
	function createPatternAlphabet(pattern) {
		const mask = {};
		for (let i = 0, len = pattern.length; i < len; i += 1) {
			const char = pattern.charAt(i);
			mask[char] = (mask[char] || 0) | 1 << len - i - 1;
		}
		return mask;
	}
	function mergeIndices(indices) {
		if (indices.length <= 1) return indices;
		indices.sort((a, b) => a[0] - b[0] || a[1] - b[1]);
		const merged = [indices[0]];
		for (let i = 1, len = indices.length; i < len; i += 1) {
			const last = merged[merged.length - 1];
			const curr = indices[i];
			if (curr[0] <= last[1] + 1) last[1] = Math.max(last[1], curr[1]);
			else merged.push(curr);
		}
		return merged;
	}
	var NON_DECOMPOSABLE_MAP = {
		"ł": "l",
		"Ł": "L",
		"đ": "d",
		"Đ": "D",
		"ø": "o",
		"Ø": "O",
		"ħ": "h",
		"Ħ": "H",
		"ŧ": "t",
		"Ŧ": "T",
		"ı": "i",
		"ß": "ss"
	};
	var NON_DECOMPOSABLE_RE = new RegExp("[" + Object.keys(NON_DECOMPOSABLE_MAP).join("") + "]", "g");
	var stripDiacritics = typeof String.prototype.normalize === "function" ? (str) => str.normalize("NFD").replace(/[\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u07FD\u0816-\u0819\u081B-\u0823\u0825-\u0827\u0829-\u082D\u0859-\u085B\u08D3-\u08E1\u08E3-\u0903\u093A-\u093C\u093E-\u094F\u0951-\u0957\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u09FE\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0AFA-\u0AFF\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C00-\u0C04\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C81-\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D00-\u0D03\u0D3B\u0D3C\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F8D-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u109A-\u109D\u135D-\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B4-\u17D3\u17DD\u180B-\u180D\u1885\u1886\u18A9\u1920-\u192B\u1930-\u193B\u1A17-\u1A1B\u1A55-\u1A5E\u1A60-\u1A7C\u1A7F\u1AB0-\u1ABE\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAD\u1BE6-\u1BF3\u1C24-\u1C37\u1CD0-\u1CD2\u1CD4-\u1CE8\u1CED\u1CF2-\u1CF4\u1CF7-\u1CF9\u1DC0-\u1DF9\u1DFB-\u1DFF\u20D0-\u20F0\u2CEF-\u2CF1\u2D7F\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA674-\uA67D\uA69E\uA69F\uA6F0\uA6F1\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C5\uA8E0-\uA8F1\uA8FF\uA926-\uA92D\uA947-\uA953\uA980-\uA983\uA9B3-\uA9C0\uA9E5\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uAA7B-\uAA7D\uAAB0\uAAB2-\uAAB4\uAAB7\uAAB8\uAABE\uAABF\uAAC1\uAAEB-\uAAEF\uAAF5\uAAF6\uABE3-\uABEA\uABEC\uABED\uFB1E\uFE00-\uFE0F\uFE20-\uFE2F]/g, "").replace(NON_DECOMPOSABLE_RE, (ch) => NON_DECOMPOSABLE_MAP[ch]) : (str) => str;
	var BitapSearch = class {
		constructor(pattern, { location = Config.location, threshold = Config.threshold, distance = Config.distance, includeMatches = Config.includeMatches, findAllMatches = Config.findAllMatches, minMatchCharLength = Config.minMatchCharLength, isCaseSensitive = Config.isCaseSensitive, ignoreDiacritics = Config.ignoreDiacritics, ignoreLocation = Config.ignoreLocation } = {}) {
			this.options = {
				location,
				threshold,
				distance,
				includeMatches,
				findAllMatches,
				minMatchCharLength,
				isCaseSensitive,
				ignoreDiacritics,
				ignoreLocation
			};
			pattern = isCaseSensitive ? pattern : pattern.toLowerCase();
			pattern = ignoreDiacritics ? stripDiacritics(pattern) : pattern;
			this.pattern = pattern;
			this.chunks = [];
			if (!this.pattern.length) return;
			const addChunk = (pattern, startIndex) => {
				this.chunks.push({
					pattern,
					alphabet: createPatternAlphabet(pattern),
					startIndex
				});
			};
			const len = this.pattern.length;
			if (len > 32) {
				let i = 0;
				const remainder = len % 32;
				const end = len - remainder;
				while (i < end) {
					addChunk(this.pattern.substr(i, 32), i);
					i += 32;
				}
				if (remainder) {
					const startIndex = len - 32;
					addChunk(this.pattern.substr(startIndex), startIndex);
				}
			} else addChunk(this.pattern, 0);
		}
		searchIn(text) {
			const { isCaseSensitive, ignoreDiacritics, includeMatches } = this.options;
			text = isCaseSensitive ? text : text.toLowerCase();
			text = ignoreDiacritics ? stripDiacritics(text) : text;
			if (this.pattern === text) {
				if (text.length < this.options.minMatchCharLength) return {
					isMatch: false,
					score: 1
				};
				const result = {
					isMatch: true,
					score: 0
				};
				if (includeMatches) result.indices = [[0, text.length - 1]];
				return result;
			}
			const { location, distance, threshold, findAllMatches, minMatchCharLength, ignoreLocation } = this.options;
			const allIndices = [];
			let totalScore = 0;
			let hasMatches = false;
			this.chunks.forEach(({ pattern, alphabet, startIndex }) => {
				const { isMatch, score, indices } = search(text, pattern, alphabet, {
					location: location + startIndex,
					distance,
					threshold,
					findAllMatches,
					minMatchCharLength,
					includeMatches,
					ignoreLocation
				});
				if (isMatch) hasMatches = true;
				totalScore += score;
				if (isMatch && indices) allIndices.push(...indices);
			});
			const result = {
				isMatch: hasMatches,
				score: hasMatches ? totalScore / this.chunks.length : 1
			};
			if (hasMatches && includeMatches) result.indices = mergeIndices(allIndices);
			return result;
		}
	};
	var MULTI_MATCH_TYPES = /* @__PURE__ */ new Set(["fuzzy", "include"]);
	function isInverse(type) {
		return type.startsWith("inverse");
	}
	var matchers = [
		{
			type: "exact",
			multiRegex: /^="(.*)"$/,
			singleRegex: /^=(.*)$/,
			create: (pattern) => ({
				type: "exact",
				search(text) {
					const isMatch = text === pattern;
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [0, pattern.length - 1]
					};
				}
			})
		},
		{
			type: "include",
			multiRegex: /^'"(.*)"$/,
			singleRegex: /^'(.*)$/,
			create: (pattern) => ({
				type: "include",
				search(text) {
					let location = 0;
					let index;
					const indices = [];
					const patternLen = pattern.length;
					while ((index = text.indexOf(pattern, location)) > -1) {
						location = index + patternLen;
						indices.push([index, location - 1]);
					}
					const isMatch = !!indices.length;
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices
					};
				}
			})
		},
		{
			type: "prefix-exact",
			multiRegex: /^\^"(.*)"$/,
			singleRegex: /^\^(.*)$/,
			create: (pattern) => ({
				type: "prefix-exact",
				search(text) {
					const isMatch = text.startsWith(pattern);
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [0, pattern.length - 1]
					};
				}
			})
		},
		{
			type: "inverse-prefix-exact",
			multiRegex: /^!\^"(.*)"$/,
			singleRegex: /^!\^(.*)$/,
			create: (pattern) => ({
				type: "inverse-prefix-exact",
				search(text) {
					const isMatch = !text.startsWith(pattern);
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [0, text.length - 1]
					};
				}
			})
		},
		{
			type: "inverse-suffix-exact",
			multiRegex: /^!"(.*)"\$$/,
			singleRegex: /^!(.*)\$$/,
			create: (pattern) => ({
				type: "inverse-suffix-exact",
				search(text) {
					const isMatch = !text.endsWith(pattern);
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [0, text.length - 1]
					};
				}
			})
		},
		{
			type: "suffix-exact",
			multiRegex: /^"(.*)"\$$/,
			singleRegex: /^(.*)\$$/,
			create: (pattern) => ({
				type: "suffix-exact",
				search(text) {
					const isMatch = text.endsWith(pattern);
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [text.length - pattern.length, text.length - 1]
					};
				}
			})
		},
		{
			type: "inverse-exact",
			multiRegex: /^!"(.*)"$/,
			singleRegex: /^!(.*)$/,
			create: (pattern) => ({
				type: "inverse-exact",
				search(text) {
					const isMatch = text.indexOf(pattern) === -1;
					return {
						isMatch,
						score: isMatch ? 0 : 1,
						indices: [0, text.length - 1]
					};
				}
			})
		},
		{
			type: "fuzzy",
			multiRegex: /^"(.*)"$/,
			singleRegex: /^(.*)$/,
			create: (pattern, options = {}) => {
				const bitap = new BitapSearch(pattern, {
					location: options.location ?? Config.location,
					threshold: options.threshold ?? Config.threshold,
					distance: options.distance ?? Config.distance,
					includeMatches: options.includeMatches ?? Config.includeMatches,
					findAllMatches: options.findAllMatches ?? Config.findAllMatches,
					minMatchCharLength: options.minMatchCharLength ?? Config.minMatchCharLength,
					isCaseSensitive: options.isCaseSensitive ?? Config.isCaseSensitive,
					ignoreDiacritics: options.ignoreDiacritics ?? Config.ignoreDiacritics,
					ignoreLocation: options.ignoreLocation ?? Config.ignoreLocation
				});
				return {
					type: "fuzzy",
					search(text) {
						return bitap.searchIn(text);
					}
				};
			}
		}
	];
	var matchersLen = matchers.length;
	var ESCAPED_PIPE = "\0";
	var OR_TOKEN = "|";
	function tokenize(pattern) {
		const tokens = [];
		const len = pattern.length;
		let i = 0;
		while (i < len) {
			while (i < len && pattern[i] === " ") i++;
			if (i >= len) break;
			let j = i;
			while (j < len && pattern[j] !== " " && pattern[j] !== "\"") j++;
			if (j < len && pattern[j] === "\"") {
				j++;
				while (j < len) {
					if (pattern[j] === "\"") {
						const next = j + 1;
						if (next >= len || pattern[next] === " ") {
							j++;
							break;
						}
						if (pattern[next] === "$" && (next + 1 >= len || pattern[next + 1] === " ")) {
							j += 2;
							break;
						}
					}
					j++;
				}
				tokens.push(pattern.substring(i, j));
				i = j;
			} else {
				while (j < len && pattern[j] !== " ") j++;
				tokens.push(pattern.substring(i, j));
				i = j;
			}
		}
		return tokens;
	}
	function getMatch(pattern, exp) {
		const matches = pattern.match(exp);
		return matches ? matches[1] : null;
	}
	function parseQuery(pattern, options = {}) {
		return pattern.replace(/\\\|/g, ESCAPED_PIPE).split(OR_TOKEN).map((item) => {
			const query = tokenize(item.replace(/\u0000/g, "|").trim()).filter((item) => item && !!item.trim());
			const results = [];
			for (let i = 0, len = query.length; i < len; i += 1) {
				const queryItem = query[i];
				let found = false;
				let idx = -1;
				while (!found && ++idx < matchersLen) {
					const def = matchers[idx];
					const token = getMatch(queryItem, def.multiRegex);
					if (token) {
						results.push(def.create(token, options));
						found = true;
					}
				}
				if (found) continue;
				idx = -1;
				while (++idx < matchersLen) {
					const def = matchers[idx];
					const token = getMatch(queryItem, def.singleRegex);
					if (token) {
						results.push(def.create(token, options));
						break;
					}
				}
			}
			return results;
		});
	}
	var ExtendedSearch = class {
		constructor(pattern, { isCaseSensitive = Config.isCaseSensitive, ignoreDiacritics = Config.ignoreDiacritics, includeMatches = Config.includeMatches, minMatchCharLength = Config.minMatchCharLength, ignoreLocation = Config.ignoreLocation, findAllMatches = Config.findAllMatches, location = Config.location, threshold = Config.threshold, distance = Config.distance } = {}) {
			this.query = null;
			this.options = {
				isCaseSensitive,
				ignoreDiacritics,
				includeMatches,
				minMatchCharLength,
				findAllMatches,
				ignoreLocation,
				location,
				threshold,
				distance
			};
			pattern = isCaseSensitive ? pattern : pattern.toLowerCase();
			pattern = ignoreDiacritics ? stripDiacritics(pattern) : pattern;
			this.pattern = pattern;
			this.query = parseQuery(this.pattern, this.options);
		}
		static condition(_, options) {
			return options.useExtendedSearch;
		}
		searchIn(text) {
			const query = this.query;
			if (!query) return {
				isMatch: false,
				score: 1
			};
			const { includeMatches, isCaseSensitive, ignoreDiacritics } = this.options;
			text = isCaseSensitive ? text : text.toLowerCase();
			text = ignoreDiacritics ? stripDiacritics(text) : text;
			let numMatches = 0;
			const allIndices = [];
			let totalScore = 0;
			let hasInverse = false;
			for (let i = 0, qLen = query.length; i < qLen; i += 1) {
				const searchers = query[i];
				allIndices.length = 0;
				numMatches = 0;
				hasInverse = false;
				for (let j = 0, pLen = searchers.length; j < pLen; j += 1) {
					const matcher = searchers[j];
					const { isMatch, indices, score } = matcher.search(text);
					if (isMatch) {
						numMatches += 1;
						totalScore += score;
						if (isInverse(matcher.type)) hasInverse = true;
						if (includeMatches) if (MULTI_MATCH_TYPES.has(matcher.type)) allIndices.push(...indices);
						else allIndices.push(indices);
					} else {
						totalScore = 0;
						numMatches = 0;
						allIndices.length = 0;
						hasInverse = false;
						break;
					}
				}
				if (numMatches) {
					const result = {
						isMatch: true,
						score: totalScore / numMatches
					};
					if (hasInverse) result.hasInverse = true;
					if (includeMatches) result.indices = mergeIndices(allIndices);
					return result;
				}
			}
			return {
				isMatch: false,
				score: 1
			};
		}
	};
	var registeredSearchers = [];
	function register(...args) {
		registeredSearchers.push(...args);
	}
	function createSearcher(pattern, options) {
		for (let i = 0, len = registeredSearchers.length; i < len; i += 1) {
			const searcherClass = registeredSearchers[i];
			if (searcherClass.condition(pattern, options)) return new searcherClass(pattern, options);
		}
		return new BitapSearch(pattern, options);
	}
	var LogicalOperator = {
		AND: "$and",
		OR: "$or"
	};
	var KeyType = {
		PATH: "$path",
		PATTERN: "$val"
	};
	var isExpression = (query) => !!(query[LogicalOperator.AND] || query[LogicalOperator.OR]);
	var isPath = (query) => !!query[KeyType.PATH];
	var isLeaf = (query) => !isArray(query) && isObject(query) && !isExpression(query);
	var convertToExplicit = (query) => ({ [LogicalOperator.AND]: Object.keys(query).map((key) => ({ [key]: query[key] })) });
	function parse(query, options, { auto = true } = {}) {
		const next = (query) => {
			if (isString(query)) {
				const obj = {
					keyId: null,
					pattern: query
				};
				if (auto) obj.searcher = createSearcher(query, options);
				return obj;
			}
			const keys = Object.keys(query);
			const isQueryPath = isPath(query);
			if (!isQueryPath && keys.length > 1 && !isExpression(query)) return next(convertToExplicit(query));
			if (isLeaf(query)) {
				const key = isQueryPath ? query[KeyType.PATH] : keys[0];
				const pattern = isQueryPath ? query[KeyType.PATTERN] : query[key];
				if (!isString(pattern)) throw new Error(LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY(key));
				const obj = {
					keyId: createKeyId(key),
					pattern
				};
				if (auto) obj.searcher = createSearcher(pattern, options);
				return obj;
			}
			const node = {
				children: [],
				operator: keys[0]
			};
			keys.forEach((key) => {
				const value = query[key];
				if (isArray(value)) value.forEach((item) => {
					node.children.push(next(item));
				});
			});
			return node;
		};
		if (!isExpression(query)) query = convertToExplicit(query);
		return next(query);
	}
	function computeScoreSingle(matches, { ignoreFieldNorm = Config.ignoreFieldNorm }) {
		let totalScore = 1;
		matches.forEach(({ key, norm, score }) => {
			const weight = key ? key.weight : null;
			totalScore *= Math.pow(score === 0 && weight ? Number.EPSILON : score, (weight || 1) * (ignoreFieldNorm ? 1 : norm));
		});
		return totalScore;
	}
	function computeScore(results, { ignoreFieldNorm = Config.ignoreFieldNorm }) {
		results.forEach((result) => {
			result.score = computeScoreSingle(result.matches, { ignoreFieldNorm });
		});
	}
	var MaxHeap = class {
		constructor(limit, comparator) {
			this.limit = limit;
			this.heap = [];
			this.comparator = comparator;
		}
		get size() {
			return this.heap.length;
		}
		insert(item) {
			if (this.size < this.limit) {
				this.heap.push(item);
				this._bubbleUp(this.size - 1);
			} else if (this.comparator(item, this.heap[0]) < 0) {
				this.heap[0] = item;
				this._sinkDown(0);
			}
		}
		extractSorted() {
			return this.heap.sort(this.comparator);
		}
		_bubbleUp(i) {
			const heap = this.heap;
			while (i > 0) {
				const parent = i - 1 >> 1;
				if (this.comparator(heap[i], heap[parent]) <= 0) break;
				const tmp = heap[i];
				heap[i] = heap[parent];
				heap[parent] = tmp;
				i = parent;
			}
		}
		_sinkDown(i) {
			const heap = this.heap;
			const len = heap.length;
			let largest = i;
			do {
				i = largest;
				const left = 2 * i + 1;
				const right = 2 * i + 2;
				if (left < len && this.comparator(heap[left], heap[largest]) > 0) largest = left;
				if (right < len && this.comparator(heap[right], heap[largest]) > 0) largest = right;
				if (largest !== i) {
					const tmp = heap[i];
					heap[i] = heap[largest];
					heap[largest] = tmp;
				}
			} while (largest !== i);
		}
	};
	function formatMatches(result) {
		const matches = [];
		result.matches.forEach((match) => {
			if (!isDefined(match.indices) || !match.indices.length) return;
			const obj = {
				indices: match.indices,
				value: match.value
			};
			if (match.key) obj.key = match.key.id;
			if (match.idx > -1) obj.refIndex = match.idx;
			matches.push(obj);
		});
		return matches;
	}
	function format(results, docs, { includeMatches = Config.includeMatches, includeScore = Config.includeScore } = {}) {
		return results.map((result) => {
			const { idx } = result;
			const data = {
				item: docs[idx],
				refIndex: idx
			};
			if (includeMatches) data.matches = formatMatches(result);
			if (includeScore) data.score = result.score;
			return data;
		});
	}
	var DEFAULT_TOKEN = /[\p{L}\p{M}\p{N}_]+/gu;
	var warned = /* @__PURE__ */ new WeakSet();
	function warnNonGlobal(regex) {
		if (!warned.has(regex)) {
			warned.add(regex);
			console.warn(`[Fuse] tokenize regex ${regex} lacks the global flag; only the first match per text will be returned. Add the 'g' flag.`);
		}
	}
	function resolveTokenize(tokenize) {
		if (typeof tokenize === "function") {
			let validated = false;
			return (text) => {
				const result = tokenize(text);
				if (!validated) {
					validated = true;
					if (!Array.isArray(result) || result.some((t) => typeof t !== "string")) throw new Error(`[Fuse] tokenize function must return string[]; received ${Array.isArray(result) ? "array containing non-strings" : typeof result}.`);
				}
				return result;
			};
		}
		if (tokenize instanceof RegExp) {
			if (!tokenize.global) warnNonGlobal(tokenize);
			return (text) => text.match(tokenize) || [];
		}
		return (text) => text.match(DEFAULT_TOKEN) || [];
	}
	function createAnalyzer({ isCaseSensitive = false, ignoreDiacritics = false, tokenize } = {}) {
		const tokenizeFn = resolveTokenize(tokenize);
		return { tokenize(text) {
			if (!isCaseSensitive) text = text.toLowerCase();
			if (ignoreDiacritics) text = stripDiacritics(text);
			return tokenizeFn(text);
		} };
	}
	var TokenSearch = class {
		static condition(_, options) {
			return options.useTokenSearch;
		}
		constructor(pattern, options) {
			this.options = options;
			this.analyzer = createAnalyzer({
				isCaseSensitive: options.isCaseSensitive,
				ignoreDiacritics: options.ignoreDiacritics,
				tokenize: options.tokenize
			});
			const queryTerms = this.analyzer.tokenize(pattern);
			const { df, fieldCount } = options._invertedIndex;
			this.termSearchers = [];
			this.idfWeights = [];
			for (const term of queryTerms) {
				this.termSearchers.push(new BitapSearch(term, {
					location: options.location,
					threshold: options.threshold,
					distance: options.distance,
					includeMatches: options.includeMatches,
					findAllMatches: options.findAllMatches,
					minMatchCharLength: options.minMatchCharLength,
					isCaseSensitive: options.isCaseSensitive,
					ignoreDiacritics: options.ignoreDiacritics,
					ignoreLocation: true
				}));
				const docFreq = df.get(term) || 0;
				const idf = Math.log(1 + (fieldCount - docFreq + .5) / (docFreq + .5));
				this.idfWeights.push(idf);
			}
			this.combineAll = options.tokenMatch === "all";
			this.numTerms = this.termSearchers.length;
			this.useMask = this.numTerms <= 31;
		}
		searchIn(text) {
			if (!this.termSearchers.length) return {
				isMatch: false,
				score: 1
			};
			const allIndices = [];
			let weightedScore = 0;
			let maxPossibleScore = 0;
			let matchedCount = 0;
			let matchedMask = 0;
			const matchedTerms = this.combineAll && !this.useMask ? /* @__PURE__ */ new Set() : null;
			for (let i = 0; i < this.termSearchers.length; i++) {
				const result = this.termSearchers[i].searchIn(text);
				const idf = this.idfWeights[i];
				maxPossibleScore += idf;
				if (result.isMatch) {
					matchedCount++;
					weightedScore += idf * (1 - result.score);
					if (result.indices) allIndices.push(...result.indices);
					if (this.combineAll) if (this.useMask) matchedMask |= 1 << i;
					else matchedTerms.add(i);
				}
			}
			if (matchedCount === 0) return {
				isMatch: false,
				score: 1
			};
			const normalized = maxPossibleScore > 0 ? 1 - weightedScore / maxPossibleScore : 0;
			const searchResult = {
				isMatch: true,
				score: Math.max(.001, normalized)
			};
			if (this.options.includeMatches && allIndices.length) searchResult.indices = mergeIndices(allIndices);
			if (this.combineAll) {
				if (this.useMask) searchResult.matchedMask = matchedMask;
				else searchResult.matchedTerms = matchedTerms;
				searchResult.termCount = this.numTerms;
			}
			return searchResult;
		}
	};
	function addField(index, text, docIdx, analyzer) {
		const tokens = analyzer.tokenize(text);
		if (!tokens.length) return;
		index.fieldCount++;
		index.docFieldCount.set(docIdx, (index.docFieldCount.get(docIdx) || 0) + 1);
		const distinctTerms = new Set(tokens);
		let perDocTerms = index.docTermFieldHits.get(docIdx);
		if (!perDocTerms) {
			perDocTerms = /* @__PURE__ */ new Map();
			index.docTermFieldHits.set(docIdx, perDocTerms);
		}
		for (const term of distinctTerms) {
			perDocTerms.set(term, (perDocTerms.get(term) || 0) + 1);
			index.df.set(term, (index.df.get(term) || 0) + 1);
		}
	}
	function ingestRecord(index, record, keyCount, analyzer) {
		const { i: docIdx, v, $: fields } = record;
		if (v !== void 0) {
			addField(index, v, docIdx, analyzer);
			return;
		}
		if (!fields) return;
		for (let keyIdx = 0; keyIdx < keyCount; keyIdx++) {
			const value = fields[keyIdx];
			if (!value) continue;
			if (Array.isArray(value)) for (const sub of value) addField(index, sub.v, docIdx, analyzer);
			else addField(index, value.v, docIdx, analyzer);
		}
	}
	function buildInvertedIndex(records, keyCount, analyzer) {
		const index = {
			fieldCount: 0,
			df: /* @__PURE__ */ new Map(),
			docFieldCount: /* @__PURE__ */ new Map(),
			docTermFieldHits: /* @__PURE__ */ new Map()
		};
		for (const record of records) ingestRecord(index, record, keyCount, analyzer);
		return index;
	}
	function addToInvertedIndex(index, record, keyCount, analyzer) {
		ingestRecord(index, record, keyCount, analyzer);
	}
	function removeFromInvertedIndex(index, docIdx) {
		const fieldCount = index.docFieldCount.get(docIdx);
		if (fieldCount === void 0) return;
		index.fieldCount -= fieldCount;
		index.docFieldCount.delete(docIdx);
		const perDocTerms = index.docTermFieldHits.get(docIdx);
		if (!perDocTerms) return;
		for (const [term, hits] of perDocTerms) {
			const next = (index.df.get(term) || 0) - hits;
			if (next <= 0) index.df.delete(term);
			else index.df.set(term, next);
		}
		index.docTermFieldHits.delete(docIdx);
	}
	function removeAndShiftInvertedIndex(index, removedIndices) {
		if (removedIndices.length === 0) return;
		const sorted = Array.from(new Set(removedIndices)).sort((a, b) => a - b);
		for (const idx of sorted) removeFromInvertedIndex(index, idx);
		const shift = (oldIdx) => {
			let lo = 0;
			let hi = sorted.length;
			while (lo < hi) {
				const mid = lo + hi >>> 1;
				if (sorted[mid] < oldIdx) lo = mid + 1;
				else hi = mid;
			}
			return oldIdx - lo;
		};
		const firstRemoved = sorted[0];
		const shiftedDocFieldCount = /* @__PURE__ */ new Map();
		for (const [oldKey, count] of index.docFieldCount) shiftedDocFieldCount.set(oldKey > firstRemoved ? shift(oldKey) : oldKey, count);
		index.docFieldCount = shiftedDocFieldCount;
		const shiftedDocTermFieldHits = /* @__PURE__ */ new Map();
		for (const [oldKey, terms] of index.docTermFieldHits) shiftedDocTermFieldHits.set(oldKey > firstRemoved ? shift(oldKey) : oldKey, terms);
		index.docTermFieldHits = shiftedDocTermFieldHits;
	}
	var Fuse = class {
		constructor(docs, options, index) {
			this.options = {
				...Config,
				...options
			};
			if (this.options.useExtendedSearch && false);
			if (this.options.useTokenSearch && false);
			this._keyStore = new KeyStore(this.options.keys);
			this._docs = docs;
			this._myIndex = null;
			this._invertedIndex = null;
			this.setCollection(docs, index);
			this._lastQuery = null;
			this._lastSearcher = null;
		}
		_getSearcher(query) {
			if (this._lastQuery === query) return this._lastSearcher;
			const searcher = createSearcher(query, this._invertedIndex ? {
				...this.options,
				_invertedIndex: this._invertedIndex
			} : this.options);
			this._lastQuery = query;
			this._lastSearcher = searcher;
			return searcher;
		}
		setCollection(docs, index) {
			this._docs = docs;
			if (index && !(index instanceof FuseIndex)) throw new Error(INCORRECT_INDEX_TYPE);
			this._myIndex = index || createIndex(this.options.keys, this._docs, {
				getFn: this.options.getFn,
				fieldNormWeight: this.options.fieldNormWeight
			});
			if (this.options.useTokenSearch) {
				const analyzer = createAnalyzer({
					isCaseSensitive: this.options.isCaseSensitive,
					ignoreDiacritics: this.options.ignoreDiacritics,
					tokenize: this.options.tokenize
				});
				this._invertedIndex = buildInvertedIndex(this._myIndex.records, this._myIndex.keys.length, analyzer);
			}
			this._invalidateSearcherCache();
		}
		add(doc) {
			if (!isDefined(doc)) return;
			this._docs.push(doc);
			const record = this._myIndex.add(doc, this._docs.length - 1);
			if (this._invertedIndex && record) {
				const analyzer = createAnalyzer({
					isCaseSensitive: this.options.isCaseSensitive,
					ignoreDiacritics: this.options.ignoreDiacritics,
					tokenize: this.options.tokenize
				});
				addToInvertedIndex(this._invertedIndex, record, this._myIndex.keys.length, analyzer);
			}
			this._invalidateSearcherCache();
		}
		remove(predicate = () => false) {
			const results = [];
			const indicesToRemove = [];
			for (let i = 0, len = this._docs.length; i < len; i += 1) if (predicate(this._docs[i], i)) {
				results.push(this._docs[i]);
				indicesToRemove.push(i);
			}
			if (indicesToRemove.length) {
				if (this._invertedIndex) removeAndShiftInvertedIndex(this._invertedIndex, indicesToRemove);
				const toRemove = new Set(indicesToRemove);
				this._docs = this._docs.filter((_, i) => !toRemove.has(i));
				this._myIndex.removeAll(indicesToRemove);
				this._invalidateSearcherCache();
			}
			return results;
		}
		removeAt(idx) {
			if (!Number.isInteger(idx) || idx < 0 || idx >= this._docs.length) throw new Error(INVALID_DOC_INDEX);
			if (this._invertedIndex) removeAndShiftInvertedIndex(this._invertedIndex, [idx]);
			const doc = this._docs.splice(idx, 1)[0];
			this._myIndex.removeAt(idx);
			this._invalidateSearcherCache();
			return doc;
		}
		_invalidateSearcherCache() {
			this._lastQuery = null;
			this._lastSearcher = null;
		}
		getIndex() {
			return this._myIndex;
		}
		_normalizedKeys() {
			return this._myIndex.keys.map((key) => this._keyStore.get(key.id) || key);
		}
		search(query, options) {
			const { limit = -1 } = options || {};
			const { includeMatches, includeScore, shouldSort, sortFn, ignoreFieldNorm } = this.options;
			if (isString(query) && !query.trim()) {
				let docs = this._docs.map((item, idx) => ({
					item,
					refIndex: idx
				}));
				if (isNumber(limit) && limit > -1) docs = docs.slice(0, limit);
				return docs;
			}
			const useHeap = shouldSort && isNumber(limit) && limit > 0 && isString(query);
			const comparator = sortFn;
			const stable = (a, b) => comparator(a, b) || a.idx - b.idx;
			let results;
			if (useHeap) {
				const heap = new MaxHeap(limit, stable);
				if (isString(this._docs[0])) this._searchStringList(query, {
					heap,
					ignoreFieldNorm
				});
				else this._searchObjectList(query, {
					heap,
					ignoreFieldNorm
				});
				results = heap.extractSorted();
			} else {
				results = isString(query) ? isString(this._docs[0]) ? this._searchStringList(query) : this._searchObjectList(query) : this._searchLogical(query);
				computeScore(results, { ignoreFieldNorm });
				if (shouldSort) results.sort(isString(query) ? stable : comparator);
				if (isNumber(limit) && limit > -1) results = results.slice(0, limit);
			}
			return format(results, this._docs, {
				includeMatches,
				includeScore
			});
		}
		_searchStringList(query, { heap, ignoreFieldNorm } = {}) {
			const searcher = this._getSearcher(query);
			const requireAllTokens = this.options.useTokenSearch && this.options.tokenMatch === "all";
			const { records } = this._myIndex;
			const results = heap ? null : [];
			records.forEach(({ v: text, i: idx, n: norm }) => {
				if (!isDefined(text)) return;
				const searchResult = searcher.searchIn(text);
				if (searchResult.isMatch) {
					const match = {
						score: searchResult.score,
						value: text,
						norm,
						indices: searchResult.indices
					};
					if (requireAllTokens) {
						match.matchedMask = searchResult.matchedMask;
						match.matchedTerms = searchResult.matchedTerms;
						match.termCount = searchResult.termCount;
					}
					const matches = [match];
					if (!requireAllTokens || this._coversAllTokens(matches)) {
						const result = {
							item: text,
							idx,
							matches
						};
						if (heap) {
							result.score = computeScoreSingle(result.matches, { ignoreFieldNorm });
							heap.insert(result);
						} else results.push(result);
					}
				}
			});
			return results;
		}
		_searchLogical(query) {
			const expression = parse(query, this.options);
			const keys = this._normalizedKeys();
			const evaluate = (node, item, idx) => {
				if (!("children" in node)) {
					const { keyId, searcher } = node;
					let matches;
					if (keyId === null) {
						matches = [];
						keys.forEach((key, keyIndex) => {
							matches.push(...this._findMatches({
								key,
								value: item[keyIndex],
								searcher
							}));
						});
					} else matches = this._findMatches({
						key: this._keyStore.get(keyId),
						value: this._myIndex.getValueForItemAtKeyId(item, keyId),
						searcher
					});
					if (matches && matches.length) return [{
						idx,
						item,
						matches
					}];
					return [];
				}
				const { children, operator } = node;
				const res = [];
				for (let i = 0, len = children.length; i < len; i += 1) {
					const child = children[i];
					const result = evaluate(child, item, idx);
					if (result.length) res.push(...result);
					else if (operator === LogicalOperator.AND) return [];
				}
				return res;
			};
			const records = this._myIndex.records;
			const resultMap = /* @__PURE__ */ new Map();
			const results = [];
			records.forEach(({ $: item, i: idx }) => {
				if (isDefined(item)) {
					const expResults = evaluate(expression, item, idx);
					if (expResults.length) {
						if (!resultMap.has(idx)) {
							resultMap.set(idx, {
								idx,
								item,
								matches: []
							});
							results.push(resultMap.get(idx));
						}
						expResults.forEach(({ matches }) => {
							resultMap.get(idx).matches.push(...matches);
						});
					}
				}
			});
			return results;
		}
		_searchObjectList(query, { heap, ignoreFieldNorm } = {}) {
			const searcher = this._getSearcher(query);
			const requireAllTokens = this.options.useTokenSearch && this.options.tokenMatch === "all";
			const { records } = this._myIndex;
			const keys = this._normalizedKeys();
			const results = heap ? null : [];
			records.forEach(({ $: item, i: idx }) => {
				if (!isDefined(item)) return;
				const matches = [];
				let anyKeyFailed = false;
				let hasInverse = false;
				keys.forEach((key, keyIndex) => {
					const keyMatches = this._findMatches({
						key,
						value: item[keyIndex],
						searcher
					});
					if (keyMatches.length) {
						matches.push(...keyMatches);
						if (keyMatches[0].hasInverse) hasInverse = true;
					} else anyKeyFailed = true;
				});
				if (hasInverse && anyKeyFailed) return;
				if (matches.length && (!requireAllTokens || this._coversAllTokens(matches))) {
					const result = {
						idx,
						item,
						matches
					};
					if (heap) {
						result.score = computeScoreSingle(result.matches, { ignoreFieldNorm });
						heap.insert(result);
					} else results.push(result);
				}
			});
			return results;
		}
		_findMatches({ key, value, searcher }) {
			if (!isDefined(value)) return [];
			const matches = [];
			if (isArray(value)) value.forEach(({ v: text, i: idx, n: norm }) => {
				if (!isDefined(text)) return;
				const searchResult = searcher.searchIn(text);
				if (searchResult.isMatch) {
					const match = {
						score: searchResult.score,
						key,
						value: text,
						idx,
						norm,
						indices: searchResult.indices,
						hasInverse: searchResult.hasInverse
					};
					if (searchResult.termCount !== void 0) {
						match.matchedMask = searchResult.matchedMask;
						match.matchedTerms = searchResult.matchedTerms;
						match.termCount = searchResult.termCount;
					}
					matches.push(match);
				}
			});
			else {
				const { v: text, n: norm } = value;
				const searchResult = searcher.searchIn(text);
				if (searchResult.isMatch) {
					const match = {
						score: searchResult.score,
						key,
						value: text,
						norm,
						indices: searchResult.indices,
						hasInverse: searchResult.hasInverse
					};
					if (searchResult.termCount !== void 0) {
						match.matchedMask = searchResult.matchedMask;
						match.matchedTerms = searchResult.matchedTerms;
						match.termCount = searchResult.termCount;
					}
					matches.push(match);
				}
			}
			return matches;
		}
		_coversAllTokens(matches) {
			const termCount = matches.length ? matches[0].termCount : void 0;
			if (termCount === void 0) return true;
			if (termCount <= 31) {
				let coverage = 0;
				for (let i = 0; i < matches.length; i++) coverage |= matches[i].matchedMask || 0;
				return coverage === 2 ** termCount - 1;
			}
			const coverage = /* @__PURE__ */ new Set();
			for (let i = 0; i < matches.length; i++) {
				const terms = matches[i].matchedTerms;
				if (terms) for (const t of terms) coverage.add(t);
			}
			return coverage.size === termCount;
		}
	};
	Fuse.version = "7.5.0";
	Fuse.createIndex = createIndex;
	Fuse.parseIndex = parseIndex;
	Fuse.config = Config;
	Fuse.match = function(pattern, text, options) {
		if (options && options.useTokenSearch) throw new Error(FUSE_MATCH_TOKEN_SEARCH_UNSUPPORTED);
		return createSearcher(pattern, {
			...Config,
			...options
		}).searchIn(text);
	};
	Fuse.parseQuery = parse;
	register(ExtendedSearch);
	register(TokenSearch);
	Fuse.use = function(...plugins) {
		plugins.forEach((plugin) => register(plugin));
	};
	var entry_default = Fuse;
	//#endregion
	//#region resources/js/components/listbox.js
	var listbox_exports = /* @__PURE__ */ __exportAll({ listbox: () => listbox });
	function listbox({ hideEmpty = false, clearOnSelect = false, ...fuseOptions } = {}) {
		return {
			input: null,
			list: null,
			noRecords: null,
			items: [],
			filteredItems: [],
			index: null,
			fuse: null,
			lastInteraction: null,
			debouncedSearch: null,
			livewireCommitCleanup: null,
			init() {
				this.input = this.$root.querySelector(dataKey("input"));
				this.list = this.$root.querySelector("[role=listbox]");
				this.noRecords = this.$root.querySelector("[role=status]");
				this.refreshItems();
				this.livewireCommitCleanup = onLivewireCommit(({ succeed }) => {
					succeed(() => {
						if (!this.$root?.isConnected) return;
						this.refreshItems();
						this.search();
					});
				});
				this.$watch(() => this.index, (index) => {
					this.setActive(index);
				});
				this.debouncedSearch = debounce(() => this.search(), 150);
				bind(this.input, {
					["@input"]() {
						this.lastInteraction = "keyboard";
						this.$dispatch("listbox-search-updated", { query: this.input.value });
						this.debouncedSearch();
					},
					["@focus"]() {
						this.search();
					},
					["@blur"]() {
						this.clear();
					},
					["@keydown.escape.prevent"]() {
						this.clear();
					},
					["@keydown.arrow-up.prevent"]() {
						this.lastInteraction = "keyboard";
						this.prev();
					},
					["@keydown.arrow-down.prevent"]() {
						this.lastInteraction = "keyboard";
						this.next();
					},
					["@keydown.home.prevent"]() {
						this.lastInteraction = "keyboard";
						this.first();
					},
					["@keydown.end.prevent"]() {
						this.lastInteraction = "keyboard";
						this.last();
					},
					["@keydown.enter.prevent"]() {
						this.select(this.index);
					},
					["@keydown.tab"]() {
						this.select(this.index);
					}
				});
				bind(this.list, {
					["@mouseleave"]: () => this.clear(),
					["@mousedown"]: (e) => {
						const item = e.target.closest("[role=option]");
						if (!item) return;
						const index = Number(item.dataset.index);
						if (!Number.isNaN(index)) this.select(index);
					},
					["@mousemove"]: (e) => {
						if (this.lastInteraction === "keyboard" && e.movementX === 0 && e.movementY === 0) return;
						this.lastInteraction = "mouse";
						const item = e.target.closest("[role=option]");
						if (!item) return;
						const index = Number(item.dataset.index);
						if (Number.isNaN(index)) return;
						if (this.isDisabled(this.filteredItems[index])) return;
						if (this.index !== index) this.index = index;
					},
					["@keydown.escape.prevent"]() {
						this.clear();
					},
					["@keydown.arrow-up.prevent"]() {
						this.lastInteraction = "keyboard";
						this.prev();
					},
					["@keydown.arrow-down.prevent"]() {
						this.lastInteraction = "keyboard";
						this.next();
					},
					["@keydown.home.prevent"]() {
						this.lastInteraction = "keyboard";
						this.first();
					},
					["@keydown.end.prevent"]() {
						this.lastInteraction = "keyboard";
						this.last();
					},
					["@keydown.enter.prevent"]() {
						this.select(this.index);
					},
					["@keydown.space.prevent"]() {
						this.select(this.index);
					}
				});
				this.$nextTick(() => {
					this.search();
					this.$dispatch("listbox-initialized");
				});
			},
			destroy() {
				this.livewireCommitCleanup?.();
			},
			refreshItems() {
				this.items = Array.from(this.list.querySelectorAll("[role=option]")).map((item) => {
					item.hidden = true;
					if (item?.firstElementChild?.disabled) item.setAttribute("aria-disabled", "true");
					else item.removeAttribute("aria-disabled");
					return {
						title: normalize(item.querySelector("[data-item-content]")?.textContent, { removeSpaces: true }),
						el: item.firstElementChild,
						li: item
					};
				});
				const fuseIndex = entry_default.createIndex(["title"], this.items);
				this.fuse = new entry_default(this.items, {
					ignoreDiacritics: true,
					includeScore: true,
					threshold: .1,
					keys: ["title"],
					...fuseOptions
				}, fuseIndex);
			},
			search() {
				const query = this.input ? this.input.value.trim() : "";
				this.clear();
				if (!query.length && hideEmpty) {
					this.filteredItems = [];
					return;
				}
				this.items.forEach((item) => {
					item.li.hidden = true;
				});
				const fragment = document.createDocumentFragment();
				let results = [];
				if (query) results = this.fuse.search(query);
				else if (!hideEmpty) results = this.items.map((item) => ({ item }));
				this.filteredItems = results.map((result, index) => {
					const li = result.item.li;
					li.hidden = false;
					li.dataset.index = String(index);
					fragment.appendChild(li);
					return result.item;
				});
				this.list.appendChild(fragment);
				this.$dispatch("listbox-items-changed", {
					list: this.list,
					items: this.items,
					filteredItems: this.filteredItems
				});
				if (this.filteredItems.length && query.length) this.$nextTick(() => {
					this.index = 0;
				});
				this.toggleNoRecords();
			},
			isDisabled(item) {
				return !!item?.el?.hasAttribute("disabled");
			},
			prev() {
				if (this.filteredItems.length === 0) return;
				let index = this.index === null ? this.filteredItems.length - 1 : (this.index - 1 + this.filteredItems.length) % this.filteredItems.length;
				for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) index = (index - 1 + this.filteredItems.length) % this.filteredItems.length;
				if (this.isDisabled(this.filteredItems[index])) return;
				this.index = index;
			},
			next() {
				if (this.filteredItems.length === 0) return;
				let index = this.index === null ? 0 : (this.index + 1) % this.filteredItems.length;
				for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) index = (index + 1) % this.filteredItems.length;
				if (this.isDisabled(this.filteredItems[index])) return;
				this.index = index;
			},
			first() {
				if (this.filteredItems.length === 0) return;
				let index = 0;
				while (index < this.filteredItems.length && this.isDisabled(this.filteredItems[index])) index++;
				if (index >= this.filteredItems.length) return;
				this.index = index;
			},
			last() {
				if (this.filteredItems.length === 0) return;
				let index = this.filteredItems.length - 1;
				while (index >= 0 && this.isDisabled(this.filteredItems[index])) index--;
				if (index < 0) return;
				this.index = index;
			},
			select(index) {
				if (index === null) return;
				const item = this.filteredItems[index];
				if (!item) return;
				const button = item.el;
				if (!button || button.hasAttribute("disabled")) return;
				button.dispatchEvent(new Event("click", { bubbles: true }));
				if (clearOnSelect) setFieldValue(this.input, "");
				this.$dispatch("listbox-item-selected", {
					index,
					item,
					button
				});
			},
			setActive(index) {
				this.clearActive();
				if (index === null) return;
				const item = this.filteredItems[index];
				if (!item) return;
				item.el.dataset.active = "true";
				item.li.setAttribute("aria-selected", "true");
				if (item.li.hasAttribute("id")) this.list.setAttribute("aria-activedescendant", item.li.getAttribute("id"));
				item.li.scrollIntoView({ block: "nearest" });
				this.$dispatch("listbox-active-changed", {
					index,
					item
				});
			},
			clearActive() {
				this.filteredItems.forEach((item) => {
					delete item.el.dataset.active;
					item.li.removeAttribute("aria-selected");
				});
				this.list.removeAttribute("aria-activedescendant");
			},
			clear() {
				this.debouncedSearch?.cancel();
				this.clearActive();
				this.index = null;
			},
			toggleNoRecords() {
				if (!this.noRecords) return;
				if (this.filteredItems.length === 0 && this.input?.value && !hideEmpty) {
					this.noRecords.removeAttribute("hidden");
					this.list.setAttribute("hidden", "");
				} else {
					this.noRecords.setAttribute("hidden", "");
					this.list.removeAttribute("hidden");
				}
			}
		};
	}
	//#endregion
	//#region resources/js/components/autocomplete.js
	var autocomplete_exports = /* @__PURE__ */ __exportAll({ autocomplete: () => autocomplete });
	function autocomplete(options = {}) {
		const _popover = popover({
			mode: "manual",
			position: "bottom",
			align: "start",
			matchTriggerWidth: true
		});
		const _listbox = listbox({
			hideEmpty: true,
			clearOnSelect: false,
			...options
		});
		return {
			..._popover,
			..._listbox,
			init() {
				_popover.init.call(this);
				_listbox.init.call(this);
				this.trigger = this.input;
				bind(this.input, {
					["@blur"]() {
						this.close();
					},
					["@keydown.escape.prevent"]() {
						this.close();
					}
				});
				bind(this.$root, { ["@listbox-item-selected"]({ detail }) {
					setFieldValue(this.input, detail.item.title);
					this.close();
				} });
			},
			search() {
				_listbox.search.call(this);
				if (this.filteredItems.length) this.open();
				else this.close();
			},
			open() {
				_popover.open.call(this, false);
			},
			close() {
				_popover.close.call(this);
				this.clear();
			}
		};
	}
	//#endregion
	//#region resources/js/components/badge.js
	var badge_exports = /* @__PURE__ */ __exportAll({ badge: () => badge });
	function badge() {
		return { ...dismissible("fade") };
	}
	//#endregion
	//#region resources/js/mixins/bindable-field.js
	function bindableField({ key, property = "value", serialize = function() {
		return this[property] ?? null;
	}, deserialize = function(raw) {
		return raw || null;
	} } = {}) {
		return {
			field: null,
			init() {
				this.field = this.$root.querySelector(dataKey(key));
				if (!this.field) return;
				if (this.$wire) {
					const prop = getWireModelInfo(this.field);
					if (prop) {
						this[property] = deserialize.call(this, this.$wire.get(prop.name) ?? null);
						this.$wire.$watch(prop.name, () => {
							this[property] = deserialize.call(this, this.field.value || null);
						});
					}
				}
				this.$watch(property, () => setFieldValue(this.field, serialize.call(this)));
			}
		};
	}
	//#endregion
	//#region resources/js/components/calendar.js
	var calendar_exports = /* @__PURE__ */ __exportAll({ calendar: () => calendar });
	function calendar({ value = null, multiple = false, mode = null, months = 1, min = null, max = null, unavailable = null, minRange = null, maxRange = null, static: isStatic = false, navigation = true, withToday = false, selectableHeader = false, fixedWeeks = false, startDay = null, openTo = null, weekNumbers = false, locale = null } = {}) {
		months = Math.max(1, Number(months) || 1);
		minRange = Number(minRange) || null;
		maxRange = Number(maxRange) || null;
		const _bindableField = bindableField({
			key: "calendar-field",
			serialize() {
				return this.valueString();
			},
			deserialize(raw) {
				return this.parseInitialValue(raw);
			}
		});
		return {
			..._bindableField,
			static: isStatic,
			navigation,
			withToday,
			selectableHeader,
			fixedWeeks,
			weekNumbers,
			locale: locale || (typeof navigator !== "undefined" ? navigator.language : "en-US"),
			startDay: 0,
			unavailable: parseCommaList(unavailable),
			value: null,
			anchorMonth: null,
			focused: null,
			hoverIso: null,
			rangeAnchor: null,
			init() {
				this.startDay = startDay !== null && startDay !== void 0 ? Number(startDay) : resolveLocaleFirstDay(this.locale);
				this.value = this.parseInitialValue(value);
				_bindableField.init.call(this);
				this.anchorMonth = startOfMonth(this.firstAnchorDate());
				this.focused = this.firstSelectedIso() ?? isoOf(/* @__PURE__ */ new Date());
			},
			parseInitialValue(raw) {
				if (mode === "range") return this.normalizeRange(raw);
				if (multiple) return this.normalizeMultiple(raw);
				return this.normalizeSingle(raw);
			},
			normalizeSingle(raw) {
				if (!raw || typeof raw === "object") return Array.isArray(raw) ? raw[0] ?? null : null;
				return String(raw).trim() || null;
			},
			normalizeMultiple(raw) {
				if (!raw) return [];
				if (Array.isArray(raw)) return raw.filter(Boolean);
				return parseCommaList(raw);
			},
			normalizeRange(raw) {
				if (!raw) return null;
				if (Array.isArray(raw)) return raw[0] || raw[1] ? {
					start: raw[0] ?? null,
					end: raw[1] ?? null
				} : null;
				if (typeof raw === "object") return raw.start || raw.end ? {
					start: raw.start ?? null,
					end: raw.end ?? null
				} : null;
				const [start, end] = String(raw).split("/");
				return start?.trim() ? {
					start: start.trim(),
					end: end?.trim() || null
				} : null;
			},
			valueString() {
				if (mode === "range") {
					if (!this.value?.start) return null;
					return this.value.end ? `${this.value.start}/${this.value.end}` : this.value.start;
				}
				if (multiple) return (this.value ?? []).join(",");
				return this.value ?? null;
			},
			firstAnchorDate() {
				const iso = this.firstSelectedIso();
				if (iso) return parseIso(iso);
				if (openTo) return parseIso(openTo) ?? /* @__PURE__ */ new Date();
				return /* @__PURE__ */ new Date();
			},
			firstSelectedIso() {
				if (mode === "range") return this.value?.start ?? null;
				if (multiple) return this.value?.[0] ?? null;
				return this.value ?? null;
			},
			monthAt(offset) {
				return addMonths(this.anchorMonth, offset);
			},
			isMonthVisible(date) {
				for (let i = 0; i < months; i++) if (sameMonth(this.monthAt(i), date)) return true;
				return false;
			},
			weekdayLabels() {
				const fmt = new Intl.DateTimeFormat(this.locale, { weekday: "short" });
				const base = new Date(1970, 0, 4);
				return Array.from({ length: 7 }, (_, i) => {
					const date = new Date(base);
					date.setDate(base.getDate() + (this.startDay + i) % 7);
					return fmt.format(date);
				});
			},
			monthLabel(monthIndex) {
				return new Intl.DateTimeFormat(this.locale, {
					month: "long",
					year: "numeric"
				}).format(this.monthAt(monthIndex));
			},
			dayAriaLabel(iso) {
				return new Intl.DateTimeFormat(this.locale, { dateStyle: "full" }).format(parseIso(iso));
			},
			monthOptions() {
				const fmt = new Intl.DateTimeFormat(this.locale, { month: "long" });
				return Array.from({ length: 12 }, (_, i) => ({
					value: i,
					label: fmt.format(new Date(2e3, i, 1))
				}));
			},
			yearOptions() {
				const span = 10;
				const current = this.anchorMonth.getFullYear();
				return Array.from({ length: 21 }, (_, i) => current - span + i);
			},
			weeksFor(monthIndex) {
				const month = this.monthAt(monthIndex);
				const year = month.getFullYear();
				const monthNum = month.getMonth();
				const firstOfMonth = new Date(year, monthNum, 1);
				const daysInMonth = new Date(year, monthNum + 1, 0).getDate();
				const startOffset = (firstOfMonth.getDay() - this.startDay + 7) % 7;
				let totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;
				if (this.fixedWeeks) totalCells = Math.max(totalCells, 42);
				const days = Array.from({ length: totalCells }, (_, i) => {
					const date = new Date(year, monthNum, i - startOffset + 1);
					return {
						iso: isoOf(date),
						label: date.getDate(),
						inMonth: date.getMonth() === monthNum
					};
				});
				const weeks = [];
				for (let i = 0; i < days.length; i += 7) {
					const weekDays = days.slice(i, i + 7);
					const thursday = weekDays[(4 - this.startDay + 7) % 7];
					weeks.push({
						key: weekDays[0].iso,
						weekNumber: this.weekNumbers ? isoWeekNumber(parseIso(thursday.iso)) : null,
						days: weekDays
					});
				}
				return weeks;
			},
			currentMonthIndex() {
				return this.anchorMonth.getMonth();
			},
			setCurrentMonthIndex(index) {
				if (this.static || !this.navigation) return;
				this.anchorMonth = new Date(this.anchorMonth.getFullYear(), Number(index), 1);
			},
			currentYear() {
				return this.anchorMonth.getFullYear();
			},
			setCurrentYear(year) {
				if (this.static || !this.navigation) return;
				this.anchorMonth = new Date(Number(year), this.anchorMonth.getMonth(), 1);
			},
			prevMonth() {
				if (this.static || !this.navigation) return;
				this.anchorMonth = addMonths(this.anchorMonth, -1);
			},
			nextMonth() {
				if (this.static || !this.navigation) return;
				this.anchorMonth = addMonths(this.anchorMonth, 1);
			},
			goToToday() {
				if (this.static) return;
				const today = /* @__PURE__ */ new Date();
				if (!this.isMonthVisible(today)) {
					if (!this.navigation) return;
					this.anchorMonth = startOfMonth(today);
					return;
				}
				this.selectDate(isoOf(today));
			},
			isDayDisabled(iso) {
				if (this.static) return true;
				if (min && iso < min) return true;
				if (max && iso > max) return true;
				if (this.unavailable.includes(iso)) return true;
				if (this.isOutOfRangeSpan(iso)) return true;
				return false;
			},
			isOutOfRangeSpan(iso) {
				if (mode !== "range") return false;
				if (!minRange && !maxRange) return false;
				if (!this.rangeAnchor || iso === this.rangeAnchor) return false;
				const days = diffDays(this.rangeAnchor <= iso ? this.rangeAnchor : iso, this.rangeAnchor <= iso ? iso : this.rangeAnchor) + 1;
				if (minRange && days < minRange) return true;
				if (maxRange && days > maxRange) return true;
				return false;
			},
			isUnavailable(iso) {
				return !this.static && this.isDayDisabled(iso);
			},
			isSelected(iso) {
				if (mode === "range") return this.value?.start === iso || this.value?.end === iso;
				if (multiple) return (this.value ?? []).includes(iso);
				return this.value === iso;
			},
			isToday(iso) {
				return iso === isoOf(/* @__PURE__ */ new Date());
			},
			displayRange() {
				if (mode !== "range") return null;
				const start = this.value?.start ?? null;
				const end = this.value?.end ?? (this.rangeAnchor ? this.hoverIso : null);
				if (!start) return null;
				if (!end) return {
					lo: start,
					hi: start
				};
				return start <= end ? {
					lo: start,
					hi: end
				} : {
					lo: end,
					hi: start
				};
			},
			isRangeStart(iso) {
				const range = this.displayRange();
				return !!range && range.lo === iso;
			},
			isRangeEnd(iso) {
				const range = this.displayRange();
				return !!range && range.hi === iso;
			},
			isInRange(iso) {
				const range = this.displayRange();
				return !!range && iso > range.lo && iso < range.hi;
			},
			selectDate(iso) {
				if (this.static || this.isDayDisabled(iso)) return;
				if (mode === "range") {
					this.pickRangeDate(iso);
					return;
				}
				if (multiple) {
					this.toggleMultiple(iso);
					return;
				}
				this.value = this.value === iso ? null : iso;
				this.focused = iso;
				this.$dispatch("calendar-picked", { value: this.value });
			},
			toggleMultiple(iso) {
				const current = this.value ?? [];
				this.value = current.includes(iso) ? current.filter((d) => d !== iso) : [...current, iso].sort();
				this.focused = iso;
				this.$dispatch("calendar-picked", { value: this.value });
			},
			pickRangeDate(iso) {
				if (!this.rangeAnchor) {
					this.rangeAnchor = iso;
					this.value = {
						start: iso,
						end: null
					};
					this.focused = iso;
					return;
				}
				let [start, end] = this.rangeAnchor <= iso ? [this.rangeAnchor, iso] : [iso, this.rangeAnchor];
				const days = diffDays(start, end) + 1;
				if (minRange && days < minRange || maxRange && days > maxRange || this.rangeContainsUnavailable(start, end)) {
					this.rangeAnchor = iso;
					this.value = {
						start: iso,
						end: null
					};
					this.focused = iso;
					return;
				}
				this.value = {
					start,
					end
				};
				this.rangeAnchor = null;
				this.hoverIso = null;
				this.focused = iso;
				this.$dispatch("calendar-picked", { value: this.value });
			},
			setRangeBound(part, iso) {
				if (mode !== "range") return;
				let next = {
					...this.value ?? {
						start: null,
						end: null
					},
					[part]: iso || null
				};
				if (next.start && next.end && next.start > next.end) next = {
					start: next.end,
					end: next.start
				};
				if (next.start === (this.value?.start ?? null) && next.end === (this.value?.end ?? null)) return;
				if (next.start && this.isDayDisabled(next.start)) return;
				if (next.end && this.isDayDisabled(next.end)) return;
				if (next.start && next.end) {
					const days = diffDays(next.start, next.end) + 1;
					if (minRange && days < minRange || maxRange && days > maxRange || this.rangeContainsUnavailable(next.start, next.end)) return;
				}
				this.value = next;
				this.rangeAnchor = null;
				this.hoverIso = null;
				this.focused = iso || this.focused;
				this.$dispatch("calendar-picked", { value: this.value });
			},
			rangeContainsUnavailable(start, end) {
				if (!this.unavailable.length) return false;
				for (let cursor = start; cursor <= end; cursor = addDays(cursor, 1)) if (this.unavailable.includes(cursor)) return true;
				return false;
			},
			previewRange(iso) {
				if (mode === "range" && this.rangeAnchor) this.hoverIso = iso;
			},
			clear() {
				this.value = mode === "range" ? null : multiple ? [] : null;
				this.rangeAnchor = null;
				this.hoverIso = null;
			},
			onCellKeydown(event, iso) {
				const rtl = isRtl(this.$root);
				const deltas = {
					ArrowLeft: rtl ? 1 : -1,
					ArrowRight: rtl ? -1 : 1,
					ArrowUp: -7,
					ArrowDown: 7
				};
				if (event.key in deltas) {
					event.preventDefault();
					this.focusIso(addDays(iso, deltas[event.key]));
				} else if (event.key === "Home") {
					event.preventDefault();
					this.focusIso(this.weekEdge(iso, "start"));
				} else if (event.key === "End") {
					event.preventDefault();
					this.focusIso(this.weekEdge(iso, "end"));
				} else if (event.key === "PageUp") {
					event.preventDefault();
					this.focusIso(this.shiftMonth(iso, event.shiftKey ? -12 : -1));
				} else if (event.key === "PageDown") {
					event.preventDefault();
					this.focusIso(this.shiftMonth(iso, event.shiftKey ? 12 : 1));
				} else if (event.key === "Enter" || event.key === " ") {
					event.preventDefault();
					this.selectDate(iso);
				}
			},
			weekEdge(iso, edge) {
				const offset = (parseIso(iso).getDay() - this.startDay + 7) % 7;
				return edge === "start" ? addDays(iso, -offset) : addDays(iso, 6 - offset);
			},
			shiftMonth(iso, deltaMonths) {
				const date = parseIso(iso);
				return isoOf(new Date(date.getFullYear(), date.getMonth() + deltaMonths, date.getDate()));
			},
			focusIso(iso) {
				const targetMonth = startOfMonth(parseIso(iso));
				if (!this.isMonthVisible(targetMonth)) {
					if (!this.navigation) return;
					this.anchorMonth = targetMonth > this.anchorMonth ? addMonths(targetMonth, -(months - 1)) : targetMonth;
				}
				this.focused = iso;
				this.$nextTick(() => {
					this.$root.querySelector(`[data-iso="${iso}"]`)?.focus();
				});
			}
		};
	}
	//#endregion
	//#region resources/js/components/chartjs.js
	var chartjs_exports = /* @__PURE__ */ __exportAll({ chartjs: () => chartjs });
	function chartjs() {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			chart: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.Chart, "https://cdn.jsdelivr.net/npm/chart.js@4"));
			},
			render(options = {}) {
				try {
					const merged = {
						...options,
						...this.getDataOptions(this.$refs.target)
					};
					if (this.chart) {
						Object.assign(this.chart.config, merged);
						this.chart.update();
					} else this.chart = new window.Chart(this.$refs.target, merged);
					this.$dispatch("rendered", { chart: this.chart });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.chart?.destroy();
				this.chart = null;
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/group-all.js
	function groupAll(type, group) {
		return {
			all: null,
			items() {
				return Array.from(document.querySelectorAll(dataKey(`${type}-group`, group)));
			},
			init() {
				this.all = this.$root.querySelector(dataKey(type));
				bind(this.all, { ["@change"]: () => this.toggleAllItems() });
				bind(this.items(), { ["@change"]: () => this.updateState() });
			},
			toggleAllItems() {
				this.items().forEach((item) => {
					item.checked = !!this.all?.checked;
					item.dispatchEvent(new Event("change", { bubbles: true }));
				});
			},
			updateState() {
				if (!this.all) return;
				const items = this.items();
				this.all.checked = allChecked(items, (item) => item.checked);
				if (type === "checkbox") {
					const checkedCount = items.filter((item) => item.checked).length;
					this.all.indeterminate = checkedCount > 0 && checkedCount < items.length;
				}
			}
		};
	}
	//#endregion
	//#region resources/js/components/checkbox-all.js
	var checkbox_all_exports = /* @__PURE__ */ __exportAll({ checkboxAll: () => checkboxAll });
	function checkboxAll({ group = "" } = {}) {
		return groupAll("checkbox", group);
	}
	//#endregion
	//#region resources/js/components/clearable.js
	var clearable_exports = /* @__PURE__ */ __exportAll({ clearable: () => clearable });
	function clearable() {
		return { init() {
			const button = this.$el;
			if (this.clear) bind(button, { ["@click"]() {
				this.clear();
			} });
			const input = findFieldInput(button);
			if (!input) return;
			button.style.display = Boolean(input.value) ? "block" : "none";
			bind(input, { ["@input"]() {
				button.style.display = Boolean(input.value) ? "block" : "none";
			} });
			bind(button, { ["@click"]() {
				setFieldValue(input, "");
				input.dispatchEvent(new Event("cleared", { bubbles: true }));
				input.focus();
			} });
		} };
	}
	//#endregion
	//#region resources/js/components/color-picker.js
	var color_picker_exports = /* @__PURE__ */ __exportAll({ colorPicker: () => colorPicker });
	function colorPicker({ value = null, format = null } = {}) {
		const _bindableField = bindableField({ key: "color-picker" });
		return {
			..._bindableField,
			value,
			format: format ?? "hex",
			init() {
				_bindableField.init.call(this);
			},
			hasEyeDropper() {
				return typeof window !== "undefined" && "EyeDropper" in window;
			},
			pick(color) {
				if (this.field.disabled) return;
				const normalized = color ? normalizeColor(color, this.format) ?? color : null;
				this.value = normalized;
			},
			commitTyped(raw) {
				if (this.field.disabled) return;
				if (!raw) {
					this.pick(null);
					return;
				}
				const normalized = normalizeColor(raw, this.format);
				if (normalized) this.pick(normalized);
				else this.field.value = this.value ?? "";
			},
			async dropColor() {
				if (!this.hasEyeDropper() || this.field.disabled) return;
				try {
					const result = await new window.EyeDropper().open();
					this.pick(result.sRGBHex);
				} catch {}
			},
			clear() {
				this.pick(null);
			}
		};
	}
	//#endregion
	//#region resources/js/components/combobox.js
	var combobox_exports = /* @__PURE__ */ __exportAll({ combobox: () => combobox });
	function combobox({ value = null, multiple = false } = {}) {
		const _popover = popover({
			mode: "manual",
			position: "bottom",
			align: "start",
			matchTriggerWidth: true
		});
		const _listbox = listbox({
			hideEmpty: false,
			clearOnSelect: !multiple
		});
		const _bindableField = bindableField({
			key: "combobox-field",
			serialize() {
				return this.valueString();
			},
			deserialize(raw) {
				return multiple ? raw ? raw.split(",").filter(Boolean) : [] : raw;
			}
		});
		return {
			..._popover,
			..._listbox,
			..._bindableField,
			value: value ?? (multiple ? [] : null),
			combobox: null,
			selectedLabel() {
				if (multiple || this.value == null) return null;
				const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(this.value));
				return item ? item.el.querySelector("[data-item-content]")?.textContent?.trim() : null;
			},
			selectedCount() {
				return this.items.filter((item) => this.isSelected(this.getElementValue(item.el))).length;
			},
			valueString() {
				return multiple ? (this.value ?? []).join(",") : this.value ?? null;
			},
			init() {
				_popover.init.call(this);
				_listbox.init.call(this);
				this.combobox = this.$root.querySelector(dataKey("combobox"));
				_bindableField.init.call(this);
				bind(this.combobox, {
					["@click"]() {
						this.combobox.focus();
						this.toggle();
					},
					["@keydown.enter.prevent"]() {
						if (!this.opened) return this.open();
						this.select(this.index);
					},
					["@keydown.space.prevent"]() {
						if (!this.opened) return this.open();
						this.select(this.index);
					},
					["@keydown.arrow-up.prevent"]() {
						if (!this.opened) return this.open();
						this.lastInteraction = "keyboard";
						this.prev();
					},
					["@keydown.arrow-down.prevent"]() {
						if (!this.opened) return this.open();
						this.lastInteraction = "keyboard";
						this.next();
					}
				});
				bind([
					this.combobox,
					this.popoverElement,
					this.input,
					this.list
				], { ["@keydown.escape.prevent"]() {
					this.closeAndFocus();
				} });
				bind(this.$root, {
					["@click.outside"]() {
						this.close();
					},
					["@listbox-item-selected"]({ detail }) {
						this.pick(this.getElementValue(detail.button));
					},
					["@listbox-items-changed"]() {
						this.syncChecked();
					}
				});
				this.$watch("value", () => this.syncChecked());
				this.$nextTick(() => this.syncChecked());
			},
			open() {
				_popover.open.call(this, false);
				const target = multiple ? this.value.at(-1) : this.value;
				const index = this.filteredItems.findIndex((item) => String(this.getElementValue(item.el)) === String(target));
				this.index = index === -1 ? null : index;
				requestAnimationFrame(() => {
					this.input?.focus();
				});
			},
			close() {
				_popover.close.call(this);
				this.clear();
			},
			closeAndFocus() {
				this.close();
				this.combobox.focus();
			},
			isSelected(v) {
				if (!multiple) return String(this.value ?? "") === String(v);
				if (!Array.isArray(this.value) && this.value != null) this.value = [this.value];
				return this.value.map(String).includes(String(v));
			},
			pick(v) {
				if (multiple) this.value = this.isSelected(v) ? this.value.filter((x) => String(x) !== String(v)) : [...this.value, v];
				else {
					this.value = this.isSelected(v) ? null : v;
					this.closeAndFocus();
				}
			},
			remove(v) {
				if (!multiple) return;
				this.value = this.value.filter((x) => String(x) !== String(v));
			},
			clearValue() {
				this.value = multiple ? [] : null;
			},
			syncChecked() {
				this.items.forEach((item) => {
					const selected = this.isSelected(this.getElementValue(item.el));
					const mark = item.el.querySelector(dataKey("checkmark"));
					if (mark) mark.classList.toggle("invisible", !selected);
					item.li.setAttribute("aria-selected", String(selected));
				});
			},
			getElementValue(el) {
				return el.getAttribute("value") ?? el.textContent?.trim() ?? null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/composer.js
	var composer_exports = /* @__PURE__ */ __exportAll({ composer: () => composer });
	function composer({ submit = false, placeholder = false } = {}) {
		const _bindableField = bindableField({ key: "composer" });
		return {
			..._bindableField,
			value: null,
			init() {
				_bindableField.init.call(this);
				const modes = !submit ? [] : Array.isArray(submit) ? submit : [submit];
				const labelFor = findInField(this.$el.parentElement, "label")?.getAttribute("for") ?? null;
				bind(this.$el.querySelector(dataKey("control")), {
					"x-model": "value",
					...labelFor && { id: labelFor },
					...placeholder && { placeholder },
					...modes.length && { ["@keydown"](e) {
						if (!modes.some((mode) => {
							switch (mode) {
								case "enter": return e.key === "Enter" && !e.shiftKey && !e.ctrlKey && !e.metaKey;
								case "ctrl+enter": return e.key === "Enter" && (e.ctrlKey || e.metaKey);
								default: return false;
							}
						})) return;
						e.preventDefault();
						this.$root?.closest("form")?.requestSubmit();
					} }
				});
			}
		};
	}
	//#endregion
	//#region resources/js/components/copyable.js
	var copyable_exports = /* @__PURE__ */ __exportAll({ copyable: () => copyable });
	function copyable(targetId = null, content = null) {
		return {
			copied: false,
			timeout: null,
			findTarget() {
				if (targetId) {
					const target = document.getElementById(targetId);
					if (target) return target;
				}
				const controlKey = dataKey("control");
				return this.$el.closest(dataKey("field-control"))?.querySelector(controlKey) ?? this.$el.previousElementSibling?.querySelector(controlKey) ?? this.$el.parentElement?.previousElementSibling?.querySelector(controlKey) ?? null;
			},
			init() {
				if (!this.findTarget() && !content) {
					this.$el.remove();
					return;
				}
				if (!navigator.clipboard) {
					this.$el.disabled = true;
					return;
				}
				bind(this.$el, {
					[":aria-pressed"]() {
						return this.copied;
					},
					async ["@click"]() {
						clearTimeout(this.timeout);
						this.copied = true;
						this.$el.dispatchEvent(new CustomEvent("open"));
						const currentTarget = content ? null : this.findTarget();
						const text = content ?? ("value" in currentTarget ? currentTarget.value : currentTarget.innerText);
						await navigator.clipboard.writeText(text);
						currentTarget?.dispatchEvent(new Event("copied", { bubbles: true }));
						this.timeout = setTimeout(() => {
							this.$el.dispatchEvent(new CustomEvent("close"));
							this.copied = false;
							this.timeout = null;
						}, 1e3);
					}
				});
			},
			destroy() {
				clearTimeout(this.timeout);
			}
		};
	}
	//#endregion
	//#region resources/js/components/credit-card.js
	var credit_card_exports = /* @__PURE__ */ __exportAll({ creditCard: () => creditCard });
	function creditCard(types = {}, options = {}) {
		const _toggleable = toggleable();
		return {
			..._toggleable,
			types,
			options: {
				opened: true,
				holderName: null,
				number: null,
				type: null,
				expirationDate: null,
				cvv: null,
				...options
			},
			init() {
				_toggleable.init.call(this);
				this.opened = this.options.opened;
				bind(this.$el, {
					["@click"]() {
						this.toggle();
					},
					["@keydown.enter.prevent"]() {
						this.toggle();
					},
					["@keydown.space.prevent"]() {
						this.toggle();
					},
					[":class"]() {
						return { "rotate-y-180": !this.isOpened() };
					}
				});
			},
			typeOptions() {
				return this.types[this.options.type] ? this.types[this.options.type] : this.types.unknown;
			},
			update(options = {}) {
				this.options = {
					...this.options,
					...options
				};
				if ("opened" in options) this.opened = this.options.opened;
			},
			flip(isBack = false) {
				if (isBack) this.close();
				else this.open();
			}
		};
	}
	//#endregion
	//#region resources/js/components/date-picker.js
	var date_picker_exports = /* @__PURE__ */ __exportAll({ datePicker: () => datePicker });
	var DATE_STYLES = [
		"full",
		"long",
		"medium",
		"short"
	];
	var DEFAULT_FORMAT = "medium";
	function datePicker({ mode = null, multiple = null, format = null, labels = null, type = null, openTo = null, forceOpenTo = null, withConfirmation = null, ...calendarOptions } = {}) {
		if (format && !DATE_STYLES.includes(format)) {
			console.warn(`[tallkit] tk:date-picker received an invalid "format" ("${format}"). Expected one of: ${DATE_STYLES.join(", ")}. Falling back to "${DEFAULT_FORMAT}".`);
			format = DEFAULT_FORMAT;
		}
		const _popover = popover({
			mode: "dropdown",
			position: "bottom",
			align: "start"
		});
		const _calendar = calendar({
			mode,
			multiple,
			openTo,
			...calendarOptions
		});
		const _bindableField = bindableField({
			key: "date-picker",
			property: "committed",
			serialize() {
				return this.committedString();
			},
			deserialize(raw) {
				return this.parseInitialValue(raw);
			}
		});
		return {
			..._popover,
			..._calendar,
			..._bindableField,
			committed: null,
			typed: "",
			typing: false,
			init() {
				_popover.init.call(this);
				_calendar.init.call(this);
				this.committed = this.value;
				_bindableField.init.call(this);
				if (JSON.stringify(this.value) !== JSON.stringify(this.committed)) this.value = this.committed;
				this.syncTyped();
				this.$watch("value", () => {
					this.syncTyped();
					if (withConfirmation) return;
					this.committed = this.value;
					if (multiple) return;
					if (mode === "range" && !(this.value?.start && this.value?.end)) return;
					if (this.typing) return;
					this.close();
				});
				this.$watch("committed", () => {
					if (JSON.stringify(this.value) !== JSON.stringify(this.committed)) this.value = this.committed;
				});
				this.$watch("typed", () => {
					if (!this.typing) return;
					this.commitTyped();
				});
			},
			onOpen() {
				if (withConfirmation) this.value = this.committed;
				if (forceOpenTo && openTo) this.anchorMonth = startOfMonth(parseIso(openTo));
				_popover.onOpen.call(this);
			},
			apply() {
				this.committed = this.value;
				this.close();
			},
			cancel() {
				this.value = this.committed;
				this.close();
			},
			setSingleValue(iso) {
				if (mode === "range" || multiple) return;
				if (iso && this.isDayDisabled(iso)) return;
				this.value = iso || null;
				this.focused = this.value ?? this.focused;
				this.$dispatch("calendar-picked", { value: this.value });
			},
			formatted() {
				if (!this.value) return null;
				let fmt;
				try {
					fmt = new Intl.DateTimeFormat(this.locale, { dateStyle: format ?? DEFAULT_FORMAT });
				} catch {
					fmt = new Intl.DateTimeFormat(void 0, { dateStyle: DEFAULT_FORMAT });
				}
				if (mode === "range") {
					if (!this.value.start || !this.value.end) return null;
					const start = parseIso(this.value.start);
					const end = parseIso(this.value.end);
					return fmt.formatRange ? fmt.formatRange(start, end) : `${fmt.format(start)} – ${fmt.format(end)}`;
				}
				if (multiple) return this.value.length ? `${this.value.length} ${labels?.selected ?? "selected"}` : null;
				return fmt.format(parseIso(this.value));
			},
			committedString() {
				if (mode === "range") {
					if (!this.committed?.start) return null;
					return this.committed.end ? `${this.committed.start}/${this.committed.end}` : this.committed.start;
				}
				if (multiple) return (this.committed ?? []).join(",");
				return this.committed ?? null;
			},
			typable() {
				return type === "input" && !multiple;
			},
			maskPattern() {
				const single = localeDateOrder(this.locale).map((part) => part === "year" ? "9999" : "99").join("/");
				return mode === "range" ? `${single} – ${single}` : single;
			},
			requiredDigitCount() {
				return mode === "range" ? 16 : 8;
			},
			syncTyped() {
				if (!this.typable()) return;
				this.typed = this.formattedEditable();
			},
			formattedEditable() {
				if (mode === "range") {
					const start = this.value?.start ? formatEditable(this.value.start, this.locale) : "";
					const end = this.value?.end ? formatEditable(this.value.end, this.locale) : "";
					if (!start && !end) return "";
					return `${start} – ${end}`;
				}
				return this.value ? formatEditable(this.value, this.locale) : "";
			},
			commitTyped() {
				if (!this.typable()) return;
				if ((this.typed.match(/\d/g) ?? []).length < this.requiredDigitCount()) return;
				if (mode === "range") {
					const [rawStart, rawEnd] = this.typed.split(/\s*[–—]\s*/);
					const start = parseTypedDate(rawStart, this.locale);
					const end = parseTypedDate(rawEnd, this.locale);
					if (start) {
						this.setRangeBound("start", start);
						this.anchorMonth = startOfMonth(parseIso(start));
					}
					if (end) {
						this.setRangeBound("end", end);
						this.anchorMonth = startOfMonth(parseIso(end));
					}
				} else {
					const iso = parseTypedDate(this.typed, this.locale);
					if (iso && !this.isDayDisabled(iso)) {
						this.value = iso;
						this.focused = iso;
						this.anchorMonth = startOfMonth(parseIso(iso));
						this.$dispatch("calendar-picked", { value: iso });
					}
				}
			},
			confirmTyped() {
				this.commitTyped();
				this.typing = false;
				this.syncTyped();
				if (!withConfirmation) this.close();
			},
			onFieldBlur(event) {
				if (!this.$root.contains(event.relatedTarget)) {
					this.confirmTyped();
					return;
				}
				this.commitTyped();
				this.typing = false;
				this.syncTyped();
			},
			presetRange(key) {
				if (mode !== "range") return null;
				const today = isoOf(/* @__PURE__ */ new Date());
				const todayDate = parseIso(today);
				switch (key) {
					case "today": return {
						start: today,
						end: today
					};
					case "yesterday": {
						const yesterday = addDays(today, -1);
						return {
							start: yesterday,
							end: yesterday
						};
					}
					case "thisWeek": return {
						start: startOfWeek(todayDate, this.startDay),
						end: today
					};
					case "last7Days": return {
						start: addDays(today, -6),
						end: today
					};
					case "last14Days": return {
						start: addDays(today, -13),
						end: today
					};
					case "last30Days": return {
						start: addDays(today, -29),
						end: today
					};
					case "thisMonth": return {
						start: isoOf(startOfMonth(todayDate)),
						end: isoOf(endOfMonth(todayDate))
					};
					case "lastMonth": {
						const lastMonth = addMonths(todayDate, -1);
						return {
							start: isoOf(startOfMonth(lastMonth)),
							end: isoOf(endOfMonth(lastMonth))
						};
					}
					case "thisYear": return {
						start: `${todayDate.getFullYear()}-01-01`,
						end: `${todayDate.getFullYear()}-12-31`
					};
					case "lastYear": return {
						start: `${todayDate.getFullYear() - 1}-01-01`,
						end: `${todayDate.getFullYear() - 1}-12-31`
					};
					default: return null;
				}
			},
			isPresetActive(key) {
				const range = this.presetRange(key);
				if (!range) return false;
				return this.value?.start === range.start && this.value?.end === range.end;
			},
			applyPreset(key) {
				const range = this.presetRange(key);
				if (!range) return;
				this.value = range;
				this.focused = range.end;
				this.$dispatch("calendar-picked", { value: range });
			}
		};
	}
	//#endregion
	//#region resources/js/components/disclosure-group.js
	var disclosure_group_exports = /* @__PURE__ */ __exportAll({ disclosureGroup: () => disclosureGroup });
	function disclosureGroup({ exclusive = false } = {}) {
		return {
			observer: null,
			init() {
				const getItems = () => Array.from(this.$root.querySelectorAll(dataKey("disclosure-item")));
				const observe = () => {
					getItems().forEach((item) => {
						this.observer.observe(item, { attributeFilter: ["data-open"] });
					});
				};
				this.observer = new MutationObserver((records) => {
					const items = getItems();
					if (exclusive) {
						const opened = new Set(records.filter((record) => record.target.hasAttribute("data-open")).map((record) => record.target));
						items.forEach((item) => {
							if (opened.has(item)) return;
							item.removeAttribute("data-open");
						});
					}
					this.observer.disconnect();
					this.$dispatch("changed", { items });
					this.$nextTick(observe);
				});
				observe();
			},
			destroy() {
				this.observer?.disconnect();
			}
		};
	}
	//#endregion
	//#region resources/js/components/disclosure.js
	var disclosure_exports = /* @__PURE__ */ __exportAll({ disclosure: () => disclosure });
	function disclosure() {
		const _toggleable = toggleable();
		return {
			..._toggleable,
			observer: null,
			init() {
				_toggleable.init.call(this, this.$root.hasAttribute("data-open"));
				const panel = this.$root.querySelector(":scope > button + *");
				if (panel && !panel.id) panel.id = generateId("disclosure");
				this.observer = new MutationObserver(() => {
					this.opened = this.$root.hasAttribute("data-open");
				});
				this.observer.observe(this.$root, { attributeFilter: ["data-open"] });
				bind(this.$root.querySelectorAll(":scope > button"), {
					[":aria-controls"]() {
						return panel?.id ?? null;
					},
					[":aria-expanded"]() {
						return String(this.opened);
					},
					["@click"]() {
						this.toggle();
					}
				});
			},
			open() {
				this.$root.setAttribute("data-open", "");
				_toggleable.open.call(this);
			},
			close() {
				this.$root.removeAttribute("data-open");
				_toggleable.close.call(this);
			},
			destroy() {
				this.observer?.disconnect();
			}
		};
	}
	//#endregion
	//#region resources/js/components/echarts.js
	var echarts_exports = /* @__PURE__ */ __exportAll({ echarts: () => echarts });
	function echarts() {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			chart: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.echarts, "https://cdn.jsdelivr.net/npm/echarts@6"));
			},
			render(options = {}) {
				try {
					this.chart ??= window.echarts.init(this.$refs.target);
					this.chart.setOption({
						...options,
						...this.getDataOptions(this.$refs.target)
					});
					this.$dispatch("rendered", { chart: this.chart });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.chart?.dispose();
				this.chart = null;
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/editor.js
	var EDITOR_GROUP_ORDER = [
		"text",
		"heading",
		"color",
		"size",
		"script",
		"align",
		"link",
		"list",
		"media",
		"table",
		"quote",
		"code"
	];
	function parseMode(mode, groupOrder) {
		const tokens = (mode ?? "").trim().split(/\s+/).filter(Boolean);
		if (!tokens.length) return null;
		if (tokens.includes("none")) return [];
		tokens.filter((token) => token !== "full" && !groupOrder.includes(token)).forEach((token) => console.warn(`[tallkit] Unknown editor mode group "${token}"`));
		if (tokens.includes("full")) return groupOrder;
		return groupOrder.filter((group) => tokens.includes(group));
	}
	function editorField() {
		return {
			input: null,
			_lastSynced: null,
			initField() {
				this.input = this.$root.querySelector(dataKey("control"));
				if (this.$wire) {
					const prop = getWireModelInfo(this.input);
					if (prop) this.$wire.$watch(prop.name, (value) => {
						if (value === this._lastSynced || !this.isCompleted()) return;
						this.applyExternalValue(value);
					});
				}
			},
			sync(value) {
				this._lastSynced = value;
				setFieldValue(this.input, value);
			}
		};
	}
	//#endregion
	//#region resources/js/components/editorjs.js
	var editorjs_exports = /* @__PURE__ */ __exportAll({ editorjs: () => editorjs });
	var GROUPS$3 = {
		text: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/inline-code@1", "https://cdn.jsdelivr.net/npm/@editorjs/underline@1"],
			inline: [
				"bold",
				"italic",
				"underline",
				"inlineCode"
			],
			tools: () => ({
				inlineCode: window.InlineCode,
				underline: window.Underline
			})
		},
		heading: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/header@2"],
			tools: () => ({ heading: window.Header })
		},
		color: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/marker@1"],
			inline: ["marker"],
			tools: () => ({ marker: window.Marker })
		},
		link: {
			scripts: [],
			inline: ["link"],
			tools: () => ({})
		},
		list: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/list@2"],
			tools: () => ({ list: {
				class: window.EditorjsList,
				inlineToolbar: true
			} })
		},
		media: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/simple-image@1", "https://cdn.jsdelivr.net/npm/@editorjs/embed@2"],
			tools: () => ({
				simpleImage: window.SimpleImage,
				embed: window.Embed
			})
		},
		table: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/table@2"],
			tools: () => ({ table: window.Table })
		},
		quote: {
			scripts: [
				"https://cdn.jsdelivr.net/npm/@editorjs/quote@2",
				"https://cdn.jsdelivr.net/npm/@editorjs/warning@1",
				"https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1"
			],
			tools: () => ({
				quote: {
					class: window.Quote,
					inlineToolbar: true
				},
				warning: window.Warning,
				delimiter: window.Delimiter
			})
		},
		code: {
			scripts: ["https://cdn.jsdelivr.net/npm/@editorjs/code@2", "https://cdn.jsdelivr.net/npm/@editorjs/raw@2"],
			tools: () => ({
				code: window.CodeTool,
				raw: window.RawTool
			})
		}
	};
	function editorjs({ options = {}, scripts = [], styles = [], mode = null } = {}) {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			editor: null,
			_saveToken: 0,
			init() {
				this.initField();
				const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
				this.load(() => loadRemoteAssets(() => !!window.EditorJS, [
					"https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2",
					...groups.flatMap((group) => GROUPS$3[group]?.scripts ?? []),
					...scripts
				], styles).then(() => this.mount(groups)));
			},
			applyExternalValue(value) {
				this.editor.render(value ? JSON.parse(value) : { blocks: [] });
			},
			mount(groups) {
				try {
					this.editor = new window.EditorJS({
						holder: this.$refs.root,
						tools: groups.reduce((tools, group) => ({
							...tools,
							...GROUPS$3[group]?.tools()
						}), {}),
						inlineToolbar: groups.flatMap((group) => GROUPS$3[group]?.inline ?? []),
						data: this.input.value ? JSON.parse(this.input.value) : void 0,
						onChange: async (api) => {
							const token = ++this._saveToken;
							const output = await api.saver.save();
							if (token !== this._saveToken) return;
							this.sync(JSON.stringify(output));
						},
						...options,
						...this.getDataOptions(this.$refs.root)
					});
					this.editor.isReady.then(() => this.$dispatch("rendered", { editor: this.editor }));
				} catch (e) {
					this.fail(e);
				}
			},
			async destroy() {
				_loadable.destroy.call(this);
				await this.editor?.destroy();
				this.editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/fetchable.js
	var fetchable_exports = /* @__PURE__ */ __exportAll({ fetchable: () => fetchable });
	function fetchable({ url = null, data = null, auto = null, options = {} } = {}) {
		const _loadable = loadable();
		return {
			..._loadable,
			url: null,
			response: null,
			data: null,
			options: null,
			_controller: null,
			init() {
				this.clear();
				this.url = url;
				this.data = data;
				this.options = {
					method: "get",
					headers: { Accept: "application/json" },
					responseType: "json",
					...options
				};
				if (this.url && auto !== false) this.fetch();
				if (!this.url && this.data) this.complete();
			},
			async fetch(url = null, options = {}, silent = false) {
				const _url = url || this.url;
				const _options = {
					...this.options ?? {},
					...options,
					headers: {
						...this.options?.headers ?? {},
						...options.headers ?? {}
					}
				};
				this.url = _url;
				this.options = _options;
				if (!_url) return;
				this._controller?.abort();
				const controller = new AbortController();
				this._controller = controller;
				this.load(async () => {
					this.response = await window.fetch(_url, {
						..._options,
						signal: controller.signal
					});
					if (!this.response.ok) throw new Error(this.response.statusText);
					this.data = _options.responseType ? await this.response[_options.responseType]() : this.response;
				}, silent);
			},
			reload() {
				return this.fetch();
			},
			update(url = null, options = {}) {
				return this.fetch(url, options, true);
			},
			destroy() {
				_loadable.destroy.call(this);
				this._controller?.abort();
				this._controller = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/form.js
	var form_exports = /* @__PURE__ */ __exportAll({ form: () => form });
	function form({ action = null, focusError = null, clearErrorsOnSubmit = null, toast = null, errorMessage = null, successMessage = null } = {}) {
		return {
			livewireCommitCleanup: null,
			init() {
				if (hasLivewire()) this.watchLivewireCommits();
				else if (focusError) this.focusFirstInvalidField();
			},
			watchLivewireCommits() {
				this.livewireCommitCleanup = onLivewireCommit(({ component, commit, succeed }) => {
					if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return;
					if (action && !commit?.calls?.some((call) => call.method === action)) return;
					if (clearErrorsOnSubmit) this.clearErrors();
					succeed(({ snapshot }) => {
						if (!this.$el?.isConnected) return;
						const id = this.$el?.getAttribute("id") ?? component?.el.getAttribute("wire:id") ?? void 0;
						if (Object.keys(snapshot?.memo?.errors ?? {}).length > 0 || !!this.$el.querySelector("[data-invalid], [aria-invalid=\"true\"]")) {
							if ((toast === true || toast === "error") && errorMessage) this.$tallkit.toast().error({
								message: errorMessage,
								id,
								duration: 3e3
							});
							if (focusError) this.focusFirstInvalidField();
							return;
						}
						if ((toast === true || toast === "success") && successMessage) {
							this.$tallkit.toast().success({
								message: successMessage,
								id,
								duration: 3e3
							});
							return;
						}
					});
				});
			},
			clearErrors() {
				this.$el.querySelectorAll("[data-invalid], [aria-invalid=\"true\"]").forEach((field) => {
					field.removeAttribute("data-invalid");
					field.removeAttribute("aria-invalid");
				});
				this.$el.querySelectorAll("[data-tallkit-error], [data-tallkit-error-group]").forEach((el) => el.remove());
			},
			focusFirstInvalidField() {
				const field = this.$el.querySelector("[data-invalid], [aria-invalid=\"true\"]");
				if (!(field instanceof HTMLElement)) return;
				field.scrollIntoView({
					behavior: "smooth",
					block: "center"
				});
				field.focus({ preventScroll: true });
			},
			destroy() {
				this.livewireCommitCleanup?.();
			}
		};
	}
	//#endregion
	//#region resources/js/components/frappe-charts.js
	var frappe_charts_exports = /* @__PURE__ */ __exportAll({ frappeCharts: () => frappeCharts });
	function frappeCharts() {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			chart: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.frappe?.Chart, "https://cdn.jsdelivr.net/npm/frappe-charts@1"));
			},
			render(options = {}) {
				try {
					this.chart?.destroy?.();
					this.chart = new window.frappe.Chart(this.$refs.target, {
						...options,
						...this.getDataOptions(this.$refs.target)
					});
					this.$dispatch("rendered", { chart: this.chart });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.chart?.destroy?.();
				this.chart = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/full-calendar.js
	var full_calendar_exports = /* @__PURE__ */ __exportAll({ fullCalendar: () => fullCalendar });
	function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			fullCalendar: null,
			init() {
				const baseUrl = "https://cdn.jsdelivr.net/npm/fullcalendar@7";
				const scripts = [`${baseUrl}/all/global.min.js`];
				if (locale && locale !== "en") scripts.push(`${baseUrl}/locales/${String(locale).replace("_", "-").toLowerCase()}/global.min.js`);
				scripts.push(`${baseUrl}/themes/${theme ?? "monarch"}/global.js`);
				this.load(() => loadRemoteAssets(() => !!window.FullCalendar, scripts, [
					`${baseUrl}/skeleton.css`,
					`${baseUrl}/themes/${theme ?? "monarch"}/theme.css`,
					`${baseUrl}/themes/${theme ?? "monarch"}/palettes/${palette ?? "blue"}.css`
				]));
			},
			render() {
				try {
					this.fullCalendar?.destroy();
					this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
						locale,
						...options,
						...this.getDataOptions()
					});
					this.fullCalendar.render();
					this.$dispatch("rendered", { fullCalendar: this.fullCalendar });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.fullCalendar?.destroy();
				this.fullCalendar = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/header.js
	var header_exports = /* @__PURE__ */ __exportAll({ header: () => header });
	function header() {
		return { ...sticky() };
	}
	//#endregion
	//#region resources/js/components/highlightjs.js
	var highlightjs_exports = /* @__PURE__ */ __exportAll({ highlightjs: () => highlightjs });
	function highlightjs() {
		return {
			...loadable(),
			language: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.hljs, "https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js", "https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css"));
			},
			render(code, language = null) {
				try {
					const result = language ? window.hljs.highlight(code, { language }) : window.hljs.highlightAuto(code);
					this.language = result.language ?? null;
					return result.value;
				} catch (e) {
					return escapeHtml(code) ?? "";
				}
			}
		};
	}
	//#endregion
	//#region resources/js/components/input-viewable.js
	var input_viewable_exports = /* @__PURE__ */ __exportAll({ inputViewable: () => inputViewable });
	function inputViewable() {
		return {
			viewed: false,
			inputObserver: null,
			originalType: "password",
			init() {
				const input = findFieldInput(this.$el);
				if (!input) return;
				if (input.type) this.originalType = input.type;
				input.setAttribute("type", this.viewed ? "text" : this.originalType);
				bind(this.$el, {
					[":aria-pressed"]() {
						return this.viewed;
					},
					["@click"]() {
						this.viewed = !this.viewed;
						input.setAttribute("type", this.viewed ? "text" : this.originalType);
						input.dispatchEvent(new Event("viewed", { bubbles: true }));
					}
				});
				this.inputObserver = new MutationObserver(() => {
					this.viewed = input?.getAttribute("type") !== "password";
				});
				this.inputObserver.observe(input, {
					attributes: true,
					attributeFilter: ["type"]
				});
			},
			destroy() {
				this.inputObserver?.disconnect();
			}
		};
	}
	//#endregion
	//#region resources/js/components/label.js
	var label_exports = /* @__PURE__ */ __exportAll({ label: () => label });
	function label() {
		return { init() {
			if (this.$el.tagName.toLowerCase() === "label" && this.$el.hasAttribute("for") && !!document.getElementById(this.$el.getAttribute("for"))) return;
			let control = findInField(this.$el.parentElement, "control");
			if (control && !control.matches("input, select, textarea, [contenteditable=\"\"], [contenteditable=\"true\"], [role=\"textbox\"]")) control = control.querySelector("input, select, textarea, [contenteditable=\"\"], [contenteditable=\"true\"], [role=\"textbox\"]");
			if (!control) return;
			bind(this.$el, { ["@click"]() {
				const tag = control.tagName.toLowerCase();
				const type = control.getAttribute("type")?.toLowerCase();
				const isEditable = control.hasAttribute("contenteditable") || control.getAttribute("role") === "textbox";
				const isReadOnly = control.hasAttribute("readonly") || control.getAttribute("aria-readonly") === "true";
				const isDisabled = control.disabled;
				if (type === "checkbox") {
					if (!isDisabled && !isReadOnly) setFieldChecked(control, !control.checked);
					return;
				}
				if (type === "radio") {
					if (!isDisabled && !isReadOnly && !control.checked) setFieldChecked(control, true);
					return;
				}
				if ((isEditable || [
					"input",
					"select",
					"textarea"
				].includes(tag)) && typeof control.focus === "function" && !isDisabled) control.focus();
			} });
		} };
	}
	//#endregion
	//#region resources/js/mixins/menu-item.js
	function menuItem(checked, type) {
		return {
			checked,
			value: void 0,
			isControlled() {
				return this.value !== void 0;
			},
			isArray() {
				return type === "checkbox" && Array.isArray(this.value);
			},
			isChecked() {
				if (!this.isControlled()) return this.checked;
				if (this.isArray()) return this.value.some((v) => v == this.$root.value);
				return this.value == this.$root.value;
			},
			init() {
				bind(this.$el, {
					["@click"]: () => this.toggle(),
					[":data-checked"]: () => this.isChecked(),
					[":aria-checked"]: () => this.isChecked()
				});
			},
			toggle() {
				if (!this.isControlled()) {
					this.checked = !this.checked;
					return;
				}
				if (this.isArray()) {
					this.value = this.isChecked() ? this.value.filter((v) => v != this.$root.value) : [...this.value, this.$root.value];
					return;
				}
				if (type === "radio") {
					this.value = this.$root.value;
					return;
				}
				this.value = this.isChecked() ? null : this.$root.value;
			}
		};
	}
	//#endregion
	//#region resources/js/components/menu-checkbox.js
	var menu_checkbox_exports = /* @__PURE__ */ __exportAll({ menuCheckbox: () => menuCheckbox });
	function menuCheckbox(checked) {
		return menuItem(checked, "checkbox");
	}
	//#endregion
	//#region resources/js/components/menu-radio.js
	var menu_radio_exports = /* @__PURE__ */ __exportAll({ menuRadio: () => menuRadio });
	function menuRadio(checked) {
		return menuItem(checked, "radio");
	}
	//#endregion
	//#region resources/js/components/menu.js
	var menu_exports = /* @__PURE__ */ __exportAll({ menu: () => menu });
	function menu() {
		return {
			init() {
				const items = Array.from(this.$el.querySelectorAll(dataKey("menu-item"))).filter((item) => item.closest(dataKey("menu")) === this.$el);
				bind(items, {
					["@mouseenter"]() {
						if (this.$el.disabled) return;
						this.$el.setAttribute("data-active", "");
					},
					["@mouseleave"]() {
						if (this.$el.disabled) return;
						this.$el.removeAttribute("data-active");
					},
					["@focus"]() {
						if (this.$el.disabled) return;
						this.$el.setAttribute("data-active", "");
					},
					["@blur"]() {
						this.$el.removeAttribute("data-active");
					}
				});
				bind(this.$el, {
					["@keydown.arrow-down.prevent"]() {
						this.focusItem(items, 1);
					},
					["@keydown.arrow-up.prevent"]() {
						this.focusItem(items, -1);
					},
					["@keydown.home.prevent"]() {
						this.focusItem(items, "first");
					},
					["@keydown.end.prevent"]() {
						this.focusItem(items, "last");
					}
				});
			},
			focusItem(items, direction) {
				const enabled = items.filter((item) => !item.disabled);
				if (!enabled.length) return;
				const currentIndex = enabled.indexOf(document.activeElement);
				let index;
				if (direction === "first") index = 0;
				else if (direction === "last") index = enabled.length - 1;
				else if (currentIndex === -1) index = direction === 1 ? 0 : enabled.length - 1;
				else index = (currentIndex + direction + enabled.length) % enabled.length;
				enabled[index].focus();
			}
		};
	}
	//#endregion
	//#region resources/js/components/modal-trigger.js
	var modal_trigger_exports = /* @__PURE__ */ __exportAll({ modalTrigger: () => modalTrigger });
	function modalTrigger({ name = null, shortcut = null } = {}) {
		return { init() {
			bind(this.$el, { ["@click"]() {
				if (this.$el.querySelector("button[disabled]")) return;
				this.$dispatch("modal-show", { name });
			} });
			if (shortcut) bindShortcut(this.$el, shortcut, () => this.$dispatch("modal-show", { name }));
		} };
	}
	//#endregion
	//#region resources/js/components/modal.js
	var modal_exports = /* @__PURE__ */ __exportAll({ modal: () => modal });
	function modal({ name = null, dismissible = null, persist = null, shortcut = null } = {}) {
		return {
			init() {
				const dialog = this.$el;
				bind(dialog, {
					["@modal-show.document"](event) {
						if (event.detail.name === name && !event.detail.scope) {
							dialog.showModal();
							return;
						}
						if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
							dialog.showModal();
							return;
						}
					},
					["@modal-close.document"](event) {
						if (!event.detail.name || event.detail.name === name && !event.detail.scope) {
							dialog.close();
							return;
						}
						if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
							dialog.close();
							return;
						}
					}
				});
				const handleCloseAttempt = (event, checkTarget = true) => {
					event.preventDefault();
					if (persist) {
						const persistAnimation = typeof persist === "string" ? persist : "tilt-shaking";
						dialog.classList.remove(persistAnimation);
						dialog.focus();
						this.$nextTick(() => dialog.classList.add(persistAnimation));
						return;
					}
					if (dismissible === false) return;
					const target = event.target;
					if (checkTarget && target !== dialog && target.getAttribute("tabindex") !== "0") return;
					dialog.close();
				};
				bind(dialog, {
					["@toggle"](event) {
						if (event.newState === "open") {
							dialog.querySelector("[tabindex=\"0\"]")?.focus();
							this.$dispatch("opened", event);
						}
						if (event.newState === "closed") this.$dispatch("closed", event);
					},
					["@click"](event) {
						if (event.target.closest(`${dataKey("modal-close")},${dataKey("modal-auto-close")}`)) {
							dialog.close();
							return;
						}
						handleCloseAttempt(event);
					},
					["@keydown.escape.prevent"](event) {
						handleCloseAttempt(event, false);
					}
				});
				if (shortcut) bindShortcut(dialog, shortcut, () => this.$dispatch("modal-show", { name }));
			},
			show() {
				this.$dispatch("modal-show", { name });
			},
			close() {
				this.$dispatch("modal-close", { name });
			}
		};
	}
	//#endregion
	//#region resources/js/components/nav-indicator.js
	var nav_indicator_exports = /* @__PURE__ */ __exportAll({ navIndicator: () => navIndicator });
	function navIndicator({ mode = null } = {}) {
		return {
			_visibilityTimeout: null,
			init() {
				this._onMove = this.move.bind(this);
				document.addEventListener("livewire:navigated", this._onMove);
				window.addEventListener("resize", this._onMove);
				this.$nextTick(() => this.move());
			},
			destroy() {
				document.removeEventListener("livewire:navigated", this._onMove);
				window.removeEventListener("resize", this._onMove);
				clearTimeout(this._visibilityTimeout);
			},
			findNav(el) {
				let node = el;
				while (node) {
					const sibling = node.previousElementSibling;
					if (sibling?.matches(dataKey("nav"))) return sibling;
					node = node.parentElement;
				}
				return null;
			},
			move() {
				requestAnimationFrame(() => {
					const indicator = this.$el;
					const nav = this.findNav(indicator);
					const link = nav?.querySelector("a[data-current]");
					if (!link) return;
					const indicatorRect = indicator.getBoundingClientRect();
					const linkRect = link.getBoundingClientRect();
					const x = link.offsetLeft + nav.offsetLeft;
					const y = link.offsetTop + nav.offsetTop;
					if (linkRect.width <= 0 || linkRect.height <= 0) {
						indicator.style.opacity = "0";
						return;
					}
					indicator.style.opacity = "1";
					if (indicatorRect.width <= 0 || indicatorRect.height <= 0 || indicatorRect.top <= 0 || indicatorRect.left <= 0) {
						indicator.style.visibility = "hidden";
						clearTimeout(this._visibilityTimeout);
						this._visibilityTimeout = setTimeout(() => {
							indicator.style.visibility = "visible";
							this._visibilityTimeout = null;
						}, getTransitionTimeout(indicator));
					}
					if (mode === "line-left" || mode === "line-right") {
						indicator.style.height = `${linkRect.height}px`;
						indicator.style.transform = `translate(${x + (mode === "line-left" ? -10 : linkRect.width + 10)}px, ${y}px)`;
						return;
					}
					if (mode === "line-top" || mode === "line-bottom") {
						indicator.style.width = `${linkRect.width}px`;
						indicator.style.transform = `translate(${x}px, ${y + (mode === "line-top" ? -10 : linkRect.height + 10)}px)`;
						return;
					}
					const style = getComputedStyle(link);
					indicator.style.transform = `translate(${x}px, ${y}px)`;
					indicator.style.width = `${linkRect.width}px`;
					indicator.style.height = `${linkRect.height}px`;
					indicator.style.borderRadius = style.borderRadius;
				});
			}
		};
	}
	//#endregion
	//#region resources/js/components/notification-item.js
	var notification_item_exports = /* @__PURE__ */ __exportAll({ notificationItem: () => notificationItem });
	function notificationItem() {
		return { ...dismissible("collapse") };
	}
	//#endregion
	//#region resources/js/components/notification.js
	var notification_exports = /* @__PURE__ */ __exportAll({ notification: () => notification });
	function notification({ channel = null } = {}) {
		return {
			init() {
				bind(this.$el.querySelectorAll(dataKey("notification-mark-all")), { ["@click"](e) {
					(e.currentTarget.closest("[role=tabpanel]") ?? this.$el).querySelectorAll(dataKey("notification-item")).forEach((el) => el.dispatchEvent(new CustomEvent("dismiss")));
				} });
				if (!channel || !window.Echo || !this.$wire) return;
				window.Echo.private(channel).notification(() => {
					this.$wire.$refresh();
				});
			},
			destroy() {
				if (channel && window.Echo) window.Echo.leave(channel);
			}
		};
	}
	//#endregion
	//#region resources/js/components/otp.js
	var otp_exports = /* @__PURE__ */ __exportAll({ otp: () => otp });
	function otp(submit) {
		const _bindableField = bindableField({
			key: "otp-field",
			deserialize(raw) {
				return raw || "";
			}
		});
		return {
			..._bindableField,
			value: "",
			inputs: [],
			_syncing: false,
			init() {
				this.inputs = Array.from(this.$root.querySelectorAll("input[data-mode]"));
				_bindableField.init.call(this);
				this.$nextTick(() => {
					this.syncFromModel();
					this.updateModel();
				});
				this.$watch("value", (val) => {
					this.syncFromModel(val);
					this.updateModel();
				});
				this.inputs.forEach((input, index) => {
					bind(input, this.bindings(input, index, this.inputs));
				});
			},
			bindings(input, index, inputs) {
				return {
					["@focus"]: () => this.handleFocus(input, index, inputs),
					["@blur"]: () => this.$dispatch("otp-blur", {
						input,
						index
					}),
					["@paste.prevent"]: (e) => this.handlePaste(e, index, inputs),
					["@input"]: () => this.handleInput(input, index, inputs),
					["@keydown"]: (e) => this.handleKeydown(e, input, index, inputs),
					["@keydown.arrow-left.prevent"]: () => inputs[index - 1]?.select(),
					["@keydown.arrow-right.prevent"]: () => inputs[index + 1]?.select(),
					["@keydown.backspace.prevent"]: () => this.handleBackspace(input, index, inputs)
				};
			},
			handleFocus(input, index, inputs) {
				if (input.value) {
					input.select();
					this.$dispatch("otp-focus", {
						input,
						index
					});
					return;
				}
				const firstEmpty = inputs.find((i) => !i.value);
				firstEmpty?.select();
				this.$dispatch("otp-focus", {
					input: firstEmpty || input,
					index: inputs.indexOf(firstEmpty || input)
				});
			},
			handlePaste(e, index, inputs) {
				const pasted = e.clipboardData?.getData("text") ?? "";
				this._syncing = true;
				try {
					spreadValue(pasted, index, inputs);
				} finally {
					this._syncing = false;
				}
				this.updateModel();
				this.$dispatch("otp-paste", {
					pasted,
					index
				});
			},
			handleInput(input, index, inputs) {
				if (this._syncing) return;
				const mode = input.dataset.mode;
				const filtered = filterValue(input.value, mode);
				if (filtered.length > 1) spreadValue(filtered, index, inputs);
				else {
					input.value = filtered;
					if (filtered) inputs[index + 1]?.focus();
				}
				this.updateModel();
			},
			handleKeydown(e, input, _index, _inputs) {
				if (e.ctrlKey || e.metaKey || e.altKey) return;
				const mode = input.dataset.mode;
				if (!isValidKey(e.key, mode)) e.preventDefault();
			},
			handleBackspace(input, index, inputs) {
				if (input.value) {
					this._syncing = true;
					setFieldValue(input, "");
					this._syncing = false;
				} else inputs[index - 1]?.select();
				this.updateModel();
			},
			syncFromModel(val) {
				val ??= this.value;
				const chars = String(val).padEnd(this.inputs.length).split("");
				this._syncing = true;
				try {
					this.inputs.forEach((input, i) => {
						const mode = input.dataset.mode;
						setFieldValue(input, filterValue(chars[i] ?? "", mode));
					});
				} finally {
					this._syncing = false;
				}
			},
			updateModel() {
				const values = this.inputs.map((i) => i.value || "");
				this.value = values.join("");
				const filled = values.filter(Boolean).length;
				this.$dispatch("otp-change", { value: this.value });
				if (filled === this.inputs.length) {
					this.$dispatch("otp-complete", { value: this.value });
					if (submit === "auto") this.$root.closest("form")?.requestSubmit();
					else if (submit && hasLivewire()) window.Livewire.dispatch(submit, this.value);
				} else this.$dispatch("otp-incomplete", { value: this.value });
				if (filled === 0) this.$dispatch("otp-clear");
			}
		};
	}
	function filterValue(value, mode = "numeric") {
		return (value.toUpperCase().match({
			numeric: /[0-9]/g,
			alpha: /[A-Z]/g,
			alphanumeric: /[A-Z0-9]/g
		}[mode]) || []).join("");
	}
	function isValidKey(key, mode) {
		if ([
			"Backspace",
			"Delete",
			"Tab",
			"ArrowLeft",
			"ArrowRight"
		].includes(key)) return true;
		return filterValue(key, mode).length > 0;
	}
	function spreadValue(value, start, inputs) {
		const chars = value.split("");
		chars.forEach((char, i) => {
			const input = inputs[start + i];
			if (!input) return;
			const mode = input.dataset.mode;
			setFieldValue(input, filterValue(char, mode));
		});
		inputs[Math.min(start + chars.length, inputs.length - 1)]?.focus();
	}
	//#endregion
	//#region resources/js/components/pretty-print-json.js
	var pretty_print_json_exports = /* @__PURE__ */ __exportAll({ prettyPrintJson: () => prettyPrintJson });
	function prettyPrintJson() {
		return {
			...loadable(),
			init() {
				this.load(() => loadRemoteAssets(() => !!window.prettyPrintJson, "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js", "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css"));
			},
			render(data = null, options = null) {
				try {
					if (typeof data === "string") data = JSON.parse(data);
					return window.prettyPrintJson.toHtml(data, options || {});
				} catch (e) {
					return escapeHtml(typeof data === "string" ? data : JSON.stringify(data, null, 2)) ?? "";
				}
			}
		};
	}
	//#endregion
	//#region resources/js/components/progress.js
	var progress_exports = /* @__PURE__ */ __exportAll({ progress: () => progress });
	function progress(percentage = null) {
		return {
			value: 0,
			init() {
				this.updateValue(percentage ?? 0);
			},
			updateValue(n) {
				const num = Number(n);
				if (Number.isNaN(num)) return;
				this.value = Math.max(0, Math.min(100, num));
			}
		};
	}
	//#endregion
	//#region resources/js/components/quill.js
	var quill_exports = /* @__PURE__ */ __exportAll({ quill: () => quill });
	var GROUPS$2 = {
		text: [[
			"bold",
			"italic",
			"underline",
			"strike"
		]],
		heading: [[{ header: [
			1,
			2,
			3,
			4,
			5,
			6,
			false
		] }]],
		color: [[{ color: [] }, { background: [] }]],
		size: [[{ size: [
			"small",
			false,
			"large",
			"huge"
		] }]],
		script: [[{ script: "sub" }, { script: "super" }]],
		align: [
			[{ align: [] }],
			[{ indent: "-1" }, { indent: "+1" }],
			[{ direction: "rtl" }]
		],
		link: [["link", "formula"]],
		list: [[
			{ list: "ordered" },
			{ list: "bullet" },
			{ list: "check" }
		]],
		media: [["image", "video"]],
		quote: [["blockquote"]],
		code: [["code-block"]]
	};
	function resolveToolbar(mode) {
		const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
		if (!groups.length) return false;
		return [...groups.flatMap((group) => GROUPS$2[group] ?? []), ["clean"]];
	}
	function quill({ options = {}, scripts = [], styles = [], mode = null } = {}) {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			editor: null,
			init() {
				this.initField();
				this.load(() => loadRemoteAssets(() => !!window.Quill && !!window.DOMPurify, [
					"https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js",
					"https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js",
					...scripts
				], ["https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css", ...styles]).then(() => this.mount()));
			},
			applyExternalValue(value) {
				this.editor.clipboard.dangerouslyPasteHTML(window.DOMPurify.sanitize(value ?? ""));
			},
			mount() {
				try {
					this.editor = new window.Quill(this.$refs.root, {
						theme: "snow",
						modules: { toolbar: resolveToolbar(mode) },
						...options,
						...this.getDataOptions(this.$refs.root)
					});
					this.editor.on("text-change", () => {
						this.sync(this.editor.root.innerHTML);
					});
					this.$dispatch("rendered", { editor: this.editor });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.editor?.off("text-change");
				this.editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/sidebar.js
	var sidebar_exports = /* @__PURE__ */ __exportAll({ sidebar: () => sidebar });
	function sidebar(name, sticky$1, stashable) {
		const _toggleable = toggleable();
		const _sticky = sticky();
		return {
			..._toggleable,
			..._sticky,
			init() {
				_toggleable.init.call(this);
				if (sticky$1) _sticky.init.call(this);
				if (stashable) {
					this.$el.removeAttribute("data-mobile-cloak");
					this.screenLg = window.innerWidth >= 1024;
					bind(this.$el, {
						[":data-stashed"]() {
							return !this.screenLg;
						},
						["x-resize.document"]() {
							this.screenLg = window.innerWidth >= 1024;
						},
						[`@sidebar-${name ?? ""}-close.window`]() {
							this.close();
						},
						[`@sidebar-${name ?? ""}-toggle.window`]() {
							this.toggle();
						},
						["@keydown.escape.window"]() {
							if (this.isOpened()) this.close();
						}
					});
					this._dispatchState();
				}
			},
			open() {
				this.$el.setAttribute("data-show-stashed-sidebar", "");
				_toggleable.open.call(this);
				this._dispatchState();
			},
			close() {
				this.$el.removeAttribute("data-show-stashed-sidebar");
				_toggleable.close.call(this);
				this._dispatchState();
			},
			_dispatchState() {
				window.dispatchEvent(new CustomEvent(`sidebar-${name ?? ""}-state`, { detail: { opened: this.opened } }));
			},
			destroy() {
				if (sticky$1) _sticky.destroy.call(this);
			}
		};
	}
	//#endregion
	//#region resources/js/components/slider.js
	var slider_exports = /* @__PURE__ */ __exportAll({ slider: () => slider });
	function slider() {
		return {
			input: null,
			value: null,
			init() {
				this.input = this.$root.querySelector(dataKey("control"));
				this.$nextTick(() => this.updateRange());
				if (this.$wire) {
					const prop = getWireModelInfo(this.input);
					if (prop) this.$wire.$watch(prop.name, () => this.updateRange());
				}
				bind(this.input, { ["@input"]: () => this.updateRange() });
				bind(this.$root.querySelector(dataKey("slider-ticks")), { ["@click"]: (e) => {
					const ticks = [...this.$root.querySelectorAll(dataKey("slider-tick"))];
					const clickX = e.clientX;
					let closestTick = null;
					let minDistance = Infinity;
					ticks.forEach((tick) => {
						const rect = tick.getBoundingClientRect();
						const centerX = rect.left + rect.width / 2;
						const distance = Math.abs(clickX - centerX);
						if (distance < minDistance) {
							minDistance = distance;
							closestTick = tick;
						}
					});
					if (closestTick) {
						let value = parseInt(closestTick.getAttribute("data-value") ?? "");
						if (isNaN(value)) value = parseInt(closestTick.textContent?.trim() ?? "");
						if (!isNaN(value)) this.setValue(value);
					}
				} });
			},
			setValue(value) {
				if (this.input.disabled) return;
				setFieldValue(this.input, value);
			},
			updateRange() {
				const min = Number(this.input.min || 0);
				const max = Number(this.input.max || 100);
				const val = Number(this.input.value);
				const p = max === min ? 0 : (val - min) * 100 / (max - min);
				this.value = this.input.value;
				this.input.style.setProperty("--range-percent", `${p}%`);
				this.input.classList.toggle("before:rounded-r-none", p < 50);
			}
		};
	}
	//#endregion
	//#region resources/js/components/submenu.js
	var submenu_exports = /* @__PURE__ */ __exportAll({ submenu: () => submenu });
	function submenu() {
		const _popover = popover({
			mode: "manual",
			position: isRtl() ? "left" : "right",
			align: "start"
		});
		return {
			..._popover,
			_i: null,
			inside: false,
			init() {
				_popover.init.call(this);
				bind(this.popoverElement, {
					["@mouseenter"]() {
						this.inside = true;
						this.trigger.setAttribute("data-active", "");
					},
					["@mouseleave"]() {
						this.inside = false;
						this.timerToClose();
					}
				});
				bind(this.trigger, {
					["@click"]() {
						this.toggle(false);
					},
					["@mouseenter"]() {
						clearTimeout(this._i);
						this.open(false);
					},
					["@mouseleave"]() {
						this.timerToClose();
					}
				});
			},
			timerToClose() {
				this._i = setTimeout(() => {
					if (!this.inside) {
						this.close();
						this.trigger.removeAttribute("data-active");
					}
				}, 10);
			},
			destroy() {
				clearTimeout(this._i);
				_popover.destroy.call(this);
			}
		};
	}
	//#endregion
	//#region resources/js/components/tab.js
	var tab_exports = /* @__PURE__ */ __exportAll({ tab: () => tab });
	function tab({ selectFirst = null, orientation = null } = {}) {
		return {
			selected: null,
			tabs() {
				return Array.from(this.$root.querySelectorAll("[role=\"tab\"]")).filter((el) => !el.disabled);
			},
			init() {
				const selected = this.$root.querySelector("[data-selected]")?.dataset.name;
				const tabs = this.tabs();
				if (selected || selectFirst && tabs.length) this.$nextTick(() => {
					this.select(selected ?? tabs[0]?.dataset.name);
				});
				const nextKey = orientation === "vertical" ? "arrow-down" : "arrow-right";
				const previousKey = orientation === "vertical" ? "arrow-up" : "arrow-left";
				bind(this.$root, {
					[`@keydown.${nextKey}`](event) {
						if (!event.target.closest("[role=\"tab\"]")) return;
						event.preventDefault();
						this.focusTab(1, event.target);
					},
					[`@keydown.${previousKey}`](event) {
						if (!event.target.closest("[role=\"tab\"]")) return;
						event.preventDefault();
						this.focusTab(-1, event.target);
					},
					["@keydown.home"](event) {
						if (!event.target.closest("[role=\"tab\"]")) return;
						event.preventDefault();
						this.focusTab("first", event.target);
					},
					["@keydown.end"](event) {
						if (!event.target.closest("[role=\"tab\"]")) return;
						event.preventDefault();
						this.focusTab("last", event.target);
					}
				});
			},
			isSelected(name) {
				return this.selected === name;
			},
			select(name) {
				this.selected = name;
			},
			focusTab(direction, current) {
				const tabs = this.tabs();
				if (!tabs.length) return;
				const currentIndex = tabs.indexOf(current);
				let index;
				if (direction === "first") index = 0;
				else if (direction === "last") index = tabs.length - 1;
				else index = (currentIndex + direction + tabs.length) % tabs.length;
				const next = tabs[index];
				next.focus();
				if (next.dataset.name) this.select(next.dataset.name);
			}
		};
	}
	//#endregion
	//#region resources/js/components/table.js
	var table_exports = /* @__PURE__ */ __exportAll({ table: () => table });
	function table() {
		return {
			boundElements: /* @__PURE__ */ new WeakSet(),
			rows: [],
			selected: [],
			selectedIds: [],
			selectAllChecked: false,
			observer: null,
			init() {
				this.resetSelection();
				const tbody = this.$el.querySelector("table > tbody");
				if (!tbody) return;
				this.observer = new MutationObserver(() => this.update());
				this.observer.observe(tbody, {
					childList: true,
					subtree: true
				});
			},
			destroy() {
				this.observer?.disconnect();
			},
			update() {
				const tbody = this.$el.querySelector("table > tbody");
				const trs = tbody ? Array.from(tbody.querySelectorAll(":scope > tr[role=\"row\"]")) : [];
				this.rows = trs.map((tr) => {
					const selection = tr.querySelector("[data-role=row-selection]");
					const expanded = tr.querySelectorAll("[data-role=row-expanded]");
					const row = {
						el: tr,
						id: tr.dataset.id,
						selection,
						expanded
					};
					if (selection && !this.boundElements.has(selection)) {
						this.boundElements.add(selection);
						bind(selection, { ["@click"]() {
							this._updateRowState(row);
							this._syncSelect();
						} });
					}
					const unboundExpanded = Array.from(expanded).filter((el) => !this.boundElements.has(el));
					if (unboundExpanded.length) {
						unboundExpanded.forEach((el) => this.boundElements.add(el));
						bind(unboundExpanded, { ["@click"]() {
							row.el.dataset.expanded = row.el.dataset.expanded === "open" ? "close" : "open";
						} });
					}
					return row;
				});
				this.rows.forEach((row) => {
					if (row.selection) setFieldChecked(row.selection, this.selectAllChecked || this.selectedIds.includes(row.id));
					this._updateRowState(row);
				});
				this._syncSelect();
			},
			toggleAll() {
				this.rows.forEach((row) => {
					if (!row.selection) return;
					setFieldChecked(row.selection, this.selectAllChecked);
					this._updateRowState(row);
				});
				this._syncSelect();
			},
			resetSelection() {
				this.selected = [];
				this.selectedIds = [];
				this.selectAllChecked = false;
				this.update();
			},
			_updateRowState(row) {
				if (row.selection) row.el.dataset.state = row.selection.checked ? "checked" : "unchecked";
				if (row.expanded.length && !row.el.dataset.expanded) row.el.dataset.expanded = "close";
			},
			_syncSelect() {
				this.selected = this.rows.filter((row) => row.selection?.checked);
				this.selectedIds = this.selected.map((row) => row.id);
				this.selectAllChecked = allChecked(this.rows, (row) => !!row.selection?.checked);
			}
		};
	}
	//#endregion
	//#region resources/js/components/textarea.js
	var textarea_exports = /* @__PURE__ */ __exportAll({ textarea: () => textarea });
	function textarea({ maxRows = null, counter = null, length = 0 } = {}) {
		return {
			length,
			init() {
				const el = this.$el.querySelector("textarea");
				const minRows = parseInt(el.getAttribute("rows"));
				const autoRows = minRows && minRows > 0 && maxRows && maxRows > minRows;
				if (counter) this.length = el.value.length;
				if (autoRows) this.resizeRows(el, minRows, maxRows);
				bind(el, { ["@input"]: () => {
					if (counter) this.length = el.value.length;
					if (autoRows) this.resizeRows(el, minRows, maxRows);
				} });
			},
			resizeRows(el, minRows, maxRows) {
				el.rows = minRows;
				const style = getComputedStyle(el);
				const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
				const lineHeight = parseFloat(style.lineHeight) || parseFloat(style.fontSize) * 1.2 || 16;
				const rows = Math.round((el.scrollHeight - padding) / lineHeight);
				el.rows = Math.min(Math.max(rows, minRows), maxRows);
			}
		};
	}
	//#endregion
	//#region resources/js/components/time-picker.js
	var time_picker_exports = /* @__PURE__ */ __exportAll({ timePicker: () => timePicker });
	var FORMATS = ["12-hour", "24-hour"];
	var MINUTES_IN_DAY = 1440;
	function timePicker({ value = null, multiple = null, format = null, locale = null, interval = null, min = null, max = null, unavailable = null, openTo = null, type = null } = {}) {
		if (format && !FORMATS.includes(format)) {
			console.warn(`[tallkit] tk:time-picker received an invalid "format" ("${format}"). Expected one of: ${FORMATS.join(", ")}. Falling back to the locale default.`);
			format = null;
		}
		interval = Math.max(1, Number(interval) || 30);
		min = min ? parseTimeToken(min) : null;
		max = max ? parseTimeToken(max) : null;
		openTo = openTo ? parseTimeToken(openTo) : null;
		multiple = Boolean(multiple);
		const unavailableRanges = parseCommaList(unavailable).map((token) => {
			if (token.includes("-")) {
				const [start, end] = token.split("-").map((part) => parseTimeToken(part));
				return start && end ? [start, end] : null;
			}
			const single = parseTimeToken(token);
			return single ? [single, single] : null;
		}).filter(Boolean);
		const _popover = popover({
			mode: "dropdown",
			position: "bottom",
			align: "start",
			matchTriggerWidth: true
		});
		const _bindableField = bindableField({
			key: "time-picker",
			serialize() {
				return multiple ? (this.value ?? []).join(",") : this.value ?? null;
			},
			deserialize(raw) {
				return this.parseInitialValue(raw);
			}
		});
		return {
			..._popover,
			..._bindableField,
			value: null,
			typed: "",
			typing: false,
			locale: locale || (typeof navigator !== "undefined" ? navigator.language : "en-US"),
			init() {
				_popover.init.call(this);
				this.value = this.parseInitialValue(value);
				_bindableField.init.call(this);
				this.syncTyped();
				this.$watch("value", () => this.syncTyped());
				this.$watch("typed", () => {
					if (!this.typing) return;
					this.commitTyped();
				});
			},
			onOpen() {
				_popover.onOpen.call(this);
				this.$nextTick(() => this.scrollToSelected());
			},
			parseInitialValue(raw) {
				if (multiple) {
					if (!raw) return [];
					return (Array.isArray(raw) ? raw : parseCommaList(raw)).map((v) => parseTimeToken(v)).filter(Boolean);
				}
				if (!raw) return null;
				if (Array.isArray(raw)) raw = raw[0];
				return parseTimeToken(raw);
			},
			slots() {
				const values = [];
				for (let m = 0; m < MINUTES_IN_DAY; m += interval) values.push(`${padDatePart(Math.floor(m / 60))}:${padDatePart(m % 60)}`);
				return values;
			},
			isTimeDisabled(hhmm) {
				if (min && hhmm < min) return true;
				if (max && hhmm > max) return true;
				return unavailableRanges.some(([start, end]) => hhmm >= start && hhmm <= end);
			},
			isSelected(hhmm) {
				if (multiple) return (this.value ?? []).includes(hhmm);
				return this.value === hhmm;
			},
			select(hhmm) {
				if (this.isTimeDisabled(hhmm)) return;
				if (multiple) {
					this.toggleMultiple(hhmm);
					return;
				}
				this.value = this.value === hhmm ? null : hhmm;
				this.close();
			},
			toggleMultiple(hhmm) {
				const current = this.value ?? [];
				this.value = current.includes(hhmm) ? current.filter((v) => v !== hhmm) : [...current, hhmm].sort();
			},
			formatter() {
				const options = {
					hour: "numeric",
					minute: "2-digit",
					hour12: format === "12-hour" ? true : format === "24-hour" ? false : void 0
				};
				try {
					return new Intl.DateTimeFormat(this.locale, options);
				} catch {
					return new Intl.DateTimeFormat(void 0, options);
				}
			},
			formatSlot(hhmm) {
				const [h, m] = hhmm.split(":").map(Number);
				return this.formatter().format(new Date(2e3, 0, 1, h, m));
			},
			formatted() {
				if (multiple) return (this.value ?? []).length ? this.value.map((v) => this.formatSlot(v)).join(", ") : null;
				return this.value ? this.formatSlot(this.value) : null;
			},
			typable() {
				return type === "input" && !multiple;
			},
			maskPattern() {
				return "99:99";
			},
			syncTyped() {
				if (!this.typable()) return;
				this.typed = this.value ?? "";
			},
			commitTyped() {
				if (!this.typable()) return;
				if ((this.typed.match(/\d/g) ?? []).length < 4) return;
				const parsed = parseTimeToken(this.typed);
				if (parsed && !this.isTimeDisabled(parsed)) this.value = parsed;
			},
			confirmTyped() {
				this.commitTyped();
				this.typing = false;
				this.syncTyped();
				this.close();
			},
			onFieldBlur(event) {
				if (!this.$root.contains(event.relatedTarget)) {
					this.confirmTyped();
					return;
				}
				this.commitTyped();
				this.typing = false;
				this.syncTyped();
			},
			clear() {
				this.value = multiple ? [] : null;
				this.typed = "";
			},
			nearestSlot(hhmm) {
				const target = toMinutes(hhmm);
				const values = this.slots();
				return values.reduce((closest, slot) => Math.abs(toMinutes(slot) - target) < Math.abs(toMinutes(closest) - target) ? slot : closest, values[0]);
			},
			scrollToSelected() {
				(this.$root.querySelector("[data-active=\"true\"]") ?? (openTo ? this.$root.querySelector(`[data-slot="${this.nearestSlot(openTo)}"]`) : null))?.scrollIntoView({ block: "nearest" });
			}
		};
	}
	//#endregion
	//#region resources/js/components/tinymce.js
	var tinymce_exports = /* @__PURE__ */ __exportAll({ tinymce: () => tinymce });
	var GROUPS$1 = {
		text: { toolbar: "bold italic underline strikethrough | removeformat" },
		heading: { toolbar: "blocks" },
		color: { toolbar: "forecolor backcolor" },
		size: { toolbar: "fontsize" },
		script: { toolbar: "subscript superscript" },
		align: { toolbar: "alignleft aligncenter alignright alignjustify" },
		link: {
			plugins: "link autolink",
			toolbar: "link"
		},
		list: {
			plugins: "lists",
			toolbar: "numlist bullist"
		},
		media: {
			plugins: "image media",
			toolbar: "image media"
		},
		table: {
			plugins: "table",
			toolbar: "table"
		},
		quote: { toolbar: "blockquote" },
		code: {
			plugins: "code codesample",
			toolbar: "code codesample"
		}
	};
	function resolveConfig(mode) {
		const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
		if (!groups.length) return {
			plugins: "",
			toolbar: false
		};
		return {
			plugins: groups.map((group) => GROUPS$1[group]?.plugins).filter(Boolean).join(" "),
			toolbar: ["undo redo", ...groups.map((group) => GROUPS$1[group]?.toolbar).filter(Boolean)].join(" | ")
		};
	}
	function tinymce({ options = {}, scripts = [], mode = null } = {}) {
		const _loadable = loadable();
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			editor: null,
			init() {
				this.initField();
				this.load(() => loadRemoteAssets(() => !!window.tinymce, ["https://cdn.jsdelivr.net/npm/tinymce@8/tinymce.min.js", ...scripts]).then(() => this.mount()));
			},
			applyExternalValue(value) {
				this.editor.setContent(value ?? "");
			},
			async mount() {
				try {
					const { plugins, toolbar } = resolveConfig(mode);
					const [editor] = await window.tinymce.init({
						target: this.input,
						license_key: "gpl",
						menubar: false,
						plugins,
						toolbar,
						promotion: false,
						branding: false,
						setup: (editor) => {
							editor.on("change input undo redo", () => {
								this.sync(editor.getContent());
							});
						},
						...options,
						...this.getDataOptions(this.input)
					});
					this.editor = editor;
					this.$dispatch("rendered", { editor: this.editor });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.editor?.remove();
				this.editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/tiptap.js
	var tiptap_exports = /* @__PURE__ */ __exportAll({ tiptap: () => tiptap });
	var DEFAULT_TIPTAP_VERSION = "3.30.3";
	var esm = (pkg, version) => `https://esm.sh/${pkg}@${version}`;
	function readAsDataURL(file) {
		return new Promise((resolve, reject) => {
			const reader = new FileReader();
			reader.onload = () => resolve(reader.result);
			reader.onerror = () => reject(reader.error);
			reader.readAsDataURL(file);
		});
	}
	function getCsrfToken() {
		const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);
		return match ? decodeURIComponent(match[1]) : null;
	}
	var GROUPS = {
		text: {},
		heading: {},
		color: {
			scripts: (v) => [esm("@tiptap/extension-text-style", v)],
			extensions: ([textStyle]) => [textStyle.TextStyleKit]
		},
		size: {
			scripts: (v) => [esm("@tiptap/extension-text-style", v)],
			extensions: ([textStyle]) => [textStyle.TextStyleKit]
		},
		script: {
			scripts: (v) => [esm("@tiptap/extension-subscript", v), esm("@tiptap/extension-superscript", v)],
			extensions: ([subscript, superscript]) => [subscript.default, superscript.default]
		},
		align: {
			scripts: (v) => [esm("@tiptap/extension-text-align", v)],
			extensions: ([textAlign]) => [textAlign.default.configure({ types: ["heading", "paragraph"] })]
		},
		link: {},
		list: {},
		media: {
			scripts: (v) => [esm("@tiptap/extension-image", v), esm("@tiptap/core", v)],
			extensions: ([image, core]) => [image.default.configure({ resize: {
				enabled: true,
				alwaysPreserveAspectRatio: true
			} }), core.Node.create({
				name: "video",
				group: "block",
				atom: true,
				draggable: true,
				addAttributes() {
					return {
						src: { default: null },
						width: {
							default: null,
							renderHTML: (attrs) => attrs.width ? { style: `width: ${attrs.width}` } : {}
						}
					};
				},
				parseHTML() {
					return [{ tag: "video" }];
				},
				renderHTML({ HTMLAttributes }) {
					return ["video", core.mergeAttributes({ controls: "" }, HTMLAttributes)];
				},
				addCommands() {
					return { setVideo: (options) => ({ commands }) => commands.insertContent({
						type: this.name,
						attrs: options
					}) };
				}
			})]
		},
		table: {
			scripts: (v) => [esm("@tiptap/extension-table", v)],
			extensions: ([table]) => [table.TableKit]
		},
		quote: {},
		code: {}
	};
	function tiptap({ options = {}, scripts = [], mode = null, upload = {}, version = null } = {}) {
		const _loadable = loadable();
		let resolvedVersion = version || DEFAULT_TIPTAP_VERSION;
		let editor = null;
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			groups: [],
			extraModules: [],
			tick: 0,
			init() {
				this.initField();
				const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
				this.groups = groups;
				this.load(async () => {
					const [{ Editor }, { default: StarterKit }] = await loadRemoteModule([esm("@tiptap/core", resolvedVersion), esm("@tiptap/starter-kit", resolvedVersion)]);
					const extensions = [StarterKit];
					for (const group of groups) {
						const config = GROUPS[group];
						const groupScripts = config?.scripts?.(resolvedVersion);
						if (!groupScripts?.length) continue;
						const mods = await loadRemoteModule(groupScripts);
						for (const extension of config.extensions?.(mods) ?? []) {
							if (!extension || extensions.includes(extension)) continue;
							extensions.push(extension);
						}
					}
					if (scripts.length) this.extraModules = await loadRemoteModule(scripts);
					this.mount(Editor, extensions);
				});
			},
			applyExternalValue(value) {
				editor.commands.setContent(value ?? "");
			},
			run(command) {
				const chain = editor.chain().focus();
				if (command === "heading1") chain.toggleHeading({ level: 1 });
				else if (command === "heading2") chain.toggleHeading({ level: 2 });
				else if (command === "heading3") chain.toggleHeading({ level: 3 });
				else if (command.startsWith("align")) chain.setTextAlign(command.slice(5).toLowerCase());
				else if (command === "link") {
					const url = window.prompt("URL", editor.getAttributes("link").href ?? "");
					url ? chain.setLink({ href: url }) : chain.unsetLink();
				} else if (command === "image") this.$refs.imageInput?.click();
				else if (command === "video") this.$refs.videoInput?.click();
				else if (command === "table") chain.insertTable({
					rows: 3,
					cols: 3,
					withHeaderRow: true
				});
				else chain[`toggle${command.charAt(0).toUpperCase()}${command.slice(1)}`]?.();
				chain.run();
			},
			async handleUpload(file, type) {
				if (!upload) return readAsDataURL(file);
				const limit = upload.maxSize?.[type];
				if (limit && file.size > limit * 1024) throw new Error(`File is larger than the ${(limit / 1024).toFixed(1)}MB limit.`);
				const body = new FormData();
				body.append("file", file);
				if (limit) body.append("max_size", String(limit));
				if (upload.disk) body.append("disk", upload.disk);
				if (upload.directory) body.append("directory", upload.directory);
				const response = await fetch(upload.url, {
					method: "POST",
					credentials: "same-origin",
					headers: {
						Accept: "application/json",
						"X-XSRF-TOKEN": getCsrfToken() ?? ""
					},
					body
				});
				if (!response.ok) throw new Error(`Upload failed with status ${response.status}`);
				return (await response.json()).url;
			},
			async insertImage(event) {
				const input = event.target;
				const file = input.files?.[0];
				input.value = "";
				if (!file) return;
				try {
					const src = await this.handleUpload(file, "image");
					editor.chain().focus().setImage({
						src,
						alt: file.name
					}).run();
				} catch (e) {
					console.error(e);
					window.alert(e instanceof Error ? e.message : "Failed to upload image.");
				}
			},
			async insertVideo(event) {
				const input = event.target;
				const file = input.files?.[0];
				input.value = "";
				if (!file) return;
				try {
					const src = await this.handleUpload(file, "video");
					editor.chain().focus().setVideo({ src }).run();
				} catch (e) {
					console.error(e);
					window.alert(e instanceof Error ? e.message : "Failed to upload video.");
				}
			},
			textStyle(attr) {
				this.tick;
				return editor?.getAttributes("textStyle")[attr] ?? null;
			},
			isActive(command) {
				this.tick;
				if (!editor) return false;
				if (command === "heading1") return editor.isActive("heading", { level: 1 });
				if (command === "heading2") return editor.isActive("heading", { level: 2 });
				if (command === "heading3") return editor.isActive("heading", { level: 3 });
				if (command.startsWith("align")) return editor.isActive({ textAlign: command.slice(5).toLowerCase() });
				return editor.isActive(command);
			},
			setColor(value) {
				value ? editor.chain().focus().setColor(value).run() : editor.chain().focus().unsetColor().run();
			},
			setBackgroundColor(value) {
				value ? editor.chain().focus().setBackgroundColor(value).run() : editor.chain().focus().unsetBackgroundColor().run();
			},
			setFontSize(value) {
				value ? editor.chain().focus().setFontSize(value).run() : editor.chain().focus().unsetFontSize().run();
			},
			mount(EditorClass, extensions) {
				try {
					editor = new EditorClass({
						element: this.$refs.root,
						extensions,
						content: this.input.value ?? "",
						editorProps: { attributes: {
							class: "tiptap-content",
							"data-tallkit-control": ""
						} },
						onUpdate: ({ editor }) => {
							this.sync(editor.getHTML());
						},
						onSelectionUpdate: () => {
							this.tick++;
						},
						onTransaction: () => {
							this.tick++;
						},
						...options,
						...this.getDataOptions(this.$refs.root)
					});
					this.$dispatch("rendered", { editor });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				editor?.destroy();
				editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/toast.js
	var toast_exports = /* @__PURE__ */ __exportAll({ toast: () => toast$1 });
	function toast$1() {
		return {
			toasts: [],
			isPageVisible: false,
			isUserActive: false,
			idleTimeout: null,
			idleDelay: 0,
			_listeners: [],
			init() {
				bind(this.$el, { ["@toast.document"](e) {
					this.addToast(e.detail);
				} });
				this.initAttentionListeners();
			},
			initAttentionListeners() {
				this.isPageVisible = true;
				this.isUserActive = true;
				this.idleTimeout = null;
				this.idleDelay = 1e4;
				this._listeners = [];
				let ticking = false;
				const markActive = () => {
					if (ticking) return;
					ticking = true;
					requestAnimationFrame(() => {
						this.isUserActive = true;
						this.resetIdleTimer();
						this.syncAttention();
						ticking = false;
					});
				};
				const markIdle = () => {
					this.isUserActive = false;
					this.syncAttention();
				};
				const add = (target, event, handler, options) => {
					target.addEventListener(event, handler, options);
					this._listeners.push(() => target.removeEventListener(event, handler, options));
				};
				add(document, "visibilitychange", () => {
					this.isPageVisible = !document.hidden;
					this.syncAttention();
				});
				[
					"mousemove",
					"mousedown",
					"keydown",
					"touchstart"
				].forEach((event) => {
					add(window, event, markActive, { passive: true });
				});
				this.resetIdleTimer = () => {
					if (this.idleTimeout) clearTimeout(this.idleTimeout);
					this.idleTimeout = setTimeout(markIdle, this.idleDelay);
				};
				this.resetIdleTimer();
			},
			destroy() {
				this._listeners.forEach((off) => off());
				clearTimeout(this.idleTimeout);
			},
			syncAttention() {
				const shouldRun = this.isPageVisible && this.isUserActive;
				this.toasts.forEach((toast) => {
					if (!toast.duration || !toast.attentionAware) return;
					if (shouldRun && toast.pausedAt) toast.resume("attention");
					if (!shouldRun && !toast.pausedAt) toast.pause("attention");
				});
			},
			addToast(props) {
				const position = normalizePosition(props.position);
				const maxStack = props.maxStack ?? 5;
				if (maxStack !== false) {
					const sameSlot = this.toasts.filter((t) => t.position === position);
					if (sameSlot.length >= maxStack) {
						const oldest = sameSlot.slice().sort((a, b) => a.createdAt - b.createdAt)[0];
						if (oldest) this.removeToast(oldest.id);
					}
				}
				const duration = props.duration ?? getDynamicDuration(props.title, props.message);
				const manager = this;
				const currentToast = props.id ? this.toasts.find((t) => t.id === props.id) : null;
				if (currentToast) return this.updateToast(currentToast.id, props);
				const toast = window.Alpine.reactive({
					id: crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`,
					createdAt: Date.now(),
					...props,
					duration,
					position,
					attentionAware: props.attentionAware ?? true,
					progress: props.progress ?? true,
					pauseOnHover: props.pauseOnHover ?? true,
					swipe: props.swipe ?? true,
					visible: false,
					progressValue: 1,
					startTime: 0,
					total: duration,
					elapsedBeforePause: 0,
					raf: null,
					pausedAt: null,
					pausedByHover: false,
					pausedByAttention: false,
					swiping: false,
					startX: 0,
					startY: 0,
					currentX: 0,
					currentY: 0,
					lockDirection: null,
					start() {
						if (!this.duration) return;
						this.startTime = performance.now();
						const loop = (time) => {
							if (!manager.toasts.find((t) => t.id === this.id)) {
								this.stop();
								return;
							}
							if (this.pausedAt) return;
							const elapsed = this.elapsedBeforePause + (time - this.startTime);
							const linear = Math.min(elapsed / this.total, 1);
							if (this.progress) this.progressValue = 1 - linear;
							if (linear >= 1) {
								manager.removeToast(this.id);
								return;
							}
							this.raf = requestAnimationFrame(loop);
						};
						this.raf = requestAnimationFrame(loop);
					},
					pause(reason = "attention") {
						if (!this.duration) return;
						if (reason === "hover") this.pausedByHover = true;
						else this.pausedByAttention = true;
						if (this.pausedAt) return;
						this.pausedAt = performance.now();
						this.elapsedBeforePause += this.pausedAt - this.startTime;
						if (this.raf) {
							cancelAnimationFrame(this.raf);
							this.raf = null;
						}
					},
					resume(reason = "attention") {
						if (reason === "hover") this.pausedByHover = false;
						else this.pausedByAttention = false;
						if (this.pausedByHover || this.pausedByAttention) return;
						if (!this.pausedAt) return;
						this.pausedAt = null;
						this.start();
					},
					stop() {
						if (this.raf) {
							cancelAnimationFrame(this.raf);
							this.raf = null;
						}
					},
					onPointerDown(e) {
						if (!this.swipe) return;
						this.swiping = true;
						this.startX = e.clientX;
						this.startY = e.clientY;
						this.lockDirection = null;
					},
					onPointerMove(e) {
						if (!this.swipe || !this.swiping) return;
						this.currentX = e.clientX - this.startX;
						this.currentY = e.clientY - this.startY;
						if (!this.lockDirection) this.lockDirection = Math.abs(this.currentX) > Math.abs(this.currentY) ? "x" : "y";
						if (this.lockDirection === "x") e.preventDefault();
					},
					onPointerUp(e) {
						if (!this.swipe) return;
						this.swiping = false;
						if (this.lockDirection !== "x") {
							this.currentX = 0;
							this.currentY = 0;
							this.lockDirection = null;
							return;
						}
						const threshold = e.currentTarget.offsetWidth * .4;
						if (Math.abs(this.currentX) > threshold) manager.removeToast(this.id);
						else {
							this.currentX = 0;
							this.currentY = 0;
							this.lockDirection = null;
						}
					}
				});
				this.toasts.push(toast);
				this.$nextTick(() => {
					toast.visible = true;
					toast.start();
				});
				return toast;
			},
			updateToast(id, data) {
				const toast = this.toasts.find((t) => t.id === id);
				if (!toast) return;
				const allowed = [
					"title",
					"message",
					"type",
					"size",
					"duration",
					"position",
					"attentionAware",
					"progress",
					"pauseOnHover",
					"swipe"
				];
				for (const key in data) if (allowed.includes(key)) toast[key] = data[key];
				toast.currentX = 0;
				toast.swiping = false;
				if (data.duration !== void 0) {
					toast.stop();
					toast.pausedAt = null;
					toast.pausedByHover = false;
					toast.pausedByAttention = false;
					toast.total = data.duration;
					toast.elapsedBeforePause = 0;
					toast.progressValue = 1;
					if (toast.visible) toast.start();
				}
				return toast;
			},
			removeToast(id) {
				const toast = this.toasts.find((t) => t.id === id);
				if (!toast) return;
				toast.stop();
				toast.raf = null;
				toast.visible = false;
				setTimeout(() => {
					this.toasts = this.toasts.filter((t) => t.id !== id);
				}, 300);
			},
			getToastsByPosition(position) {
				return this.toasts.filter((t) => t.position === position);
			},
			notify(props) {
				return this.addToast(props);
			},
			success(message, props = {}) {
				return this.notify({
					title: message,
					type: "success",
					...props
				});
			},
			error(message, props = {}) {
				return this.notify({
					title: message,
					type: "error",
					...props
				});
			},
			info(message, props = {}) {
				return this.notify({
					title: message,
					type: "info",
					...props
				});
			},
			warning(message, props = {}) {
				return this.notify({
					title: message,
					type: "warning",
					...props
				});
			},
			loading(message, props = {}) {
				return this.notify({
					title: message,
					type: "loading",
					duration: null,
					progress: false,
					swipe: false,
					...props
				});
			},
			group(props, key) {
				const existing = this.toasts.find((t) => t.groupKey === key);
				if (existing) {
					existing.count = (existing.count || 1) + 1;
					existing.meta = {
						...existing.meta || {},
						count: existing.count
					};
					existing.currentX = 0;
					existing.swiping = false;
					if (existing.visible && existing.duration) {
						existing.stop();
						existing.pausedAt = null;
						existing.progressValue = 1;
						existing.start();
					}
					return existing;
				}
				return this.addToast({
					...props,
					groupKey: key,
					count: 1,
					meta: { count: 1 }
				});
			},
			promise(promise, messages = {}) {
				const toast = this.loading(messages.loading ?? "Loading...");
				const resolveMessage = (msg, data) => typeof msg === "function" ? msg(data) : msg;
				promise.then((data) => {
					if (!this.toasts.find((t) => t.id === toast.id)) return;
					this.updateToast(toast.id, {
						title: resolveMessage(messages.success, data) ?? "Success!",
						type: "success",
						duration: getDynamicDuration(resolveMessage(messages.success, data)),
						progress: true,
						swipe: true
					});
				}).catch((error) => {
					if (!this.toasts.find((t) => t.id === toast.id)) return;
					this.updateToast(toast.id, {
						title: resolveMessage(messages.error, error) ?? "Error!",
						type: "error",
						duration: getDynamicDuration(resolveMessage(messages.error, error)) * 1.3,
						progress: true,
						swipe: true
					});
				});
				return promise;
			},
			queue(props, max = 3) {
				const visible = this.toasts.filter((t) => t.visible);
				if (visible.length >= max) {
					const oldest = visible.slice().sort((a, b) => a.createdAt - b.createdAt)[0];
					this.removeToast(oldest.id);
				}
				return this.addToast(props);
			},
			dedupe(props, windowMs = 2e3) {
				const now = Date.now();
				const exists = this.toasts.find((t) => t.title === props.title && t.type === props.type && now - t.createdAt < windowMs);
				if (exists) return exists;
				return this.addToast({
					...props,
					createdAt: now
				});
			}
		};
	}
	function normalizePosition(position = "bottom-right") {
		if (position === "top") return "top-right";
		if (position === "bottom") return "bottom-right";
		return position;
	}
	function getDynamicDuration(title = "", message = "") {
		const text = `${title} ${message}`.trim();
		const min = 3e3;
		const max = 9e3;
		let time = 1e3 + (title.length * 1.2 + message.length * 1.6) / 16 * 1e3;
		const lines = text.split("\n").length;
		time += lines * 300;
		return Math.min(max, Math.max(min, time));
	}
	//#endregion
	//#region resources/js/components/toggle-all.js
	var toggle_all_exports = /* @__PURE__ */ __exportAll({ toggleAll: () => toggleAll });
	function toggleAll({ group = null } = {}) {
		return groupAll("toggle", group ?? "");
	}
	//#endregion
	//#region resources/js/components/upload.js
	var upload_exports = /* @__PURE__ */ __exportAll({ upload: () => upload });
	var PREVIEWABLE_TYPES = [
		"image",
		"video",
		"audio",
		"pdf"
	];
	function upload({ wireModel = false, multiple = false, droppable = true, maxSize = null, maxFiles = null, sortable = false, invalid = false, files = [], tooLargeMessage = "This file is too large.", invalidTypeMessage = "This file type is not allowed.", tooManyFilesMessage = "Too many files selected.", previewName = null } = {}) {
		return {
			dragOver: false,
			dragIndex: null,
			dragOverIndex: null,
			sortable,
			previewId: null,
			files: files.map((file) => ({
				id: file.id ?? generateId("upload-file"),
				raw: null,
				name: file.name ?? "",
				size: file.size ?? 0,
				url: file.url ?? null,
				value: file.value ?? null,
				type: file.type ?? "unknown",
				status: file.status ?? "done",
				progress: file.progress ?? 100,
				error: null,
				tmpFilename: file.tmpFilename ?? null,
				previewLoaded: PREVIEWABLE_TYPES.includes(file.type ?? "")
			})),
			queue: [],
			activeId: null,
			multiple() {
				return this.$refs.fileInput?.multiple ?? multiple;
			},
			accept() {
				return this.$refs.fileInput?.accept || null;
			},
			activeFiles() {
				return this.files.filter((file) => file.status === "uploading" || file.status === "queued");
			},
			hasPendingUploads() {
				return this.files.some((file) => file.status === "uploading" || file.status === "queued");
			},
			aggregateProgress() {
				const active = this.activeFiles();
				if (!active.length) return 100;
				return Math.round(active.reduce((sum, file) => sum + file.progress, 0) / active.length);
			},
			isUploading() {
				return this.activeFiles().length > 0;
			},
			hasError() {
				return this.files.some((file) => file.status === "error");
			},
			isInvalid() {
				return invalid || this.hasError();
			},
			previewFile() {
				return this.find(this.previewId);
			},
			init() {
				bind(this.$refs.fileInput, { ["@change"](e) {
					const target = e.target;
					this.addFiles(target.files);
					target.value = "";
				} });
				if (!droppable) return;
				bind(this.$root.querySelector(dataKey("upload-dropzone")), {
					["@dragover.prevent"]() {
						this.dragOver = true;
					},
					["@dragleave.prevent"](e) {
						if (e.currentTarget && e.currentTarget.contains(e.relatedTarget)) return;
						this.dragOver = false;
					},
					["@drop.prevent"](e) {
						this.dragOver = false;
						this.addFiles(e.dataTransfer?.files ?? null);
					}
				});
			},
			destroy() {
				this.files.forEach((file) => this.revoke(file));
			},
			selectFile() {
				this.$refs.fileInput.click();
			},
			viewFile(id) {
				this.previewId = id;
				if (!this.previewFile()) {
					this.previewId = null;
					return;
				}
				if (this.previewFile().previewLoaded) {
					this.$dispatch("modal-show", { name: previewName });
					return;
				}
				this.openFile();
			},
			openFile() {
				const url = this.previewFile()?.url;
				if (!url) return;
				window.open(url, "_blank", "noopener");
			},
			addFiles(fileList) {
				if (!fileList?.length) return;
				if (!this.multiple()) {
					if (this.activeId) this.cancelUpload(this.activeId);
					this.files.forEach((file) => this.revoke(file));
					this.files = [];
					this.queue = [];
				}
				const incoming = Array.from(fileList);
				const remaining = this.multiple() ? maxFiles ? Math.max(maxFiles - this.files.length, 0) : Infinity : 1;
				const accepted = incoming.slice(0, remaining);
				const rejected = this.multiple() && maxFiles ? incoming.slice(remaining) : [];
				accepted.forEach((raw) => {
					const entry = this.createFileEntry(raw);
					this.files.push(entry);
					if (!entry.error) this.queue.push(entry.id);
				});
				rejected.forEach((raw) => {
					const entry = {
						id: generateId("upload-file"),
						raw,
						name: raw.name,
						size: raw.size,
						url: null,
						value: null,
						type: detectFileType(raw.type, raw.name),
						status: "error",
						progress: 0,
						error: tooManyFilesMessage,
						tmpFilename: null
					};
					this.files.push(entry);
				});
				this.processQueue();
				this.syncFieldError();
			},
			createFileEntry(raw) {
				const type = detectFileType(raw.type, raw.name);
				const previewable = PREVIEWABLE_TYPES.includes(type);
				const url = previewable ? URL.createObjectURL(raw) : null;
				const error = this.validate(raw);
				return {
					id: generateId("upload-file"),
					raw,
					name: raw.name,
					size: raw.size,
					url,
					value: null,
					type,
					status: error ? "error" : "queued",
					progress: 0,
					error,
					tmpFilename: null,
					previewLoaded: previewable
				};
			},
			validate(file) {
				if (maxSize && file.size > maxSize * 1024) return tooLargeMessage;
				if (this.accept() && !this.matchesAccept(file, this.accept())) return invalidTypeMessage;
				return null;
			},
			matchesAccept(file, accept) {
				return accept.split(",").some((rule) => {
					rule = rule.trim();
					if (!rule) return false;
					if (rule.startsWith(".")) return file.name.toLowerCase().endsWith(rule.toLowerCase());
					if (rule.endsWith("/*")) return file.type.startsWith(rule.slice(0, -1));
					return file.type === rule;
				});
			},
			processQueue() {
				if (this.activeId || !this.queue.length) return;
				const entry = this.find(this.queue.shift());
				if (!entry) {
					this.processQueue();
					return;
				}
				this.activeId = entry.id;
				entry.status = "uploading";
				if (!this.$wire || !wireModel) {
					entry.status = "done";
					entry.progress = 100;
					this.activeId = null;
					this.$nextTick(() => this.processQueue());
					return;
				}
				this.$wire.upload(wireModel, entry.raw, (tmpFilename) => {
					entry.status = "done";
					entry.progress = 100;
					entry.tmpFilename = tmpFilename;
					this.activeId = null;
					this.processQueue();
					this.syncFieldError();
				}, (message) => {
					entry.status = "error";
					entry.error = message || "Upload failed.";
					this.activeId = null;
					this.processQueue();
					this.syncFieldError();
				}, (e) => {
					entry.progress = e.detail.progress;
				}, () => {
					entry.status = "cancelled";
					this.activeId = null;
					this.processQueue();
				});
			},
			retryUpload(id) {
				const entry = this.find(id);
				if (!entry?.raw) return;
				entry.status = "queued";
				entry.error = null;
				entry.progress = 0;
				this.queue.unshift(entry.id);
				this.processQueue();
			},
			cancelUpload(id) {
				if (id !== this.activeId || !this.$wire || !wireModel) return;
				this.$wire.cancelUpload(wireModel);
			},
			removeFile(id) {
				const index = this.files.findIndex((file) => file.id === id);
				if (index === -1) return;
				const entry = this.files[index];
				if (entry.id === this.activeId) this.cancelUpload(id);
				else this.queue = this.queue.filter((queuedId) => queuedId !== id);
				this.detachFromWire(entry, this.files.filter((file) => file.id !== id));
				this.revoke(entry);
				this.files.splice(index, 1);
				this.syncFieldError();
			},
			replaceFile(index, fileList) {
				const raw = fileList?.[0];
				const entry = this.files[index];
				if (!raw || !entry) return;
				if (entry.id === this.activeId) this.cancelUpload(entry.id);
				else this.queue = this.queue.filter((queuedId) => queuedId !== entry.id);
				this.detachFromWire(entry, this.files.filter((file) => file.id !== entry.id));
				this.revoke(entry);
				const next = this.createFileEntry(raw);
				this.files.splice(index, 1, next);
				if (!next.error) {
					this.queue.push(next.id);
					this.processQueue();
				}
				this.syncFieldError();
			},
			detachFromWire(entry, remainingFiles) {
				if (!this.$wire || !wireModel) return;
				if (entry.tmpFilename) this.$wire.removeUpload(wireModel, entry.tmpFilename);
				else if (entry.value !== null) {
					if (this.multiple()) {
						if (!this.hasPendingUploads()) this.$wire.set(wireModel, remainingFiles.filter((file) => file.value !== null).map((file) => file.value));
					} else this.$wire.set(wireModel, null);
				}
			},
			syncFieldError() {
				if (this.isInvalid()) return;
				findInField(this.$root, "error")?.remove();
			},
			revoke(entry) {
				if (entry.raw && entry.url) URL.revokeObjectURL(entry.url);
			},
			find(id) {
				return this.files.find((file) => file.id === id) ?? null;
			},
			dragStart(index, e) {
				this.dragIndex = index;
				e.dataTransfer?.setData("text/plain", String(index));
			},
			dragOverTile(index) {
				if (this.dragIndex === index) return;
				this.dragOverIndex = index;
			},
			dragLeaveTile(index, e) {
				if (this.dragOverIndex !== index) return;
				if (e.currentTarget && e.currentTarget.contains(e.relatedTarget)) return;
				this.dragOverIndex = null;
			},
			dropOnTile(index, e) {
				this.dragOverIndex = null;
				this.dragOver = false;
				const fileList = e.dataTransfer?.files;
				if (fileList?.length) {
					this.replaceFile(index, fileList);
					return;
				}
				this.drop(index);
			},
			drop(index) {
				if (this.dragIndex === null || this.dragIndex === index) return;
				const [moved] = this.files.splice(this.dragIndex, 1);
				this.files.splice(index, 0, moved);
				this.dragIndex = null;
				if (this.multiple() && this.$wire && wireModel && !this.hasPendingUploads()) this.$wire.set(wireModel, this.files.filter((file) => file.value !== null).map((file) => file.value));
			},
			dragEnd() {
				this.dragIndex = null;
				this.dragOverIndex = null;
			},
			formatSize(bytes) {
				return formatBytes(bytes);
			}
		};
	}
	//#endregion
	//#region resources/js/alpine.js
	async function loadAlpine() {
		if (window.Alpine) return;
		await loadScript([
			"https://unpkg.com/@alpinejs/resize@3.x.x/dist/cdn.min.js",
			"https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js",
			"https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
		]);
	}
	function initAlpine() {
		if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", loadAlpine);
		else loadAlpine();
	}
	function setupAlpine(tallkit) {
		const Alpine = window.Alpine;
		if (!Alpine) return;
		registerAlpineComponents();
		Alpine.store("tallkit", tallkit);
		Alpine.magic("tallkit", () => tallkit);
		Alpine.magic("tk", () => tallkit);
	}
	function registerAlpineComponents() {
		const components = Object.fromEntries(Object.values([
			address_form_exports,
			alert_component_exports,
			apexcharts_exports,
			aside_exports,
			autocomplete_exports,
			badge_exports,
			calendar_exports,
			chartjs_exports,
			checkbox_all_exports,
			clearable_exports,
			color_picker_exports,
			combobox_exports,
			composer_exports,
			copyable_exports,
			credit_card_exports,
			date_picker_exports,
			disclosure_group_exports,
			disclosure_exports,
			echarts_exports,
			editorjs_exports,
			fetchable_exports,
			form_exports,
			frappe_charts_exports,
			full_calendar_exports,
			header_exports,
			highlightjs_exports,
			input_viewable_exports,
			label_exports,
			listbox_exports,
			loadable_exports,
			menu_checkbox_exports,
			menu_radio_exports,
			menu_exports,
			modal_trigger_exports,
			modal_exports,
			nav_indicator_exports,
			notification_item_exports,
			notification_exports,
			otp_exports,
			popover_exports,
			pretty_print_json_exports,
			progress_exports,
			quill_exports,
			sidebar_exports,
			slider_exports,
			submenu_exports,
			tab_exports,
			table_exports,
			textarea_exports,
			time_picker_exports,
			tinymce_exports,
			tiptap_exports,
			toast_exports,
			toggle_all_exports,
			upload_exports
		]).flatMap((module) => Object.entries(module).filter(([, v]) => typeof v === "function")));
		for (const [name, fn] of Object.entries(components)) window.Alpine.data(name, fn);
	}
	//#endregion
	//#region resources/js/appearance.js
	var appearance = {
		mode: window.localStorage.getItem("tallkit.appearance") || "system",
		init() {
			this.apply(this.mode);
			document.addEventListener("livewire:navigated", () => this.apply(this.mode));
			window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
				if (this.mode === "system") this.apply("system");
			});
		},
		isDark() {
			return document.documentElement.classList.contains("dark");
		},
		isLight() {
			return !this.isDark();
		},
		applyDark(storage = true) {
			document.documentElement.classList.add("dark");
			if (storage) window.localStorage.setItem("tallkit.appearance", "dark");
			this.mode = "dark";
		},
		applyLight(storage = true) {
			document.documentElement.classList.remove("dark");
			if (storage) window.localStorage.setItem("tallkit.appearance", "light");
			this.mode = "light";
		},
		apply(appearance) {
			if (appearance === "system") {
				const media = window.matchMedia("(prefers-color-scheme: dark)");
				window.localStorage.removeItem("tallkit.appearance");
				if (media.matches) this.applyDark(false);
				else this.applyLight(false);
				this.mode = "system";
			} else if (appearance === "dark") this.applyDark();
			else if (appearance === "light") this.applyLight();
		},
		toggle(event, options = {}) {
			if (!(typeof document !== "undefined" && typeof document.startViewTransition === "function" && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) || !event) return this.isDark() ? this.applyLight() : this.applyDark();
			const transition = document.startViewTransition(() => this.isDark() ? this.applyLight() : this.applyDark());
			const x = event.clientX || 0;
			const y = event.clientY || 0;
			const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
			transition.ready.then(() => {
				const clipPath = [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`];
				document.documentElement.animate({ clipPath: this.isDark() ? [...clipPath].reverse() : clipPath }, {
					duration: 300,
					easing: "ease-in",
					...options || {},
					pseudoElement: this.isDark() ? "::view-transition-old(root)" : "::view-transition-new(root)"
				});
			});
		}
	};
	//#endregion
	//#region resources/js/toast.js
	function toast(...args) {
		if (args.length === 0) return {
			success: (...props) => toast({
				...parseArgs(...props),
				type: "success"
			}),
			error: (...props) => toast({
				...parseArgs(...props),
				type: "error"
			}),
			info: (...props) => toast({
				...parseArgs(...props),
				type: "info"
			}),
			warning: (...props) => toast({
				...parseArgs(...props),
				type: "warning"
			})
		};
		document.dispatchEvent(new CustomEvent("toast", { detail: parseArgs(...args) }));
	}
	var parseArgs = (...args) => {
		if (typeof args[0] === "object" && args[0] !== null && !Array.isArray(args[0])) return args[0];
		const [message, title, type, duration, position, progress, size] = args;
		return {
			message,
			title,
			type,
			duration,
			position,
			progress,
			size
		};
	};
	//#endregion
	//#region resources/js/tallkit.js
	var tallkit = {
		appearance,
		toast,
		loadScript,
		loadStyle,
		modal: (name) => {
			return {
				show: () => {
					document.dispatchEvent(new CustomEvent("modal-show", { detail: { name } }));
				},
				close: () => {
					document.dispatchEvent(new CustomEvent("modal-close", { detail: { name } }));
				}
			};
		},
		modals: () => {
			return { close: () => {
				document.dispatchEvent(new CustomEvent("modal-close", { detail: {} }));
			} };
		}
	};
	window.TALLKit = window.TK = window.tk = window.tallkit = tallkit;
	document.dispatchEvent(new CustomEvent("tallkit:init"));
	initAlpine();
	document.addEventListener("alpine:init", () => setupAlpine(tallkit));
	//#endregion
	exports.tallkit = tallkit;
});
