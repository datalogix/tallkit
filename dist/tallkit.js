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
	//#region resources/js/utils/locale.js
	function canonical(locale) {
		try {
			return Intl.getCanonicalLocales(String(locale).replace("_", "-"))[0] ?? null;
		} catch {
			return null;
		}
	}
	function resolveLocale(locale = null) {
		return locale && canonical(locale) || typeof document !== "undefined" && document.documentElement.lang && canonical(document.documentElement.lang) || typeof navigator !== "undefined" && navigator.language && canonical(navigator.language) || "en-US";
	}
	//#endregion
	//#region resources/js/utils/number.js
	function toNumber(value, fallback = null) {
		const parsed = Number.parseFloat(value);
		return Number.isFinite(parsed) ? parsed : fallback;
	}
	function clamp(value, min, max) {
		return Math.max(min, Math.min(value, max));
	}
	function formatNumber(value, options = {}) {
		return new Intl.NumberFormat(resolveLocale(), options).format(value);
	}
	//#endregion
	//#region resources/js/utils/timer.js
	function toMilliseconds(value, fallback = 0) {
		const parsed = toNumber(value);
		if (parsed === null) return fallback;
		return Math.max(/\ds$/.test(String(value).trim()) ? parsed * 1e3 : parsed, 0);
	}
	function startTimeout(callback, milliseconds, defaultMilliseconds = 500) {
		return setTimeout(callback, toMilliseconds(milliseconds, defaultMilliseconds));
	}
	function startInterval(callback, milliseconds, defaultMilliseconds = 500) {
		return setInterval(callback, toMilliseconds(milliseconds, defaultMilliseconds));
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
	//#region resources/js/utils/animation.js
	function prefersReducedMotion() {
		return window.matchMedia?.("(prefers-reduced-motion: reduce)").matches ?? false;
	}
	function getTransitionTimeout(element) {
		const style = window.getComputedStyle(element);
		const durations = style.transitionDuration.split(",");
		const delays = style.transitionDelay.split(",");
		return durations.reduce((max, duration, index) => {
			const delay = delays[index] ?? delays[delays.length - 1] ?? "0s";
			return Math.max(max, toMilliseconds(duration) + toMilliseconds(delay));
		}, 0);
	}
	function runTransition(el, options = {}) {
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
		const applyClasses = (remove = [], add = []) => {
			if (remove.length) el.classList.remove(...remove);
			if (add.length) el.classList.add(...add);
		};
		if (prefersReducedMotion()) {
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
		return runTransition(el, {
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
		return runTransition(el, {
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
	//#region resources/js/utils/string.js
	function parseCommaList(value) {
		if (!value) return [];
		if (Array.isArray(value)) return value.filter(Boolean);
		return String(value).split(",").map((v) => v.trim()).filter(Boolean);
	}
	function escapeHtml(str) {
		if (str == null) return str;
		return String(str).replace(/[&<>"']/g, (char) => ({
			"&": "&amp;",
			"<": "&lt;",
			">": "&gt;",
			"\"": "&quot;",
			"'": "&#39;"
		})[char]);
	}
	function slug(str) {
		return normalizeText(str, {
			replaceAccents: true,
			removeSpaces: true,
			replaceSpaces: "-",
			lowercase: true,
			mode: "alphanumeric"
		});
	}
	function normalizeText(str, options) {
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
	function safeUrl(url) {
		if (url === null || url === void 0 || String(url).trim() === "") return null;
		try {
			const { protocol } = new URL(String(url), window.location.href);
			return protocol === "http:" || protocol === "https:" ? String(url) : null;
		} catch {
			return null;
		}
	}
	//#endregion
	//#region resources/js/utils/naming.js
	var PREFIX = "tallkit";
	function eventName(name) {
		return `${PREFIX}:${name}`;
	}
	function storageKey(...parts) {
		return parts.every((part) => part !== null && part !== void 0 && part !== "") ? [PREFIX, ...parts].join(".") : null;
	}
	function dataKey(name) {
		return `data-${PREFIX}-${name}`;
	}
	function dataSelector(name, value) {
		return value ? `[${dataKey(name)}="${CSS.escape(String(value))}"]` : `[${dataKey(name)}]`;
	}
	function queryData(root, name, value) {
		return root?.querySelector(dataSelector(name, value)) ?? null;
	}
	function queryAllData(root, name, value) {
		return Array.from(root?.querySelectorAll(dataSelector(name, value)) ?? []);
	}
	function generateId(prefix, name, suffix) {
		return slug([
			"tallkit",
			prefix,
			name ?? Math.random().toString(36).slice(2, 9),
			suffix
		].filter(Boolean).join("-")) ?? "";
	}
	//#endregion
	//#region resources/js/utils/announce.js
	var region = null;
	function announce(text) {
		if (!text || typeof document === "undefined") return;
		if (!region || !region.isConnected) {
			region = document.createElement("div");
			region.setAttribute("aria-live", "polite");
			region.setAttribute("aria-atomic", "true");
			region.setAttribute(dataKey("announcer"), "");
			region.style.cssText = "position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0";
			document.body.appendChild(region);
		}
		region.textContent = "";
		setTimeout(() => {
			region.textContent = String(text);
		}, 50);
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
			const existing = findElement("script", "src", src);
			if (existing) {
				waitForExisting(existing, resolve, reject);
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
				script.remove();
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
			const existing = findElement("link[rel=\"stylesheet\"]", "href", href);
			if (existing) {
				waitForExisting(existing, resolve, reject);
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
				link.remove();
				reject(e);
			};
			document.head.appendChild(link);
		});
		styles.set(href, promise);
		return promise;
	}
	function findElement(selector, attribute, value) {
		return Array.from(document.querySelectorAll(selector)).find((el) => el.getAttribute(attribute) === value) ?? null;
	}
	function waitForExisting(el, resolve, reject) {
		if (document.readyState === "complete") {
			resolve(new Event("load"));
			return;
		}
		const done = (event) => {
			el.removeEventListener("load", done);
			el.removeEventListener("error", fail);
			window.removeEventListener("load", done);
			resolve(event);
		};
		const fail = (event) => {
			el.removeEventListener("load", done);
			el.removeEventListener("error", fail);
			window.removeEventListener("load", done);
			reject(event);
		};
		el.addEventListener("load", done);
		el.addEventListener("error", fail);
		window.addEventListener("load", done);
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
		const typable = !String(shortcut).split(".").some((key) => [
			"ctrl",
			"cmd",
			"meta",
			"alt"
		].includes(key));
		bind(el, { [`@keydown.${shortcut}.document`](event) {
			if (typable && isTypingIn(event.target)) return;
			if (callback(event) === false) return;
			event.preventDefault();
		} });
	}
	var NON_TEXT_INPUTS = [
		"checkbox",
		"radio",
		"button",
		"submit",
		"reset",
		"range",
		"color",
		"file",
		"image"
	];
	function isTypingIn(target) {
		if (!(target instanceof Element)) return false;
		if (target.isContentEditable || target.closest("[contenteditable]:not([contenteditable=\"false\"])")) return true;
		if (target.matches("textarea, select")) return true;
		return target.matches("input") && !NON_TEXT_INPUTS.includes(target.type);
	}
	//#endregion
	//#region resources/js/utils/storage.js
	var STORAGES = {
		local: "localStorage",
		session: "sessionStorage"
	};
	function getBrowserStorage(type = "local") {
		try {
			return STORAGES[type] ? window[STORAGES[type]] : null;
		} catch (e) {
			return null;
		}
	}
	function browserStorage(type = "local") {
		const store = () => getBrowserStorage(type);
		const getText = (key, fallback = null) => {
			const s = store();
			if (!key || !s) return fallback;
			try {
				return s.getItem(key) ?? fallback;
			} catch (e) {
				return fallback;
			}
		};
		const setText = (key, text) => {
			const s = store();
			if (!key || !s) return false;
			try {
				s.setItem(key, String(text));
				return true;
			} catch (e) {
				return false;
			}
		};
		const remove = (key) => {
			const s = store();
			if (!key || !s) return;
			try {
				s.removeItem(key);
			} catch (e) {}
		};
		const get = (key, fallback = null) => {
			const text = getText(key);
			if (text === null) return fallback;
			try {
				return JSON.parse(text);
			} catch (e) {
				return fallback;
			}
		};
		const set = (key, value) => {
			try {
				return setText(key, JSON.stringify(value));
			} catch (e) {
				return false;
			}
		};
		const getPart = (key, part, fallback = null) => {
			const stored = get(key);
			return isPlainObject(stored) && part in stored ? stored[part] : fallback;
		};
		const setPart = (key, part, value) => {
			const stored = get(key);
			return set(key, {
				...isPlainObject(stored) ? stored : {},
				[part]: value
			});
		};
		const removePart = (key, part) => {
			const stored = get(key);
			if (!isPlainObject(stored) || !(part in stored)) return;
			const { [part]: removed, ...rest } = stored;
			if (Object.keys(rest).length) set(key, rest);
			else remove(key);
		};
		const keys = (prefix = "") => {
			const s = store();
			if (!s) return [];
			try {
				return Array.from({ length: s.length }, (_, index) => s.key(index)).filter((key) => key?.startsWith(prefix));
			} catch (e) {
				return [];
			}
		};
		return {
			getText,
			setText,
			remove,
			get,
			set,
			getPart,
			setPart,
			removePart,
			keys
		};
	}
	function isPlainObject(value) {
		return value !== null && typeof value === "object" && !Array.isArray(value);
	}
	var local = browserStorage("local");
	var getStoredText = local.getText;
	var setStoredText = local.setText;
	var removeStored = local.remove;
	var getStoredPart = local.getPart;
	var setStoredPart = local.setPart;
	var removeStoredPart = local.removePart;
	//#endregion
	//#region resources/js/utils/cache.js
	function createCache(name, { ttl = 36e5, persist = true, storage = "local" } = {}) {
		const memory = /* @__PURE__ */ new Map();
		const store = persist ? browserStorage(storage) : null;
		const isFresh = (entry) => entry !== null && typeof entry === "object" && Date.now() <= entry.exp;
		return {
			getStorageKey(key) {
				return storageKey("cache", name, key);
			},
			get(key) {
				const mem = memory.get(key);
				if (mem) {
					if (isFresh(mem)) return mem.data;
					memory.delete(key);
				}
				if (!store) return null;
				const stored = store.get(this.getStorageKey(key));
				if (stored === null) return null;
				if (!isFresh(stored)) {
					store.remove(this.getStorageKey(key));
					return null;
				}
				memory.set(key, stored);
				return stored.data;
			},
			set(key, data) {
				const entry = {
					data,
					exp: Date.now() + ttl
				};
				memory.set(key, entry);
				if (store) {
					this.prune();
					store.set(this.getStorageKey(key), entry);
				}
			},
			prune() {
				if (!store) return;
				for (const key of store.keys(`${storageKey("cache", name)}.`)) if (!isFresh(store.get(key))) store.remove(key);
			}
		};
	}
	//#endregion
	//#region resources/js/utils/color.js
	var HEX_RE = /^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i;
	var RGB_RE = /^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+%?)\s*)?\)$/i;
	var HSL_RE = /^hsla?\(\s*([\d.]+)\s*,\s*([\d.]+)%\s*,\s*([\d.]+)%\s*(?:,\s*([\d.]+%?)\s*)?\)$/i;
	function clamp255(value) {
		return clamp(Math.round(Number(value)), 0, 255);
	}
	function clampAlpha(value) {
		return clamp(Number.isFinite(value) ? value : 1, 0, 1);
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
		return parseCssColor(value);
	}
	var CONTEXTUAL = /^(currentcolor|inherit|initial|unset|revert|revert-layer)$|var\(|env\(|attr\(/i;
	var pixel = null;
	function parseCssColor(value) {
		if (CONTEXTUAL.test(value) || typeof document === "undefined" || !window.CSS?.supports?.("color", value)) return null;
		try {
			pixel ??= document.createElement("canvas").getContext("2d", { willReadFrequently: true });
			if (!pixel) return null;
			pixel.clearRect(0, 0, 1, 1);
			pixel.fillStyle = value;
			pixel.fillRect(0, 0, 1, 1);
			const [r, g, b, alpha] = pixel.getImageData(0, 0, 1, 1).data;
			return {
				r,
				g,
				b,
				a: roundAlpha(alpha / 255)
			};
		} catch {
			return null;
		}
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
	//#region resources/js/utils/color-scheme.js
	var isDarkMode = () => document.documentElement.classList.contains("dark");
	function onColorSchemeChange(callback) {
		let dark = isDarkMode();
		const observer = new MutationObserver(() => {
			if (isDarkMode() === dark) return;
			dark = isDarkMode();
			callback(dark);
		});
		observer.observe(document.documentElement, {
			attributes: true,
			attributeFilter: ["class"]
		});
		return () => observer.disconnect();
	}
	//#endregion
	//#region resources/js/utils/datetime.js
	function padDatePart(n) {
		return String(n).padStart(2, "0");
	}
	function formatIsoDate(date) {
		return `${date.getFullYear()}-${padDatePart(date.getMonth() + 1)}-${padDatePart(date.getDate())}`;
	}
	function normalizeIsoDate(value) {
		const match = /^(\d{4})-(\d{2})-(\d{2})(?:$|[T\s])/.exec(String(value ?? "").trim());
		if (!match) return null;
		const [, y, m, d] = match.map(Number);
		const date = new Date(y, m - 1, d);
		if (date.getFullYear() !== y || date.getMonth() !== m - 1 || date.getDate() !== d) return null;
		return `${match[1]}-${match[2]}-${match[3]}`;
	}
	function parseIsoDate(iso) {
		iso = normalizeIsoDate(iso);
		if (!iso) return null;
		const [y, m, d] = iso.split("-").map(Number);
		const date = new Date(y, m - 1, d);
		if (date.getFullYear() !== y || date.getMonth() !== m - 1 || date.getDate() !== d) return null;
		return date;
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
		return addDays(formatIsoDate(date), -offset);
	}
	function addDays(iso, n) {
		const date = parseIsoDate(iso);
		date.setDate(date.getDate() + n);
		return formatIsoDate(date);
	}
	function isSameMonth(a, b) {
		return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth();
	}
	function diffDays(isoA, isoB) {
		return Math.round((parseIsoDate(isoB) - parseIsoDate(isoA)) / 864e5);
	}
	function isoWeekNumber(date) {
		const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
		const dayNum = d.getUTCDay() || 7;
		d.setUTCDate(d.getUTCDate() + 4 - dayNum);
		const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
		return Math.ceil(((d - yearStart) / 864e5 + 1) / 7);
	}
	function localeFirstDay(locale) {
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
	function formatTypedDate(iso, locale) {
		const date = parseIsoDate(iso);
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
		const order = /^\s*\d{4}\D/.test(String(text)) ? [
			"year",
			"month",
			"day"
		] : localeDateOrder(locale);
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
		return formatIsoDate(date);
	}
	function timeToMinutes(hhmm) {
		const [h, m] = hhmm.split(":").map(Number);
		return h * 60 + m;
	}
	function parseTypedTime(token) {
		let text = String(token ?? "").trim().toLowerCase().replace(/\./g, "");
		if (!text) return null;
		const dateTime = /^\d{4}-\d{2}-\d{2}[t ](.+)$/.exec(text);
		if (dateTime) text = dateTime[1].replace(/(z|[+-]\d{2}:?\d{2})$/, "");
		const match = /^(\d{1,2}):(\d{2})(?::\d{2}(?:\d+)?)?\s*(am|pm)?$/.exec(text) ?? /^(\d{1,2})h(\d{2})?$/.exec(text) ?? /^(\d{1,2})(\d{2})\s*(am|pm)?$/.exec(text) ?? /^(\d{1,2})()\s*(am|pm)$/.exec(text);
		if (!match) return null;
		let h = Number(match[1]);
		const m = Number(match[2] || 0);
		const meridiem = match[3];
		if (meridiem) {
			if (h < 1 || h > 12) return null;
			if (meridiem === "pm" && h < 12) h += 12;
			if (meridiem === "am" && h === 12) h = 0;
		}
		if (h > 23 || m > 59) return null;
		return `${padDatePart(h)}:${padDatePart(m)}`;
	}
	//#endregion
	//#region resources/js/utils/direction.js
	function isRtl(el = document.documentElement) {
		return el.dir === "rtl" || getComputedStyle(el).direction === "rtl";
	}
	//#endregion
	//#region resources/js/utils/escape-key.js
	var handled = /* @__PURE__ */ new WeakSet();
	function markEscapeHandled(event) {
		handled.add(event);
	}
	function isEscapeHandled(event) {
		return handled.has(event);
	}
	var layers = [];
	function onEscape(event) {
		if (event.key !== "Escape") return;
		while (layers.length && layers[layers.length - 1].popoverElement?.isConnected === false) layers.pop();
		if (!layers.length) window.removeEventListener("keydown", onEscape, true);
		const top = layers[layers.length - 1];
		if (!top) return;
		markEscapeHandled(event);
		const focusWasInside = top.popoverElement?.contains(document.activeElement);
		top.close();
		if (focusWasInside) top.ariaTrigger?.focus?.();
	}
	function pushEscapeLayer(layer) {
		removeEscapeLayer(layer);
		layers.push(layer);
		if (layers.length === 1) window.addEventListener("keydown", onEscape, true);
	}
	function removeEscapeLayer(layer) {
		const index = layers.indexOf(layer);
		if (index !== -1) layers.splice(index, 1);
		if (!layers.length) window.removeEventListener("keydown", onEscape, true);
	}
	//#endregion
	//#region resources/js/utils/event.js
	function emit(el, name, detail = {}, { later = false, bubbles = false, cancelable = false } = {}) {
		if (!el) return null;
		const event = new CustomEvent(name, {
			detail,
			bubbles,
			cancelable
		});
		if (later) {
			queueMicrotask(() => el.dispatchEvent(event));
			return null;
		}
		el.dispatchEvent(event);
		return event;
	}
	function listenWhileConnected(root, target, type, handler) {
		if (!target) return () => {};
		const controller = new AbortController();
		target.addEventListener(type, handler, { signal: controller.signal });
		window.Alpine?.onElRemoved?.(root, () => controller.abort());
		return () => controller.abort();
	}
	function onFormReset(root, form, callback) {
		return listenWhileConnected(root, form, "reset", () => setTimeout(() => {
			if (root.isConnected) callback();
		}));
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
		const units = [
			"B",
			"KB",
			"MB",
			"GB",
			"TB"
		];
		const exponent = bytes > 0 ? Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1) : 0;
		return `${formatNumber(bytes > 0 ? bytes / Math.pow(1024, exponent) : 0, { maximumFractionDigits: exponent === 0 ? 0 : decimals })} ${units[exponent]}`;
	}
	function detectFileType(type, name, fileTypes = {}) {
		if (type?.startsWith("image/")) return "image";
		if (type?.startsWith("video/")) return "video";
		if (type?.startsWith("audio/")) return "audio";
		const extension = name?.includes(".") ? name.split(".").pop().toLowerCase() : "";
		return Object.keys(fileTypes).find((kind) => fileTypes[kind].includes(extension)) ?? "unknown";
	}
	//#endregion
	//#region resources/js/utils/focus.js
	var FOCUSABLE$1 = [
		"a[href]",
		"area[href]",
		"button:not([disabled])",
		"input:not([disabled]):not([type=\"hidden\"])",
		"select:not([disabled])",
		"textarea:not([disabled])",
		"summary",
		"[tabindex]:not([tabindex=\"-1\"])",
		"[contenteditable]:not([contenteditable=\"false\"])"
	].join(",");
	function isRendered(el) {
		return el.getClientRects().length > 0;
	}
	function focusTargetOutside(el) {
		const candidates = Array.from(document.querySelectorAll(FOCUSABLE$1)).filter((node) => !el.contains(node) && !node.closest("[hidden], [inert]") && isRendered(node));
		return candidates.find((node) => el.compareDocumentPosition(node) & Node.DOCUMENT_POSITION_FOLLOWING) ?? candidates.reverse().find((node) => el.compareDocumentPosition(node) & Node.DOCUMENT_POSITION_PRECEDING) ?? null;
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
		return queryData(el?.closest(dataSelector(ancestorKey)), childKey);
	}
	function findFieldInput(el) {
		return findInField(el, "input", "field-control");
	}
	function allChecked(items, getChecked) {
		return items.length > 0 && items.every(getChecked);
	}
	var labelTargets = /* @__PURE__ */ new Map();
	function onLabelClick(e) {
		const label = e.target?.closest?.("label");
		const target = label?.htmlFor ? document.getElementById(label.htmlFor) : null;
		const focus = target && labelTargets.get(target);
		if (!focus) return;
		e.preventDefault();
		focus();
	}
	function focusOnLabelClick(target, focus) {
		if (!target?.id) return () => {};
		if (labelTargets.size === 0) document.addEventListener("click", onLabelClick);
		labelTargets.set(target, focus);
		return () => {
			labelTargets.delete(target);
			if (labelTargets.size === 0) document.removeEventListener("click", onLabelClick);
		};
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
	function keepAttributesOnMorph(match, names) {
		const off = window.Livewire?.hook("morph.updating", ({ el, toEl }) => {
			if (!toEl?.setAttribute || !el?.getAttribute || !match(el)) return;
			for (const name of names) {
				const value = el.getAttribute(name);
				if (value !== null) toEl.setAttribute(name, value);
			}
		});
		return typeof off === "function" ? off : () => {};
	}
	var skippingDismissed = false;
	function keepDismissed(el) {
		el.hidden = true;
		el.setAttribute(dataKey("dismissed"), "");
		if (skippingDismissed || !window.Livewire) return;
		skippingDismissed = true;
		window.Livewire.hook("morph.updating", ({ el: target, toEl, skip }) => {
			if (!target?.hasAttribute?.(dataKey("dismissed"))) return;
			const html = toEl?.outerHTML ?? null;
			target.__tallkitDismissedHtml ??= html;
			if (html !== null && html !== target.__tallkitDismissedHtml) {
				target.removeAttribute(dataKey("dismissed"));
				target.hidden = false;
				delete target.__tallkitDismissedHtml;
				emit(target, "restored");
				return;
			}
			skip();
		});
	}
	var syncingIgnoredFields = false;
	function syncIgnoredFieldState() {
		if (syncingIgnoredFields || !window.Livewire) return;
		syncingIgnoredFields = true;
		const attributes = [
			"aria-invalid",
			"data-invalid",
			"aria-describedby"
		];
		window.Livewire.hook("morph.updating", ({ el, toEl }) => {
			if (!el?.hasAttribute?.("wire:ignore") || !toEl?.querySelectorAll) return;
			const pairs = [[el, toEl]];
			for (const to of toEl.querySelectorAll("[id]")) {
				const from = el.querySelector(`#${CSS.escape(to.id)}`);
				if (from) pairs.push([from, to]);
			}
			for (const [from, to] of pairs) for (const name of attributes) {
				const value = to.getAttribute(name);
				if (value === null) from.removeAttribute(name);
				else if (from.getAttribute(name) !== value) from.setAttribute(name, value);
			}
		});
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
	function hasBlurModel(field) {
		return !!field && [...field.attributes].some((attr) => /^(wire:model|x-model)\b/.test(attr.name) && /\.blur\b/.test(attr.name));
	}
	function blurOnFocusLeave(root, field, onBlur = () => field.dispatchEvent(new Event("blur"))) {
		if (!root || !field) return;
		root.addEventListener("focusout", (e) => {
			if (e.target === field) return;
			if (e.relatedTarget && root.contains(e.relatedTarget)) return;
			onBlur();
		});
	}
	//#endregion
	//#region resources/js/utils/position.js
	function placeNextTo(panel, rect, { position = "bottom", align = "end", margin = 4, rtl = false } = {}) {
		if (!panel.offsetWidth && !panel.offsetHeight) return false;
		panel.style.position = "absolute";
		panel.style.inset = "auto";
		panel.style.top = "0px";
		panel.style.left = "0px";
		const scrollTop = window.scrollY;
		const scrollLeft = window.scrollX;
		const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
		const viewportHeight = document.documentElement.clientHeight || window.innerHeight;
		const panelHeight = panel.offsetHeight;
		const panelWidth = panel.offsetWidth;
		const resolveAlign = (align) => {
			if (align === "start") return rtl ? "right" : "left";
			if (align === "end") return rtl ? "left" : "right";
			return align;
		};
		const getCenterOffset = (pos, align) => {
			align = resolveAlign(align);
			if (align === "left") return 0;
			if (align === "right") return pos === "left" || pos === "right" ? rect.height - panelHeight : rect.width - panelWidth;
			return pos === "left" || pos === "right" ? (rect.height - panelHeight) / 2 : (rect.width - panelWidth) / 2;
		};
		const getCoords = (pos, align) => {
			const center = getCenterOffset(pos, align);
			let top = 0, left = 0;
			switch (pos) {
				case "right":
					left = rect.right + margin + scrollLeft;
					top = rect.top + center + scrollTop;
					break;
				case "left":
					left = rect.left - panelWidth - margin + scrollLeft;
					top = rect.top + center + scrollTop;
					break;
				case "bottom":
					top = rect.bottom + margin + scrollTop;
					left = rect.left + center + scrollLeft;
					break;
				case "top":
					top = rect.top - panelHeight - margin + scrollTop;
					left = rect.left + center + scrollLeft;
			}
			return {
				top,
				left
			};
		};
		const isVisible = ({ top, left }) => top >= scrollTop && left >= scrollLeft && top + panelHeight <= scrollTop + viewportHeight && left + panelWidth <= scrollLeft + viewportWidth;
		const opposites = {
			top: "bottom",
			bottom: "top",
			left: "right",
			right: "left"
		};
		const aligns = [
			"start",
			"left",
			"end",
			"right",
			"center"
		];
		let computedPosition = {
			start: rtl ? "right" : "left",
			end: rtl ? "left" : "right"
		}[position] ?? (position || "bottom");
		let computedAlign = align || "end";
		let coords = getCoords(computedPosition, computedAlign);
		let found = false;
		if (!isVisible(coords)) {
			const fallbacks = [opposites[computedPosition], ...[
				"top",
				"bottom",
				"left",
				"right"
			].filter((p) => p !== computedPosition && p !== opposites[computedPosition])];
			for (const pos of [computedPosition, ...fallbacks]) {
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
		if (!found && !isVisible(coords)) {
			const gap = 8;
			const fit = (value, size, start, room) => size + 16 > room ? start + gap : clamp(value, start + gap, start + room - size - gap);
			coords = {
				top: fit(coords.top, panelHeight, scrollTop, viewportHeight),
				left: fit(coords.left, panelWidth, scrollLeft, viewportWidth)
			};
		}
		const vertical = computedPosition === "left" || computedPosition === "right";
		const size = vertical ? panelHeight : panelWidth;
		const middle = vertical ? rect.top + rect.height / 2 + scrollTop - coords.top : rect.left + rect.width / 2 + scrollLeft - coords.left;
		const corner = 12;
		const arrowOffset = size < 24 ? size / 2 : clamp(middle, corner, size - corner);
		panel.style.top = `${coords.top}px`;
		panel.style.left = `${coords.left}px`;
		panel.style.setProperty("--tk-arrow-offset", `${arrowOffset}px`);
		panel.dataset.position = computedPosition;
		panel.dataset.align = computedAlign === "center" ? "center" : resolveAlign(computedAlign);
		return true;
	}
	//#endregion
	//#region resources/js/utils/submit.js
	var guarding = false;
	function guardFormResubmit() {
		if (guarding) return;
		guarding = true;
		document.addEventListener("submit", (event) => {
			const form = event.target;
			setTimeout(() => {
				if (event.defaultPrevented || !(form instanceof HTMLFormElement)) return;
				const target = event.submitter?.getAttribute("formtarget") || form.getAttribute("target");
				if (target && target !== "_self" || form.hasAttribute("data-allow-resubmit") || event.submitter?.hasAttribute("data-allow-resubmit")) return;
				const buttons = [...form.querySelectorAll(`button[type=submit]${dataSelector("button-loading")}:not([disabled])`)];
				for (const button of buttons) {
					button.disabled = true;
					button.setAttribute(dataKey("submitting"), "");
				}
				setTimeout(() => {
					for (const button of buttons) {
						if (!button.hasAttribute(dataKey("submitting"))) continue;
						button.disabled = false;
						button.removeAttribute(dataKey("submitting"));
					}
				}, 1e4);
			});
		});
		window.addEventListener("pageshow", (event) => {
			if (!event.persisted) return;
			for (const button of queryAllData(document, "submitting")) {
				button.disabled = false;
				button.removeAttribute(dataKey("submitting"));
			}
		});
	}
	var guardingLoading = false;
	function guardLoadingButtons() {
		if (guardingLoading) return;
		guardingLoading = true;
		document.addEventListener("click", (event) => {
			if (event.target?.closest?.(`${dataSelector("button")}[aria-disabled=true]`)) {
				event.preventDefault();
				event.stopImmediatePropagation();
				return;
			}
			const button = event.target?.closest?.(`${dataSelector("button")}${dataSelector("button-loading")}`);
			if (!button || !button.hasAttribute("wire:loading.attr")) return;
			event.preventDefault();
			event.stopImmediatePropagation();
		}, true);
	}
	//#endregion
	//#region resources/js/toast.js
	function toast$1(...args) {
		if (args.length === 0) return {
			success: (...props) => toast$1({
				...parseArgs(...props),
				type: "success"
			}),
			error: (...props) => toast$1({
				...parseArgs(...props),
				type: "error"
			}),
			info: (...props) => toast$1({
				...parseArgs(...props),
				type: "info"
			}),
			warning: (...props) => toast$1({
				...parseArgs(...props),
				type: "warning"
			}),
			loading: (...props) => toast$1({
				duration: false,
				progress: false,
				swipe: false,
				...parseArgs(...props),
				type: "loading"
			}),
			promise: (promise, messages = {}) => container()?.promise(promise, messages) ?? promise,
			close: (id) => sendToastEvent(eventName("toast-close"), { id })
		};
		sendToastEvent(eventName("toast"), parseArgs(...args));
	}
	var container = () => {
		const el = window.__tallkitToastReady ? window.__tallkitToastContainer : null;
		return el?.isConnected && window.Alpine ? window.Alpine.$data(el) : null;
	};
	function sendToastEvent(event, detail) {
		if (window.__tallkitToastReady) emit(document, event, detail);
		else (window.__tallkitToastQueue ??= []).push({
			event,
			detail
		});
	}
	var parseArgs = (...args) => {
		if (typeof args[0] === "object" && args[0] !== null && !Array.isArray(args[0])) return args[0];
		const [message, title, type, duration, position, progress, size, invert, actions, id] = args;
		return Object.fromEntries(Object.entries({
			message,
			title,
			type,
			duration,
			position,
			progress,
			size,
			invert,
			actions,
			id
		}).filter(([, value]) => value !== null && value !== void 0));
	};
	//#endregion
	//#region resources/js/utils/upload.js
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
	function text(messages, key, replace = {}) {
		return Object.entries(replace).reduce((result, [name, value]) => result.replaceAll(`:${name}`, value), messages?.[key] ?? {
			tooLarge: "The file may not be larger than :size.",
			invalidType: "This file type is not allowed.",
			failed: "The file could not be uploaded."
		}[key]);
	}
	async function uploadEditorFile(file, type, upload, messages = {}) {
		if (!upload?.url) return readAsDataURL(file);
		if (upload.maxSize && !(type in upload.maxSize)) throw new Error(text(messages, "invalidType"));
		const limit = upload.maxSize?.[type];
		if (limit && file.size > limit * 1024) throw new Error(text(messages, "tooLarge", { size: formatBytes(limit * 1024) }));
		const body = new FormData();
		body.append("file", file, file.name);
		if (limit) body.append("max_size", String(limit));
		const response = await fetch(upload.url, {
			method: "POST",
			credentials: "same-origin",
			headers: {
				Accept: "application/json",
				"X-XSRF-TOKEN": getCsrfToken() ?? ""
			},
			body
		}).catch(() => {
			throw new Error(text(messages, "failed"));
		});
		const result = await response.json().catch(() => ({}));
		if (!response.ok || !result.url) throw new Error(result.message || text(messages, "failed"));
		return result.url;
	}
	function reportUploadFailed(el, error, file, type, messages = {}, notify = true) {
		console.error("[tallkit] An upload failed.", error);
		const message = error instanceof Error && error.message ? error.message : text(messages, "failed");
		if (emit(el, "upload-failed", {
			error,
			file,
			type,
			message
		}, {
			bubbles: true,
			cancelable: true
		})?.defaultPrevented || !notify) return message;
		if (window.__tallkitToastContainer?.isConnected) toast$1().error({ message });
		else window.alert(message);
		return message;
	}
	//#endregion
	//#region resources/js/components/address-form.js
	var address_form_exports = /* @__PURE__ */ __exportAll({ addressForm: () => addressForm });
	function addressForm(options = {}) {
		const _cache = createCache("zipcode", {
			storage: "session",
			...options
		});
		return {
			abortController: null,
			$els: {},
			init() {
				this.$els = {
					loading: queryData(this.$root, "loading"),
					zipcode: queryData(this.$root, "address-form-zipcode"),
					address: queryData(this.$root, "address-form-address"),
					number: queryData(this.$root, "address-form-number"),
					complement: queryData(this.$root, "address-form-complement"),
					neighborhood: queryData(this.$root, "address-form-neighborhood"),
					city: queryData(this.$root, "address-form-city"),
					state: queryData(this.$root, "address-form-state")
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
				if (this.abortController) {
					this.abortController.abort();
					this.abortController = null;
					this.setLoading(false);
				}
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
					emit(this.$root, "loaded", {
						zipcode,
						data: cached,
						cached: true
					});
					this.setLoading(false);
					this.abortController = null;
					return;
				}
				this.setLoading(true);
				emit(this.$root, "loading", { zipcode });
				try {
					const data = await this.resolveAddress(zipcode, signal);
					if (signal.aborted) return;
					_cache.set(zipcode, data);
					this.fill(data);
					emit(this.$root, "loaded", {
						zipcode,
						data,
						cached: false
					});
				} catch (e) {
					if (e.name === "AbortError" || signal.aborted) return;
					emit(this.$root, "error", {
						zipcode,
						error: e
					});
					this.$els.zipcode?.focus();
				} finally {
					if (!signal.aborted) this.setLoading(false);
					if (this.abortController === controller) this.abortController = null;
				}
			},
			destroy() {
				this.abortController?.abort();
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/dismissible.js
	function closestDismissible(el) {
		for (let node = el; node; node = node.parentElement) if (node.__tallkitDismissible) return node;
		return null;
	}
	function dismissible(animation) {
		return {
			cancelDismiss: null,
			isDismissing: false,
			_dismissTimeout: null,
			init() {
				this.$root.__tallkitDismissible = true;
				bind(this.$root, {
					["@click"]: (event) => {
						const trigger = event.target.closest?.(dataSelector("dismissible"));
						if (!trigger || !this.$root.contains(trigger)) return;
						if (closestDismissible(trigger) !== this.$root) return;
						event.stopPropagation();
						this.dismiss("manual");
					},
					[`@${eventName("dismiss")}`]: (e) => {
						const detail = e.detail || {};
						this.dismiss(detail.reason || "programmatic");
					}
				});
			},
			beforeDismiss() {},
			dismiss(reason = "programmatic") {
				if (this.isDismissing) return;
				if (emit(this.$root, "before-dismiss", { reason }, { cancelable: true })?.defaultPrevented) return;
				this.isDismissing = true;
				this.beforeDismiss();
				const focusTarget = this.$root.contains(document.activeElement) ? focusTargetOutside(this.$root) : null;
				this.cancelDismiss?.();
				this.cancelDismiss = null;
				const onDone = () => {
					this.isDismissing = false;
					this.cancelDismiss = null;
					emit(this.$root, "dismissed", { reason });
					focusTarget?.isConnected && focusTarget.focus({ preventScroll: true });
					if (!this.$root.isConnected) return;
					if (hasLivewire() && this.$root.closest("[wire\\:id]")) keepDismissed(this.$root);
					else this.$root.remove();
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
	function alertComponent({ duration: given = 0, pauseOnHover = false } = {}) {
		const _dismissible = dismissible("collapse");
		const duration = given === true ? 7e3 : toMilliseconds(given);
		return {
			..._dismissible,
			timeoutId: null,
			remaining: duration,
			startedAt: 0,
			pauseReasons: /* @__PURE__ */ new Set(),
			progressValue: 100,
			progressFrame: null,
			visibilityHandler: null,
			state: "idle",
			init() {
				_dismissible.init.call(this);
				this.startTimer();
				this.visibilityHandler = this.handleVisibility.bind(this);
				document.addEventListener("visibilitychange", this.visibilityHandler);
				if (document.hidden) this.pause("visibility");
				bind(this.$root, {
					...pauseOnHover ? {
						["@mouseenter"]: () => this.pause("hover"),
						["@mouseleave"]: () => this.resume("hover")
					} : {},
					["@focusin"]: () => this.pause("focus"),
					["@focusout"]: (event) => {
						if (!this.$root.contains(event.relatedTarget)) this.resume("focus");
					},
					[`@${eventName("pause")}`]: () => this.pause("external"),
					[`@${eventName("resume")}`]: () => this.resume("external"),
					["@restored.self"]: () => this.$nextTick(() => this.restart())
				});
			},
			startTimer() {
				if (!duration || this.remaining <= 0 || this.timeoutId || this.state === "dismissing") return;
				this.state = "running";
				this.startedAt = Date.now();
				this.timeoutId = startTimeout(() => this.dismiss("timeout"), this.remaining, duration);
				this.trackProgress();
			},
			trackProgress() {
				cancelAnimationFrame(this.progressFrame);
				const step = () => {
					if (this.state !== "running") return;
					const left = this.remaining - (Date.now() - this.startedAt);
					this.progressValue = Math.max(0, Math.min(100, left / duration * 100));
					this.progressFrame = requestAnimationFrame(step);
				};
				step();
			},
			pause(reason = "manual") {
				this.pauseReasons.add(reason);
				if (!this.timeoutId) return;
				const elapsed = Date.now() - this.startedAt;
				this.remaining = Math.max(this.remaining - elapsed, 0);
				clearTimeout(this.timeoutId);
				this.timeoutId = null;
				this.state = "paused";
				cancelAnimationFrame(this.progressFrame);
				this.progressValue = this.remaining / duration * 100;
			},
			resume(reason = "manual") {
				if (!this.pauseReasons.delete(reason)) return;
				if (this.pauseReasons.size > 0) return;
				if (this.state !== "paused" || this.remaining <= 0) return;
				this.startTimer();
			},
			restart() {
				if (this.timeoutId) clearTimeout(this.timeoutId);
				this.timeoutId = null;
				this.remaining = duration;
				this.state = "idle";
				this.pauseReasons.clear();
				this.progressValue = 100;
				this.startTimer();
				if (document.hidden) this.pause("visibility");
			},
			handleVisibility() {
				if (document.hidden) this.pause("visibility");
				else this.resume("visibility");
			},
			beforeDismiss() {
				this.state = "dismissing";
				this.remaining = 0;
				cancelAnimationFrame(this.progressFrame);
				if (this.timeoutId) {
					clearTimeout(this.timeoutId);
					this.timeoutId = null;
				}
			},
			destroy() {
				cancelAnimationFrame(this.progressFrame);
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
	//#region resources/js/mixins/server-options.js
	function serverOptions() {
		return {
			_serverOptionsText: null,
			_stopServerOptions: null,
			serverOptionsElement() {
				const el = this.$root?.nextElementSibling;
				return el?.matches?.(`script${dataSelector("options")}`) ? el : null;
			},
			serverOptions() {
				const el = this.serverOptionsElement();
				if (!el) return null;
				this._serverOptionsText = el.textContent;
				try {
					return JSON.parse(el.textContent);
				} catch {
					return null;
				}
			},
			followServerOptions(apply) {
				this._serverOptionsText ??= this.serverOptionsElement()?.textContent ?? null;
				this._stopServerOptions = onLivewireCommit(({ component, succeed }) => {
					if (component?.el && !component.el.contains(this.$root)) return;
					succeed(() => this.$nextTick(() => {
						const el = this.serverOptionsElement();
						if (!el || el.textContent === this._serverOptionsText) return;
						const next = this.serverOptions();
						if (next) apply(next);
					}));
				});
			},
			stopFollowingServerOptions() {
				this._stopServerOptions?.();
				this._stopServerOptions = null;
			}
		};
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
			_destroyed: false,
			async load(cb, silent = false) {
				if (!silent && !this.$el.hasAttribute("data-silent")) this.start();
				else this._loadToken++;
				const token = this._loadToken;
				try {
					const result = await cb();
					this.complete(0, token);
					if (typeof result === "function" && !this._destroyed) this.$nextTick(result);
				} catch (e) {
					if (e?.name === "AbortError") return;
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
				emit(this.$root, "started");
			},
			complete(milliseconds = 0, token) {
				if (this._destroyed) return;
				token ??= this._loadToken;
				this._clearPendingLoad();
				this._pendingLoad = startTimeout(() => {
					this._pendingLoad = null;
					if (token !== this._loadToken) return;
					this.reset();
					this.loaded = true;
					emit(this.$root, "completed");
				}, milliseconds, 0);
			},
			fail(error, milliseconds = 0, token) {
				if (this._destroyed) return;
				token ??= this._loadToken;
				this._clearPendingLoad();
				this._pendingLoad = startTimeout(() => {
					this._pendingLoad = null;
					if (token !== this._loadToken) return;
					this.reset();
					this.error = error;
					emit(this.$root, "failed");
				}, milliseconds, 0);
			},
			_clearPendingLoad() {
				if (this._pendingLoad) {
					clearTimeout(this._pendingLoad);
					this._pendingLoad = null;
				}
			},
			destroy() {
				this._destroyed = true;
				this._clearPendingLoad();
			},
			isDestroyed() {
				return this._destroyed;
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
		let chart = null;
		let source = null;
		return {
			..._loadable,
			...dataOptions(),
			...serverOptions(),
			_fixedMode: false,
			_palette: "palette1",
			_stopColorScheme: null,
			getChart() {
				return chart;
			},
			init() {
				this.load(() => loadRemoteAssets(() => !!window.ApexCharts, "https://cdn.jsdelivr.net/npm/apexcharts@5"));
				this.followServerOptions((next) => {
					if (this.isCompleted() && this.$refs.target) this.render(next);
				});
				this._stopColorScheme = onColorSchemeChange(() => {
					if (!chart || this._fixedMode || !source) return;
					chart.destroy();
					chart = null;
					this.render(source);
				});
			},
			render(options = {}) {
				try {
					source = {
						...source,
						...options
					};
					const merged = {
						...options,
						...this.getDataOptions(this.$refs.target)
					};
					this._fixedMode ||= !!merged.theme?.mode;
					if (!this._fixedMode) {
						this._palette = merged.theme?.palette ?? this._palette;
						merged.theme = {
							...merged.theme,
							palette: this._palette,
							mode: isDarkMode() ? "dark" : "light"
						};
						merged.chart = {
							background: "transparent",
							...merged.chart
						};
					}
					if (chart) chart.updateOptions(merged);
					else {
						chart = new window.ApexCharts(this.$refs.target, merged);
						chart.render();
					}
					emit(this.$refs.target, "rendered", { chart }, { later: true });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingServerOptions();
				this._stopColorScheme?.();
				chart?.destroy();
				chart = null;
				source = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/appearance-selector.js
	var appearance_selector_exports = /* @__PURE__ */ __exportAll({ appearanceSelector: () => appearanceSelector });
	var MODES = [
		"system",
		"light",
		"dark"
	];
	function appearanceSelector() {
		return {
			init() {
				bind(this.$root, {
					["@keydown.right.prevent"]: () => this.move(isRtl(this.$root) ? -1 : 1),
					["@keydown.down.prevent"]: () => this.move(1),
					["@keydown.left.prevent"]: () => this.move(isRtl(this.$root) ? 1 : -1),
					["@keydown.up.prevent"]: () => this.move(-1)
				});
				bind(this.$root.querySelectorAll("[data-mode]"), (button) => {
					const mode = button.dataset.mode;
					return {
						["@click"]: () => this.$tallkit.appearance.apply(mode),
						[":aria-checked"]: () => this.$tallkit.appearance.mode === mode,
						[":tabindex"]: () => this.$tallkit.appearance.mode === mode ? 0 : -1
					};
				});
			},
			move(step) {
				const next = MODES[(MODES.indexOf(this.$tallkit.appearance.mode) + step + MODES.length) % MODES.length];
				this.$tallkit.appearance.apply(next);
				this.$nextTick(() => this.$root.querySelector(`[data-mode='${next}']`)?.focus());
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/stickable.js
	function stickable() {
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
				this.$el.style.position = "static";
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
		return { ...stickable() };
	}
	//#endregion
	//#region resources/js/mixins/toggleable.js
	function toggleable() {
		return {
			opened: false,
			init(opened = false) {
				this.opened = Boolean(opened);
			},
			open() {
				this.setOpened(true);
			},
			close() {
				this.setOpened(false);
			},
			setOpened(opened) {
				if (this.opened === opened) return;
				this.opened = opened;
				emit(this.$root, opened ? "opened" : "closed");
			},
			toggle(...args) {
				if (this.isOpened()) this.close(...args);
				else this.open(...args);
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
	function popover({ mode = "hover", position = "bottom", align = "end", matchTriggerWidth = false, margin = 4, delay = 0 } = {}) {
		delay = toMilliseconds(delay);
		const _toggleable = toggleable();
		const usesClick = () => mode !== "manual" && (window.matchMedia("(hover: none)").matches || mode === "dropdown");
		return {
			..._toggleable,
			popoverElement: null,
			trigger: null,
			ariaTrigger: null,
			resizeObserver: null,
			mutationObserver: null,
			livewireCommitCleanup: null,
			_syncObserver: null,
			_onBeforeToggle: null,
			_popoverId: null,
			_unbindTrigger: null,
			_stopOutsideClick: null,
			_rAF: null,
			_cancelPendingClose: null,
			_hoverCloseTimer: null,
			_hoverOpenTimer: null,
			mouseX: 0,
			mouseY: 0,
			_hasPointerPosition: false,
			init() {
				_toggleable.init.call(this);
				this._onBeforeToggle = (e) => {
					if (e.newState === "open" && this.isPopoverReadonly()) {
						e.preventDefault();
						return;
					}
					queueMicrotask(() => {
						if (e.newState === "open") this.onOpen();
						else this.onClose();
					});
				};
				this.refreshPopover();
				this.livewireCommitCleanup = onLivewireCommit(({ succeed }) => {
					succeed(() => {
						if (!this.popoverElement?.matches(":popover-open")) return;
						if (!this.$root?.isConnected) return;
						this.boundSetPosition();
					});
				});
				this._syncObserver = new MutationObserver((records) => {
					if (records.some((record) => record.type === "childList" ? !this.popoverElement?.contains(record.target) : record.target === this.ariaTrigger || record.target === this.popoverElement)) this.refreshPopover();
				});
				this._syncObserver.observe(this.$root, {
					childList: true,
					subtree: true,
					attributes: true,
					attributeFilter: [
						"id",
						"tabindex",
						"aria-haspopup",
						"aria-expanded",
						"aria-controls",
						"aria-describedby"
					]
				});
			},
			resolvePopoverElement() {
				const last = this.$root.lastElementChild;
				return last?.matches("[popover]") ? last : null;
			},
			resolvePopoverTrigger() {
				const first = this.$root.firstElementChild;
				return first !== this.popoverElement ? first : this.$root;
			},
			refreshPopover() {
				if (!this.$root?.isConnected) return;
				const popoverElement = this.resolvePopoverElement();
				const popoverChanged = popoverElement !== this.popoverElement;
				if (popoverChanged) {
					this.releasePopoverElement();
					this.popoverElement = popoverElement;
					this.adoptPopoverElement();
				}
				if (!this.popoverElement) return;
				const trigger = this.resolvePopoverTrigger();
				if (trigger && (trigger !== this.trigger || popoverChanged)) {
					this.trigger = trigger;
					this.ariaTrigger = trigger.matches(dataSelector("control")) ? trigger : queryData(trigger, "control") ?? trigger;
					this.bindPopoverTrigger();
					if (this.isOpened()) this.boundSetPosition();
				}
				this.syncPopoverTrigger();
			},
			releasePopoverElement() {
				if (!this.popoverElement) return;
				this.popoverElement.removeEventListener("beforetoggle", this._onBeforeToggle);
				this._cancelPendingClose?.();
				this.onClose();
			},
			adoptPopoverElement() {
				this.popoverElement?.addEventListener("beforetoggle", this._onBeforeToggle);
			},
			popoverRole() {
				return this.popoverElement?.getAttribute("role") ?? this.popoverElement?.querySelector("[role=menu], [role=listbox], [role=dialog]")?.getAttribute("role") ?? null;
			},
			syncPopoverTrigger() {
				const el = this.ariaTrigger;
				const popoverElement = this.popoverElement;
				if (!el || !popoverElement) return;
				if (!popoverElement.id) popoverElement.id = this._popoverId ??= generateId("popover");
				const set = (name, value) => {
					if (el.getAttribute(name) !== value) el.setAttribute(name, value);
				};
				const role = this.popoverRole();
				if (this.triggerTakesPopupAria()) {
					if (!el.hasAttribute("aria-haspopup")) set("aria-haspopup", role === "listbox" || role === "dialog" ? role : "true");
					set("aria-expanded", this.isOpened() ? "true" : "false");
				}
				const current = el.getAttribute("aria-controls");
				if (!(current ? document.getElementById(current) : null)) set("aria-controls", popoverElement.id);
				if (mode === "context" && !usesClick() && !this.trigger.hasAttribute("tabindex") && ![
					"A",
					"BUTTON",
					"INPUT",
					"SELECT",
					"TEXTAREA"
				].includes(this.trigger.tagName)) this.trigger.setAttribute("tabindex", "0");
			},
			triggerTakesPopupAria() {
				return !!this.ariaTrigger?.matches("a[href], button, select, [role]:not([role=none]):not([role=presentation])");
			},
			setPopoverExpanded(expanded) {
				if (!this.ariaTrigger || !this.triggerTakesPopupAria()) return;
				const value = expanded ? "true" : "false";
				if (this.ariaTrigger.getAttribute("aria-expanded") !== value) this.ariaTrigger.setAttribute("aria-expanded", value);
			},
			bindPopoverTrigger() {
				this.unbindPopoverTrigger();
				const trigger = this.trigger;
				const cleanups = [];
				const on = (target, type, handler) => {
					target.addEventListener(type, handler);
					cleanups.push(() => target.removeEventListener(type, handler));
				};
				if (usesClick()) {
					on(trigger, "click", (event) => this.toggle(this.popoverRole() !== "menu" || event.detail === 0));
					on(trigger, "keydown", (event) => {
						if (!["ArrowDown", "ArrowUp"].includes(event.key) || this.isOpened()) return;
						event.preventDefault();
						this.open(event.key === "ArrowUp" ? "last" : true);
					});
					const leaves = (event) => {
						const to = event.relatedTarget;
						if (!(to instanceof Element) || !this.isOpened()) return;
						if (this.trigger?.contains(to) || this.popoverElement?.contains(to)) return;
						this.close();
					};
					on(trigger, "focusout", leaves);
					if (this.popoverElement) on(this.popoverElement, "focusout", leaves);
				} else if (mode === "hover") {
					on(trigger, "mouseenter", () => this.hoverOpen());
					on(trigger, "mouseleave", () => this.hoverClose());
					const holdsFocus = (el) => !!el && (this.trigger?.contains(el) || this.popoverElement?.contains(el));
					on(trigger, "focusin", (event) => {
						if (!event.target.matches?.(":focus-visible")) return;
						this.cancelHoverClose();
						this.open(false);
					});
					on(trigger, "focusout", (event) => {
						if (!holdsFocus(event.relatedTarget)) this.close();
					});
					if (this.popoverElement) {
						on(this.popoverElement, "focusout", (event) => {
							if (!holdsFocus(event.relatedTarget)) this.close();
						});
						on(this.popoverElement, "mouseenter", () => {
							this.cancelHoverClose();
							if (this._cancelPendingClose) this.open(false);
						});
						on(this.popoverElement, "mouseleave", () => this.hoverClose());
					}
				} else if (mode === "context") {
					on(trigger, "contextmenu", (event) => {
						event.preventDefault();
						this.close();
						this.mouseX = event.clientX;
						this.mouseY = event.clientY;
						this._hasPointerPosition = true;
						this.open();
					});
					on(trigger, "keydown", (event) => {
						if (event.key !== "ContextMenu" && !(event.shiftKey && event.key === "F10")) return;
						event.preventDefault();
						this.close();
						this._hasPointerPosition = false;
						this.open();
					});
				}
				on(trigger, eventName("open"), () => this.open());
				on(trigger, eventName("close"), () => this.close());
				this._unbindTrigger = () => cleanups.forEach((cleanup) => cleanup());
			},
			unbindPopoverTrigger() {
				this.cancelHoverOpen();
				this.cancelHoverClose();
				this._unbindTrigger?.();
				this._unbindTrigger = null;
			},
			hoverOpen() {
				this.cancelHoverClose();
				this.cancelHoverOpen();
				if (!delay || this.isOpened() || this._cancelPendingClose) {
					this.open(false);
					return;
				}
				this._hoverOpenTimer = setTimeout(() => {
					this._hoverOpenTimer = null;
					this.open(false);
				}, delay);
			},
			cancelHoverOpen() {
				clearTimeout(this._hoverOpenTimer);
				this._hoverOpenTimer = null;
			},
			hoverClose() {
				this.cancelHoverOpen();
				this.cancelHoverClose();
				this._hoverCloseTimer = setTimeout(() => this.close(), 100);
			},
			cancelHoverClose() {
				clearTimeout(this._hoverCloseTimer);
				this._hoverCloseTimer = null;
			},
			listenOutsideClick() {
				this.stopOutsideClick();
				const handler = (e) => {
					if (!e.target?.isConnected) return;
					if (usesClick()) {
						if (this.trigger?.contains(e.target)) return;
						if ((this.popoverElement?.hasAttribute("data-keep-open") || e.target.hasAttribute("data-keep-open") || e.target.closest("[data-keep-open]")) && this.popoverElement?.contains(e.target)) return;
					} else if (mode === "context") {
						if (this.popoverElement?.contains(e.target)) return;
					} else return;
					this.close();
				};
				document.addEventListener("click", handler);
				this._stopOutsideClick = () => document.removeEventListener("click", handler);
			},
			stopOutsideClick() {
				this._stopOutsideClick?.();
				this._stopOutsideClick = null;
			},
			destroy() {
				this.onClose();
				this.stopOutsideClick();
				this.unbindPopoverTrigger();
				this.livewireCommitCleanup?.();
				this._syncObserver?.disconnect();
				this.popoverElement?.removeEventListener("beforetoggle", this._onBeforeToggle);
			},
			isPopoverReadonly() {
				return this.ariaTrigger?.getAttribute("aria-readonly") === "true";
			},
			open(focus = true) {
				if (this.isPopoverReadonly()) return;
				requestAnimationFrame(() => {
					if (!this.popoverElement?.isConnected) this.refreshPopover();
					if (!this.popoverElement?.isConnected) return;
					if (this._cancelPendingClose) {
						this._cancelPendingClose();
						this._cancelPendingClose = null;
						this.onOpen();
					} else {
						if (this.popoverElement.matches(":popover-open")) return;
						this.popoverElement.showPopover();
					}
					this.$nextTick(() => requestAnimationFrame(() => {
						if (!this.popoverElement?.matches(":popover-open")) return;
						this.popoverElement.querySelector("[role=option][data-active]:not([data-active=\"false\"]), [role=option][aria-selected=\"true\"]")?.scrollIntoView({ block: "nearest" });
						if (!focus) return;
						const items = Array.from(this.popoverElement.querySelectorAll("[role=menuitem], [role=menuitemcheckbox], [role=menuitemradio], [role=option], [role=tab], [role=gridcell][tabindex=\"0\"]")).filter((item) => !item.disabled && item.getAttribute("aria-disabled") !== "true" && isRendered(item));
						((focus === "last" ? null : items.find((item) => item.getAttribute("aria-selected") === "true")) ?? (focus === "last" ? items.at(-1) : items[0]) ?? this.popoverElement).focus();
					}));
				});
			},
			close() {
				this.cancelHoverOpen();
				requestAnimationFrame(() => {
					if (!this.popoverElement?.isConnected) return;
					if (!this.popoverElement.matches(":popover-open")) return;
					if (this._cancelPendingClose) return;
					this.onClose();
					const target = this.popoverElement.firstElementChild ?? this.popoverElement;
					let fallback;
					const hide = (event) => {
						if (event && event.target !== target) return;
						target.removeEventListener("transitionend", hide);
						clearTimeout(fallback);
						this._cancelPendingClose = null;
						if (this.popoverElement?.isConnected && this.popoverElement.matches(":popover-open")) {
							const hadFocus = this.popoverElement.contains(document.activeElement);
							this.popoverElement.hidePopover();
							if (hadFocus) this.ariaTrigger?.focus?.();
						}
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
						target.addEventListener("transitionend", hide);
						fallback = setTimeout(hide, timeout + 50);
					});
				});
			},
			onOpen() {
				pushEscapeLayer(this);
				_toggleable.open.call(this);
				this.setPopoverExpanded(true);
				this.listenOutsideClick();
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
				removeEscapeLayer(this);
				if (this.isClosed()) return;
				_toggleable.close.call(this);
				this.setPopoverExpanded(false);
				this.stopOutsideClick();
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
				if (matchTriggerWidth) this.popoverElement.style.width = `${triggerRect.width}px`;
				placeNextTo(this.popoverElement, triggerRect, {
					position,
					align,
					margin,
					rtl: isRtl(this.trigger)
				});
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
	var warned$1 = /* @__PURE__ */ new WeakSet();
	function warnNonGlobal(regex) {
		if (!warned$1.has(regex)) {
			warned$1.add(regex);
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
	function listbox({ hideEmpty = false, clearOnSelect = false, autoHighlight = true, tabSelects = true, ...fuseOptions } = {}) {
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
			listboxCommitCleanup: null,
			_itemsByElement: null,
			init() {
				this.input = queryData(this.$root, "input");
				this.list = this.$root.querySelector("[role=listbox]");
				this.noRecords = queryData(this.$root, "listbox-no-records");
				this.refreshItems();
				this.listboxCommitCleanup = onLivewireCommit(({ component, succeed }) => {
					succeed(() => {
						if (!this.$root?.isConnected) return;
						if (component?.el && !component.el.contains(this.$root)) return;
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
						emit(this.$root, "searched", { query: this.input.value });
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
					["@keydown.enter"](e) {
						if (this.index === null || !this.filteredItems[this.index]) return;
						e.preventDefault();
						this.select(this.index);
					},
					["@keydown.tab"]() {
						if (tabSelects) this.select(this.index);
					}
				});
				bind(this.list, {
					["@mouseleave"]: () => this.clear(),
					["@mousedown"]: (e) => {
						const item = e.target.closest("[role=option]");
						if (!item) return;
						const index = toNumber(item.dataset.index);
						if (index !== null) this.select(index);
					},
					["@mousemove"]: (e) => {
						if (this.lastInteraction === "keyboard" && e.movementX === 0 && e.movementY === 0) return;
						this.lastInteraction = "mouse";
						const item = e.target.closest("[role=option]");
						if (!item) return;
						const index = toNumber(item.dataset.index);
						if (index === null) return;
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
					emit(this.$root, "ready");
				});
			},
			destroy() {
				this.listboxCommitCleanup?.();
			},
			refreshItems() {
				const items = Array.from(this.list.querySelectorAll("[role=option]")).map((item) => {
					item.hidden = true;
					if (item?.firstElementChild?.hasAttribute("disabled")) item.setAttribute("aria-disabled", "true");
					else item.removeAttribute("aria-disabled");
					return {
						title: normalizeText(item.querySelector("[data-item-content]")?.textContent, { removeSpaces: true }),
						el: item.firstElementChild,
						li: item
					};
				});
				const key = (item) => `${item.title}\u0000${item.li.hasAttribute("aria-disabled")}`;
				const previous = this._itemsByElement;
				if (this.fuse && previous && previous.size === items.length && items.every((item) => previous.get(item.li) === key(item))) return;
				this.items = items;
				this._itemsByElement = new Map(items.map((item) => [item.li, key(item)]));
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
				emit(this.$root, "filtered", {
					list: this.list,
					items: this.items,
					filteredItems: this.filteredItems
				});
				if (autoHighlight && this.filteredItems.length && query.length) this.$nextTick(() => {
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
				emit(this.$root, "selected", {
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
				if (!item.li.id) item.li.id = generateId("listbox-option");
				this.list.setAttribute("aria-activedescendant", item.li.id);
				this.input?.setAttribute("aria-activedescendant", item.li.id);
				item.li.scrollIntoView({ block: "nearest" });
				emit(this.$root, "highlighted", {
					index,
					item
				});
			},
			clearActive() {
				this.filteredItems.forEach((item) => {
					delete item.el.dataset.active;
				});
				this.list.removeAttribute("aria-activedescendant");
				this.input?.removeAttribute("aria-activedescendant");
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
			autoHighlight: false,
			tabSelects: false,
			...options
		});
		return {
			..._popover,
			..._listbox,
			_chosen: false,
			resolvePopoverTrigger() {
				return this.input ?? _popover.resolvePopoverTrigger.call(this);
			},
			destroy() {
				_popover.destroy.call(this);
				_listbox.destroy.call(this);
			},
			init() {
				_popover.init.call(this);
				_listbox.init.call(this);
				this.trigger = this.input;
				bind(this.input, {
					["@keydown"]() {
						this._chosen = false;
					},
					["@blur"]() {
						this.close();
					},
					["@keydown.escape.prevent"]() {
						this.close();
					}
				});
				bind(this.$root, { ["@selected"]({ detail }) {
					setFieldValue(this.input, detail.item.title);
					this.debouncedSearch?.cancel();
					this._chosen = true;
					this.close();
				} });
			},
			search() {
				_listbox.search.call(this);
				if (this._chosen) return this.close();
				const typing = document.activeElement === this.input && !this.input.disabled && !this.input.readOnly;
				if (this.filteredItems.length && typing) this.open();
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
	}, toWire = null } = {}) {
		return {
			field: null,
			dispatchPicked(value) {
				emit(this.field ?? this.$root, "picked", { value });
			},
			init() {
				this.field = queryData(this.$root, key);
				if (!this.field) return;
				const prop = this.$wire ? getWireModelInfo(this.field) : null;
				if (prop) {
					this[property] = deserialize.call(this, this.$wire.get(prop.name) ?? null);
					this.$wire.$watch(prop.name, (value) => {
						this[property] = deserialize.call(this, value ?? null);
					});
				}
				if (!prop && [
					null,
					void 0,
					""
				].includes(this[property]) && this.field.value !== "") this[property] = deserialize.call(this, this.field.value);
				if (!prop) this.$nextTick(() => {
					const model = this.field._x_model;
					if (!model || !window.Alpine?.effect) return;
					let last;
					const effect = window.Alpine.effect(() => {
						const value = model.get();
						if (!this.$root.isConnected) return queueMicrotask(() => window.Alpine.release(effect));
						const key = JSON.stringify(value ?? null);
						if (key === last) return;
						last = key;
						const next = deserialize.call(this, value ?? null);
						if (JSON.stringify(next) !== JSON.stringify(this[property] ?? null)) this[property] = next;
					});
				});
				if (!prop && this.field.form) {
					let initial = null;
					this.$nextTick(() => {
						initial = JSON.stringify(this[property] ?? null);
					});
					onFormReset(this.$root, this.field.form, () => {
						if (initial !== null) this[property] = deserialize.call(this, JSON.parse(initial));
					});
				}
				if (hasBlurModel(this.field)) blurOnFocusLeave(this.$root, this.field, prop && toWire ? () => {
					if (/\blive\b/.test(prop.modifier)) this.$wire.$commit();
				} : void 0);
				this.$watch(property, () => {
					if (prop && toWire) {
						const next = toWire.call(this);
						if (JSON.stringify(next) === JSON.stringify(this.$wire.get(prop.name) ?? null)) return;
						const live = /\b(live|change)\b/.test(prop.modifier) && !/\bblur\b/.test(prop.modifier);
						this.$wire.set(prop.name, next, live);
						return;
					}
					setFieldValue(this.field, serialize.call(this));
					if (!prop && this.field._x_model) this.field._x_model.set(toWire ? toWire.call(this) : serialize.call(this));
				});
			}
		};
	}
	//#endregion
	//#region resources/js/components/calendar.js
	var calendar_exports = /* @__PURE__ */ __exportAll({ calendar: () => calendar });
	function calendar({ value = null, multiple = false, range = false, months = 1, min = null, max = null, unavailable = null, minRange = null, maxRange = null, static: isStatic = false, navigation = true, today = false, selectableHeader = false, fixedWeeks = false, startDay = null, openTo = null, weekNumbers = false, locale = null } = {}) {
		const mode = range ? "range" : null;
		months = Math.max(1, toNumber(months, 1));
		min = normalizeIsoDate(min);
		max = normalizeIsoDate(max);
		minRange = toNumber(minRange);
		maxRange = toNumber(maxRange);
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
			today,
			selectableHeader,
			fixedWeeks,
			weekNumbers,
			locale: resolveLocale(locale),
			startDay: 0,
			unavailable: parseCommaList(unavailable).map(normalizeIsoDate).filter(Boolean),
			value: null,
			anchorMonth: null,
			focused: null,
			hoverIso: null,
			rangeAnchor: null,
			dispatchPicked(value) {
				emit(this.$root, "picked", { value });
			},
			init() {
				this.startDay = toNumber(startDay) ?? localeFirstDay(this.locale);
				this.value = this.parseInitialValue(value);
				_bindableField.init.call(this);
				this.anchorMonth = startOfMonth(this.firstAnchorDate());
				this.focused = this.firstSelectedIso() ?? formatIsoDate(/* @__PURE__ */ new Date());
			},
			parseInitialValue(raw) {
				if (mode === "range") return this.normalizeRange(raw);
				if (multiple) return this.normalizeMultiple(raw);
				return this.normalizeSingle(raw);
			},
			normalizeSingle(raw) {
				if (!raw || typeof raw === "object") return Array.isArray(raw) ? normalizeIsoDate(raw[0]) : null;
				return normalizeIsoDate(raw);
			},
			normalizeMultiple(raw) {
				if (!raw) return [];
				return (Array.isArray(raw) ? raw : parseCommaList(raw)).map(normalizeIsoDate).filter(Boolean);
			},
			normalizeRange(raw) {
				if (!raw) return null;
				const range = (start, end) => {
					start = normalizeIsoDate(start);
					end = normalizeIsoDate(end);
					return start || end ? {
						start,
						end
					} : null;
				};
				if (Array.isArray(raw)) return range(raw[0], raw[1]);
				if (typeof raw === "object") return range(raw.start, raw.end);
				const [start, end] = String(raw).split("/");
				return normalizeIsoDate(start) ? range(start, end) : null;
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
				if (iso && parseIsoDate(iso)) return parseIsoDate(iso);
				if (openTo) return parseIsoDate(openTo) ?? /* @__PURE__ */ new Date();
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
				for (let i = 0; i < months; i++) if (isSameMonth(this.monthAt(i), date)) return true;
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
				return new Intl.DateTimeFormat(this.locale, { dateStyle: "full" }).format(parseIsoDate(iso));
			},
			monthOptions() {
				const fmt = new Intl.DateTimeFormat(this.locale, { month: "long" });
				return Array.from({ length: 12 }, (_, i) => ({
					value: i,
					label: fmt.format(new Date(2e3, i, 1))
				}));
			},
			yearOptions() {
				const current = this.anchorMonth.getFullYear();
				const from = Math.min(min ? Number(min.slice(0, 4)) : current - 100, current);
				const to = Math.max(max ? Number(max.slice(0, 4)) : current + 100, current);
				return Array.from({ length: to - from + 1 }, (_, i) => from + i);
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
						iso: formatIsoDate(date),
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
						weekNumber: this.weekNumbers ? isoWeekNumber(parseIsoDate(thursday.iso)) : null,
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
				this.selectDate(formatIsoDate(today));
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
				return iso === formatIsoDate(/* @__PURE__ */ new Date());
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
				this.dispatchPicked(this.value);
			},
			toggleMultiple(iso) {
				const current = this.value ?? [];
				this.value = current.includes(iso) ? current.filter((d) => d !== iso) : [...current, iso].sort();
				this.focused = iso;
				this.dispatchPicked(this.value);
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
				this.dispatchPicked(this.value);
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
				this.dispatchPicked(this.value);
			},
			rangeAllowed(start, end) {
				if (this.static || !start || !end) return false;
				if (min && start < min || max && end > max) return false;
				const days = diffDays(start, end) + 1;
				if (minRange && days < minRange || maxRange && days > maxRange) return false;
				return !this.rangeContainsUnavailable(start, end);
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
			tabbableIso() {
				const usable = (iso) => !!iso && this.isMonthVisible(parseIsoDate(iso)) && !this.isDayDisabled(iso);
				for (const iso of [
					this.focused,
					this.firstSelectedIso(),
					formatIsoDate(/* @__PURE__ */ new Date())
				]) if (usable(iso)) return iso;
				for (let i = 0; i < months; i++) {
					const month = this.monthAt(i);
					const days = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
					for (let day = 1; day <= days; day++) {
						const iso = formatIsoDate(new Date(month.getFullYear(), month.getMonth(), day));
						if (!this.isDayDisabled(iso)) return iso;
					}
				}
				return null;
			},
			enabledFrom(iso, step) {
				for (let i = 0; i < 366; i++, iso = addDays(iso, step)) if (!this.isDayDisabled(iso)) return iso;
				return null;
			},
			onCellKeydown(event, iso) {
				const rtl = isRtl(this.$root);
				const deltas = {
					ArrowLeft: rtl ? 1 : -1,
					ArrowRight: rtl ? -1 : 1,
					ArrowUp: -7,
					ArrowDown: 7
				};
				let target = null;
				let step = 1;
				if (event.key in deltas) {
					target = addDays(iso, deltas[event.key]);
					step = deltas[event.key];
				} else if (event.key === "Home") target = this.weekEdge(iso, "start");
				else if (event.key === "End") {
					target = this.weekEdge(iso, "end");
					step = -1;
				} else if (event.key === "PageUp") target = this.shiftMonth(iso, event.shiftKey ? -12 : -1);
				else if (event.key === "PageDown") {
					target = this.shiftMonth(iso, event.shiftKey ? 12 : 1);
					step = -1;
				}
				if (target) {
					event.preventDefault();
					const next = this.enabledFrom(target, step);
					if (next) this.focusIso(next);
				} else if (event.key === "Enter" || event.key === " ") {
					event.preventDefault();
					this.selectDate(iso);
				}
			},
			weekEdge(iso, edge) {
				const offset = (parseIsoDate(iso).getDay() - this.startDay + 7) % 7;
				return edge === "start" ? addDays(iso, -offset) : addDays(iso, 6 - offset);
			},
			shiftMonth(iso, deltaMonths) {
				const date = parseIsoDate(iso);
				const target = new Date(date.getFullYear(), date.getMonth() + deltaMonths, 1);
				const lastDay = new Date(target.getFullYear(), target.getMonth() + 1, 0).getDate();
				target.setDate(Math.min(date.getDate(), lastDay));
				return formatIsoDate(target);
			},
			focusIso(iso) {
				const root = this.$root;
				const targetMonth = startOfMonth(parseIsoDate(iso));
				if (!this.isMonthVisible(targetMonth)) {
					if (!this.navigation) return;
					this.anchorMonth = targetMonth > this.anchorMonth ? addMonths(targetMonth, -(months - 1)) : targetMonth;
				}
				this.focused = iso;
				this.$nextTick(() => {
					root.querySelector(`[data-iso="${iso}"]:not([data-outside-month])`)?.focus();
				});
			}
		};
	}
	//#endregion
	//#region resources/js/components/carousel.js
	var carousel_exports = /* @__PURE__ */ __exportAll({
		carousel: () => carousel,
		carouselControls: () => carouselControls
	});
	function registry() {
		return window.__tallkitCarousels ??= window.Alpine.reactive({});
	}
	var SWIPE_THRESHOLD = 50;
	var DRAG_LOCK = 10;
	var AUTOPLAY_TICK = 100;
	function carousel({ name = null, autoplay = false, interval = 5e3, advance = "slide", wrap = true, fade = false } = {}) {
		let offset = 0;
		let drag = null;
		let lastTick = 0;
		return {
			current: 0,
			slideCount: 0,
			slideList: [],
			visibleCount: 1,
			elapsed: 0,
			_autoplayId: null,
			_paused: false,
			_hovered: false,
			_focused: false,
			_hidden: false,
			_offscreen: false,
			init() {
				this._root = this.$root;
				if (name) registry()[name] = this;
				this.measure();
				this.$nextTick(() => this.render({ instant: true }));
				this.bindDrag();
				this._slidesObserver = new MutationObserver(() => {
					this.measure();
					this.current = Math.min(this.current, this.maxIndex());
					this.render({ instant: true });
				});
				const track = this.track();
				if (track) this._slidesObserver.observe(track, { childList: true });
				this._onVisibilityChange = () => {
					this._hidden = document.hidden;
				};
				document.addEventListener("visibilitychange", this._onVisibilityChange);
				this._visibilityObserver = new IntersectionObserver(([entry]) => {
					this._offscreen = !entry.isIntersecting;
				});
				this._visibilityObserver.observe(this._root);
				bind(this._root, {
					["@keydown.arrow-left"](event) {
						if (isTypingIn(event.target)) return;
						isRtl(this._root) ? this.next() : this.prev();
					},
					["@keydown.arrow-right"](event) {
						if (isTypingIn(event.target)) return;
						isRtl(this._root) ? this.prev() : this.next();
					},
					["@mouseenter"]() {
						this._hovered = true;
					},
					["@mouseleave"]() {
						this._hovered = false;
					},
					["@focusin"]() {
						this._focused = true;
					},
					["@focusout"](event) {
						if (!this._root.contains(event.relatedTarget)) this._focused = false;
					},
					["x-resize"]() {
						this.measure();
						this.render({ instant: true });
					}
				});
				this.startAutoplay();
			},
			destroy() {
				clearInterval(this._autoplayId);
				this._slidesObserver?.disconnect();
				this._visibilityObserver?.disconnect();
				document.removeEventListener("visibilitychange", this._onVisibilityChange);
				if (name && registry()[name] === this) delete registry()[name];
			},
			bindDrag() {
				const viewport = queryData(this._root, "carousel-viewport");
				bind(viewport, {
					["@pointerdown"](event) {
						if (event.pointerType === "mouse" || !this.canNavigate()) return;
						drag = {
							id: event.pointerId,
							x: event.clientX,
							y: event.clientY,
							dx: 0,
							locked: false
						};
					},
					["@pointermove"](event) {
						if (!drag || event.pointerId !== drag.id) return;
						const dx = event.clientX - drag.x;
						const dy = event.clientY - drag.y;
						if (!drag.locked) {
							if (Math.max(Math.abs(dx), Math.abs(dy)) < DRAG_LOCK) return;
							if (Math.abs(dy) > Math.abs(dx)) {
								drag = null;
								return;
							}
							drag.locked = true;
							viewport.setPointerCapture?.(event.pointerId);
						}
						drag.dx = dx;
						if (fade) return;
						const track = this.track();
						const forward = dx < 0 !== isRtl(this._root);
						const atEdge = !wrap && (forward ? this.current >= this.maxIndex() : this.current <= 0);
						track.style.transition = "none";
						track.style.transform = `translateX(${offset + (atEdge ? dx / 3 : dx)}px)`;
					},
					["@pointerup"](event) {
						if (!drag || event.pointerId !== drag.id) return;
						const { dx, locked } = drag;
						drag = null;
						if (!locked) return;
						this.track().style.transition = "";
						if (Math.abs(dx) < SWIPE_THRESHOLD) {
							this.render();
							return;
						}
						dx < 0 !== isRtl(this._root) ? this.next() : this.prev();
					},
					["@pointercancel"]() {
						if (!drag) return;
						const { locked } = drag;
						drag = null;
						if (!locked) return;
						this.track().style.transition = "";
						this.render();
					}
				});
			},
			slides() {
				return this.slideList;
			},
			track() {
				return queryData(this._root, "carousel-track");
			},
			measure() {
				this.slideList = queryAllData(this._root, "carousel-slide");
				const slides = this.slideList;
				const track = this.track();
				this.slideCount = slides.length;
				if (fade) {
					this.visibleCount = 1;
					return;
				}
				if (!slides.length || !track?.parentElement) {
					this.visibleCount = 1;
					return;
				}
				const rtl = isRtl(this._root);
				const viewportWidth = track.parentElement.getBoundingClientRect().width;
				const first = slides[0].getBoundingClientRect();
				const start = rtl ? first.right : first.left;
				let count = 0;
				for (const slide of slides) {
					const rect = slide.getBoundingClientRect();
					if ((rtl ? start - rect.left : rect.right - start) > viewportWidth + 1) break;
					count++;
				}
				this.visibleCount = Math.max(1, count);
			},
			maxIndex() {
				return Math.max(0, this.slideCount - this.visibleCount);
			},
			step() {
				return advance === "page" ? this.visibleCount : 1;
			},
			pageCount() {
				return advance === "page" ? Math.max(1, Math.ceil(this.slideCount / this.visibleCount)) : this.maxIndex() + 1;
			},
			currentPage() {
				return advance === "page" ? Math.ceil(this.current / this.visibleCount) : this.current;
			},
			isPageActive(page) {
				return this.currentPage() === page;
			},
			canNavigate() {
				return this.pageCount() > 1;
			},
			isFirst() {
				return !wrap && this.current <= 0;
			},
			isLast() {
				return !wrap && this.current >= this.maxIndex();
			},
			slideNumber(el) {
				return this.slides().indexOf(el) + 1;
			},
			isIndexVisible(index) {
				return fade ? index === this.current : index >= this.current && index < this.current + this.visibleCount;
			},
			isSlideVisible(el) {
				const index = this.slides().indexOf(el);
				return index !== -1 && this.isIndexVisible(index);
			},
			thumbnailOf(slide) {
				return slide.dataset.thumbnail || slide.querySelector("img")?.getAttribute("src") || null;
			},
			next() {
				this.goTo(this.current + this.step());
			},
			prev() {
				this.goTo(this.current - this.step());
			},
			goToPage(page) {
				this.goTo(advance === "page" ? page * this.visibleCount : page);
			},
			goTo(index) {
				const max = this.maxIndex();
				const previous = this.current;
				let target = Math.min(Math.max(index, 0), max);
				let wrapped = false;
				if (wrap && max > 0) {
					if (index > max && previous >= max) {
						target = 0;
						wrapped = true;
					} else if (index < 0 && previous <= 0) {
						target = max;
						wrapped = true;
					}
				}
				this.current = target;
				this.render({ instant: wrapped });
				this.resetAutoplay();
				if (this.current !== previous) {
					emit(queryData(this.$root, "carousel-viewport"), "changed", { index: this.current });
					this.$nextTick(() => this.revealThumbnail());
				}
			},
			render({ instant = false } = {}) {
				const slides = this.slides();
				this.preload();
				if (fade) {
					slides.forEach((slide, index) => {
						const active = index === this.current;
						slide.style.opacity = active ? "1" : "0";
						slide.toggleAttribute("data-active", active);
						slide.style.pointerEvents = active ? "" : "none";
					});
					return;
				}
				const track = this.track();
				const target = slides[this.current];
				if (!track || !target) return;
				const rtl = isRtl(this._root);
				const trackRect = track.getBoundingClientRect();
				const targetRect = target.getBoundingClientRect();
				const distance = rtl ? trackRect.right - targetRect.right : targetRect.left - trackRect.left;
				offset = rtl ? distance : -distance;
				if (instant) track.style.transition = "none";
				track.style.transform = `translateX(${offset}px)`;
				if (instant) {
					track.offsetWidth;
					track.style.transition = "";
				}
			},
			preload() {
				const slides = this.slides();
				const count = slides.length;
				if (!count) return;
				for (let index = this.current - 1; index <= this.current + this.visibleCount; index++) slides[(index + count) % count]?.querySelectorAll("img[loading=\"lazy\"]").forEach((img) => {
					img.loading = "eager";
				});
			},
			revealThumbnail() {
				const strip = queryData(this._root, "carousel-thumbnails");
				const thumb = strip?.querySelectorAll(":scope > button")[this.current];
				if (!thumb || strip.scrollWidth <= strip.clientWidth) return;
				const stripRect = strip.getBoundingClientRect();
				const thumbRect = thumb.getBoundingClientRect();
				strip.scrollBy({
					left: thumbRect.left + thumbRect.width / 2 - (stripRect.left + stripRect.width / 2),
					behavior: prefersReducedMotion() ? "auto" : "smooth"
				});
			},
			autoplays() {
				return autoplay && !prefersReducedMotion();
			},
			startAutoplay() {
				if (!this.autoplays()) return;
				lastTick = performance.now();
				this._autoplayId = startInterval(() => {
					const now = performance.now();
					const delta = now - lastTick;
					lastTick = now;
					if (!this.isRotating() || !this.canNavigate()) return;
					this.elapsed = Math.min(this.elapsed + delta, interval);
					if (this.elapsed >= interval) this.next();
				}, AUTOPLAY_TICK);
			},
			resetAutoplay() {
				if (!autoplay) return;
				this.elapsed = 0;
				clearInterval(this._autoplayId);
				this.startAutoplay();
			},
			progress() {
				return interval > 0 ? this.elapsed / interval : 0;
			},
			pause() {
				this._paused = true;
			},
			resume() {
				this._paused = false;
			},
			isPaused() {
				return this._paused;
			},
			isRotating() {
				return this.autoplays() && !this._paused && !this._hovered && !this._focused && !this._hidden && !this._offscreen;
			},
			togglePause() {
				this._paused = !this._paused;
			}
		};
	}
	function carouselControls({ name = null } = {}) {
		return {
			target() {
				return registry()[name] ?? null;
			},
			next() {
				this.target()?.next();
			},
			prev() {
				this.target()?.prev();
			},
			goToPage(page) {
				this.target()?.goToPage(page);
			},
			pageCount() {
				return this.target()?.pageCount() ?? 0;
			},
			currentPage() {
				return this.target()?.currentPage() ?? 0;
			},
			isPageActive(page) {
				return !!this.target()?.isPageActive(page);
			},
			canNavigate() {
				return !!this.target()?.canNavigate();
			},
			isFirst() {
				return this.target()?.isFirst() ?? true;
			},
			isLast() {
				return this.target()?.isLast() ?? true;
			}
		};
	}
	//#endregion
	//#region resources/js/components/chartjs.js
	var chartjs_exports = /* @__PURE__ */ __exportAll({ chartjs: () => chartjs });
	var TEXT = {
		light: "#666",
		dark: "rgba(255,255,255,0.7)"
	};
	var GRID = {
		light: "rgba(0,0,0,0.1)",
		dark: "rgba(255,255,255,0.1)"
	};
	function applyColorScheme(dark) {
		const defaults = window.Chart?.defaults;
		if (!defaults) return;
		if (Object.values(TEXT).includes(defaults.color)) defaults.color = dark ? TEXT.dark : TEXT.light;
		const grid = defaults.scale?.grid;
		if (grid && Object.values(GRID).includes(grid.color)) grid.color = dark ? GRID.dark : GRID.light;
	}
	var copy = (value) => {
		if (Array.isArray(value)) return value.map(copy);
		if (value && Object.getPrototypeOf(value) === Object.prototype) return Object.fromEntries(Object.entries(value).map(([k, v]) => [k, copy(v)]));
		return value;
	};
	function chartjs() {
		const _loadable = loadable();
		let chart = null;
		let source = null;
		return {
			..._loadable,
			...dataOptions(),
			...serverOptions(),
			_stopColorScheme: null,
			getChart() {
				return chart;
			},
			init() {
				this.load(() => loadRemoteAssets(() => !!window.Chart, "https://cdn.jsdelivr.net/npm/chart.js@4"));
				this.followServerOptions((next) => {
					if (this.isCompleted() && this.$refs.target) this.render(next);
				});
				this._stopColorScheme = onColorSchemeChange((dark) => {
					applyColorScheme(dark);
					if (!chart) return;
					chart.destroy();
					chart = new window.Chart(this.$refs.target, copy(source));
					emit(this.$refs.target, "rendered", { chart }, { later: true });
				});
			},
			render(options = {}) {
				try {
					applyColorScheme(isDarkMode());
					const merged = {
						...options,
						...this.getDataOptions(this.$refs.target)
					};
					source = {
						...source,
						...merged
					};
					if (chart && !("plugins" in merged)) {
						for (const key of [
							"type",
							"data",
							"options"
						]) if (key in merged) chart.config[key] = copy(merged[key]);
						chart.update();
					} else {
						chart?.destroy();
						chart = new window.Chart(this.$refs.target, copy(source));
					}
					emit(this.$refs.target, "rendered", { chart }, { later: true });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingServerOptions();
				this._stopColorScheme?.();
				chart?.destroy();
				chart = null;
				source = null;
			}
		};
	}
	//#endregion
	//#region resources/js/mixins/check-all.js
	function checkAll(type, group) {
		return {
			all: null,
			_toggling: false,
			_onChange: null,
			_stopCommits: null,
			items() {
				const selector = group ? dataSelector(`${type}-group`, group) : `${dataSelector(type)}:not(${dataSelector(`${type}-group`)})`;
				const scope = group ? document : this.all?.closest("form") ?? this.all?.closest("[wire\\:id]") ?? document;
				return Array.from(scope.querySelectorAll(selector)).filter((item) => item !== this.all && !item.disabled && !item.closest("[x-data^=\"checkboxAll\"], [x-data^=\"switchAll\"]"));
			},
			init() {
				this.all = queryData(this.$root, type);
				if (!this.all) return;
				this._onChange = (event) => {
					if (event.target === this.all) this.toggleAllItems();
					else if (!this._toggling && this.items().includes(event.target)) this.updateState();
				};
				document.addEventListener("change", this._onChange);
				this._stopCommits = onLivewireCommit(({ succeed }) => {
					succeed(() => this.$nextTick(() => this.updateState()));
				});
				this.updateState();
			},
			destroy() {
				document.removeEventListener("change", this._onChange);
				this._stopCommits?.();
			},
			toggleAllItems() {
				const checked = !!this.all?.checked;
				this._toggling = true;
				try {
					this.items().forEach((item) => {
						if (item.checked === checked) return;
						item.checked = checked;
						item.dispatchEvent(new Event("change", { bubbles: true }));
					});
				} finally {
					this._toggling = false;
				}
				this.updateState();
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
		return checkAll("checkbox", group);
	}
	//#endregion
	//#region resources/js/components/clearable.js
	var clearable_exports = /* @__PURE__ */ __exportAll({ clearable: () => clearable });
	function clearable() {
		return {
			destroy() {
				this._stopCommits?.();
			},
			init() {
				const button = this.$el;
				if (this.clear) bind(button, { ["@click"]() {
					this.clear();
				} });
				const input = findFieldInput(button);
				if (!input) return;
				const sync = () => {
					button.style.display = input.value ? "" : "none";
				};
				sync();
				bind(input, { ["@input"]: sync });
				this._stopCommits = onLivewireCommit(({ component, succeed }) => {
					if (!component?.el?.contains(input)) return;
					succeed(() => this.$nextTick(sync));
				});
				bind(button, { ["@click"]() {
					if (input.disabled || input.readOnly) return;
					setFieldValue(input, "");
					emit(input, "cleared", {}, { bubbles: true });
					input.focus();
				} });
			}
		};
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
			isDisabled() {
				return !!this.field?.disabled;
			},
			pick(color) {
				if (this.isDisabled()) return;
				const normalized = color ? normalizeColor(color, this.format) ?? color : null;
				if (normalized === this.value) return;
				this.value = normalized;
				this.dispatchPicked(normalized);
			},
			commitTyped(raw) {
				if (this.isDisabled()) return;
				if (!raw) {
					this.pick(null);
					return;
				}
				const normalized = normalizeColor(raw, this.format);
				if (normalized) this.pick(normalized);
				else if (this.field) this.field.value = this.value ?? "";
			},
			async dropColor() {
				if (!this.hasEyeDropper() || this.isDisabled()) return;
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
	function combobox({ value = null, multiple = false, trigger = null } = {}) {
		const isInputTrigger = trigger === "input";
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
				if (!multiple) return raw;
				if (Array.isArray(raw)) return [...raw];
				return raw ? String(raw).split(",").filter(Boolean) : [];
			},
			toWire: multiple ? function() {
				return [...this.value ?? []];
			} : null
		});
		return {
			..._popover,
			..._listbox,
			..._bindableField,
			value: value ?? (multiple ? [] : null),
			combobox: null,
			_stopLabelFocus: null,
			selectedLabel() {
				if (multiple || this.value == null) return null;
				const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(this.value));
				return item ? item.el.querySelector("[data-item-content]")?.textContent?.trim() : null;
			},
			selectedCount() {
				return this.items.filter((item) => this.isSelected(this.getElementValue(item.el))).length;
			},
			selectedValues() {
				return multiple && Array.isArray(this.value) ? this.value : [];
			},
			optionLabel(v) {
				return (this.items.find((i) => String(this.getElementValue(i.el)) === String(v))?.el ?? this.$root.querySelector(`[role=option] [value="${CSS.escape(String(v))}"]`))?.querySelector("[data-item-content]")?.textContent?.trim() || String(v);
			},
			isDisabled() {
				return this.combobox.hasAttribute("disabled");
			},
			isReadonly() {
				return this.combobox.getAttribute("aria-readonly") === "true";
			},
			valueString() {
				return multiple ? (this.value ?? []).join(",") : this.value ?? null;
			},
			syncInputDisplay() {
				if (!isInputTrigger) return;
				setFieldValue(this.input, multiple ? "" : this.selectedLabel() ?? "");
			},
			destroy() {
				_popover.destroy.call(this);
				_listbox.destroy.call(this);
				this._stopLabelFocus?.();
			},
			init() {
				_popover.init.call(this);
				_listbox.init.call(this);
				this.combobox = isInputTrigger ? this.input : queryData(this.$root, "combobox");
				_bindableField.init.call(this);
				if (this.combobox && !("labels" in this.combobox)) this._stopLabelFocus = focusOnLabelClick(this.combobox, () => this.isDisabled() || this.combobox.focus());
				if (isInputTrigger) {
					bind(this.input, {
						["@focus"]() {
							if (this.isDisabled()) return;
							this.open();
						},
						["@blur"]() {
							this.syncInputDisplay();
						},
						["@keydown.backspace"]() {
							if (this.isDisabled() || this.isReadonly()) return;
							if (!multiple || this.input.value || this.value.length === 0) return;
							this.remove(this.value.at(-1));
						}
					});
					this.syncInputDisplay();
				} else bind(this.combobox, {
					["@keydown.backspace.prevent"]() {
						this.clearFromKeyboard();
					},
					["@keydown.delete.prevent"]() {
						this.clearFromKeyboard();
					},
					["@click"]() {
						if (this.isDisabled()) return;
						this.combobox.focus();
						this.toggle();
					},
					["@keydown.enter.prevent"]() {
						if (this.isDisabled()) return;
						if (!this.opened) return this.open();
						this.select(this.index);
					},
					["@keydown.space.prevent"]() {
						if (this.isDisabled()) return;
						if (!this.opened) return this.open();
						this.select(this.index);
					},
					["@keydown.arrow-up.prevent"]() {
						if (this.isDisabled()) return;
						if (!this.opened) return this.open();
						this.lastInteraction = "keyboard";
						this.prev();
					},
					["@keydown.arrow-down.prevent"]() {
						if (this.isDisabled()) return;
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
				bind(this.input, { ["@keydown.enter.prevent"]() {} });
				bind(this.$root, {
					["@click.outside"]() {
						this.close();
					},
					["@focusout"](event) {
						const to = event.relatedTarget;
						if (to instanceof Element && !this.$root.contains(to) && this.isOpened()) this.close();
					},
					["@selected"]({ detail }) {
						this.pick(this.getElementValue(detail.button));
					},
					["@filtered"]() {
						this.syncChecked();
					}
				});
				this.$watch("value", () => this.syncChecked());
				this.$nextTick(() => this.syncChecked());
			},
			open() {
				if (this.isDisabled()) return;
				_popover.open.call(this, false);
				const highlightChosen = () => {
					const target = multiple ? this.value.at(-1) : this.value;
					const index = this.filteredItems.findIndex((item) => String(this.getElementValue(item.el)) === String(target));
					this.index = index === -1 ? null : index;
				};
				highlightChosen();
				requestAnimationFrame(() => {
					requestAnimationFrame(() => {
						this.input?.focus();
						if (this.input) highlightChosen();
					});
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
				if (multiple) {
					this.value = this.isSelected(v) ? this.value.filter((x) => String(x) !== String(v)) : [...this.value, v];
					if (isInputTrigger) {
						this.syncInputDisplay();
						this.search();
					}
				} else {
					this.value = v;
					if (isInputTrigger) this.syncInputDisplay();
					this.closeAndFocus();
				}
				this.dispatchPicked(this.value);
			},
			remove(v) {
				if (!multiple || this.isReadonly()) return;
				this.value = this.value.filter((x) => String(x) !== String(v));
				this.dispatchPicked(this.value);
			},
			clearFromKeyboard() {
				if (this.isDisabled() || this.isReadonly() || this.opened) return;
				if (multiple) {
					if (this.value.length) this.remove(this.value.at(-1));
				} else this.clearValue();
			},
			clearValue() {
				if (this.isDisabled() || this.isReadonly()) return;
				this.value = multiple ? [] : null;
				this.syncInputDisplay();
			},
			syncChecked() {
				this.items.forEach((item) => {
					const selected = this.isSelected(this.getElementValue(item.el));
					const mark = queryData(item.el, "checkmark");
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
	function composer({ submit = false } = {}) {
		const _bindableField = bindableField({ key: "composer" });
		return {
			..._bindableField,
			value: null,
			init() {
				_bindableField.init.call(this);
				if (hasBlurModel(this.$root)) blurOnFocusLeave(this.$root, this.$root);
				const modes = !submit ? [] : Array.isArray(submit) ? submit : [submit];
				const control = queryData(this.$el, "control");
				const labelFor = control && !control.id ? findInField(this.$el.parentElement, "label")?.getAttribute("for") : null;
				bind(control, {
					"x-model": "value",
					...labelFor && { id: labelFor },
					...modes.length && { ["@keydown"](e) {
						if (e.isComposing || e.keyCode === 229) return;
						if (!modes.some((mode) => {
							switch (mode) {
								case "enter": return e.key === "Enter" && !e.shiftKey && !e.ctrlKey && !e.metaKey;
								case "ctrl+enter": return e.key === "Enter" && (e.ctrlKey || e.metaKey);
								default: return false;
							}
						})) return;
						e.preventDefault();
						if (!String(e.target.value ?? "").trim()) return;
						this.$root?.closest("form")?.requestSubmit();
					} }
				});
			}
		};
	}
	//#endregion
	//#region resources/js/tooltip.js
	var DEFAULTS = {
		delay: 200,
		position: "top",
		align: "center",
		arrow: true,
		variant: null,
		size: null
	};
	var WARM_MS = 300;
	var LEAVE_MS = 100;
	var MARGIN = 4;
	var ARROW_MARGIN = 10;
	var INTERACTIVE = "a[href], button, input, select, textarea, summary, [role=button], [role=link]";
	var FOCUSABLE = "a[href], button, input:not([type=hidden]), select, textarea, summary, [tabindex]:not([tabindex=\"-1\"])";
	var HOLD = {
		hover: 0,
		touch: 1,
		focus: 2,
		manual: 3
	};
	var TRIGGER = dataSelector("tooltip");
	var CSS_VARIABLES = ["--tk-tooltip-max-width", "--tk-tooltip-duration"];
	var SCRIPT_TIP_PREFIX = `${generateId("tip-js", "")}-`;
	var config = { ...DEFAULTS };
	var installed$1 = false;
	var panel = null;
	var descriptions = null;
	var current = null;
	var pending = null;
	var openTimer = null;
	var closeTimer = null;
	var warmUntil = 0;
	var suppressed = null;
	var lastPointerType = "mouse";
	var resizeObserver = null;
	var frame = null;
	var pruneScheduled = false;
	var layer = {
		close: () => hide(),
		popoverElement: null,
		ariaTrigger: null
	};
	function installTooltips() {
		if (installed$1) return;
		installed$1 = true;
		const on = (type, handler) => document.addEventListener(type, handler, {
			capture: true,
			passive: true
		});
		on("pointerover", onPointerOver);
		on("pointerout", onPointerOut);
		on("pointerdown", onPointerDown);
		on("click", onClick);
		on("focusin", onFocusIn);
		on("focusout", onFocusOut);
		onLivewireCommit(({ succeed }) => succeed(() => {
			describeAll();
			refresh();
		}));
		document.addEventListener("livewire:navigating", () => hide());
		document.addEventListener("livewire:navigated", () => describeAll());
		if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", () => describeAll(), { once: true });
		else describeAll();
	}
	var tooltip = {
		show(el, text = null) {
			if (!el) return;
			const original = textOf(el);
			if (text !== null) el.setAttribute(dataKey("tooltip"), text);
			if (!el.hasAttribute(dataKey("tooltip"))) return;
			show(el, "manual");
			if (text === null || current?.trigger !== el) return;
			fill(el);
			if (!("restore" in current)) current.restore = original;
		},
		hide() {
			hide();
		},
		configure(options = {}) {
			Object.assign(config, options);
			config.arrow = config.arrow !== false && config.arrow !== "false";
		},
		refresh() {
			describeAll();
		}
	};
	function tooltipDirective(Alpine) {
		Alpine.directive("tooltip", (el, { expression, modifiers }, { effect, evaluateLater, cleanup }) => {
			const position = modifiers.find((m) => [
				"top",
				"bottom",
				"left",
				"right"
			].includes(m));
			const align = modifiers.find((m) => [
				"start",
				"end",
				"center"
			].includes(m));
			if (position) el.setAttribute(dataKey("tooltip-position"), position);
			if (align) el.setAttribute(dataKey("tooltip-align"), align);
			if (modifiers.includes("arrow")) el.setAttribute(dataKey("tooltip-arrow"), "true");
			if (modifiers.includes("no-arrow")) el.setAttribute(dataKey("tooltip-arrow"), "false");
			if (modifiers.includes("manual")) el.setAttribute(dataKey("tooltip-open"), "manual");
			const evaluate = evaluateLater(expression);
			effect(() => evaluate((value) => {
				if (value === null || value === void 0 || value === false || value === "") {
					el.removeAttribute(dataKey("tooltip"));
					if (current?.trigger === el) hide();
				} else {
					el.setAttribute(dataKey("tooltip"), String(value));
					el.removeAttribute(dataKey("tooltip-id"));
					describe(el);
					schedulePrune();
					if (current?.trigger === el) fill(el);
				}
			}));
			cleanup(() => {
				if (current?.trigger === el) hide();
			});
		});
	}
	var textOf = (trigger) => trigger.getAttribute(dataKey("tooltip"));
	var option = (trigger, name) => trigger.getAttribute(dataKey(`tooltip-${name}`));
	var triggerOf = (el) => el?.closest?.(TRIGGER) ?? null;
	var isManual = (trigger) => option(trigger, "open") === "manual";
	var holds = (el) => !!el && !!current && (current.trigger.contains(el) || panel?.contains(el));
	function hasArrow(trigger) {
		const arrow = option(trigger, "arrow");
		return arrow === null ? config.arrow : arrow !== "false";
	}
	function controlOf(trigger) {
		const control = trigger.matches(dataSelector("control")) ? trigger : queryData(trigger, "control") ?? trigger;
		return control.matches(FOCUSABLE) ? control : control.querySelector(FOCUSABLE) ?? control;
	}
	function onPointerOver(event) {
		if (event.pointerType === "touch") return;
		const target = event.target;
		if (panel?.contains(target)) {
			cancelClose();
			return;
		}
		if (suppressed && !suppressed.contains(target)) suppressed = null;
		const trigger = triggerOf(target);
		if (!trigger || isManual(trigger) || trigger === suppressed) {
			scheduleClose();
			return;
		}
		cancelClose();
		if (current?.trigger !== trigger) scheduleOpen(trigger);
	}
	function onPointerOut(event) {
		if (event.relatedTarget) return;
		suppressed = null;
		scheduleClose();
	}
	function onPointerDown(event) {
		lastPointerType = event.pointerType;
		if (event.pointerType === "touch") {
			if (current && !current.trigger.contains(event.target)) hide();
			return;
		}
		const trigger = triggerOf(event.target);
		if (!trigger || isManual(trigger)) return;
		suppressed = trigger;
		cancelOpen();
		if (current?.trigger === trigger) hide();
	}
	function onClick(event) {
		if (lastPointerType !== "touch") return;
		const trigger = triggerOf(event.target);
		if (!trigger || isManual(trigger)) return;
		if (trigger.matches(INTERACTIVE) || trigger.querySelector(INTERACTIVE)) return;
		if (current?.trigger === trigger) hide();
		else show(trigger, "touch");
	}
	function onFocusIn(event) {
		const trigger = triggerOf(event.target);
		if (trigger && !isManual(trigger) && event.target.matches?.(":focus-visible")) show(trigger, "focus");
	}
	function onFocusOut(event) {
		if (current?.via === "focus" && !holds(event.relatedTarget)) hide();
	}
	function scheduleOpen(trigger) {
		if (pending === trigger) return;
		cancelOpen();
		const delay = toMilliseconds(option(trigger, "delay") ?? config.delay);
		if (!(delay > 0) || current || Date.now() < warmUntil) {
			show(trigger, "hover");
			return;
		}
		pending = trigger;
		openTimer = setTimeout(() => {
			const next = pending;
			pending = null;
			openTimer = null;
			if (next?.isConnected) show(next, "hover");
		}, delay);
	}
	function cancelOpen() {
		clearTimeout(openTimer);
		openTimer = null;
		pending = null;
	}
	function scheduleClose() {
		cancelOpen();
		if (!current || current.via !== "hover" || closeTimer) return;
		closeTimer = setTimeout(hide, LEAVE_MS);
	}
	function cancelClose() {
		clearTimeout(closeTimer);
		closeTimer = null;
	}
	function panelFor(trigger) {
		if (!panel?.isConnected) {
			panel = document.createElement("div");
			panel.setAttribute("popover", "manual");
			panel.setAttribute("role", "tooltip");
			panel.id = generateId("tooltip", "panel");
			panel.setAttribute(dataKey("tooltip-panel"), "");
			panel.innerHTML = `<span ${dataKey("tooltip-panel-arrow")} aria-hidden="true"></span><span ${dataKey("tooltip-panel-body")}><span ${dataKey("tooltip-panel-text")}></span><kbd ${dataKey("tooltip-panel-kbd")}></kbd></span>`;
			document.body.appendChild(panel);
		}
		const host = trigger.closest("dialog[open]") ?? document.body;
		if (panel.parentElement !== host) {
			if (panel.matches(":popover-open")) panel.hidePopover();
			host.appendChild(panel);
		}
		return panel;
	}
	function fill(trigger) {
		const text = queryData(panel, "tooltip-panel-text");
		const kbd = queryData(panel, "tooltip-panel-kbd");
		const template = trigger.querySelector(`:scope > template${dataSelector("tooltip-content")}`);
		const shortcut = option(trigger, "kbd");
		const color = option(trigger, "color");
		if (template) text.replaceChildren(template.content.cloneNode(true));
		else text.textContent = textOf(trigger) ?? "";
		kbd.textContent = shortcut ?? "";
		kbd.hidden = !shortcut;
		panel.className = option(trigger, "class") ?? "";
		panel.dataset.variant = option(trigger, "variant") ?? config.variant ?? "";
		panel.dataset.color = color ?? "";
		panel.dataset.size = option(trigger, "size") ?? config.size ?? "";
		panel.toggleAttribute("data-arrow", hasArrow(trigger));
		if (color) panel.classList.add(`tk-color-${color}`);
		const style = getComputedStyle(trigger);
		for (const name of CSS_VARIABLES) {
			const value = style.getPropertyValue(name).trim();
			if (value) panel.style.setProperty(name, value);
			else panel.style.removeProperty(name);
		}
	}
	function show(trigger, via) {
		cancelOpen();
		cancelClose();
		if (current?.trigger === trigger) {
			if (HOLD[via] > HOLD[current.via]) current.via = via;
			return;
		}
		hide();
		if (!trigger.isConnected || !trigger.hasAttribute(dataKey("tooltip"))) return;
		panelFor(trigger);
		fill(trigger);
		current = {
			trigger,
			via
		};
		if (!panel.matches(":popover-open")) panel.showPopover();
		layer.popoverElement = panel;
		layer.ariaTrigger = controlOf(trigger);
		pushEscapeLayer(layer);
		window.addEventListener("scroll", reposition, true);
		window.addEventListener("resize", reposition, true);
		resizeObserver = new ResizeObserver(() => reposition());
		resizeObserver.observe(trigger);
		resizeObserver.observe(panel);
		place();
	}
	function hide() {
		cancelClose();
		if (!current) return;
		const { trigger, restore } = current;
		current = null;
		warmUntil = Date.now() + WARM_MS;
		if (restore !== void 0) {
			if (restore === null) trigger.removeAttribute(dataKey("tooltip"));
			else trigger.setAttribute(dataKey("tooltip"), restore);
		}
		removeEscapeLayer(layer);
		layer.popoverElement = layer.ariaTrigger = null;
		window.removeEventListener("scroll", reposition, true);
		window.removeEventListener("resize", reposition, true);
		resizeObserver?.disconnect();
		resizeObserver = null;
		cancelAnimationFrame(frame);
		frame = null;
		if (panel?.isConnected && panel.matches(":popover-open")) panel.hidePopover();
	}
	function refresh() {
		if (!current) return;
		if (!current.trigger.isConnected || !current.trigger.hasAttribute(dataKey("tooltip"))) {
			hide();
			return;
		}
		fill(current.trigger);
		reposition();
	}
	function reposition() {
		if (!current || frame) return;
		frame = requestAnimationFrame(() => {
			frame = null;
			place();
		});
	}
	function place() {
		if (!current) return;
		const { trigger } = current;
		if (!trigger.isConnected || !panel?.isConnected) {
			hide();
			return;
		}
		placeNextTo(panel, trigger.getBoundingClientRect(), {
			position: option(trigger, "position") || config.position,
			align: option(trigger, "align") || config.align,
			margin: hasArrow(trigger) ? ARROW_MARGIN : MARGIN,
			rtl: isRtl(trigger)
		});
	}
	function describeAll() {
		document.querySelectorAll(TRIGGER).forEach(describe);
		prune();
	}
	function describe(trigger) {
		if (isManual(trigger)) return;
		const name = textOf(trigger);
		const text = [name, option(trigger, "kbd")].filter(Boolean).join(" ");
		if (!text) return;
		let id = option(trigger, "id");
		if (!id) {
			const control = controlOf(trigger);
			if (normalizeName(nameOf(control)) === normalizeName(name)) return;
			id = hashId(text);
			trigger.setAttribute(dataKey("tooltip-id"), id);
			const ids = (control.getAttribute("aria-describedby") ?? "").split(" ").filter((other) => other && !other.startsWith(SCRIPT_TIP_PREFIX));
			control.setAttribute("aria-describedby", [...ids, id].join(" "));
		}
		if (!document.getElementById(id)) {
			const span = document.createElement("span");
			span.id = id;
			span.textContent = text;
			descriptionsHost().appendChild(span);
		}
	}
	function descriptionsHost() {
		if (!descriptions?.isConnected) {
			descriptions = document.createElement("div");
			descriptions.hidden = true;
			descriptions.setAttribute(dataKey("tooltip-descriptions"), "");
			document.body.appendChild(descriptions);
		}
		return descriptions;
	}
	function schedulePrune() {
		if (pruneScheduled) return;
		pruneScheduled = true;
		queueMicrotask(prune);
	}
	function prune() {
		pruneScheduled = false;
		if (!descriptions?.isConnected) return;
		const used = new Set(queryAllData(document, "tooltip-id").map((el) => el.getAttribute(dataKey("tooltip-id"))));
		Array.from(descriptions.children).forEach((span) => {
			if (!used.has(span.id)) span.remove();
		});
	}
	function nameOf(control) {
		const labelledBy = control.getAttribute("aria-labelledby");
		if (control.getAttribute("aria-label")) return control.getAttribute("aria-label");
		if (labelledBy) return labelledBy.split(/\s+/).map((id) => document.getElementById(id)?.textContent ?? "").join(" ");
		if (control.labels?.length) return Array.from(control.labels).map((label) => label.textContent).join(" ");
		return control.textContent;
	}
	var normalizeName = (text) => (text ?? "").replace(/\s+/g, " ").trim().toLowerCase();
	function hashId(text) {
		let hash = 2166136261;
		for (let i = 0; i < text.length; i++) {
			hash ^= text.charCodeAt(i);
			hash = Math.imul(hash, 16777619);
		}
		return generateId("tip-js", (hash >>> 0).toString(36));
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
				return queryData(this.$el.closest(dataSelector("field-control")), "control") ?? queryData(this.$el.previousElementSibling, "control") ?? queryData(this.$el.parentElement?.previousElementSibling, "control");
			},
			init() {
				if (!this.findTarget() && !content) {
					this.$el.remove();
					return;
				}
				bind(this.$el, { async ["@click"]() {
					clearTimeout(this.timeout);
					const currentTarget = content ? null : this.findTarget();
					const text = content ?? (currentTarget ? "value" in currentTarget ? currentTarget.value : currentTarget.innerText : null);
					if (text === null || !await copyText(text)) {
						this.copied = false;
						emit(this.$root, "failed");
						return;
					}
					this.copied = true;
					this.$nextTick(() => {
						tooltip.show(this.$el);
						announce(this.$el.getAttribute("aria-label"));
					});
					emit(currentTarget, "copied", {}, { bubbles: true });
					this.timeout = setTimeout(() => {
						tooltip.hide();
						this.copied = false;
						this.timeout = null;
					}, 1e3);
				} });
			},
			destroy() {
				clearTimeout(this.timeout);
			}
		};
	}
	async function copyText(text) {
		try {
			if (navigator.clipboard?.writeText) {
				await navigator.clipboard.writeText(text);
				return true;
			}
		} catch {}
		const area = document.createElement("textarea");
		area.value = text;
		area.setAttribute("readonly", "");
		area.style.position = "fixed";
		area.style.opacity = "0";
		document.body.appendChild(area);
		area.select();
		try {
			return document.execCommand("copy");
		} catch {
			return false;
		} finally {
			area.remove();
		}
	}
	//#endregion
	//#region resources/js/components/credit-card.js
	var credit_card_exports = /* @__PURE__ */ __exportAll({ creditCard: () => creditCard });
	function creditCard(types = {}, options = {}) {
		const _toggleable = toggleable();
		return {
			..._toggleable,
			types,
			_iconUid: generateId("card-icon"),
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
					[":class"]() {
						return { "rotate-y-180": !this.isOpened() };
					}
				});
			},
			typeIcon() {
				const uid = this._iconUid;
				return (this.typeOptions().icon ?? "").replace(/id="([^"]+)"/g, `id="$1-${uid}"`).replace(/url\(#([^)]+)\)/g, `url(#$1-${uid})`).replace(/href="#([^"]+)"/g, `href="#$1-${uid}"`);
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
	var startOfQuarter = (date) => new Date(date.getFullYear(), Math.floor(date.getMonth() / 3) * 3, 1);
	var endOfQuarter = (date) => new Date(date.getFullYear(), Math.floor(date.getMonth() / 3) * 3 + 3, 0);
	var DATE_STYLES = [
		"full",
		"long",
		"medium",
		"short"
	];
	var DEFAULT_FORMAT = "medium";
	function datePicker({ range = false, dateRange = false, multiple = null, format = null, trigger = null, openTo = null, forceOpenTo = null, confirm = null, ...calendarOptions } = {}) {
		if (format && !DATE_STYLES.includes(format)) {
			console.warn(`[tallkit] tk:date-picker received an invalid "format" ("${format}"). Expected one of: ${DATE_STYLES.join(", ")}. Falling back to "${DEFAULT_FORMAT}".`);
			format = DEFAULT_FORMAT;
		}
		const mode = range ? "range" : null;
		const _popover = popover({
			mode: "dropdown",
			position: "bottom",
			align: "start"
		});
		const _calendar = calendar({
			range,
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
				this.preset = raw?.preset ?? null;
				return this.parseInitialValue(raw);
			},
			toWire: dateRange ? function() {
				return this.committedRange();
			} : null
		});
		return {
			..._popover,
			..._calendar,
			..._bindableField,
			committed: null,
			preset: null,
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
					if (confirm) return;
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
			isDisabled() {
				return !!queryData(this.$root, "control")?.disabled;
			},
			open(focus = true) {
				if (this.isDisabled()) return;
				_popover.open.call(this, focus);
			},
			onOpen() {
				if (confirm) this.value = this.committed;
				if (forceOpenTo && openTo) this.anchorMonth = startOfMonth(parseIsoDate(openTo));
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
				if (this.isDisabled()) return;
				if (mode === "range" || multiple) return;
				if (iso && this.isDayDisabled(iso)) return;
				this.value = iso || null;
				this.focused = this.value ?? this.focused;
				this.dispatchPicked(this.value);
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
					const start = parseIsoDate(this.value.start);
					const end = parseIsoDate(this.value.end);
					if (!start || !end) return null;
					return fmt.formatRange ? fmt.formatRange(start, end) : `${fmt.format(start)} – ${fmt.format(end)}`;
				}
				if (multiple) {
					const dates = (this.value ?? []).map(parseIsoDate).filter(Boolean);
					return dates.length ? dates.map((date) => fmt.format(date)).join(", ") : null;
				}
				const date = parseIsoDate(this.value);
				return date ? fmt.format(date) : null;
			},
			committedRange() {
				if (!this.committed?.start) return null;
				const range = {
					start: this.committed.start,
					end: this.committed.end ?? null
				};
				const preset = this.presetRange(this.preset);
				if (preset && preset.start === range.start && preset.end === range.end) range.preset = this.preset;
				return range;
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
				return trigger === "input" && !multiple;
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
					const start = this.value?.start ? formatTypedDate(this.value.start, this.locale) : "";
					const end = this.value?.end ? formatTypedDate(this.value.end, this.locale) : "";
					if (!start && !end) return "";
					return `${start} – ${end}`;
				}
				return this.value ? formatTypedDate(this.value, this.locale) : "";
			},
			commitTyped() {
				if (this.isDisabled()) return;
				if (!this.typable()) return;
				if ((this.typed.match(/\d/g) ?? []).length < this.requiredDigitCount()) return;
				if (mode === "range") {
					const [rawStart, rawEnd] = this.typed.split(/\s*[–—]\s*/);
					const start = parseTypedDate(rawStart, this.locale);
					const end = parseTypedDate(rawEnd, this.locale);
					if (start) {
						this.setRangeBound("start", start);
						this.anchorMonth = startOfMonth(parseIsoDate(start));
					}
					if (end) {
						this.setRangeBound("end", end);
						this.anchorMonth = startOfMonth(parseIsoDate(end));
					}
				} else {
					const iso = parseTypedDate(this.typed, this.locale);
					if (iso && !this.isDayDisabled(iso)) {
						this.value = iso;
						this.focused = iso;
						this.anchorMonth = startOfMonth(parseIsoDate(iso));
						this.dispatchPicked(iso);
					}
				}
			},
			confirmTyped() {
				this.commitTyped();
				this.typing = false;
				this.syncTyped();
				if (!confirm) this.close();
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
				const today = formatIsoDate(/* @__PURE__ */ new Date());
				const todayDate = parseIsoDate(today);
				const week = (iso) => {
					const start = startOfWeek(parseIsoDate(iso), this.startDay);
					return {
						start,
						end: addDays(start, 6)
					};
				};
				const month = (offset) => {
					const date = addMonths(todayDate, offset);
					return {
						start: formatIsoDate(startOfMonth(date)),
						end: formatIsoDate(endOfMonth(date))
					};
				};
				const quarter = (offset) => {
					const date = addMonths(startOfQuarter(todayDate), offset * 3);
					return {
						start: formatIsoDate(startOfQuarter(date)),
						end: formatIsoDate(endOfQuarter(date))
					};
				};
				const year = (offset) => ({
					start: `${todayDate.getFullYear() + offset}-01-01`,
					end: `${todayDate.getFullYear() + offset}-12-31`
				});
				switch (key) {
					case "today": return {
						start: today,
						end: today
					};
					case "yesterday": return {
						start: addDays(today, -1),
						end: addDays(today, -1)
					};
					case "tomorrow": return {
						start: addDays(today, 1),
						end: addDays(today, 1)
					};
					case "thisWeek": return week(today);
					case "lastWeek": return week(addDays(today, -7));
					case "nextWeek": return week(addDays(today, 7));
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
					case "next7Days": return {
						start: today,
						end: addDays(today, 6)
					};
					case "next14Days": return {
						start: today,
						end: addDays(today, 13)
					};
					case "next30Days": return {
						start: today,
						end: addDays(today, 29)
					};
					case "thisMonth": return month(0);
					case "lastMonth": return month(-1);
					case "nextMonth": return month(1);
					case "thisQuarter": return quarter(0);
					case "lastQuarter": return quarter(-1);
					case "nextQuarter": return quarter(1);
					case "thisYear": return year(0);
					case "lastYear": return year(-1);
					case "nextYear": return year(1);
					case "yearToDate": return {
						start: `${todayDate.getFullYear()}-01-01`,
						end: today
					};
					case "last3Months": return {
						start: this.shiftMonth(addDays(today, 1), -3),
						end: today
					};
					case "last6Months": return {
						start: this.shiftMonth(addDays(today, 1), -6),
						end: today
					};
					case "next3Months": return {
						start: today,
						end: this.shiftMonth(addDays(today, -1), 3)
					};
					case "next6Months": return {
						start: today,
						end: this.shiftMonth(addDays(today, -1), 6)
					};
					default: return null;
				}
			},
			presetAvailable(key) {
				const range = this.presetRange(key);
				return !!range && this.rangeAllowed(range.start, range.end);
			},
			isPresetActive(key) {
				const range = this.presetRange(key);
				if (!range) return false;
				const chosen = this.presetRange(this.preset);
				if (chosen && this.value?.start === chosen.start && this.value?.end === chosen.end) return key === this.preset;
				return this.value?.start === range.start && this.value?.end === range.end;
			},
			applyPreset(key) {
				if (this.isDisabled()) return;
				const range = this.presetRange(key);
				if (!range || !this.rangeAllowed(range.start, range.end)) return;
				this.preset = key;
				this.value = range;
				this.focused = range.end;
				this.dispatchPicked(range);
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
				const own = (el) => el.matches?.(dataSelector("disclosure-item")) && el.closest("[x-data^=\"disclosureGroup\"]") === this.$root;
				const getItems = () => queryAllData(this.$root, "disclosure-item").filter(own);
				const observe = () => this.observer.observe(this.$root, {
					subtree: true,
					attributeFilter: ["data-open"]
				});
				this.observer = new MutationObserver((records) => {
					const changed = records.map((record) => record.target).filter(own);
					if (!changed.length) return;
					const items = getItems();
					if (exclusive) {
						const opened = new Set(changed.filter((item) => item.hasAttribute("data-open")));
						if (opened.size) items.forEach((item) => {
							if (!opened.has(item)) item.removeAttribute("data-open");
						});
					}
					this.observer.disconnect();
					emit(this.$root, "changed", { items });
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
				const panel = this.$root.querySelector(":scope > button + *, :scope > [role=\"heading\"] + *");
				if (panel && !panel.id) panel.id = generateId("disclosure");
				this.observer = new MutationObserver(() => this.setOpened(this.$root.hasAttribute("data-open")));
				this.observer.observe(this.$root, { attributeFilter: ["data-open"] });
				bind(this.$root.querySelectorAll(":scope > button, :scope > [role=\"heading\"] > button"), {
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
		let chart = null;
		let source = null;
		return {
			..._loadable,
			...dataOptions(),
			...serverOptions(),
			_resizeObserver: null,
			_stopColorScheme: null,
			getChart() {
				return chart;
			},
			init() {
				this.load(() => loadRemoteAssets(() => !!window.echarts, "https://cdn.jsdelivr.net/npm/echarts@6"));
				this.followServerOptions((next) => {
					if (this.isCompleted() && this.$refs.target) this.render(next);
				});
				this._stopColorScheme = onColorSchemeChange(() => {
					if (!chart || !source) return;
					this._resizeObserver?.disconnect();
					this._resizeObserver = null;
					chart.dispose();
					chart = null;
					this.render(source);
				});
			},
			render(options = {}) {
				try {
					source = {
						...source,
						...options
					};
					if (!chart) {
						chart = window.echarts.init(this.$refs.target, isDarkMode() ? "dark" : null);
						if (isDarkMode()) chart.setOption({ backgroundColor: "transparent" });
						const resize = debounce(() => chart?.resize(), 100);
						this._resizeObserver = new ResizeObserver(resize);
						this._resizeObserver.observe(this.$refs.target);
					}
					const option = {
						...options,
						...this.getDataOptions(this.$refs.target)
					};
					chart.setOption(option, "series" in option ? { replaceMerge: ["series"] } : {});
					emit(this.$refs.target, "rendered", { chart }, { later: true });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingServerOptions();
				this._stopColorScheme?.();
				this._resizeObserver?.disconnect();
				this._resizeObserver = null;
				chart?.dispose();
				chart = null;
				source = null;
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
	function parseToolbar(toolbar, groupOrder) {
		const tokens = (toolbar ?? "").trim().split(/\s+/).filter(Boolean);
		if (!tokens.length) return null;
		if (tokens.includes("none")) return [];
		tokens.filter((token) => token !== "full" && !groupOrder.includes(token)).forEach((token) => console.warn(`[tallkit] Unknown editor toolbar group "${token}"`));
		if (tokens.includes("full")) return groupOrder;
		return groupOrder.filter((group) => tokens.includes(group));
	}
	function editorField() {
		return {
			input: null,
			_lastSynced: null,
			initField() {
				this.input = queryData(this.$root, "control");
				if (hasBlurModel(this.input)) blurOnFocusLeave(this.$root, this.input);
				if (!getWireModelInfo(this.input)) onFormReset(this.$root, this.input?.form, () => {
					if (this.isCompleted()) this.applyExternalValue(this.input.value);
				});
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
			},
			lockState() {
				if (this.input?.disabled) return "disabled";
				if (this.input?.readOnly) return "readonly";
				return null;
			},
			followLockState(apply, toolbar = () => null) {
				const update = () => {
					const state = this.lockState();
					apply(state !== null);
					toolbar()?.toggleAttribute("inert", state !== null);
					if (state) this.$root.setAttribute(dataKey("editor-state"), state);
					else this.$root.removeAttribute(dataKey("editor-state"));
				};
				update();
				this._lockObserver?.disconnect();
				this._lockObserver = new MutationObserver(update);
				this._lockObserver.observe(this.input, {
					attributes: true,
					attributeFilter: ["disabled", "readonly"]
				});
			},
			stopFollowingLockState() {
				this._lockObserver?.disconnect();
				this._lockObserver = null;
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
	function parseData(value) {
		if (!value) return void 0;
		try {
			const data = typeof value === "string" ? JSON.parse(value) : value;
			if (data && Array.isArray(data.blocks)) return data;
		} catch {}
		console.warn("[tallkit] The Editor.js value is not Editor.js data (JSON with \"blocks\"): it starts empty.", value);
	}
	function editorjs({ options = {}, scripts = [], styles = [], toolbar = null, i18n = null } = {}) {
		const _loadable = loadable();
		let editor = null;
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			_saveToken: 0,
			getEditor() {
				return editor;
			},
			init() {
				this.initField();
				const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
				this.load(() => loadRemoteAssets(() => !!window.EditorJS, [
					"https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2",
					...groups.flatMap((group) => GROUPS$3[group]?.scripts ?? []),
					...scripts
				], styles).then(() => this.mount(groups)));
			},
			applyExternalValue(value) {
				editor.render(parseData(value) ?? { blocks: [] });
			},
			mount(groups) {
				if (this.isDestroyed()) return;
				editor = new window.EditorJS({
					holder: this.$refs.root,
					tools: groups.reduce((tools, group) => ({
						...tools,
						...GROUPS$3[group]?.tools()
					}), {}),
					inlineToolbar: groups.flatMap((group) => GROUPS$3[group]?.inline ?? []),
					...i18n ? { i18n: { messages: i18n } } : {},
					data: parseData(this.input.value),
					readOnly: this.lockState() !== null,
					onChange: async (api) => {
						const token = ++this._saveToken;
						const output = await api.saver.save();
						if (token !== this._saveToken) return;
						this.sync(output.blocks?.length ? JSON.stringify(output) : "");
					},
					...options,
					...this.getDataOptions(this.$refs.root)
				});
				return editor.isReady.then(() => {
					const instance = editor;
					this.followLockState((locked) => {
						if (instance?.readOnly && instance.readOnly.isEnabled !== locked) instance.readOnly.toggle(locked);
					});
					emit(this.input, "rendered", { editor }, { later: true });
				});
			},
			async destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingLockState();
				const instance = editor;
				editor = null;
				await instance?.destroy();
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
				const method = String(_options.method ?? "get").toUpperCase();
				const sameOrigin = new URL(_url, window.location.href).origin === window.location.origin;
				const csrf = getCsrfToken();
				const csrfMeta = document.querySelector("meta[name=\"csrf-token\"]")?.content;
				const headers = ![
					"GET",
					"HEAD",
					"OPTIONS"
				].includes(method) && sameOrigin ? {
					...csrf ? { "X-XSRF-TOKEN": csrf } : csrfMeta ? { "X-CSRF-TOKEN": csrfMeta } : {},
					..._options.headers
				} : _options.headers;
				this.load(async () => {
					this.response = await window.fetch(_url, {
						..._options,
						headers,
						signal: controller.signal
					});
					if (!this.response.ok) throw new Error(this.response.statusText || `HTTP ${this.response.status}`);
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
				if (hasLivewire() && this.$el.closest("[wire\\:id]")) this.watchLivewireCommits();
				else if (focusError) this.focusFirstInvalidField();
			},
			watchLivewireCommits() {
				this.livewireCommitCleanup = onLivewireCommit(({ component, commit, succeed }) => {
					if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return;
					const calls = commit?.calls ?? [];
					const method = action ?? this.submitMethod();
					if (method ? !calls.some((call) => call.method === method) : calls.length === 0) return;
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
			submitMethod() {
				const form = this.$el.matches("form") ? this.$el : this.$el.querySelector("form");
				return Array.from(form?.attributes ?? []).find((attr) => attr.name.startsWith("wire:submit"))?.value.trim().split("(")[0].trim() || null;
			},
			clearErrors() {
				this.$el.querySelectorAll("[data-invalid], [aria-invalid=\"true\"]").forEach((field) => {
					field.removeAttribute("data-invalid");
					field.removeAttribute("aria-invalid");
				});
				this.$el.querySelectorAll(`${dataSelector("error")}, ${dataSelector("error-group")}`).forEach((el) => el.remove());
			},
			focusFirstInvalidField() {
				const focusable = "input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [contenteditable=\"true\"], [tabindex]:not([tabindex=\"-1\"])";
				const field = Array.from(this.$el.querySelectorAll("[data-invalid], [aria-invalid=\"true\"]")).map((marked) => marked.matches(focusable) ? marked : marked.querySelector(focusable) ?? marked.closest(`${dataSelector("field-control")}, ${dataSelector("field")}`)?.querySelector(focusable)).find(Boolean);
				if (!(field instanceof HTMLElement)) return;
				field.scrollIntoView({
					behavior: prefersReducedMotion() ? "auto" : "smooth",
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
		let chart = null;
		return {
			..._loadable,
			...dataOptions(),
			...serverOptions(),
			_resizeObserver: null,
			getChart() {
				return chart;
			},
			init() {
				this.load(() => loadRemoteAssets(() => !!window.frappe?.Chart, "https://cdn.jsdelivr.net/npm/frappe-charts@1"));
				this.followServerOptions((next) => {
					if (this.isCompleted() && this.$refs.target) this.render(next);
				});
			},
			render(options = {}) {
				try {
					chart?.destroy?.();
					chart = new window.frappe.Chart(this.$refs.target, {
						...options,
						...this.getDataOptions(this.$refs.target)
					});
					if (chart.boundDrawFn) {
						chart.resizeObserver?.disconnect();
						window.removeEventListener("resize", chart.boundDrawFn);
						window.removeEventListener("orientationchange", chart.boundDrawFn);
						this._resizeObserver?.disconnect();
						this._resizeObserver = new ResizeObserver(debounce(() => {
							try {
								chart?.draw?.(true);
							} catch {
								requestAnimationFrame(() => {
									try {
										chart?.draw?.(true);
									} catch {}
								});
							}
						}, 100));
						this._resizeObserver.observe(this.$refs.target);
					}
					emit(this.$refs.target, "rendered", { chart }, { later: true });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingServerOptions();
				this._resizeObserver?.disconnect();
				this._resizeObserver = null;
				chart?.destroy?.();
				chart = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/full-calendar.js
	var full_calendar_exports = /* @__PURE__ */ __exportAll({ fullCalendar: () => fullCalendar });
	function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
		const _loadable = loadable();
		let calendar = null;
		const calendarLocale = () => locale ? String(locale).replace("_", "-").toLowerCase() : null;
		return {
			..._loadable,
			...dataOptions(),
			...serverOptions(),
			_stopColorScheme: null,
			getCalendar() {
				return calendar;
			},
			init() {
				const syncScheme = (dark) => this.$root.setAttribute("data-color-scheme", dark ? "dark" : "light");
				syncScheme(isDarkMode());
				this._stopColorScheme = onColorSchemeChange(syncScheme);
				this.followServerOptions((next) => {
					options = next;
					if (calendar) this.render();
				});
				const baseUrl = "https://cdn.jsdelivr.net/npm/fullcalendar@7";
				this.load(async () => {
					await loadRemoteAssets(() => !!window.FullCalendar, [`${baseUrl}/all/global.min.js`, `${baseUrl}/themes/${theme ?? "monarch"}/global.js`], [
						`${baseUrl}/skeleton.css`,
						`${baseUrl}/themes/${theme ?? "monarch"}/theme.css`,
						`${baseUrl}/themes/${theme ?? "monarch"}/palettes/${palette ?? "blue"}.css`
					]);
					const code = calendarLocale();
					if (code && code !== "en" && code !== "en-us") {
						const file = (name) => loadScript(`${baseUrl}/locales/${name}/global.min.js`);
						await file(code).catch(() => code.includes("-") ? file(code.split("-")[0]) : null).catch(() => null);
					}
				});
			},
			render() {
				try {
					this._calendarEl ??= this.$el;
					calendar?.destroy();
					calendar = new window.FullCalendar.Calendar(this._calendarEl, {
						locale: calendarLocale() || void 0,
						...options,
						...this.getDataOptions(this._calendarEl)
					});
					calendar.render();
					emit(this._calendarEl, "rendered", { fullCalendar: calendar }, { later: true });
				} catch (e) {
					this.fail(e);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingServerOptions();
				this._stopColorScheme?.();
				calendar?.destroy();
				calendar = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/header.js
	var header_exports = /* @__PURE__ */ __exportAll({ header: () => header });
	function header() {
		return { ...stickable() };
	}
	//#endregion
	//#region resources/js/components/highlightjs.js
	var highlightjs_exports = /* @__PURE__ */ __exportAll({ highlightjs: () => highlightjs });
	var CDN = "https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build";
	var THEMES = {
		light: `${CDN}/styles/github.min.css`,
		dark: `${CDN}/styles/github-dark.min.css`
	};
	var followingTheme = false;
	function syncTheme() {
		const href = isDarkMode() ? THEMES.dark : THEMES.light;
		let link = document.querySelector(`link${dataSelector("highlightjs-theme")}`);
		if (!link) {
			link = document.createElement("link");
			link.rel = "stylesheet";
			link.setAttribute(dataKey("highlightjs-theme"), "");
			document.head.appendChild(link);
		}
		if (link.getAttribute("href") !== href) link.setAttribute("href", href);
	}
	function followTheme() {
		syncTheme();
		if (followingTheme) return;
		followingTheme = true;
		onColorSchemeChange(syncTheme);
	}
	function highlightjs() {
		return {
			...loadable(),
			language: null,
			init() {
				this.load(() => loadRemoteAssets(() => !!window.hljs, `${CDN}/highlight.min.js`).then(() => followTheme()));
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
				bind(this.$el, { ["@click"]() {
					this.viewed = !this.viewed;
					input.setAttribute("type", this.viewed ? "text" : this.originalType);
					emit(input, "viewed", {}, { bubbles: true });
				} });
				this.inputObserver = new MutationObserver(() => {
					this.viewed = input?.getAttribute("type") !== "password";
				});
				this.inputObserver.observe(input, {
					attributes: true,
					attributeFilter: ["type"]
				});
				this._stopMorphHook = keepAttributesOnMorph((el) => el === input, ["type"]);
			},
			destroy() {
				this.inputObserver?.disconnect();
				this._stopMorphHook?.();
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
			isControlled() {
				return this.menuGroup === true;
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
					[":aria-checked"]: () => this.isChecked() ? "true" : "false"
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
			observer: null,
			_current: null,
			typed: "",
			typedTimeout: null,
			init() {
				const menu = this.$el;
				const itemOf = (event) => {
					const item = event.target instanceof Element ? event.target.closest(dataSelector("menu-item")) : null;
					return item && item.closest(dataSelector("menu")) === menu && !item.disabled ? item : null;
				};
				const activate = (item) => {
					this.menuItems().forEach((other) => {
						if (other !== item) other.removeAttribute("data-active");
					});
					item.setAttribute("data-active", "");
				};
				bind(menu, {
					["@mouseover"](event) {
						const item = itemOf(event);
						if (item) activate(item);
					},
					["@mouseout"](event) {
						const item = itemOf(event);
						if (!item || item.contains(event.relatedTarget)) return;
						item.removeAttribute("data-active");
						const focused = this.menuItems().find((other) => other === document.activeElement);
						if (focused) activate(focused);
					},
					["@focusin"](event) {
						const item = itemOf(event);
						if (!item) return;
						activate(item);
						this.syncTabindex(item);
					},
					["@focusout"](event) {
						const item = event.target instanceof Element ? event.target.closest(dataSelector("menu-item")) : null;
						if (item && item.closest(dataSelector("menu")) === menu) item.removeAttribute("data-active");
						const to = event.relatedTarget;
						if (to instanceof Element && !menu.contains(to) && typeof this.close === "function" && this.isOpened?.()) this.close();
					},
					["@keydown.arrow-down.prevent"]() {
						this.focusItem(this.menuItems(), 1);
					},
					["@keydown.arrow-up.prevent"]() {
						this.focusItem(this.menuItems(), -1);
					},
					["@keydown.home.prevent"]() {
						this.focusItem(this.menuItems(), "first");
					},
					["@keydown.end.prevent"]() {
						this.focusItem(this.menuItems(), "last");
					},
					["@keydown"](event) {
						if (event.key.length !== 1 || event.ctrlKey || event.metaKey || event.altKey || event.key === " ") return;
						if (event.target.closest?.("input, textarea, select, [contenteditable]")) return;
						this.typeAhead(event.key);
					}
				});
				if (typeof this.isOpened !== "function") menu.closest("[popover]")?.removeAttribute("popover");
				this.syncTabindex();
				this.observer = new MutationObserver(() => this.syncTabindex());
				this.observer.observe(menu, {
					childList: true,
					subtree: true,
					attributes: true,
					attributeFilter: ["tabindex"]
				});
			},
			destroy() {
				this.observer?.disconnect();
				clearTimeout(this.typedTimeout);
			},
			syncTabindex(active = null) {
				const items = this.menuItems();
				const usable = (item) => item && items.includes(item) && !item.disabled;
				const current = active ?? (usable(this._current) ? this._current : null) ?? items.find((item) => item.getAttribute("tabindex") === "0" && !item.disabled) ?? items.find((item) => !item.disabled);
				this._current = current;
				items.forEach((item) => {
					const value = item === current ? "0" : "-1";
					if (item.getAttribute("tabindex") !== value) item.setAttribute("tabindex", value);
				});
			},
			typeAhead(key) {
				clearTimeout(this.typedTimeout);
				this.typed += key.toLowerCase();
				this.typedTimeout = setTimeout(() => {
					this.typed = "";
				}, 500);
				const enabled = this.menuItems().filter((item) => !item.disabled);
				const start = enabled.indexOf(document.activeElement);
				const from = this.typed.length === 1 ? start + 1 : Math.max(start, 0);
				[...enabled.slice(from), ...enabled.slice(0, from)].find((item) => item.textContent.trim().toLowerCase().startsWith(this.typed))?.focus();
			},
			menuItems() {
				return queryAllData(this.$root, "menu-item").filter((item) => item.closest(dataSelector("menu")) === this.$root);
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
		return {
			init() {
				bind(this.$el, { ["@click"]() {
					this.show();
				} });
				if (shortcut) bindShortcut(this.$el, shortcut, () => this.show());
			},
			show() {
				if (this.$root.querySelector("button[disabled]")) return false;
				this.$dispatch(eventName("modal-show"), { name });
			}
		};
	}
	//#endregion
	//#region resources/js/components/modal.js
	var modal_exports = /* @__PURE__ */ __exportAll({ modal: () => modal });
	function modal({ name = null, dismissible = null, persist = null, shortcut = null, open = false } = {}) {
		return {
			init() {
				const dialog = this.$el;
				bind(dialog, {
					[`@${eventName("modal-show")}.document`](event) {
						if (event.detail.name === name && !event.detail.scope) {
							dialog.showModal();
							return;
						}
						if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
							dialog.showModal();
							return;
						}
					},
					[`@${eventName("modal-close")}.document`](event) {
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
				const fromInnerModal = (event) => event.target instanceof Element && event.target.closest("dialog") !== dialog;
				let pressedOn = null;
				const handleCloseAttempt = (event, checkTarget = true) => {
					if (checkTarget) {
						const target = event.target;
						const started = pressedOn;
						pressedOn = null;
						if (target !== dialog || started !== dialog) return;
					}
					event.preventDefault();
					if (persist) {
						const persistAnimation = typeof persist === "string" ? persist : "tilt-shaking";
						dialog.classList.remove(persistAnimation);
						dialog.focus();
						this.$nextTick(() => dialog.classList.add(persistAnimation));
						return;
					}
					if (dismissible === false) return;
					dialog.close();
				};
				bind(dialog, {
					["@toggle"](event) {
						if (event.newState === "open") {
							const autofocus = Array.from(dialog.querySelectorAll("[autofocus]")).find((el) => el.closest("dialog") === dialog);
							const title = dialog.getAttribute("aria-labelledby") ? document.getElementById(dialog.getAttribute("aria-labelledby")) : null;
							const start = autofocus ?? (title && dialog.contains(title) ? title : dialog);
							if (!autofocus && !start.hasAttribute("tabindex")) {
								start.setAttribute("tabindex", "-1");
								start.style.outline = "none";
							}
							start.focus();
							emit(dialog, "opened", { name });
						}
						if (event.newState === "closed") emit(dialog, "closed", { name });
					},
					["@pointerdown"](event) {
						pressedOn = event.target;
					},
					["@click"](event) {
						if (fromInnerModal(event)) return;
						if (event.target.closest(`${dataSelector("modal-close")},${dataSelector("modal-auto-close")}`)) {
							dialog.close();
							return;
						}
						handleCloseAttempt(event);
					},
					["@keydown.escape.prevent"](event) {
						if (fromInnerModal(event) || isEscapeHandled(event)) return;
						handleCloseAttempt(event, false);
					},
					["@cancel"](event) {
						if (event.target !== dialog) return;
						handleCloseAttempt(event, false);
					}
				});
				if (shortcut) bindShortcut(dialog, shortcut, () => this.$dispatch(eventName("modal-show"), { name }));
				if (open) this.$nextTick(() => dialog.isConnected && !dialog.open && dialog.showModal());
			},
			show() {
				this.$dispatch(eventName("modal-show"), { name });
			},
			close() {
				this.$dispatch(eventName("modal-close"), { name });
			}
		};
	}
	//#endregion
	//#region resources/js/components/money.js
	var money_exports = /* @__PURE__ */ __exportAll({ money: () => money });
	function money({ delimiter = ",", thousands = ".", precision = 2, as = "decimal", model = null, modifiers = "" } = {}) {
		const toDecimal = (text) => {
			let value = String(text ?? "").trim();
			if (thousands) value = value.split(thousands).join("");
			if (delimiter) value = value.split(delimiter).join(".");
			const negative = value.startsWith("-");
			const [integer = "", fraction = ""] = value.replace(/[^0-9.]/g, "").split(".");
			const digits = integer.replace(/^0+(?=\d)/, "");
			if (digits === "" && fraction === "") return null;
			const kept = fraction.slice(0, precision);
			const decimal = kept ? `${digits || "0"}.${kept}` : digits || "0";
			return negative ? `-${decimal}` : decimal;
		};
		const toValue = (text) => {
			const decimal = toDecimal(text);
			if (decimal === null || as !== "cents") return decimal;
			const [integer, fraction = ""] = decimal.replace("-", "").split(".");
			const digits = (integer + fraction.padEnd(precision, "0")).replace(/^0+(?=\d)/, "");
			const cents = Number(digits);
			const sign = decimal.startsWith("-") ? "-" : "";
			return Number.isSafeInteger(cents) ? sign ? -cents : cents : sign + digits;
		};
		const toDisplay = (amount) => {
			if (amount === null || amount === void 0 || amount === "") return "";
			const text = typeof amount === "number" ? Number.isFinite(amount) ? amount.toFixed(Math.min(precision + 2, 20)) : "" : String(amount).trim();
			const match = /^(-?)(\d*)(?:\.(\d*))?$/.exec(text);
			if (!match || match[2] === "" && !match[3]) return "";
			const [, minus, integer, fraction = ""] = match;
			let scaled = BigInt((integer || "0") + fraction.slice(0, precision).padEnd(precision, "0"));
			if ((fraction[precision] ?? "0") >= "5") scaled += 1n;
			const digits = scaled.toString().padStart(precision + 1, "0");
			const whole = precision ? digits.slice(0, -precision) : digits;
			const decimals = precision ? digits.slice(-precision) : "";
			const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousands ?? "");
			return `${minus && scaled !== 0n ? "-" : ""}${grouped}${decimals ? delimiter + decimals : ""}`;
		};
		const fromValue = (value) => {
			if (as !== "cents" || value === null || value === "" || value === void 0) return toDisplay(value);
			const match = /^(-?)(\d+)$/.exec(String(value).trim());
			if (!match) return toDisplay(Number(value) / 10 ** precision);
			const digits = match[2].padStart(precision + 1, "0");
			return toDisplay(`${match[1]}${precision ? `${digits.slice(0, -precision)}.${digits.slice(-precision)}` : digits}`);
		};
		const same = (a, b) => a === null || a === "" || a === void 0 ? b === null || b === "" || b === void 0 : Number(a) === Number(b);
		return { init() {
			const input = this.$el;
			listenWhileConnected(this.$root, input.form, "formdata", (event) => {
				if (input.name && !input.disabled) event.formData.set(input.name, toValue(input.value) ?? "");
			});
			if (!model || !this.$wire) return;
			input.value = fromValue(this.$wire.get(model));
			this.$wire.$watch(model, (value) => {
				if (!same(toValue(input.value), value)) input.value = fromValue(value);
			});
			const live = /\b(live|change)\b/.test(modifiers);
			const event = /\bblur\b/.test(modifiers) ? "blur" : /\bchange\b/.test(modifiers) ? "change" : "input";
			const send = () => {
				const value = toValue(input.value);
				if (!same(value, this.$wire.get(model))) this.$wire.set(model, value, live);
			};
			input.addEventListener(event, live && event === "input" ? debounce(send, 150) : send);
		} };
	}
	//#endregion
	//#region resources/js/components/nav-indicator.js
	var nav_indicator_exports = /* @__PURE__ */ __exportAll({ navIndicator: () => navIndicator });
	function navIndicator({ mode = null } = {}) {
		return {
			_visibilityTimeout: null,
			_frame: null,
			init() {
				this._onMove = this.move.bind(this);
				document.addEventListener("livewire:navigated", this._onMove);
				window.addEventListener("resize", this._onMove);
				const nav = this.findNav(this.$el);
				if (nav) {
					this._resizeObserver = new ResizeObserver(this._onMove);
					this._resizeObserver.observe(nav);
					nav.querySelectorAll("a").forEach((link) => this._resizeObserver.observe(link));
					this._mutationObserver = new MutationObserver(this._onMove);
					this._mutationObserver.observe(nav, {
						subtree: true,
						childList: true,
						attributeFilter: ["data-current"]
					});
				}
				this.$nextTick(() => this.move());
			},
			destroy() {
				document.removeEventListener("livewire:navigated", this._onMove);
				window.removeEventListener("resize", this._onMove);
				this._resizeObserver?.disconnect();
				this._mutationObserver?.disconnect();
				cancelAnimationFrame(this._frame);
				clearTimeout(this._visibilityTimeout);
			},
			findNav(el) {
				let node = el;
				while (node) {
					const sibling = node.previousElementSibling;
					if (sibling?.matches(dataSelector("nav"))) return sibling;
					node = node.parentElement;
				}
				return null;
			},
			move() {
				if (this._frame) return;
				this._frame = requestAnimationFrame(() => {
					this._frame = null;
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
	var NOTIFICATION_EVENT = ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated";
	var listening = /* @__PURE__ */ new Map();
	var warned = false;
	function notification({ channel = null } = {}) {
		return {
			_onNotification: null,
			init() {
				bind(queryAllData(this.$el, "notification-mark-all"), { ["@click"](e) {
					queryAllData(e.currentTarget.closest("[role=tabpanel]") ?? this.$el, "notification-item").forEach((el) => emit(el, eventName("dismiss")));
				} });
				if (!channel || !this.$wire) return;
				if (!window.Echo) {
					if (!warned) console.warn("[tallkit] <tk:notification echo> needs Laravel Echo on the page (window.Echo).");
					warned = true;
					return;
				}
				this._onNotification = () => this.$wire.$refresh();
				window.Echo.private(channel).notification(this._onNotification);
				listening.set(channel, (listening.get(channel) ?? 0) + 1);
			},
			destroy() {
				if (!this._onNotification || !window.Echo) return;
				const subscription = window.Echo.private(channel);
				subscription.stopListeningForNotification ? subscription.stopListeningForNotification(this._onNotification) : subscription.stopListening(NOTIFICATION_EVENT, this._onNotification);
				this._onNotification = null;
				const left = (listening.get(channel) ?? 1) - 1;
				if (left > 0) listening.set(channel, left);
				else {
					listening.delete(channel);
					window.Echo.leave(channel);
				}
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
			_submitted: null,
			_stopLabelFocus: null,
			init() {
				this.inputs = Array.from(this.$root.querySelectorAll("input[data-charset]"));
				_bindableField.init.call(this);
				this.$nextTick(() => {
					this.syncFromModel();
					this.updateModel(false);
				});
				this.$watch("value", (val) => {
					if (val === this.boxesValue()) return;
					this.syncFromModel(val);
					this.updateModel(false);
				});
				this.inputs.forEach((input, index) => {
					bind(input, this.bindings(input, index, this.inputs));
				});
				this._stopLabelFocus = focusOnLabelClick(this.$root, () => this.inputs[0]?.focus());
			},
			destroy() {
				this._stopLabelFocus?.();
			},
			emitOnBox(name, detail, box = this.inputs[0]) {
				emit(box, name, detail);
			},
			bindings(input, index, inputs) {
				return {
					["@focus"]: (e) => this.handleFocus(input, index, inputs, e),
					["@blur"]: () => this.emitOnBox("blurred", {
						input,
						index
					}, input),
					["@paste.prevent"]: (e) => this.handlePaste(e, index, inputs),
					["@input"]: () => this.handleInput(input, index, inputs),
					["@keydown"]: (e) => this.handleKeydown(e, input, index, inputs),
					["@keydown.arrow-left.prevent"]: () => inputs[index - 1]?.select(),
					["@keydown.arrow-right.prevent"]: () => inputs[index + 1]?.select(),
					["@keydown.backspace.prevent"]: () => this.handleBackspace(input, index, inputs)
				};
			},
			handleFocus(input, index, inputs, event = null) {
				if (input.value || inputs.includes(event?.relatedTarget)) {
					input.select();
					this.emitOnBox("focused", {
						input,
						index
					}, input);
					return;
				}
				const firstEmpty = inputs.find((i) => !i.value);
				firstEmpty?.select();
				this.emitOnBox("focused", {
					input: firstEmpty || input,
					index: inputs.indexOf(firstEmpty || input)
				}, firstEmpty || input);
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
				this.emitOnBox("pasted", {
					pasted,
					index
				});
			},
			handleInput(input, index, inputs) {
				if (this._syncing) return;
				const charset = input.dataset.charset;
				const filtered = filterValue(input.value, charset);
				if (filtered.length > 1) spreadValue(filtered, index, inputs);
				else {
					input.value = filtered;
					if (filtered) inputs[index + 1]?.focus();
				}
				this.updateModel();
			},
			handleKeydown(e, input, _index, _inputs) {
				if (e.ctrlKey || e.metaKey || e.altKey) return;
				const charset = input.dataset.charset;
				if (!isValidKey(e.key, charset)) e.preventDefault();
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
				const chars = String(val ?? "").padEnd(this.inputs.length).split("");
				this._syncing = true;
				try {
					this.inputs.forEach((input, i) => {
						const charset = input.dataset.charset;
						setFieldValue(input, filterValue(chars[i] ?? "", charset));
					});
				} finally {
					this._syncing = false;
				}
			},
			boxesValue() {
				return this.inputs.map((i) => i.value || "").join("");
			},
			updateModel(byUser = true) {
				const values = this.inputs.map((i) => i.value || "");
				this.value = values.join("");
				const filled = values.filter(Boolean).length;
				if (!byUser) {
					if (filled < this.inputs.length) this._submitted = null;
					return;
				}
				this.emitOnBox("changed", { value: this.value });
				if (filled === this.inputs.length) {
					this.emitOnBox("completed", { value: this.value });
					if (this.value !== this._submitted) {
						this._submitted = this.value;
						if (submit === "auto") this.$root.closest("form")?.requestSubmit();
						else if (submit && hasLivewire()) window.Livewire.dispatch(submit, this.value);
					}
				} else {
					this._submitted = null;
					this.emitOnBox("incomplete", { value: this.value });
				}
				if (filled === 0) this.emitOnBox("cleared", {});
			}
		};
	}
	function filterValue(value, charset = "numeric") {
		return (value.toUpperCase().match({
			numeric: /[0-9]/g,
			alpha: /[A-Z]/g,
			alphanumeric: /[A-Z0-9]/g
		}[charset]) || []).join("");
	}
	function isValidKey(key, charset) {
		if ([
			"Backspace",
			"Delete",
			"Tab",
			"ArrowLeft",
			"ArrowRight",
			"Home",
			"End",
			"Enter",
			"Escape"
		].includes(key)) return true;
		return filterValue(key, charset).length > 0;
	}
	function spreadValue(value, start, inputs) {
		const chars = value.split("");
		let box = start;
		for (const char of chars) {
			const input = inputs[box];
			if (!input) break;
			const filtered = filterValue(char, input.dataset.charset);
			if (!filtered) continue;
			setFieldValue(input, filtered);
			box++;
		}
		inputs[Math.min(box, inputs.length - 1)]?.focus();
	}
	//#endregion
	//#region resources/js/components/pretty-print-json.js
	var pretty_print_json_exports = /* @__PURE__ */ __exportAll({ prettyPrintJson: () => prettyPrintJson });
	function prettyPrintJson() {
		const _loadable = loadable();
		return {
			..._loadable,
			_stopColorScheme: null,
			init() {
				const syncScheme = (dark) => this.$root.classList.toggle("dark-mode", dark);
				syncScheme(isDarkMode());
				this._stopColorScheme = onColorSchemeChange(syncScheme);
				this.load(() => loadRemoteAssets(() => !!window.prettyPrintJson, "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js", "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css"));
			},
			destroy() {
				_loadable.destroy.call(this);
				this._stopColorScheme?.();
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
				const num = toNumber(n);
				if (num === null) return;
				this.value = clamp(num, 0, 100);
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
		link: [["link"]],
		list: [[
			{ list: "ordered" },
			{ list: "bullet" },
			{ list: "check" }
		]],
		media: [["image", "video"]],
		quote: [["blockquote"]],
		code: [["code-block"]]
	};
	function resolveToolbar(toolbar) {
		const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
		if (!groups.length) return false;
		return [...groups.flatMap((group) => GROUPS$2[group] ?? []), ["clean"]];
	}
	function translateStylesheet(texts = {}) {
		if (!Object.keys(texts).length || document.querySelector(`style${dataSelector("quill-i18n")}`)) return;
		const q = (text) => JSON.stringify(String(text ?? ""));
		const picker = (name, value) => value === null ? `.ql-snow .ql-picker.ql-${name} .ql-picker-label::before, .ql-snow .ql-picker.ql-${name} .ql-picker-item::before` : `.ql-snow .ql-picker.ql-${name} .ql-picker-label[data-value="${value}"]::before, .ql-snow .ql-picker.ql-${name} .ql-picker-item[data-value="${value}"]::before`;
		const rules = [
			[picker("header", null), texts.normal],
			...[
				1,
				2,
				3,
				4,
				5,
				6
			].map((level) => [picker("header", level), texts[`heading${level}`]]),
			[picker("size", null), texts.normal],
			[picker("size", "small"), texts.small],
			[picker("size", "large"), texts.large],
			[picker("size", "huge"), texts.huge],
			[".ql-snow .ql-tooltip::before", texts.visit],
			[".ql-snow .ql-tooltip a.ql-action::after", texts.edit],
			[".ql-snow .ql-tooltip a.ql-remove::before", texts.remove],
			[".ql-snow .ql-tooltip.ql-editing a.ql-action::after", texts.save],
			[".ql-snow .ql-tooltip[data-mode=link]::before", texts.enterLink],
			[".ql-snow .ql-tooltip[data-mode=video]::before", texts.enterVideo],
			[".ql-snow .ql-tooltip[data-mode=formula]::before", texts.enterFormula]
		].filter(([, text]) => text);
		const style = document.createElement("style");
		style.setAttribute(dataKey("quill-i18n"), "");
		style.textContent = rules.map(([selector, text]) => `${selector} { content: ${q(text)}; }`).join("\n");
		document.head.appendChild(style);
	}
	function quill({ options = {}, scripts = [], styles = [], toolbar = null, upload = null, messages = {}, labelledBy = null, i18n = {} } = {}) {
		const _loadable = loadable();
		let editor = null;
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			getEditor() {
				return editor;
			},
			init() {
				this.initField();
				this.load(() => loadRemoteAssets(() => !!window.Quill && !!window.DOMPurify, [
					"https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js",
					"https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js",
					...scripts
				], ["https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css", ...styles]).then(() => this.mount()));
			},
			applyExternalValue(value) {
				this.setHtml(value);
			},
			setHtml(value) {
				editor.setContents(editor.clipboard.convert({ html: window.DOMPurify.sanitize(value ?? "") }), "silent");
			},
			html() {
				return editor.getLength() <= 1 ? "" : editor.root.innerHTML;
			},
			mount() {
				if (this.isDestroyed()) return;
				const { modules = {}, ...rest } = options;
				editor = new window.Quill(this.$refs.root, {
					theme: "snow",
					...rest,
					modules: {
						toolbar: resolveToolbar(toolbar),
						uploader: {
							mimetypes: [
								"image/png",
								"image/jpeg",
								"image/gif",
								"image/webp"
							],
							handler: (range, files) => this.uploadImages(range, files)
						},
						...modules,
						keyboard: {
							...modules.keyboard ?? {},
							bindings: {
								tab: {
									key: "Tab",
									handler: () => true
								},
								...modules.keyboard?.bindings ?? {}
							}
						}
					},
					...this.getDataOptions(this.$refs.root)
				});
				editor.root.setAttribute("role", "textbox");
				editor.root.setAttribute("aria-multiline", "true");
				if (labelledBy) editor.root.setAttribute("aria-labelledby", labelledBy);
				if (this.input.value) this.setHtml(this.input.value);
				editor.on("text-change", () => {
					this.sync(this.html());
				});
				this.followLockState((locked) => editor?.enable(!locked), () => editor?.getModule("toolbar")?.container);
				const buttons = i18n.buttons ?? {};
				(editor.getModule("toolbar")?.container)?.querySelectorAll("button[class*=\"ql-\"], .ql-picker").forEach((control) => {
					const format = Array.from(control.classList).find((name) => name.startsWith("ql-") && name !== "ql-picker")?.slice(3);
					const label = buttons[control.value ? `${format}:${control.value}` : format] ?? buttons[format];
					if (!label) return;
					const target = control.matches(".ql-picker") ? control.querySelector(".ql-picker-label") : control;
					target?.setAttribute("aria-label", label);
					target?.setAttribute("title", label);
				});
				translateStylesheet(i18n.texts);
				emit(this.input, "rendered", { editor }, { later: true });
			},
			async uploadImages(range, files) {
				let index = range?.index ?? editor.getLength();
				for (const file of files) try {
					const url = await uploadEditorFile(file, "image", upload, messages);
					if (!editor) return;
					editor.insertEmbed(index, "image", url, "user");
					editor.setSelection(++index, 0, "silent");
				} catch (e) {
					reportUploadFailed(this.input ?? this.$root, e, file, "image", messages);
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingLockState();
				editor?.off("text-change");
				editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/sidebar.js
	var sidebar_exports = /* @__PURE__ */ __exportAll({ sidebar: () => sidebar });
	function sidebar(name, sticky, stashable) {
		const _toggleable = toggleable();
		const _sticky = stickable();
		return {
			..._toggleable,
			..._sticky,
			init() {
				_toggleable.init.call(this);
				if (sticky) _sticky.init.call(this);
				if (stashable) {
					this.$el.removeAttribute("data-mobile-cloak");
					this.screenLg = window.innerWidth >= 1024;
					bind(this.$el, {
						[":data-stashed"]() {
							return !this.screenLg;
						},
						[":inert"]() {
							return !this.screenLg && !this.isOpened();
						},
						["x-resize.document"]() {
							this.screenLg = window.innerWidth >= 1024;
						},
						[`@${eventName("sidebar-close")}.window`](event) {
							if ((event.detail?.name ?? null) === (name ?? null)) this.close();
						},
						[`@${eventName("sidebar-toggle")}.window`](event) {
							if ((event.detail?.name ?? null) === (name ?? null)) this.toggle();
						},
						["@keydown.escape.window"](event) {
							if (this.isOpened() && !isEscapeHandled(event)) this.close();
						}
					});
					this._dispatchState();
				}
			},
			open() {
				const wasOpened = this.isOpened();
				this.$el.setAttribute("data-show-stashed-sidebar", "");
				_toggleable.open.call(this);
				this._dispatchState();
				if (!wasOpened) this._focusInside();
			},
			close() {
				const wasOpened = this.isOpened();
				this.$el.removeAttribute("data-show-stashed-sidebar");
				_toggleable.close.call(this);
				this._dispatchState();
				if (wasOpened) this._focusBack();
			},
			_focusInside() {
				if (!stashable || this.screenLg) return;
				this._returnFocus = document.activeElement;
				this.$nextTick(() => {
					const first = Array.from(this.$el.querySelectorAll(FOCUSABLE$1)).find(isRendered);
					if (first) {
						first.focus();
						return;
					}
					if (!this.$el.hasAttribute("tabindex")) this.$el.setAttribute("tabindex", "-1");
					this.$el.focus();
				});
			},
			_focusBack() {
				if (!stashable || this.screenLg) return;
				const active = document.activeElement;
				if (active && active !== document.body && !this.$el.contains(active)) return;
				const target = this._returnFocus?.isConnected && this._returnFocus !== document.body ? this._returnFocus : document.querySelector(`${dataSelector("sidebar-toggle", name ?? "")} button, ${dataSelector("sidebar-toggle", name ?? "")}`);
				this._returnFocus = null;
				target?.focus?.();
			},
			_dispatchState() {
				emit(window, eventName("sidebar-state"), {
					name: name ?? null,
					opened: this.opened
				});
			},
			destroy() {
				if (sticky) _sticky.destroy.call(this);
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
				this.input = queryData(this.$root, "control");
				this.$nextTick(() => this.updateRange());
				if (this.$wire) {
					const prop = getWireModelInfo(this.input);
					if (prop) this.$wire.$watch(prop.name, () => this.updateRange());
				}
				if (this.isReadonly()) this.input.setAttribute("aria-readonly", "true");
				onFormReset(this.$root, this.input.form, () => this.updateRange());
				bind(this.input, {
					["@input"]: () => this.updateRange(),
					["@keydown"]: (e) => {
						if (this.isReadonly() && [
							"ArrowLeft",
							"ArrowRight",
							"ArrowUp",
							"ArrowDown",
							"Home",
							"End",
							"PageUp",
							"PageDown"
						].includes(e.key)) e.preventDefault();
					},
					["@pointerdown"]: (e) => {
						if (this.isReadonly()) e.preventDefault();
					}
				});
				bind(queryData(this.$root, "slider-ticks"), { ["@click"]: (e) => {
					const ticks = queryAllData(this.$root, "slider-tick");
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
						const value = toNumber(closestTick.getAttribute("data-value")) ?? toNumber(closestTick.textContent);
						if (value !== null) this.setValue(value);
					}
				} });
			},
			isReadonly() {
				return this.input.hasAttribute("readonly");
			},
			setValue(value) {
				if (this.input.disabled || this.isReadonly()) return;
				setFieldValue(this.input, value);
			},
			updateRange() {
				const min = toNumber(this.input.min, 0);
				const max = toNumber(this.input.max, 100);
				const val = toNumber(this.input.value, min);
				const p = max === min ? 0 : (val - min) * 100 / (max - min);
				this.value = this.input.value;
				if (this.input.id) queryAllData(document, "slider-value", this.input.id).forEach((el) => {
					el.textContent = this.input.value;
				});
				this.input.style.setProperty("--range-percent", `${p}%`);
				this.input.toggleAttribute("data-low", p < 50);
			}
		};
	}
	//#endregion
	//#region resources/js/components/submenu.js
	var submenu_exports = /* @__PURE__ */ __exportAll({ submenu: () => submenu });
	function submenu() {
		const _popover = popover({
			mode: "manual",
			position: "end",
			align: "start",
			margin: -4
		});
		return {
			..._popover,
			_i: null,
			inside: false,
			init() {
				_popover.init.call(this);
			},
			bindPopoverTrigger() {
				_popover.bindPopoverTrigger.call(this);
				const trigger = this.trigger;
				const panel = this.popoverElement;
				const cleanups = [];
				const on = (target, type, handler) => {
					target?.addEventListener(type, handler);
					cleanups.push(() => target?.removeEventListener(type, handler));
				};
				on(panel, "mouseenter", () => {
					this.inside = true;
					this.trigger?.setAttribute("data-active", "");
				});
				on(panel, "mouseleave", () => {
					this.inside = false;
					this.timerToClose();
				});
				on(trigger, "click", () => this.toggle(false));
				on(trigger, "mouseenter", () => {
					clearTimeout(this._i);
					this.open(false);
				});
				on(trigger, "mouseleave", () => this.timerToClose());
				const unbindBase = this._unbindTrigger;
				this._unbindTrigger = () => {
					unbindBase?.();
					cleanups.forEach((cleanup) => cleanup());
				};
			},
			timerToClose() {
				clearTimeout(this._i);
				this._i = setTimeout(() => {
					if (!this.inside) {
						this.close();
						this.trigger?.removeAttribute("data-active");
					}
				}, 100);
			},
			destroy() {
				clearTimeout(this._i);
				_popover.destroy.call(this);
			}
		};
	}
	//#endregion
	//#region resources/js/components/switch-all.js
	var switch_all_exports = /* @__PURE__ */ __exportAll({ switchAll: () => switchAll });
	function switchAll({ group = null } = {}) {
		return checkAll("switch", group ?? "");
	}
	//#endregion
	//#region resources/js/components/tab.js
	var tab_exports = /* @__PURE__ */ __exportAll({ tab: () => tab });
	function tab({ selectFirst = null, orientation = null } = {}) {
		return {
			selected: null,
			own(selector) {
				return Array.from(this.$root.querySelectorAll(selector)).filter((el) => el.closest(dataSelector("tab-group")) === this.$root);
			},
			isOwnTab(target) {
				const tab = target.closest("[role=\"tab\"]");
				return !!tab && tab.closest(dataSelector("tab-group")) === this.$root;
			},
			tabs() {
				return this.own("[role=\"tab\"]").filter((el) => !el.disabled && el.getAttribute("aria-disabled") !== "true");
			},
			init() {
				const selected = this.own("[data-selected]")[0]?.dataset.name;
				const tabs = this.tabs();
				if (selected || selectFirst && tabs.length) this.$nextTick(() => {
					this.selected = selected ?? tabs[0]?.dataset.name;
				});
				const nextKey = orientation === "vertical" ? "arrow-down" : "arrow-right";
				const previousKey = orientation === "vertical" ? "arrow-up" : "arrow-left";
				const step = (forward) => orientation !== "vertical" && isRtl(this.$root) ? -forward : forward;
				bind(this.$root, {
					[`@keydown.${nextKey}`](event) {
						if (!this.isOwnTab(event.target)) return;
						event.preventDefault();
						this.focusTab(step(1), event.target);
					},
					[`@keydown.${previousKey}`](event) {
						if (!this.isOwnTab(event.target)) return;
						event.preventDefault();
						this.focusTab(step(-1), event.target);
					},
					["@keydown.home"](event) {
						if (!this.isOwnTab(event.target)) return;
						event.preventDefault();
						this.focusTab("first", event.target);
					},
					["@keydown.end"](event) {
						if (!this.isOwnTab(event.target)) return;
						event.preventDefault();
						this.focusTab("last", event.target);
					}
				});
			},
			isSelected(name) {
				return this.selected === name;
			},
			select(name) {
				if (this.selected === name) return;
				this.selected = name;
				emit(this.$root, "changed", { name });
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
	function columnVar(side, name) {
		return `--tk-column-${side}-${String(name).replace(/[^\w-]/g, "-")}`;
	}
	function table({ draggable = false, resizable = false, toggleable = false, pinnable = false, persist = null, minColumnWidth = 80, maxMinColumnWidth = 400 } = {}) {
		const tableKey = storageKey("table", persist);
		return {
			boundElements: /* @__PURE__ */ new WeakSet(),
			rows: [],
			selected: [],
			selectedIds: [],
			selectAllChecked: false,
			observer: null,
			columnNames: [],
			columnOrder: [],
			columnVisibility: {},
			columnVisibilityDefault: {},
			columnShown: {},
			columnLocked: [],
			columnResizableNames: [],
			columnPinnable: [],
			columnPinned: {},
			columnPinnedDefault: {},
			columnFixedCount: 0,
			columnsDraggable: false,
			columnsResizable: false,
			columnOrderObserver: null,
			columnResizing: false,
			columnWidths: {},
			columnFixedWidths: {},
			columnMeasured: {},
			columnContentWidth: {},
			columnMinWidth: minColumnWidth,
			columnMaxMinWidth: maxMinColumnWidth || null,
			columnDefaultWidth: 160,
			columnMeasureObserver: null,
			columnMeasureFrame: null,
			columnStickyOffsets: {},
			columnStickyObserver: null,
			get columnAnchored() {
				return this.columnNames.filter((name) => this.columnLocked.includes(name) || this.columnPinned[name]);
			},
			init() {
				this.resetSelection();
				const tbody = this.$root.querySelector("table > tbody");
				if (tbody) {
					this.observer = new MutationObserver(() => this.update());
					this.observer.observe(tbody, {
						childList: true,
						subtree: true
					});
					const inBody = (el, selector) => el.matches(selector) && el.closest("tbody") === tbody;
					const stops = [
						keepAttributesOnMorph((el) => inBody(el, "tr[role=row]"), ["data-state", "data-expanded"]),
						keepAttributesOnMorph((el) => inBody(el, "button[data-role=row-expanded]"), ["aria-expanded", "aria-controls"]),
						keepAttributesOnMorph((el) => inBody(el, "tr[data-role=row-expanded]"), ["id"])
					];
					this._stopMorphHook = () => stops.forEach((stop) => stop());
				}
				this.columnsInit();
			},
			destroy() {
				this.observer?.disconnect();
				this._stopMorphHook?.();
				this.columnOrderObserver?.disconnect();
				this.columnMeasureObserver?.disconnect();
				this.columnStickyObserver?.disconnect();
				cancelAnimationFrame(this.columnMeasureFrame);
			},
			tableElement() {
				return this.$root.querySelector("table");
			},
			update() {
				const tbody = this.$root.querySelector("table > tbody");
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
						bind(unboundExpanded, { ["@click"]: () => {
							row.el.dataset.expanded = row.el.dataset.expanded === "open" ? "close" : "open";
							this._updateRowState(row);
						} });
					}
					return row;
				});
				this.rows.forEach((row) => {
					if (row.selection && row.id !== void 0) setFieldChecked(row.selection, this.selectedIds.includes(row.id));
					this._updateRowState(row);
				});
				this._syncSelect();
			},
			_pickableRows() {
				return this.rows.filter((row) => row.selection && !row.selection.disabled);
			},
			toggleAll() {
				this._pickableRows().forEach((row) => {
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
				if (row.expanded.length) {
					const details = row.el.nextElementSibling?.matches("[data-role=\"row-expanded\"]") ? row.el.nextElementSibling : null;
					if (details && !details.id) details.id = generateId("table-row-details");
					row.expanded.forEach((el) => {
						el.setAttribute("aria-expanded", String(row.el.dataset.expanded === "open"));
						if (details) el.setAttribute("aria-controls", details.id);
					});
				}
			},
			_syncSelect() {
				const shownIds = this.rows.filter((row) => row.selection && row.id !== void 0).map((row) => row.id);
				this.selected = this.rows.filter((row) => row.selection?.checked);
				this.selectedIds = [...this.selectedIds.filter((id) => !shownIds.includes(id)), ...this.selected.map((row) => row.id).filter((id) => id !== void 0)];
				this.selectAllChecked = allChecked(this._pickableRows(), (row) => row.selection.checked);
			},
			columnHeaderRow() {
				return this.$root.querySelector("table thead th[data-column-key]")?.parentElement ?? null;
			},
			columnsInit() {
				const header = this.columnHeaderRow();
				if (header) this.columnsRead(header);
				this.columnsVisibilityInit();
				if (this.columnsDraggable) this.columnsOrderInit();
				if (this.columnsResizable) this.columnsWidthsInit();
				if (this.columnPinnable.length || Object.values(this.columnPinnedDefault).includes(true)) this.columnsPinInit();
				this.$nextTick(() => emit(this.tableElement(), "ready"));
			},
			columnsRead(header) {
				const cells = Array.from(header.children).filter((th) => th.dataset.columnKey !== void 0);
				const names = (attribute) => cells.filter((th) => th.hasAttribute(attribute)).map((th) => th.dataset.columnKey);
				const draggableNames = names("data-column-draggable");
				this.columnNames = cells.map((th) => th.dataset.columnKey);
				this.columnOrder = [...this.columnNames];
				this.columnVisibilityDefault = Object.fromEntries(cells.map((th) => [th.dataset.columnKey, !th.hasAttribute("data-column-hidden")]));
				this.columnVisibility = { ...this.columnVisibilityDefault };
				this.columnLocked = this.columnNames.filter((name) => !draggableNames.includes(name));
				this.columnResizableNames = names("data-column-resizable");
				this.columnPinnable = names("data-column-pinnable");
				this.columnPinnedDefault = Object.fromEntries(cells.map((th) => [th.dataset.columnKey, th.dataset.columnSticky === "left"]));
				this.columnPinned = { ...this.columnPinnedDefault };
				this.columnFixedCount = header.children.length - cells.length;
				this.columnsDraggable = draggable && draggableNames.length > 1;
				this.columnsResizable = resizable && this.columnResizableNames.length > 0;
			},
			isColumnHidden(name) {
				return this.columnVisibility[name] === false;
			},
			columnHide(name) {
				this.columnVisibility[name] = false;
			},
			columnsVisibilityInit() {
				if (toggleable) {
					const stored = getStoredPart(tableKey, "visibility");
					if (stored && typeof stored === "object" && !Array.isArray(stored)) Object.keys(this.columnVisibility).forEach((name) => {
						if (typeof stored[name] === "boolean") this.columnVisibility[name] = stored[name];
					});
				}
				this.columnShown = { ...this.columnVisibility };
				this.$watch("columnVisibility", (value) => {
					if (toggleable) setStoredPart(tableKey, "visibility", value);
					Object.keys(value).forEach((name) => {
						if (value[name] !== this.columnShown[name]) emit(this.tableElement(), "column-toggled", {
							name,
							visible: value[name]
						});
					});
					this.columnShown = { ...value };
					if (this.columnsResizable) this.columnScheduleMeasure();
				});
			},
			columnReset() {
				if (toggleable) this.columnResetVisibility();
				if (this.columnsDraggable) this.columnResetOrder();
				if (this.columnsResizable) this.columnResetWidths();
				if (this.columnPinnable.length) this.columnResetPins();
				this.tableElement()?.parentElement?.scrollTo({ left: 0 });
			},
			columnResetVisibility() {
				this.columnVisibility = { ...this.columnVisibilityDefault };
				this.$nextTick(() => removeStoredPart(tableKey, "visibility"));
			},
			columnsOrderInit() {
				const stored = getStoredPart(tableKey, "order");
				if (Array.isArray(stored)) {
					const known = this.columnOrder;
					const kept = [...new Set(stored)].filter((name) => known.includes(name));
					this.columnOrder = this.columnAnchor([...kept, ...known.filter((name) => !kept.includes(name))]);
				}
				this.$watch("columnOrder", (value) => {
					setStoredPart(tableKey, "order", value);
					this.applyColumnOrder();
					this.columnStickyRefresh();
				});
				this.applyColumnOrder();
				const table = this.tableElement();
				if (table) {
					this.columnOrderObserver = new MutationObserver(() => {
						if (!document.body.classList.contains("sorting")) this.applyColumnOrder();
					});
					this.columnOrderObserver.observe(table, {
						childList: true,
						subtree: true
					});
				}
			},
			columnSortConfig() {
				return {
					onMove: (event) => event.related.hasAttribute("data-column-key") && !this.columnAnchored.includes(event.related.dataset.columnKey),
					onStart: (event) => {
						document.body.classList.add("sorting");
						this.columnDragging(event.item.dataset.columnKey);
						this.columnMeasured = this.columnsResizable && this.columnResizeActive() ? this.columnMeasure().data : {};
					},
					onChange: (event) => {
						const order = this.columnResolveOrder(event.to);
						if (!order) return;
						this.columnHold(event.to, order);
						this.applyColumnOrder(order, {
							skip: event.to,
							animate: true
						});
					},
					onEnd: () => {
						document.body.classList.remove("sorting");
						this.columnDragging(null);
					}
				};
			},
			columnDragging(key) {
				const table = this.tableElement();
				table?.querySelectorAll("[data-column-key]").forEach((cell) => {
					if (cell.matches("col") || cell.closest("table") !== table) return;
					cell.toggleAttribute("data-column-dragging", key !== null && cell.dataset.columnKey === key);
				});
			},
			columnMovable(name) {
				return this.columnVisibility[name] && !this.columnAnchored.includes(name);
			},
			columnResolveOrder(row) {
				const movable = Array.from(row.children).map((cell) => cell.dataset.columnKey).filter((name) => name !== void 0 && this.columnMovable(name));
				if (movable.length !== this.columnOrder.filter((name) => this.columnMovable(name)).length) return null;
				return this.columnOrder.map((name) => this.columnMovable(name) ? movable.shift() : name);
			},
			columnHold(row, order) {
				order.forEach((name, index) => {
					if (this.columnMovable(name)) return;
					const cells = Array.from(row.children).filter((cell) => cell.hasAttribute("data-column-key"));
					const cell = cells.find((candidate) => candidate.dataset.columnKey === name);
					const others = cells.filter((candidate) => candidate !== cell);
					if (!cell || cells.indexOf(cell) === index) return;
					index < others.length ? others[index].before(cell) : others[others.length - 1].after(cell);
				});
			},
			columnSorted(row, key) {
				const cells = Array.from(row.children).filter((cell) => cell.hasAttribute("data-column-key"));
				const item = cells.find((cell) => cell.dataset.columnKey === key);
				const others = cells.filter((cell) => cell !== item);
				if (item && others.length) {
					const index = cells.indexOf(item);
					index > 0 ? others[index - 1].after(item) : others[0].before(item);
				}
				const order = this.columnResolveOrder(row);
				if (!order) return;
				this.columnOrder = order;
				if (this.columnsResizable && this.columnResizeActive()) {
					const flexible = this.columnFlexible();
					this.columnNames.forEach((name) => {
						if (name !== flexible && this.columnResizableNames.includes(name) && this.columnVisibility[name] && this.columnWidths[name] === void 0) this.columnWidths[name] = this.columnMeasured[name] ?? this.columnDefaultWidth;
					});
				}
				this.applyColumnOrder();
			},
			applyColumnOrder(order = this.columnOrder, { skip = null, animate = false } = {}) {
				const table = this.tableElement();
				if (!table) return;
				const rank = (name) => order.indexOf(name);
				const rows = Array.from(table.querySelectorAll("tr, colgroup")).filter((row) => row !== skip && row.closest("table") === table);
				const dataCells = (row) => Array.from(row.children).filter((cell) => cell.hasAttribute("data-column-key"));
				const moving = animate && !prefersReducedMotion() ? rows.flatMap(dataCells).filter((cell) => !cell.hidden && !cell.matches("col")) : [];
				const before = new Map(moving.map((cell) => [cell, cell.offsetLeft]));
				rows.forEach((row) => {
					const cells = dataCells(row);
					const sorted = [...cells].sort((a, b) => rank(a.dataset.columnKey) - rank(b.dataset.columnKey));
					if (sorted.every((cell, index) => cell === cells[index])) return;
					cells.map((cell) => {
						const slot = document.createComment("");
						cell.replaceWith(slot);
						return slot;
					}).forEach((slot, index) => slot.replaceWith(sorted[index]));
				});
				moving.forEach((cell) => {
					const distance = before.get(cell) - cell.offsetLeft;
					if (distance) cell.animate({ transform: [`translateX(${distance}px)`, "none"] }, {
						duration: 150,
						easing: "ease"
					});
				});
			},
			columnAnchor(order) {
				const locked = this.columnNames.filter((name) => this.columnLocked.includes(name));
				const rest = order.filter((name) => !locked.includes(name));
				locked.forEach((name) => rest.splice(this.columnNames.indexOf(name), 0, name));
				return rest;
			},
			columnResetOrder() {
				this.columnOrder = [...this.columnNames];
				this.$nextTick(() => removeStoredPart(tableKey, "order"));
			},
			columnsWidthsInit() {
				const stored = getStoredPart(tableKey, "widths");
				const widths = {};
				if (stored && typeof stored === "object" && !Array.isArray(stored)) this.columnResizableNames.forEach((name) => {
					if (typeof stored[name] === "number" && Number.isFinite(stored[name])) widths[name] = clamp(Math.round(stored[name]), this.columnMinWidth, 1e4);
				});
				if (Object.keys(widths).length) this.$nextTick(() => {
					this.columnFixedWidths = this.columnMeasure().fixed;
					this.columnContentWidth = this.columnMeasureContent();
					this.columnWidths = widths;
				});
				const table = this.tableElement();
				if (table) {
					this.columnMeasureObserver = new MutationObserver(() => this.columnScheduleMeasure());
					this.columnMeasureObserver.observe(table, {
						childList: true,
						subtree: true,
						characterData: true
					});
				}
			},
			columnResizeActive() {
				return Object.keys(this.columnWidths).length > 0;
			},
			columnFlexible() {
				return [...this.columnOrder].reverse().find((name) => this.columnVisibility[name]);
			},
			columnEffective(name, flexible = this.columnFlexible()) {
				if (!this.columnResizableNames.includes(name)) return this.columnFitFor(name);
				return Math.max(this.columnWidths[name] ?? (name === flexible ? 0 : this.columnDefaultWidth), this.columnMinFor(name));
			},
			columnStyle(name) {
				if (!this.columnResizeActive() || name === this.columnFlexible()) return "";
				return `width: ${this.columnEffective(name)}px`;
			},
			columnFixedStyle(role) {
				return this.columnResizeActive() && this.columnFixedWidths[role] ? `width: ${this.columnFixedWidths[role]}px` : "";
			},
			columnResizeStyle() {
				if (!this.columnResizeActive()) return "";
				const flexible = this.columnFlexible();
				return `table-layout: fixed; width: 100%; min-width: ${this.columnNames.filter((name) => this.columnVisibility[name]).reduce((sum, name) => sum + this.columnEffective(name, flexible), 0) + Object.values(this.columnFixedWidths).reduce((sum, width) => sum + width, 0)}px`;
			},
			columnTableStyle() {
				return [this.columnsResizable ? this.columnResizeStyle() : "", this.columnStickyStyle()].filter(Boolean).join("; ");
			},
			columnColspan() {
				return this.columnFixedCount + this.columnNames.filter((name) => this.columnVisibility[name]).length;
			},
			columnMeasure() {
				const table = this.tableElement();
				const row = this.columnHeaderRow();
				const cols = table?.querySelector("colgroup")?.children;
				const measured = {
					fixed: {},
					data: {}
				};
				if (!row || !cols) return measured;
				Array.from(row.children).forEach((th, index) => {
					const col = cols[index];
					if (!col || th.hidden) return;
					const width = Math.round(th.getBoundingClientRect().width);
					if (col.dataset.columnFixed) measured.fixed[col.dataset.columnFixed] = width;
					else measured.data[col.dataset.columnKey] = width;
				});
				return measured;
			},
			columnResizeFreeze() {
				const measured = this.columnMeasure();
				const flexible = this.columnFlexible();
				Object.entries(measured.data).forEach(([name, width]) => {
					if (name !== flexible && this.columnResizableNames.includes(name) && this.columnWidths[name] === void 0) this.columnWidths[name] = width;
				});
				Object.entries(measured.fixed).forEach(([role, width]) => {
					if (this.columnFixedWidths[role] === void 0) this.columnFixedWidths[role] = width;
				});
			},
			columnMeasureContent() {
				const table = this.tableElement();
				const row = this.columnHeaderRow();
				const cols = Array.from(table?.querySelector("colgroup")?.children ?? []);
				const min = {};
				if (!row || !cols.length) return min;
				const aside = Array.from(table.querySelectorAll("tr")).filter((tr) => tr.closest("table") === table && tr !== row && !tr.hasAttribute("data-id"));
				const saved = {
					table: table.style.cssText,
					cols: cols.map((col) => col.style.cssText),
					rows: aside.map((tr) => tr.style.cssText)
				};
				table.style.cssText = "table-layout: auto; width: auto; min-width: 0";
				cols.forEach((col) => col.style.cssText = "");
				aside.forEach((tr) => tr.style.display = "none");
				Array.from(row.children).forEach((th, index) => {
					const name = cols[index]?.dataset.columnKey;
					if (name && !th.hidden) min[name] = Math.ceil(th.getBoundingClientRect().width);
				});
				table.style.cssText = saved.table;
				cols.forEach((col, index) => col.style.cssText = saved.cols[index]);
				aside.forEach((tr, index) => tr.style.cssText = saved.rows[index]);
				return min;
			},
			columnMinFor(name) {
				return clamp(this.columnContentWidth[name] ?? 0, this.columnMinWidth, this.columnMaxMinWidth ?? Infinity);
			},
			columnFitFor(name) {
				return Math.max(this.columnMinWidth, this.columnContentWidth[name] ?? 0);
			},
			columnScheduleMeasure() {
				if (this.columnMeasureFrame) return;
				this.columnMeasureFrame = requestAnimationFrame(() => {
					this.columnMeasureFrame = null;
					if (!this.columnResizeActive() || this.columnResizing || document.body.classList.contains("sorting")) return;
					const min = this.columnMeasureContent();
					if (JSON.stringify(min) !== JSON.stringify(this.columnContentWidth)) this.columnContentWidth = min;
				});
			},
			columnResizeFit(name) {
				this.columnResizeFreeze();
				this.columnContentWidth = this.columnMeasureContent();
				this.columnWidths[name] = this.columnFitFor(name);
				this.columnSaveWidths();
			},
			columnResizeStart(name, event) {
				event.preventDefault();
				const handle = event.currentTarget;
				const startX = event.clientX;
				const startWidth = Math.round(handle.closest("th").getBoundingClientRect().width);
				const direction = isRtl(handle) ? -1 : 1;
				let started = false;
				let min = this.columnMinWidth;
				handle.setPointerCapture(event.pointerId);
				const move = (moveEvent) => {
					if (!started && Math.abs(moveEvent.clientX - startX) >= 3) {
						started = true;
						this.columnResizeFreeze();
						this.columnContentWidth = this.columnMeasureContent();
						min = this.columnMinFor(name);
						this.columnResizing = true;
						document.body.style.cursor = "col-resize";
					}
					if (!started) return;
					this.columnWidths[name] = Math.max(min, Math.round(startWidth + direction * (moveEvent.clientX - startX)));
				};
				const stop = () => {
					handle.removeEventListener("pointermove", move);
					handle.removeEventListener("pointerup", stop);
					handle.removeEventListener("pointercancel", stop);
					document.body.style.cursor = "";
					this.columnResizing = false;
					if (started) {
						this.columnSaveWidths();
						this.columnScheduleMeasure();
					}
				};
				handle.addEventListener("pointermove", move);
				handle.addEventListener("pointerup", stop);
				handle.addEventListener("pointercancel", stop);
			},
			columnResizeKey(name, event) {
				const handle = event.currentTarget;
				const [narrower, wider] = isRtl(handle) ? ["ArrowRight", "ArrowLeft"] : ["ArrowLeft", "ArrowRight"];
				if (event.key === "Enter") {
					event.preventDefault();
					this.columnResizeFit(name);
					return;
				}
				if (![
					narrower,
					wider,
					"Home"
				].includes(event.key)) return;
				event.preventDefault();
				this.columnResizeFreeze();
				this.columnContentWidth = this.columnMeasureContent();
				const min = this.columnMinFor(name);
				const width = Math.round(handle.closest("th").getBoundingClientRect().width);
				const step = (event.shiftKey ? 50 : 10) * (event.key === wider ? 1 : -1);
				this.columnWidths[name] = event.key === "Home" ? min : Math.max(min, width + step);
				this.columnSaveWidths();
				this.columnScheduleMeasure();
			},
			columnResizeValue(name, handle) {
				this.columnWidths[name];
				return Math.round(handle.closest("th")?.getBoundingClientRect().width ?? 0) || null;
			},
			columnSaveWidths() {
				setStoredPart(tableKey, "widths", this.columnWidths);
			},
			columnResetWidths() {
				this.columnWidths = {};
				this.columnFixedWidths = {};
				removeStoredPart(tableKey, "widths");
			},
			isColumnPinned(name) {
				return this.columnPinned[name] === true;
			},
			columnTogglePin(name) {
				if (!this.columnPinnable.includes(name)) return;
				this.columnPinned[name] = !this.columnPinned[name];
			},
			columnsPinInit() {
				if (pinnable) {
					const stored = getStoredPart(tableKey, "pinned");
					if (stored && typeof stored === "object" && !Array.isArray(stored)) this.columnPinnable.forEach((name) => {
						if (typeof stored[name] === "boolean") this.columnPinned[name] = stored[name];
					});
				}
				this.$watch("columnPinned", (value) => {
					if (pinnable) setStoredPart(tableKey, "pinned", value);
					this.$nextTick(() => this.columnStickyRefresh());
				});
				this.$nextTick(() => this.columnStickyObserve());
			},
			columnResetPins() {
				this.columnPinned = { ...this.columnPinnedDefault };
				this.$nextTick(() => removeStoredPart(tableKey, "pinned"));
			},
			columnStickyStyle() {
				return Object.entries(this.columnStickyOffsets).map(([name, left]) => `${columnVar("left", name)}: ${left}px`).join("; ");
			},
			columnStickyRefresh() {
				const row = this.columnHeaderRow();
				if (!row) return;
				let left = 0;
				const offsets = {};
				Array.from(row.children).forEach((th) => {
					const name = th.dataset.columnKey;
					if (name === void 0 || !this.columnPinned[name]) return;
					offsets[name] = Math.round(left * 100) / 100;
					if (!th.hidden) left += th.getBoundingClientRect().width;
				});
				if (JSON.stringify(offsets) !== JSON.stringify(this.columnStickyOffsets)) this.columnStickyOffsets = offsets;
			},
			columnStickyObserve() {
				const row = this.columnHeaderRow();
				this.columnStickyObserver?.disconnect();
				if (!row) return;
				this.columnStickyObserver = new ResizeObserver(() => this.columnStickyRefresh());
				Array.from(row.children).filter((th) => this.columnPinnable.includes(th.dataset.columnKey) || this.columnPinnedDefault[th.dataset.columnKey]).forEach((th) => this.columnStickyObserver.observe(th));
				this.columnStickyRefresh();
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
				const minRows = toNumber(el.getAttribute("rows"));
				const autoRows = minRows && minRows > 0 && maxRows && maxRows > minRows;
				const sync = () => {
					if (counter) this.length = el.value.length;
					if (autoRows) this.resizeRows(el, minRows, maxRows);
				};
				sync();
				bind(el, { ["@input"]: sync });
				this._stopCommits = onLivewireCommit(({ component, succeed }) => {
					if (!component?.el?.contains(el)) return;
					succeed(() => this.$nextTick(sync));
				});
			},
			destroy() {
				this._stopCommits?.();
			},
			resizeRows(el, minRows, maxRows) {
				el.rows = minRows;
				const style = getComputedStyle(el);
				const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
				const lineHeight = toNumber(style.lineHeight) || toNumber(style.fontSize, 0) * 1.2 || 16;
				el.rows = clamp(Math.round((el.scrollHeight - padding) / lineHeight), minRows, maxRows);
			}
		};
	}
	//#endregion
	//#region resources/js/components/time-picker.js
	var time_picker_exports = /* @__PURE__ */ __exportAll({ timePicker: () => timePicker });
	var FORMATS = ["12-hour", "24-hour"];
	var MINUTES_IN_DAY = 1440;
	function timePicker({ value = null, multiple = null, format = null, locale = null, interval = null, min = null, max = null, unavailable = null, openTo = null, trigger = null } = {}) {
		if (format && !FORMATS.includes(format)) {
			console.warn(`[tallkit] tk:time-picker received an invalid "format" ("${format}"). Expected one of: ${FORMATS.join(", ")}. Falling back to the locale default.`);
			format = null;
		}
		interval = Math.max(1, toNumber(interval) || 30);
		min = min ? parseTypedTime(min) : null;
		max = max ? parseTypedTime(max) : null;
		openTo = openTo ? parseTypedTime(openTo) : null;
		multiple = Boolean(multiple);
		const unavailableRanges = parseCommaList(unavailable).map((token) => {
			if (token.includes("-")) {
				const [start, end] = token.split("-").map((part) => parseTypedTime(part));
				return start && end ? [start, end] : null;
			}
			const single = parseTypedTime(token);
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
			locale: resolveLocale(locale),
			init() {
				_popover.init.call(this);
				this.value = this.parseInitialValue(value);
				_bindableField.init.call(this);
				this.syncTyped();
				this.$watch("value", () => {
					if (!this.typing) this.syncTyped();
				});
				this.$watch("typed", () => {
					if (!this.typing) return;
					this.commitTyped();
				});
			},
			isDisabled() {
				return !!queryData(this.$root, "control")?.disabled;
			},
			open(focus = true) {
				if (this.isDisabled()) return;
				_popover.open.call(this, focus);
			},
			onOpen() {
				_popover.onOpen.call(this);
				this.$nextTick(() => this.scrollToSelected());
			},
			parseInitialValue(raw) {
				if (multiple) {
					if (!raw) return [];
					return (Array.isArray(raw) ? raw : parseCommaList(raw)).map((v) => parseTypedTime(v)).filter(Boolean);
				}
				if (!raw) return null;
				if (Array.isArray(raw)) raw = raw[0];
				return parseTypedTime(raw);
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
				if (this.isDisabled()) return;
				if (this.isTimeDisabled(hhmm)) return;
				if (multiple) {
					this.toggleMultiple(hhmm);
					this.dispatchPicked(this.value);
					return;
				}
				this.value = this.value === hhmm ? null : hhmm;
				this.dispatchPicked(this.value);
				this.close();
			},
			toggleMultiple(hhmm) {
				const current = this.value ?? [];
				this.value = current.includes(hhmm) ? current.filter((v) => v !== hhmm) : [...current, hhmm].sort();
			},
			usesHour12() {
				if (format === "12-hour") return true;
				if (format === "24-hour") return false;
				try {
					return !!new Intl.DateTimeFormat(this.locale, { hour: "numeric" }).resolvedOptions().hour12;
				} catch {
					return false;
				}
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
				return trigger === "input" && !multiple;
			},
			maskPattern() {
				return this.usesHour12() ? "99:99 aa" : "99:99";
			},
			editable(hhmm) {
				if (!hhmm || !this.usesHour12()) return hhmm ?? "";
				const [h, m] = hhmm.split(":").map(Number);
				return `${padDatePart(h % 12 || 12)}:${padDatePart(m)} ${h < 12 ? "AM" : "PM"}`;
			},
			syncTyped() {
				if (!this.typable()) return;
				this.typed = this.editable(this.value);
			},
			commitTyped() {
				if (this.isDisabled()) return;
				if (!this.typable()) return;
				if ((this.typed.match(/\d/g) ?? []).length < 4) return;
				const parsed = parseTypedTime(this.typed);
				if (parsed && !this.isTimeDisabled(parsed) && parsed !== this.value) {
					this.value = parsed;
					this.dispatchPicked(parsed);
				}
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
				const target = timeToMinutes(hhmm);
				const values = this.slots();
				return values.reduce((closest, slot) => Math.abs(timeToMinutes(slot) - target) < Math.abs(timeToMinutes(closest) - target) ? slot : closest, values[0]);
			},
			moveSlotFocus(event) {
				if (![
					"ArrowDown",
					"ArrowUp",
					"Home",
					"End"
				].includes(event.key)) return;
				const options = [...event.currentTarget.querySelectorAll("[role=option]")].filter((option) => !option.disabled);
				if (!options.length) return;
				event.preventDefault();
				const index = options.indexOf(document.activeElement);
				const next = {
					ArrowDown: Math.min(index + 1, options.length - 1),
					ArrowUp: Math.max(index - 1, 0),
					Home: 0,
					End: options.length - 1
				}[event.key];
				options[index === -1 ? 0 : next].focus();
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
	var isDark = () => document.documentElement.classList.contains("dark");
	function resolveConfig(toolbar) {
		const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
		if (!groups.length) return {
			plugins: "",
			toolbar: false
		};
		return {
			plugins: groups.map((group) => GROUPS$1[group]?.plugins).filter(Boolean).join(" "),
			toolbar: ["undo redo", ...groups.map((group) => GROUPS$1[group]?.toolbar).filter(Boolean)].join(" | ")
		};
	}
	var LANGUAGES = "https://cdn.jsdelivr.net/npm/tinymce-i18n@26/langs8";
	async function loadLanguage(locale) {
		if (!locale) return null;
		const [language, region] = String(locale).replace("_", "-").split("-");
		if (language.toLowerCase() === "en") return null;
		const candidates = [...new Set([
			region ? `${language.toLowerCase()}-${region.toUpperCase()}` : null,
			language.toLowerCase(),
			`${language.toLowerCase()}-${language.toUpperCase()}`
		].filter(Boolean))];
		for (const name of candidates) try {
			await loadScript(`${LANGUAGES}/${name}.js`);
			return name;
		} catch {}
		return null;
	}
	function tinymce({ options = {}, scripts = [], toolbar = null, upload = null, messages = {}, title = null, locale = null } = {}) {
		const _loadable = loadable();
		let editor = null;
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			_appearanceObserver: null,
			getEditor() {
				return editor;
			},
			init() {
				this.initField();
				this.load(() => loadRemoteAssets(() => !!window.tinymce, ["https://cdn.jsdelivr.net/npm/tinymce@8/tinymce.min.js", ...scripts]).then(() => this.mount()));
				let dark = isDark();
				this._appearanceObserver = new MutationObserver(() => {
					if (isDark() === dark) return;
					dark = isDark();
					if (!editor || this.isDestroyed()) return;
					editor.remove();
					editor = null;
					this.mount().catch((e) => this.fail(e));
				});
				this._appearanceObserver.observe(document.documentElement, {
					attributes: true,
					attributeFilter: ["class"]
				});
			},
			applyExternalValue(value) {
				editor.setContent(value ?? "");
			},
			async mount() {
				if (this.isDestroyed()) return;
				const { plugins, toolbar: buttons } = resolveConfig(toolbar);
				const dark = isDark();
				const language = await loadLanguage(locale);
				const [created] = await window.tinymce.init({
					...language ? { language } : {},
					target: this.input,
					license_key: "gpl",
					menubar: false,
					plugins,
					toolbar: buttons,
					promotion: false,
					branding: false,
					skin: dark ? "oxide-dark" : "oxide",
					content_css: dark ? "dark" : "default",
					...upload?.url ? { images_upload_handler: (blobInfo) => this.uploadImage(blobInfo) } : {},
					convert_urls: false,
					...title ? { iframe_aria_text: title } : {},
					setup: (instance) => {
						instance.on("change input undo redo", () => {
							this.sync(instance.getContent());
						});
					},
					...options,
					...this.getDataOptions(this.input)
				});
				if (this.isDestroyed()) {
					created?.remove();
					return;
				}
				editor = created;
				editor.iframeElement?.setAttribute("title", title || editor.options?.get?.("iframe_aria_text") || "Rich Text Area");
				this.followLockState((locked) => editor?.mode.set(locked ? "readonly" : "design"));
				emit(this.input, "rendered", { editor }, { later: true });
			},
			async uploadImage(blobInfo) {
				const blob = blobInfo.blob();
				const file = new File([blob], blobInfo.filename(), { type: blob.type });
				try {
					return await uploadEditorFile(file, "image", upload, messages);
				} catch (e) {
					throw {
						message: reportUploadFailed(this.input ?? this.$root, e, file, "image", messages, false),
						remove: true
					};
				}
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingLockState();
				this._appearanceObserver?.disconnect();
				editor?.remove();
				editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/tiptap.js
	var tiptap_exports = /* @__PURE__ */ __exportAll({ tiptap: () => tiptap });
	var DEFAULT_TIPTAP_VERSION = "3.30.3";
	var esm = (pkg, version) => {
		return `https://esm.sh/${pkg}@${version}?deps=${["@tiptap/core", "@tiptap/pm"].filter((dep) => dep !== pkg).map((dep) => `${dep}@${version}`).join(",")}`;
	};
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
	function tiptap({ options = {}, scripts = [], toolbar = null, upload = {}, version = null, messages = {}, labelledBy = null } = {}) {
		const _loadable = loadable();
		let resolvedVersion = version || DEFAULT_TIPTAP_VERSION;
		let editor = null;
		const message = (key, replace = {}) => Object.entries(replace).reduce((text, [name, value]) => text.replaceAll(`:${name}`, value), messages[key] ?? { linkUrl: "Link URL" }[key]);
		return {
			..._loadable,
			...dataOptions(),
			...editorField(),
			groups: [],
			extraModules: [],
			tick: 0,
			getEditor() {
				return editor;
			},
			init() {
				this.initField();
				const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER;
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
					const url = window.prompt(message("linkUrl"), editor.getAttributes("link").href ?? "");
					if (url === null) return;
					url.trim() ? chain.setLink({ href: url.trim() }) : chain.unsetLink();
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
			handleUpload(file, type) {
				return uploadEditorFile(file, type, upload, messages);
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
					this.uploadFailed(e, file, "image");
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
					this.uploadFailed(e, file, "video");
				}
			},
			uploadFailed(error, file, type) {
				reportUploadFailed(this.input ?? this.$root, error, file, type, messages);
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
				if (this.isDestroyed()) return;
				editor = new EditorClass({
					element: this.$refs.root,
					extensions,
					content: this.input.value ?? "",
					editorProps: { attributes: {
						class: "tiptap-content",
						[dataKey("control")]: "",
						role: "textbox",
						"aria-multiline": "true",
						...labelledBy ? { "aria-labelledby": labelledBy } : {}
					} },
					onUpdate: ({ editor }) => {
						this.sync(editor.isEmpty ? "" : editor.getHTML());
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
				this.followLockState((locked) => {
					if (editor && editor.isEditable === locked) editor.setEditable(!locked);
				}, () => queryData(this.$root, "editor-toolbar"));
				this.initToolbarKeys();
				emit(this.input, "rendered", { editor }, { later: true });
			},
			pressedState(command) {
				if ([
					"link",
					"image",
					"video",
					"table"
				].includes(command)) return null;
				return this.isActive(command) ? "true" : "false";
			},
			initToolbarKeys() {
				const toolbar = queryData(this.$root, "editor-toolbar");
				if (!toolbar) return;
				const items = () => Array.from(toolbar.querySelectorAll("button")).filter((el) => !el.closest("[popover]") && !el.disabled && isRendered(el));
				const makeCurrent = (current) => {
					items().forEach((el) => el.setAttribute("tabindex", el === current ? "0" : "-1"));
				};
				makeCurrent(items()[0]);
				toolbar.addEventListener("focusin", (event) => {
					if (items().includes(event.target)) makeCurrent(event.target);
				});
				toolbar.addEventListener("keydown", (event) => {
					const list = items();
					const index = list.indexOf(event.target);
					if (index === -1) return;
					const rtl = isRtl(toolbar);
					const step = {
						ArrowRight: rtl ? -1 : 1,
						ArrowLeft: rtl ? 1 : -1
					}[event.key];
					let next = null;
					if (step) next = list[(index + step + list.length) % list.length];
					else if (event.key === "Home") next = list[0];
					else if (event.key === "End") next = list.at(-1);
					if (!next) return;
					event.preventDefault();
					makeCurrent(next);
					next.focus();
				});
			},
			destroy() {
				_loadable.destroy.call(this);
				this.stopFollowingLockState();
				editor?.destroy();
				editor = null;
			}
		};
	}
	//#endregion
	//#region resources/js/components/toast.js
	var toast_exports = /* @__PURE__ */ __exportAll({ toast: () => toast });
	function toast(flashed = [], texts = {}) {
		texts = {
			loading: "Loading...",
			success: "Success!",
			error: "Error!",
			...texts
		};
		return {
			toasts: [],
			isPageVisible: false,
			isUserActive: false,
			idleTimeout: null,
			idleDelay: 0,
			_listeners: [],
			init() {
				const active = window.__tallkitToastContainer;
				if (active && active !== this.$el && active.isConnected) {
					console.warn("[tallkit] There is already a <tk:toast> on the page: this one stays inert.");
					flashed.forEach((detail) => sendToastEvent(eventName("toast"), detail));
					return;
				}
				window.__tallkitToastContainer = this.$el;
				bind(this.$el, {
					[`@${eventName("toast")}.document`](e) {
						this.addToast(e.detail);
					},
					[`@${eventName("toast-close")}.document`](e) {
						this.removeToast(e.detail.id);
					}
				});
				window.__tallkitToastReady = true;
				(window.__tallkitToastQueue ?? []).forEach(({ event, detail }) => {
					if (event === eventName("toast")) this.addToast(detail);
					if (event === eventName("toast-close")) this.removeToast(detail.id);
				});
				window.__tallkitToastQueue = [];
				flashed.forEach((detail) => this.addToast(detail));
				this.initAttentionListeners();
			},
			initAttentionListeners() {
				this.isPageVisible = !document.hidden;
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
				if (window.__tallkitToastContainer === this.$el) {
					window.__tallkitToastContainer = null;
					window.__tallkitToastReady = false;
				}
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
				const currentToast = props.id ? this.toasts.find((t) => t.id === props.id) : null;
				if (currentToast) return this.updateToast(currentToast.id, props);
				const toast = createToast(props, position, this);
				this.toasts.push(toast);
				this.announce(toast);
				this.$nextTick(() => {
					toast.visible = true;
					toast.start();
					this.syncAttention();
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
					"swipe",
					"invert",
					"actions"
				];
				for (const key in data) {
					if (!allowed.includes(key) || key === "duration") continue;
					toast[key] = key === "actions" ? normalizeActions(data[key]) : data[key];
				}
				if ("title" in data || "message" in data) toast.html = data.html === true;
				toast.resetSwipe();
				if ("title" in data || "message" in data || "type" in data) this.announce(toast);
				if (data.duration !== void 0) {
					toast.duration = resolveDuration(data.duration, toast.title, toast.message, toast.actions?.length > 0);
					toast.restart();
				}
				return toast;
			},
			announce(toast) {
				const region = toast.type === "error" ? this.$refs.assertiveRegion : this.$refs.politeRegion;
				if (!region) return;
				const text = (value) => toast.html ? new DOMParser().parseFromString(String(value ?? ""), "text/html").body.textContent : String(value ?? "");
				const words = [text(toast.title), text(toast.message)].map((part) => part.trim()).filter(Boolean).join(". ");
				if (!words) return;
				region.textContent = "";
				clearTimeout(region._tallkitAnnounce);
				region._tallkitAnnounce = setTimeout(() => {
					region.textContent = words;
				}, 100);
			},
			showContent(el, value, html) {
				if (html) el.innerHTML = value ?? "";
				else el.textContent = value ?? "";
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
					duration: false,
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
					existing.resetSwipe();
					existing.restart();
					this.announce(existing);
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
				const toast = this.loading(messages.loading ?? texts.loading);
				const resolveMessage = (msg, data) => typeof msg === "function" ? msg(data) : msg;
				promise.then((data) => {
					if (!this.toasts.find((t) => t.id === toast.id)) return;
					this.updateToast(toast.id, {
						title: resolveMessage(messages.success, data) ?? texts.success,
						type: "success",
						duration: getDynamicDuration(resolveMessage(messages.success, data)),
						progress: true,
						swipe: true
					});
				}).catch((error) => {
					if (!this.toasts.find((t) => t.id === toast.id)) return;
					this.updateToast(toast.id, {
						title: resolveMessage(messages.error, error) ?? texts.error,
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
	function createToast(props, position, manager) {
		const duration = resolveDuration(props.duration, props.title, props.message, props.actions?.length > 0);
		return window.Alpine.reactive({
			createdAt: Date.now(),
			...props,
			id: props.id ?? generateId("toast"),
			duration,
			position,
			html: props.html === true,
			attentionAware: props.attentionAware ?? true,
			progress: props.progress ?? true,
			pauseOnHover: props.pauseOnHover ?? true,
			swipe: props.swipe ?? true,
			actions: normalizeActions(props.actions),
			visible: false,
			...toastCountdown(duration, manager),
			...toastSwipe(manager)
		});
	}
	var PAUSED_BY = {
		hover: "pausedByHover",
		focus: "pausedByFocus",
		attention: "pausedByAttention"
	};
	function toastCountdown(duration, manager) {
		return {
			progressValue: 1,
			startTime: 0,
			total: duration,
			elapsedBeforePause: 0,
			raf: null,
			pausedAt: null,
			pausedByHover: false,
			pausedByFocus: false,
			pausedByAttention: false,
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
				this[PAUSED_BY[reason] ?? PAUSED_BY.attention] = true;
				if (this.pausedAt) return;
				this.pausedAt = performance.now();
				this.elapsedBeforePause += this.pausedAt - this.startTime;
				this.stop();
			},
			resume(reason = "attention") {
				this[PAUSED_BY[reason] ?? PAUSED_BY.attention] = false;
				if (this.pausedByHover || this.pausedByFocus || this.pausedByAttention) return;
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
			restart() {
				this.stop();
				this.pausedAt = null;
				this.elapsedBeforePause = 0;
				this.total = this.duration;
				this.progressValue = 1;
				if (!this.visible || !this.duration) return;
				this.start();
				if (this.pausedByHover) this.pause("hover");
				if (this.pausedByFocus) this.pause("focus");
				if (this.pausedByAttention) this.pause("attention");
			}
		};
	}
	function toastSwipe(manager) {
		return {
			swiping: false,
			startX: 0,
			startY: 0,
			currentX: 0,
			currentY: 0,
			lockDirection: null,
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
				if (!this.lockDirection && Math.max(Math.abs(this.currentX), Math.abs(this.currentY)) > 4) {
					this.lockDirection = Math.abs(this.currentX) > Math.abs(this.currentY) ? "x" : "y";
					if (this.lockDirection === "x") e.currentTarget.setPointerCapture?.(e.pointerId);
				}
				if (this.lockDirection === "x") e.preventDefault();
			},
			onPointerCancel() {
				this.resetSwipe();
			},
			onPointerUp(e) {
				if (!this.swipe) return;
				this.swiping = false;
				if (this.lockDirection !== "x") {
					this.resetSwipe();
					return;
				}
				const threshold = e.currentTarget.offsetWidth * .4;
				if (Math.abs(this.currentX) > threshold) manager.removeToast(this.id);
				else this.resetSwipe();
			},
			resetSwipe() {
				this.swiping = false;
				this.currentX = 0;
				this.currentY = 0;
				this.lockDirection = null;
			}
		};
	}
	function normalizeActions(actions) {
		return (actions ?? []).map((action) => ({
			loading: false,
			...action,
			href: safeUrl(action.href),
			run() {
				let result;
				if (this.onClick) result = this.onClick();
				else if (this.method) {
					const component = window.Livewire?.find(this.component);
					if (!component) {
						console.warn(`[tallkit] Toast action "${this.label}" could not find Livewire component "${this.component}" to call "${this.method}".`, this);
						return;
					}
					result = component.call(this.method, ...normalizeParams(this.params));
				} else if (this.event) {
					window.Livewire?.dispatch(this.event, this.params ?? {});
					return;
				} else {
					console.warn(`[tallkit] Toast action "${this.label}" has no onClick, method, event, or href handler.`, this);
					return;
				}
				if (result instanceof Promise) {
					this.loading = true;
					result.finally(() => {
						this.loading = false;
					});
				}
			}
		}));
	}
	function normalizeParams(params) {
		if (params == null) return [];
		return Array.isArray(params) ? params : Object.values(params);
	}
	function resolveDuration(duration, title, message, hasActions = false) {
		if (duration === false) return null;
		if (duration === true) return getDynamicDuration(title, message);
		if (duration == null) return hasActions ? null : getDynamicDuration(title, message);
		return toMilliseconds(duration, getDynamicDuration(title, message));
	}
	function normalizePosition(position) {
		position ??= "bottom-right";
		if (position === "top") return "top-right";
		if (position === "bottom") return "bottom-right";
		return position;
	}
	function getDynamicDuration(title = "", message = "") {
		const text = `${title} ${message}`.trim();
		const min = 3e3;
		const max = 9e3;
		let time = 1e3 + ((title?.length ?? 0) * 1.2 + (message?.length ?? 0) * 1.6) / 16 * 1e3;
		const lines = text.split("\n").length;
		time += lines * 300;
		return clamp(time, min, max);
	}
	//#endregion
	//#region resources/js/components/toggle.js
	var toggle_exports = /* @__PURE__ */ __exportAll({ toggle: () => toggle });
	function toggle({ action = null, model = null, delay = null, minDuration = null } = {}) {
		return {
			blocking: false,
			busy: false,
			busyShownAt: null,
			delayTimeout: null,
			minDurationTimeout: null,
			originalDisabled: false,
			livewireCommitCleanup: null,
			init() {
				const input = this.$root.querySelector("input[type=\"checkbox\"]");
				this.originalDisabled = !!input?.disabled;
				if (hasLivewire()) this.watchLivewireCommits();
				bind(input, {
					[":aria-busy"]: () => this.blocking ? "true" : null,
					[":aria-disabled"]: () => this.blocking || this.originalDisabled ? "true" : null,
					["@click"](event) {
						if (this.blocking) event.preventDefault();
					}
				});
			},
			watchLivewireCommits() {
				this.livewireCommitCleanup = onLivewireCommit(({ component, commit, succeed, fail }) => {
					if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return;
					const matchesAction = action && commit?.calls?.some((call) => call.method === action);
					const matchesModel = model && Object.prototype.hasOwnProperty.call(commit?.updates ?? {}, model);
					if (!matchesAction && !matchesModel) return;
					clearTimeout(this.delayTimeout);
					clearTimeout(this.minDurationTimeout);
					this.blocking = true;
					this.delayTimeout = startTimeout(() => {
						this.busy = true;
						this.busyShownAt = Date.now();
					}, delay, 150);
					const stop = () => {
						this.blocking = false;
						clearTimeout(this.delayTimeout);
						if (!this.busy) return;
						const remaining = toMilliseconds(minDuration, 700) - (Date.now() - this.busyShownAt);
						if (remaining > 0) this.minDurationTimeout = startTimeout(() => {
							this.busy = false;
						}, remaining);
						else this.busy = false;
					};
					succeed(stop);
					fail(stop);
				});
			},
			destroy() {
				clearTimeout(this.delayTimeout);
				clearTimeout(this.minDurationTimeout);
				this.livewireCommitCleanup?.();
			}
		};
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
	function upload({ wireModel = false, multiple = false, droppable = true, maxSize = null, maxSizes = {}, maxFiles = null, sortable = false, invalid = false, files = [], tooLargeMessage = "The file may not be larger than :size.", invalidTypeMessage = "This file type is not allowed.", tooManyFilesMessage = "Too many files selected.", uploadFailedMessage = "The file could not be uploaded.", movedMessage = "Moved to position :position of :total.", sortHint = "Drag, or press Alt and an arrow key, to move it.", sortHintId = null, previewName = null, fileTypes = {} } = {}) {
		const fromServer = (file) => ({
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
			clientError: false,
			tmpFilename: file.tmpFilename ?? null,
			previewLoaded: false,
			previewFailed: false
		});
		return {
			dragOver: false,
			dragIndex: null,
			dragOverIndex: null,
			sortable,
			previewId: null,
			announcement: "",
			sortHint,
			sortHintId,
			files: files.map(fromServer),
			queue: [],
			batchIds: [],
			activeId: null,
			needsValueSync: false,
			_stopCommits: null,
			wired() {
				return !!(this.$wire && wireModel);
			},
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
				if (!this.batchIds.length) return 100;
				const total = this.batchIds.reduce((sum, id) => {
					const file = this.find(id);
					if (!file || file.status === "done" || file.status === "error" || file.status === "cancelled") return sum + 100;
					return sum + file.progress;
				}, 0);
				return Math.round(total / this.batchIds.length);
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
					const picked = Array.from(target.files ?? []);
					if (this.wired()) target.value = "";
					this.addFiles(picked);
				} });
				if (this.wired()) this._stopCommits = onLivewireCommit(({ component, succeed }) => {
					if (!component?.el?.contains(this.$root)) return;
					succeed(() => this.$nextTick(() => this.syncFromServer()));
				});
				if (!this.wired() && this.$refs.fileInput?.form) {
					const initial = this.files.map((file) => ({ ...file }));
					onFormReset(this.$root, this.$refs.fileInput.form, () => {
						this.files.forEach((file) => this.revoke(file));
						this.queue = [];
						this.batchIds = [];
						this.activeId = null;
						this.previewId = null;
						this.files = initial.map((file) => ({ ...file }));
						this.syncInput();
					});
				}
				if (!droppable) return;
				bind(queryData(this.$root, "upload-dropzone"), {
					["@dragover.prevent"]() {
						if (this.dragIndex !== null) return;
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
				this._stopCommits?.();
			},
			syncInput() {
				if (this.wired() || !this.$refs.fileInput) return;
				try {
					const transfer = new DataTransfer();
					this.files.filter((file) => file.raw && file.status !== "error").forEach((file) => transfer.items.add(file.raw));
					this.$refs.fileInput.files = transfer.files;
				} catch {}
			},
			syncFromServer() {
				if (!this.wired() || this.activeId || this.hasPendingUploads()) return;
				const server = [].concat(this.$wire.get(wireModel) ?? []).filter((value) => value !== null && value !== "");
				const shown = this.files.filter((file) => file.status === "done");
				const holds = (file, value) => file.value !== null && file.value === value || !!file.tmpFilename && typeof value === "string" && value.endsWith(`:${file.tmpFilename}`);
				if (server.length === shown.length && server.every((value) => shown.some((file) => holds(file, value)))) return;
				let state = this.$root.nextElementSibling;
				while (state && !state.matches(dataSelector("upload-state"))) state = state.nextElementSibling;
				if (!state) return;
				try {
					const next = JSON.parse(state.textContent || "[]").map((file) => {
						const entry = fromServer(file);
						const before = entry.tmpFilename && this.files.find((shown) => shown.tmpFilename === entry.tmpFilename);
						if (before && !entry.url && before.url) {
							entry.url = before.url;
							entry.raw = before.raw;
							before.url = null;
						}
						return entry;
					});
					this.files.forEach((file) => this.revoke(file));
					this.files = next;
				} catch {}
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
					this.$dispatch(eventName("modal-show"), { name: previewName });
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
					this.files.forEach((file) => {
						this.revoke(file);
						this.detachFromWire(file);
					});
					this.files = [];
					this.queue = [];
				}
				const incoming = Array.from(fileList);
				const kept = this.files.filter((file) => file.status !== "error" && file.status !== "cancelled").length;
				const remaining = this.multiple() ? maxFiles ? Math.max(maxFiles - kept, 0) : Infinity : 1;
				const accepted = incoming.slice(0, remaining);
				const rejected = this.multiple() && maxFiles ? incoming.slice(remaining) : [];
				if (!this.activeId && !this.queue.length) this.batchIds = [];
				accepted.forEach((raw) => {
					const entry = this.createFileEntry(raw);
					this.files.push(entry);
					if (!entry.error) {
						this.queue.push(entry.id);
						this.batchIds.push(entry.id);
					}
				});
				rejected.forEach((raw) => {
					const entry = {
						id: generateId("upload-file"),
						raw,
						name: raw.name,
						size: raw.size,
						url: null,
						value: null,
						type: detectFileType(raw.type, raw.name, fileTypes),
						status: "error",
						progress: 0,
						error: tooManyFilesMessage,
						clientError: true,
						tmpFilename: null
					};
					this.files.push(entry);
				});
				this.processQueue();
				this.syncInput();
				this.syncFieldError();
			},
			createFileEntry(raw) {
				const type = detectFileType(raw.type, raw.name, fileTypes);
				const previewable = PREVIEWABLE_TYPES.includes(type);
				const error = this.validate(raw);
				const url = previewable && !error ? URL.createObjectURL(raw) : null;
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
					clientError: !!error,
					tmpFilename: null,
					previewLoaded: false,
					previewFailed: false
				};
			},
			validate(file) {
				const limit = maxSizes[detectFileType(file.type, file.name, fileTypes)] ?? maxSizes.default ?? maxSize;
				if (limit && file.size > limit * 1024) return tooLargeMessage.replaceAll(":size", formatBytes(limit * 1024));
				if (this.accept() && !this.matchesAccept(file, this.accept())) return invalidTypeMessage;
				return null;
			},
			matchesAccept(file, accept) {
				return accept.split(",").some((rule) => {
					rule = rule.trim();
					if (!rule) return false;
					if (rule === "*/*") return true;
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
				if (!this.wired()) {
					entry.status = "done";
					entry.progress = 100;
					this.activeId = null;
					this.$nextTick(() => this.processQueue());
					return;
				}
				if (this.multiple() && !Array.isArray(this.$wire.get(wireModel))) this.$wire.set(wireModel, [], false);
				this.$wire.upload(wireModel, entry.raw, (tmpFilename) => {
					entry.status = "done";
					entry.progress = 100;
					entry.tmpFilename = tmpFilename;
					this.activeId = null;
					this.processQueue();
					this.syncValues();
				}, (message) => {
					entry.status = "error";
					entry.error = message || uploadFailedMessage;
					this.activeId = null;
					this.processQueue();
					this.syncValues();
					this.$nextTick(() => {
						const rendered = findInField(this.$root, "error")?.textContent?.trim();
						if (rendered) entry.error = rendered;
					});
				}, (e) => {
					entry.progress = e.detail.progress;
				}, () => {
					entry.status = "cancelled";
					this.activeId = null;
					this.processQueue();
					this.syncValues();
				});
			},
			canRetry(file) {
				return !!file.raw && (file.status === "cancelled" || file.status === "error" && !file.clientError);
			},
			retryUpload(id) {
				const entry = this.find(id);
				if (!entry || !this.canRetry(entry)) return;
				const kept = this.files.filter((file) => file !== entry && file.status !== "error" && file.status !== "cancelled").length;
				if (this.multiple() && maxFiles && kept >= maxFiles) {
					entry.status = "error";
					entry.error = tooManyFilesMessage;
					entry.clientError = true;
					return;
				}
				entry.status = "queued";
				entry.error = null;
				entry.progress = 0;
				this.queue.unshift(entry.id);
				if (!this.batchIds.includes(entry.id)) this.batchIds.push(entry.id);
				this.processQueue();
				this.syncInput();
				this.syncFieldError();
			},
			cancelUpload(id) {
				if (id !== this.activeId || !this.$wire || !wireModel) return;
				this.$wire.cancelUpload(wireModel);
				setTimeout(() => {
					if (this.activeId === id) {
						this.activeId = null;
						this.processQueue();
					}
				}, 3e3);
			},
			removeFile(id) {
				const index = this.files.findIndex((file) => file.id === id);
				if (index === -1) return;
				const entry = this.files[index];
				if (entry.id === this.activeId) this.cancelUpload(id);
				else this.queue = this.queue.filter((queuedId) => queuedId !== id);
				this.revoke(entry);
				this.files.splice(index, 1);
				this.detachFromWire(entry);
				this.syncInput();
				this.syncFieldError();
			},
			replaceFile(index, fileList) {
				const raw = fileList?.[0];
				const entry = this.files[index];
				if (!raw || !entry) return;
				if (entry.id === this.activeId) this.cancelUpload(entry.id);
				else this.queue = this.queue.filter((queuedId) => queuedId !== entry.id);
				this.revoke(entry);
				const next = this.createFileEntry(raw);
				this.files.splice(index, 1, next);
				this.detachFromWire(entry);
				if (!next.error) {
					this.queue.push(next.id);
					this.batchIds.push(next.id);
					this.processQueue();
				}
				this.syncInput();
				this.syncFieldError();
			},
			detachFromWire(entry) {
				if (!this.$wire || !wireModel) return;
				if (entry.tmpFilename) {
					const first = [].concat(this.$wire.get(wireModel) ?? [])[0];
					if (!this.multiple() || typeof first === "string" && first.startsWith("livewire-file:")) {
						this.$wire.removeUpload(wireModel, entry.tmpFilename);
						return;
					}
					this.needsValueSync = true;
					this.syncValues();
					return;
				}
				if (entry.value === null) return;
				if (!this.multiple()) {
					this.$wire.set(wireModel, null);
					return;
				}
				this.needsValueSync = true;
				this.syncValues();
			},
			syncValues() {
				if (!this.needsValueSync || !this.$wire || !wireModel || this.hasPendingUploads()) return;
				this.needsValueSync = false;
				const held = [].concat(this.$wire.get(wireModel) ?? []);
				const signed = (tmpFilename) => held.find((value) => typeof value === "string" && value.startsWith("livewire-file:") && value.endsWith(`:${tmpFilename}`)) ?? null;
				this.$wire.set(wireModel, this.files.map((file) => file.value ?? (file.tmpFilename ? signed(file.tmpFilename) : null)).filter((value) => value !== null && value !== void 0));
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
				if (this.dragIndex === null && !droppable) return;
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
				const fileList = Array.from(e.dataTransfer?.files ?? []);
				if (fileList.length) {
					if (!droppable) return;
					this.replaceFile(index, fileList);
					if (this.multiple() && fileList.length > 1) this.addFiles(fileList.slice(1));
					return;
				}
				this.drop(index);
			},
			drop(index) {
				if (this.dragIndex === null || this.dragIndex === index) return;
				this.move(this.dragIndex, index);
				this.dragIndex = null;
			},
			move(from, to) {
				const [moved] = this.files.splice(from, 1);
				this.files.splice(to, 0, moved);
				if (this.multiple() && this.wired()) {
					this.needsValueSync = true;
					this.syncValues();
				}
				this.syncInput();
			},
			moveByKey(index, key) {
				if (!this.sortable) return;
				const rtl = isRtl(this.$root);
				const to = index + (key === "up" || key === (rtl ? "right" : "left") ? -1 : 1);
				if (to < 0 || to >= this.files.length) return;
				const focused = document.activeElement;
				this.move(index, to);
				this.$nextTick(() => {
					if (focused?.isConnected && document.activeElement !== focused) focused.focus();
					this.announcement = "";
					this.$nextTick(() => {
						this.announcement = movedMessage.replaceAll(":position", String(to + 1)).replaceAll(":total", String(this.files.length));
					});
				});
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
	var ALPINE_VERSION = "3.17.4";
	var ALPINE_URL = (name) => `https://unpkg.com/${name}@${ALPINE_VERSION}/dist/cdn.min.js`;
	var ALPINE_PLUGINS = [
		"@alpinejs/collapse",
		"@alpinejs/focus",
		"@alpinejs/persist",
		"@alpinejs/resize",
		"@alpinejs/mask",
		"@alpinejs/sort"
	];
	function loadAlpine() {
		return Promise.all(ALPINE_PLUGINS.map((name) => loadScript(ALPINE_URL(name)))).then(() => loadScript(ALPINE_URL("alpinejs")));
	}
	var installed = /* @__PURE__ */ new WeakSet();
	function install(Alpine, tallkit) {
		if (!Alpine || installed.has(Alpine)) return;
		installed.add(Alpine);
		registerAlpineComponents(Alpine);
		syncIgnoredFieldState();
		guardFormResubmit();
		guardLoadingButtons();
		installTooltips();
		tooltipDirective(Alpine);
		tallkit.appearance = Alpine.reactive(tallkit.appearance);
		Alpine.store("tallkit", tallkit);
		Alpine.magic("tallkit", () => tallkit);
		Alpine.magic("tk", () => tallkit);
	}
	function bootAlpine(tallkit, { load = true } = {}) {
		let started = false;
		document.addEventListener("alpine:init", () => {
			started = true;
			if (window.Alpine) {
				install(window.Alpine, tallkit);
				return;
			}
			console.warn("[tallkit] Alpine started without `window.Alpine`: register tallkit with `Alpine.plugin(tallkit)` (dist/tallkit.esm.js).");
		});
		if (window.Alpine) {
			warnIfAlreadyStarted();
			install(window.Alpine, tallkit);
			return;
		}
		onReady(() => {
			if (window.Alpine || started) return;
			if (!load) {
				console.warn("[tallkit] No Alpine found on the page, and loading it is turned off (tallkit.load_alpine).");
				return;
			}
			loadAlpine().catch((e) => console.error("[tallkit] Alpine could not be loaded.", e));
		});
	}
	function registerAlpineComponents(Alpine = window.Alpine) {
		const components = Object.fromEntries(Object.values([
			address_form_exports,
			alert_component_exports,
			apexcharts_exports,
			appearance_selector_exports,
			aside_exports,
			autocomplete_exports,
			badge_exports,
			calendar_exports,
			carousel_exports,
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
			money_exports,
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
			switch_all_exports,
			tab_exports,
			table_exports,
			textarea_exports,
			time_picker_exports,
			tinymce_exports,
			tiptap_exports,
			toast_exports,
			toggle_exports,
			upload_exports
		]).flatMap((module) => Object.entries(module).filter(([, v]) => typeof v === "function")));
		for (const [name, fn] of Object.entries(components)) Alpine.data(name, fn);
	}
	function warnIfAlreadyStarted() {
		if (Array.from(document.querySelectorAll("[x-data]")).some((el) => el._x_dataStack)) console.warn("[tallkit] Alpine had already started when tallkit loaded: load tallkit.js before Alpine (or Livewire).");
	}
	function onReady(callback) {
		if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", callback, { once: true });
		else callback();
	}
	//#endregion
	//#region resources/js/appearance.js
	var STORAGE_KEY = storageKey("appearance");
	var appearance = {
		mode: getStoredText(STORAGE_KEY) || "system",
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
		applyDark(persist = true) {
			document.documentElement.classList.add("dark");
			if (persist) setStoredText(STORAGE_KEY, "dark");
			this.mode = "dark";
		},
		applyLight(persist = true) {
			document.documentElement.classList.remove("dark");
			if (persist) setStoredText(STORAGE_KEY, "light");
			this.mode = "light";
		},
		apply(appearance) {
			if (appearance === "system") {
				const media = window.matchMedia("(prefers-color-scheme: dark)");
				removeStored(STORAGE_KEY);
				if (media.matches) this.applyDark(false);
				else this.applyLight(false);
				this.mode = "system";
			} else if (appearance === "dark") this.applyDark();
			else if (appearance === "light") this.applyLight();
		},
		toggle(event, options = {}) {
			if (!(typeof document !== "undefined" && typeof document.startViewTransition === "function" && !prefersReducedMotion()) || !event) return this.isDark() ? this.applyLight() : this.applyDark();
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
	//#region resources/js/consent.js
	function consent(state = "granted") {
		const value = state === "granted" ? "granted" : "denied";
		window.dataLayer = window.dataLayer || [];
		const update = {
			ad_storage: value,
			ad_user_data: value,
			ad_personalization: value,
			analytics_storage: value
		};
		window.gtag = window.gtag || function() {
			window.dataLayer.push(arguments);
		};
		window.gtag("consent", "update", update);
		setStoredText(storageKey("consent"), value);
		emit(document, eventName("consent"), { state: value });
	}
	//#endregion
	//#region resources/js/core.js
	var tallkit = {
		appearance,
		consent,
		toast: toast$1,
		tooltip,
		loadScript,
		loadStyle,
		modal: (name) => {
			return {
				show: () => {
					emit(document, eventName("modal-show"), { name });
				},
				close: () => {
					emit(document, eventName("modal-close"), { name });
				}
			};
		},
		modals: () => {
			return { close: () => {
				emit(document, eventName("modal-close"));
			} };
		}
	};
	function exposeGlobals() {
		if (window.tallkit) return;
		window.TALLKit = window.TK = window.tk = window.tallkit = tallkit;
		emit(document, eventName("init"));
	}
	//#endregion
	//#region resources/js/tallkit.js
	var script = document.currentScript;
	var load = script?.dataset.loadAlpine !== "false";
	exposeGlobals();
	try {
		if (script?.dataset.tooltip) tallkit.tooltip.configure(JSON.parse(script.dataset.tooltip));
	} catch {
		console.warn("[tallkit] The tooltip defaults on the script tag are not valid JSON.");
	}
	bootAlpine(tallkit, { load });
	//#endregion
	exports.tallkit = tallkit;
});
