(function(factory) {
  typeof define === "function" && define.amd ? define(factory) : factory();
})((function() {
  "use strict";
  function parseTimeToMilliseconds(value) {
    const parsed = Number.parseFloat(value);
    if (Number.isNaN(parsed)) {
      return 0;
    }
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
      if (options.remove && el.isConnected) {
        el.remove();
      }
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
      return () => {
      };
    }
    applyClasses(options.to, options.from);
    options.start?.();
    requestAnimationFrame(() => {
      void el.offsetHeight;
      applyClasses(options.from, options.to);
      options.finish?.();
    });
    onTransitionEnd = (event) => {
      if (event.target !== el) return;
      finish();
    };
    el.addEventListener("transitionend", onTransitionEnd);
    const timeout2 = getTransitionTimeout(el);
    if (timeout2 === 0) {
      finish();
    } else {
      const fallbackDelay = Math.max(timeout2 * 0.1, 50);
      fallbackId = window.setTimeout(finish, timeout2 + fallbackDelay);
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
    void el.offsetHeight;
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
  const scripts = /* @__PURE__ */ new Map();
  async function loadScript(src) {
    if (Array.isArray(src)) {
      return src.reduce(
        (p, s) => p.then(async (events) => [...events, await loadScript(s)]),
        Promise.resolve([])
      );
    }
    if (scripts.has(src)) {
      return scripts.get(src);
    }
    const promise = new Promise((resolve, reject) => {
      if (document.querySelector(`script[src="${src}"]`)) {
        resolve(new Event("load"));
        return;
      }
      const script = document.createElement("script");
      script.src = src;
      script.defer = true;
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
    if (check()) {
      return;
    }
    await loadScript(scriptSrc);
    if (styleHref) {
      await loadStyle(styleHref);
    }
  }
  const styles = /* @__PURE__ */ new Map();
  function loadStyle(href) {
    if (Array.isArray(href)) {
      return href.reduce(
        (p, s) => p.then(async (events) => [...events, await loadStyle(s)]),
        Promise.resolve([])
      );
    }
    if (styles.has(href)) {
      return styles.get(href);
    }
    const promise = new Promise((resolve, reject) => {
      if (document.querySelector(`link[rel="stylesheet"][href="${href}"]`)) {
        resolve(new Event("load"));
        return;
      }
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = href;
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
  function bind(el, bindings) {
    const elements = el instanceof Element ? [el] : el;
    Array.from(elements ?? []).filter((element) => element instanceof Element).forEach((element, index) => {
      window.Alpine.bind(element, typeof bindings === "function" ? bindings(element, index) : bindings);
    });
  }
  function bindShortcut(el, shortcut, callback) {
    bind(el, {
      [`@keydown.${shortcut}.document`](event) {
        event.preventDefault();
        callback(event);
      }
    });
  }
  function cache(name, {
    ttl = 1e3 * 60 * 60,
    // 1h
    persist = true
  } = {}) {
    const memory = /* @__PURE__ */ new Map();
    return {
      getStorageKey(key) {
        return ["tallkit", "cache", name, key].filter(Boolean).join(":");
      },
      get(key) {
        const mem = memory.get(key);
        if (mem) {
          if (Date.now() < mem.exp) {
            return mem.data;
          }
          memory.delete(key);
        }
        if (persist) {
          try {
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
            console.warn("[tallkit] cache read failed", e);
            return null;
          }
        }
        return null;
      },
      set(key, data) {
        const entry = {
          data,
          exp: Date.now() + ttl
        };
        memory.set(key, entry);
        if (persist) {
          try {
            localStorage.setItem(this.getStorageKey(key), JSON.stringify(entry));
          } catch (e) {
            console.warn("[tallkit] cache write failed", e);
          }
        }
      }
    };
  }
  async function fetchWithRetry(fn, retries = 2) {
    try {
      return await fn();
    } catch (e) {
      if (retries <= 0 || e.name === "AbortError") throw e;
      return fetchWithRetry(fn, retries - 1);
    }
  }
  function formatBytes(bytes, decimals = 1) {
    if (!bytes) return "0 B";
    const units = ["B", "KB", "MB", "GB", "TB"];
    const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    const value = bytes / Math.pow(1024, exponent);
    return `${exponent === 0 ? value : value.toFixed(decimals)} ${units[exponent]}`;
  }
  function detectFileType(type, name) {
    if (type.startsWith("image/")) return "image";
    if (type.startsWith("video/")) return "video";
    if (type.startsWith("audio/")) return "audio";
    const extension = name.split(".").pop()?.toLowerCase() ?? "";
    switch (extension) {
      case "jpg":
      case "jpeg":
      case "png":
      case "gif":
        return "image";
      case "mp4":
        return "video";
      case "mp3":
        return "audio";
      case "pdf":
        return "pdf";
      case "doc":
      case "docx":
        return "doc";
      case "xls":
      case "xlsx":
        return "xls";
      case "ppt":
      case "pptx":
        return "ppt";
      case "rar":
      case "zip":
      case "7z":
        return "archive";
      case "txt":
      case "md":
        return "text";
      case "csv":
        return "csv";
      case "json":
      case "js":
      case "ts":
      case "html":
      case "css":
        return "code";
      default:
        return "unknown";
    }
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
      '"': "&quot;",
      "'": "&#39;"
    })[char]);
  }
  function generateId(prefix, name, suffix) {
    return slug([
      "tallkit",
      prefix,
      Math.random().toString(36).slice(2, 9),
      suffix
    ].filter(Boolean).join("-"));
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
    if (opts?.replaceAccents) {
      str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }
    switch (opts.mode) {
      case "alpha":
        str = str.replace(/[^a-z\s-]/gi, "");
        break;
      case "alphanumeric":
        str = str.replace(/[^a-z0-9\s-]/gi, "");
        break;
      case "numeric":
        str = str.replace(/[^0-9\s-]/g, "");
        break;
    }
    if (opts?.removeSpaces) {
      str = str.replace(/\s+/g, " ").trim();
    }
    if (opts?.replaceSpaces) {
      str = str.replace(/\s+/g, opts.replaceSpaces).trim();
    }
    if (opts.uppercase && !opts.lowercase) {
      str = str.toUpperCase();
    } else if (opts.lowercase && !opts.uppercase) {
      str = str.toLowerCase();
    }
    return str;
  }
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
  function findFieldInput(el) {
    return el?.closest(dataKey("field-control"))?.querySelector(dataKey("input")) ?? null;
  }
  function getWireModelInfo(element) {
    for (const attr of element.attributes) {
      if (attr.name.startsWith("wire:model")) {
        const modifier = attr.name.includes(".") ? attr.name.split(".").slice(1).join(".") : "";
        return {
          name: attr.value,
          modifier
        };
      }
    }
    return null;
  }
  function timeout(callback, milliseconds, defaultMilliseconds = 500) {
    const ms = !milliseconds || isNaN(parseInt(milliseconds.toString())) ? defaultMilliseconds : parseInt(milliseconds.toString());
    return setTimeout(callback, ms);
  }
  function debounce(callback, delay = 300) {
    let timeout2 = void 0;
    const debounced = (...args) => {
      clearTimeout(timeout2);
      timeout2 = setTimeout(() => callback(...args), delay);
    };
    debounced.cancel = () => clearTimeout(timeout2);
    return debounced;
  }
  function addressForm(options = {}) {
    const _cache = cache("zipcode", options);
    return {
      abortController: null,
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
        bind(this.$els.zipcode, {
          ["@input"]() {
            debouncedSearch(this.$el.value);
          }
        });
      },
      setLoading(state) {
        this.$els.loading?.classList.toggle("hidden", !state);
        ["address", "neighborhood", "city", "state"].map((k) => this.$els[k]).filter(Boolean).forEach((el) => el.disabled = state);
      },
      resolveState(data) {
        const el = this.$els.state;
        if (!el) return "";
        const value = data.estado ?? data.uf;
        if (el.tagName.toLowerCase() === "input") return value ?? "";
        const hasOption = value != null && Array.from(el.options ?? []).some((option) => option.value === value);
        return hasOption ? value : data.uf ?? "";
      },
      normalizeZipcode(value) {
        return value.replace(/\D/g, "");
      },
      async viaCep(zipcode, signal) {
        const res = await fetch(`https://viacep.com.br/ws/${zipcode}/json/`, { signal });
        const data = await res.json();
        if (data.erro) throw new Error("ViaCEP not found");
        return data;
      },
      async brasilApi(zipcode, signal) {
        const res = await fetch(`https://brasilapi.com.br/api/cep/v1/${zipcode}`, { signal });
        if (!res.ok) throw new Error("BrasilAPI error");
        const data = await res.json();
        return {
          logradouro: data.street,
          bairro: data.neighborhood,
          localidade: data.city,
          uf: data.state
        };
      },
      async resolveAddress(zipcode, signal) {
        const providers = [
          this.viaCep.bind(this),
          this.brasilApi.bind(this)
        ];
        for (const provider of providers) {
          try {
            return await fetchWithRetry(() => provider(zipcode, signal));
          } catch (e) {
            if (e.name === "AbortError") throw e;
          }
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
          this.$dispatch("loaded", { zipcode, data: cached, cached: true });
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
          this.$dispatch("loaded", { zipcode, data, cached: false });
        } catch (e) {
          if (e.name === "AbortError" || signal.aborted) return;
          this.$dispatch("error", { zipcode, error: e });
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
  const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    addressForm
  }, Symbol.toStringTag, { value: "Module" }));
  function dismissible(animation2) {
    return {
      cancelDismiss: null,
      isDismissing: false,
      _dismissTimeout: null,
      init() {
        bind(this.$root.querySelectorAll(dataKey("dismissible")), {
          ["@click.stop"]: (e) => {
            e.currentTarget.dispatchEvent(new CustomEvent("close"));
            this.dismiss("manual");
          }
        });
        bind(this.$root, {
          ["@dismiss"]: (e) => {
            const detail = e.detail || {};
            this.dismiss(detail.reason || "programmatic");
          }
        });
      },
      beforeDismiss() {
      },
      dismiss(reason = "programmatic") {
        if (this.isDismissing) return;
        const event = new CustomEvent("before-dismiss", {
          detail: { reason },
          cancelable: true
        });
        this.$root.dispatchEvent(event);
        if (event.defaultPrevented) {
          return;
        }
        this.isDismissing = true;
        this.beforeDismiss();
        this.cancelDismiss?.();
        this.cancelDismiss = null;
        const onDone = () => {
          this.isDismissing = false;
          this.cancelDismiss = null;
          this.$dispatch("dismissed", { reason });
          if (this.$root.isConnected) {
            this.$root.remove();
          }
        };
        if (animation2 === "fade") {
          this.cancelDismiss = fadeOut(this.$root, { onDone });
        } else if (animation2 === "collapse") {
          this.cancelDismiss = collapse(this.$root, { onDone });
        } else {
          onDone();
        }
        if (this._dismissTimeout) {
          clearTimeout(this._dismissTimeout);
        }
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
  const __vite_glob_0_15 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    dismissible
  }, Symbol.toStringTag, { value: "Module" }));
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
        this.timeoutId = timeout(
          () => this.dismiss("timeout"),
          this.remaining,
          7e3
        );
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
        if (document.hidden) {
          this.pause("visibility");
        } else {
          this.resume("visibility");
        }
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
        const computed = getComputedStyle(this.progressEl);
        const size = computed.backgroundSize;
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
  const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    alertComponent
  }, Symbol.toStringTag, { value: "Module" }));
  function dataOptions() {
    return {
      getDataOptions() {
        return window.Alpine.evaluate(this.$el, this.$el.getAttribute("data-options") || "{}");
      }
    };
  }
  const __vite_glob_0_12 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    dataOptions
  }, Symbol.toStringTag, { value: "Module" }));
  function loadable() {
    return {
      empty: null,
      loaded: null,
      error: null,
      _loadToken: 0,
      _pendingLoad: null,
      async load(cb, silent = false) {
        if (!silent && !this.$el.hasAttribute("data-silent")) {
          this.start();
        }
        const token = this._loadToken;
        try {
          const result = await cb();
          this.complete(0, token);
          if (typeof result === "function") {
            this.$nextTick(result);
          }
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
      complete(milliseconds = 0, token = this._loadToken) {
        this._clearPendingLoad();
        this._pendingLoad = setTimeout(() => {
          this._pendingLoad = null;
          if (token !== this._loadToken) return;
          this.reset();
          this.loaded = true;
          this.$dispatch("completed");
        }, milliseconds);
      },
      fail(error, milliseconds = 0, token = this._loadToken) {
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
        if (completeOnNextTick) {
          this.$nextTick(() => this.complete());
        }
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
  const __vite_glob_0_27 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    loadable
  }, Symbol.toStringTag, { value: "Module" }));
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
        const merged = { ...options, ...this.getDataOptions() };
        if (this.chart) {
          this.chart.updateOptions(merged);
        } else {
          this.chart = new window.ApexCharts(this.$el, merged);
          this.chart.render();
        }
        this.$dispatch("rendered", { chart: this.chart });
      },
      destroy() {
        _loadable.destroy.call(this);
        this.chart?.destroy();
        this.chart = null;
      }
    };
  }
  const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    apexcharts
  }, Symbol.toStringTag, { value: "Module" }));
  function sticky() {
    return {
      init() {
        this.updateOffset();
        this._onResize = () => this.updateOffset();
        window.addEventListener("resize", this._onResize);
      },
      updateOffset() {
        const top = this.$el.offsetTop;
        this.$el.style.position = "sticky";
        this.$el.style.top = `${top}px`;
        this.$el.style.maxHeight = `calc(100dvh - ${top}px)`;
      },
      destroy() {
        window.removeEventListener("resize", this._onResize);
      }
    };
  }
  const __vite_glob_0_42 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    sticky
  }, Symbol.toStringTag, { value: "Module" }));
  function aside() {
    return {
      ...sticky()
    };
  }
  const __vite_glob_0_3 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    aside
  }, Symbol.toStringTag, { value: "Module" }));
  function toggleable() {
    return {
      opened: false,
      lastOpened: null,
      init(opened = false) {
        if (Number.isInteger(opened)) {
          return timeout(() => this.open(), opened);
        }
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
        if (this.isOpened()) {
          this.close(storage);
        } else {
          this.open(storage);
        }
      },
      isOpened() {
        return this.opened === true;
      },
      isClosed() {
        return this.opened === false;
      }
    };
  }
  const __vite_glob_0_48 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    toggleable
  }, Symbol.toStringTag, { value: "Module" }));
  function popover({ mode = "hover", position = "bottom", align = "end" } = {}) {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      popoverElement: null,
      trigger: null,
      resizeObserver: null,
      mutationObserver: null,
      livewireCommitCleanup: null,
      _rAF: null,
      mouseX: 0,
      mouseY: 0,
      init() {
        _toggleable.init.call(this);
        this.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
        if (!this.popoverElement) return;
        this.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root;
        if (this.trigger?.matches(dataKey("tooltip"))) {
          this.trigger = this.trigger.firstElementChild;
        }
        const role = this.popoverElement.getAttribute("role");
        if (!this.trigger.hasAttribute("aria-haspopup") && role !== "tooltip") {
          this.trigger.setAttribute("aria-haspopup", role === "listbox" || role === "dialog" ? role : "true");
        }
        if (!this.trigger.hasAttribute("aria-expanded")) {
          this.trigger.setAttribute("aria-expanded", "false");
        }
        if (!this.popoverElement.id) {
          this.popoverElement.id = generateId("popover");
        }
        if (!this.trigger.hasAttribute("aria-controls")) {
          this.trigger.setAttribute("aria-controls", this.popoverElement.id);
        }
        if (role === "tooltip") {
          const ids = new Set((this.trigger.getAttribute("aria-describedby") ?? "").split(" ").filter(Boolean));
          ids.add(this.popoverElement.id);
          this.trigger.setAttribute("aria-describedby", Array.from(ids).join(" "));
        }
        this.popoverElement.addEventListener("beforetoggle", (e) => {
          queueMicrotask(() => {
            if (e.newState === "open") {
              this.onOpen();
            } else {
              this.onClose();
            }
          });
        });
        const offCommit = window.Livewire?.hook("commit", ({ succeed }) => {
          succeed(() => {
            if (!this.popoverElement?.matches(":popover-open")) return;
            if (!this.$root?.isConnected) return;
            this.boundSetPosition();
          });
        });
        this.livewireCommitCleanup = typeof offCommit === "function" ? offCommit : () => {
        };
        if (mode !== "manual" && (window.matchMedia("(hover: none)").matches || mode === "dropdown")) {
          bind(this.trigger, {
            ["@click"]() {
              this.toggle(!["menu"].includes(role));
            },
            ["@click.outside"](e) {
              if ((this.popoverElement.hasAttribute("data-keep-open") || e.target?.hasAttribute("data-keep-open") || e.target?.closest("[data-keep-open]")) && this.popoverElement.contains(e.target)) {
                return;
              }
              this.close();
            }
          });
        } else if (mode === "hover") {
          bind(this.trigger, {
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
        } else if (mode === "context") {
          bind(this.trigger, {
            ["@contextmenu.prevent"](event) {
              this.close();
              this.mouseX = event.clientX;
              this.mouseY = event.clientY;
              this.open();
            }
          });
          bind(this.popoverElement, {
            ["@click.outside"]() {
              this.close();
            }
          });
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
          if (this.popoverElement.matches(":popover-open")) return;
          this.popoverElement.showPopover();
          if (focus) {
            const firstItem = this.popoverElement.querySelector("[role=menuitem], [role=option], [role=tab]");
            (firstItem ?? this.popoverElement).focus();
          }
        });
      },
      close() {
        requestAnimationFrame(() => {
          if (!this.popoverElement?.isConnected) return;
          if (!this.popoverElement.matches(":popover-open")) return;
          this.popoverElement.hidePopover();
        });
      },
      onOpen() {
        _toggleable.open.call(this);
        this.trigger.setAttribute("aria-expanded", "true");
        this._onScroll ??= () => this.boundSetPosition();
        this._onResize ??= () => this.boundSetPosition();
        window.addEventListener("scroll", this._onScroll, true);
        window.addEventListener("resize", this._onResize, true);
        this.resizeObserver = new ResizeObserver(() => this.boundSetPosition());
        this.resizeObserver.observe(this.trigger);
        this.resizeObserver.observe(this.popoverElement);
        this.mutationObserver = new MutationObserver(() => this.boundSetPosition());
        this.mutationObserver.observe(this.trigger, {
          childList: true
        });
        this.mutationObserver.observe(this.popoverElement, {
          childList: true
        });
        this.setPosition();
      },
      onClose() {
        _toggleable.close.call(this);
        this.trigger.setAttribute("aria-expanded", "false");
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
        if (!this.trigger?.isConnected && mode !== "context") return;
        if (!this.popoverElement.matches(":popover-open")) return;
        let triggerRect;
        if (mode === "context") {
          triggerRect = {
            top: this.mouseY,
            bottom: this.mouseY,
            left: this.mouseX,
            right: this.mouseX,
            height: 0,
            width: 0
          };
        } else {
          triggerRect = this.trigger.getBoundingClientRect();
        }
        const triggerHeight = triggerRect.height;
        const triggerWidth = triggerRect.width;
        const scrollTop = window.scrollY;
        const scrollLeft = window.scrollX;
        const tooltipHeight = this.popoverElement.offsetHeight;
        const tooltipWidth = this.popoverElement.offsetWidth;
        const margin = 4;
        const getCenterOffset = (pos, align2) => {
          if (align2 === "start" || align2 === "left") return 0;
          if (align2 === "end" || align2 === "right") {
            return pos === "left" || pos === "right" ? triggerHeight - tooltipHeight : triggerWidth - tooltipWidth;
          }
          return pos === "left" || pos === "right" ? (triggerHeight - tooltipHeight) / 2 : (triggerWidth - tooltipWidth) / 2;
        };
        const getCoords = (pos, align2) => {
          const center = getCenterOffset(pos, align2);
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
              break;
          }
          return { top, left };
        };
        const isVisible = ({ top, left }) => {
          return top >= scrollTop && left >= scrollLeft && top + tooltipHeight <= scrollTop + window.innerHeight && left + tooltipWidth <= scrollLeft + window.innerWidth;
        };
        const positions = ["top", "bottom", "left", "right"];
        const aligns = ["start", "left", "end", "right", "center"];
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
            if (found) {
              break;
            }
          }
        }
        this.popoverElement.style.position = "absolute";
        this.popoverElement.style.inset = "auto";
        this.popoverElement.style.top = `${coords.top}px`;
        this.popoverElement.style.left = `${coords.left}px`;
        this.popoverElement.dataset.position = computedPosition;
        this.popoverElement.dataset.align = computedAlign;
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
  const __vite_glob_0_37 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    popover
  }, Symbol.toStringTag, { value: "Module" }));
  function isArray(value) {
    return !Array.isArray ? getTag(value) === "[object Array]" : Array.isArray(value);
  }
  function baseToString(value) {
    if (typeof value == "string") {
      return value;
    }
    let result = value + "";
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
  const INCORRECT_INDEX_TYPE = "Incorrect 'index' type";
  const LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY = (key) => `Invalid value for key ${key}`;
  const PATTERN_LENGTH_TOO_LARGE = (max) => `Pattern length exceeds max of ${max}.`;
  const MISSING_KEY_PROPERTY = (name) => `Missing ${name} property in key`;
  const INVALID_KEY_WEIGHT_VALUE = (key) => `Property 'weight' in key '${key}' must be a positive integer`;
  const hasOwn = Object.prototype.hasOwnProperty;
  class KeyStore {
    constructor(keys) {
      this._keys = [];
      this._keyMap = {};
      let totalWeight = 0;
      keys.forEach((key) => {
        let obj = createKey(key);
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
  }
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
      if (!hasOwn.call(key, "name")) {
        throw new Error(MISSING_KEY_PROPERTY("name"));
      }
      const name = key.name;
      src = name;
      if (hasOwn.call(key, "weight")) {
        weight = key.weight;
        if (weight <= 0) {
          throw new Error(INVALID_KEY_WEIGHT_VALUE(name));
        }
      }
      path = createKeyPath(name);
      id = createKeyId(name);
      getFn = key.getFn;
    }
    return { path, id, weight, src, getFn };
  }
  function createKeyPath(key) {
    return isArray(key) ? key : key.split(".");
  }
  function createKeyId(key) {
    return isArray(key) ? key.join(".") : key;
  }
  function get(obj, path) {
    let list = [];
    let arr = false;
    const deepGet = (obj2, path2, index) => {
      if (!isDefined(obj2)) {
        return;
      }
      if (!path2[index]) {
        list.push(obj2);
      } else {
        let key = path2[index];
        const value = obj2[key];
        if (!isDefined(value)) {
          return;
        }
        if (index === path2.length - 1 && (isString(value) || isNumber(value) || isBoolean(value))) {
          list.push(toString(value));
        } else if (isArray(value)) {
          arr = true;
          for (let i = 0, len = value.length; i < len; i += 1) {
            deepGet(value[i], path2, index + 1);
          }
        } else if (path2.length) {
          deepGet(value, path2, index + 1);
        }
      }
    };
    deepGet(obj, isString(path) ? path.split(".") : path, 0);
    return arr ? list : list[0];
  }
  const MatchOptions = {
    // Whether the matches should be included in the result set. When `true`, each record in the result
    // set will include the indices of the matched characters.
    // These can consequently be used for highlighting purposes.
    includeMatches: false,
    // When `true`, the matching function will continue to the end of a search pattern even if
    // a perfect match has already been located in the string.
    findAllMatches: false,
    // Minimum number of characters that must be matched before a result is considered a match
    minMatchCharLength: 1
  };
  const BasicOptions = {
    // When `true`, the algorithm continues searching to the end of the input even if a perfect
    // match is found before the end of the same input.
    isCaseSensitive: false,
    // When `true`, the algorithm will ignore diacritics (accents) in comparisons
    ignoreDiacritics: false,
    // When true, the matching function will continue to the end of a search pattern even if
    includeScore: false,
    // List of properties that will be searched. This also supports nested properties.
    keys: [],
    // Whether to sort the result list, by score
    shouldSort: true,
    // Default sort function: sort by ascending score, ascending index
    sortFn: (a, b) => a.score === b.score ? a.idx < b.idx ? -1 : 1 : a.score < b.score ? -1 : 1
  };
  const FuzzyOptions = {
    // Approximately where in the text is the pattern expected to be found?
    location: 0,
    // At what point does the match algorithm give up. A threshold of '0.0' requires a perfect match
    // (of both letters and location), a threshold of '1.0' would match anything.
    threshold: 0.6,
    // Determines how close the match must be to the fuzzy location (specified above).
    // An exact letter match which is 'distance' characters away from the fuzzy location
    // would score as a complete mismatch. A distance of '0' requires the match be at
    // the exact location specified, a threshold of '1000' would require a perfect match
    // to be within 800 characters of the fuzzy location to be found using a 0.8 threshold.
    distance: 100
  };
  const AdvancedOptions = {
    // When `true`, it enables the use of unix-like search commands
    useExtendedSearch: false,
    // The get function to use when fetching an object's properties.
    // The default will search nested paths *ie foo.bar.baz*
    getFn: get,
    // When `true`, search will ignore `location` and `distance`, so it won't matter
    // where in the string the pattern appears.
    // More info: https://fusejs.io/concepts/scoring-theory.html#fuzziness-score
    ignoreLocation: false,
    // When `true`, the calculation for the relevance score (used for sorting) will
    // ignore the field-length norm.
    // More info: https://fusejs.io/concepts/scoring-theory.html#field-length-norm
    ignoreFieldNorm: false,
    // The weight to determine how much field length norm effects scoring.
    fieldNormWeight: 1
  };
  var Config = {
    ...BasicOptions,
    ...MatchOptions,
    ...FuzzyOptions,
    ...AdvancedOptions
  };
  const SPACE = /[^ ]+/g;
  function norm(weight = 1, mantissa = 3) {
    const cache2 = /* @__PURE__ */ new Map();
    const m = Math.pow(10, mantissa);
    return {
      get(value) {
        const numTokens = value.match(SPACE).length;
        if (cache2.has(numTokens)) {
          return cache2.get(numTokens);
        }
        const norm2 = 1 / Math.pow(numTokens, 0.5 * weight);
        const n = parseFloat(Math.round(norm2 * m) / m);
        cache2.set(numTokens, n);
        return n;
      },
      clear() {
        cache2.clear();
      }
    };
  }
  class FuseIndex {
    constructor({
      getFn = Config.getFn,
      fieldNormWeight = Config.fieldNormWeight
    } = {}) {
      this.norm = norm(fieldNormWeight, 3);
      this.getFn = getFn;
      this.isCreated = false;
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
      if (this.isCreated || !this.docs.length) {
        return;
      }
      this.isCreated = true;
      if (isString(this.docs[0])) {
        this.docs.forEach((doc, docIndex) => {
          this._addString(doc, docIndex);
        });
      } else {
        this.docs.forEach((doc, docIndex) => {
          this._addObject(doc, docIndex);
        });
      }
      this.norm.clear();
    }
    // Adds a doc to the end of the index
    add(doc) {
      const idx = this.size();
      if (isString(doc)) {
        this._addString(doc, idx);
      } else {
        this._addObject(doc, idx);
      }
    }
    // Removes the doc at the specified index of the index
    removeAt(idx) {
      this.records.splice(idx, 1);
      for (let i = idx, len = this.size(); i < len; i += 1) {
        this.records[i].i -= 1;
      }
    }
    getValueForItemAtKeyId(item, keyId) {
      return item[this._keysMap[keyId]];
    }
    size() {
      return this.records.length;
    }
    _addString(doc, docIndex) {
      if (!isDefined(doc) || isBlank(doc)) {
        return;
      }
      let record = {
        v: doc,
        i: docIndex,
        n: this.norm.get(doc)
      };
      this.records.push(record);
    }
    _addObject(doc, docIndex) {
      let record = { i: docIndex, $: {} };
      this.keys.forEach((key, keyIndex) => {
        let value = key.getFn ? key.getFn(doc) : this.getFn(doc, key.path);
        if (!isDefined(value)) {
          return;
        }
        if (isArray(value)) {
          let subRecords = [];
          const stack = [{ nestedArrIndex: -1, value }];
          while (stack.length) {
            const { nestedArrIndex, value: value2 } = stack.pop();
            if (!isDefined(value2)) {
              continue;
            }
            if (isString(value2) && !isBlank(value2)) {
              let subRecord = {
                v: value2,
                i: nestedArrIndex,
                n: this.norm.get(value2)
              };
              subRecords.push(subRecord);
            } else if (isArray(value2)) {
              value2.forEach((item, k) => {
                stack.push({
                  nestedArrIndex: k,
                  value: item
                });
              });
            } else ;
          }
          record.$[keyIndex] = subRecords;
        } else if (isString(value) && !isBlank(value)) {
          let subRecord = {
            v: value,
            n: this.norm.get(value)
          };
          record.$[keyIndex] = subRecord;
        }
      });
      this.records.push(record);
    }
    toJSON() {
      return {
        keys: this.keys,
        records: this.records
      };
    }
  }
  function createIndex(keys, docs, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
    const myIndex = new FuseIndex({ getFn, fieldNormWeight });
    myIndex.setKeys(keys.map(createKey));
    myIndex.setSources(docs);
    myIndex.create();
    return myIndex;
  }
  function parseIndex(data, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
    const { keys, records } = data;
    const myIndex = new FuseIndex({ getFn, fieldNormWeight });
    myIndex.setKeys(keys);
    myIndex.setIndexRecords(records);
    return myIndex;
  }
  function computeScore$1(pattern, {
    errors = 0,
    currentLocation = 0,
    expectedLocation = 0,
    distance = Config.distance,
    ignoreLocation = Config.ignoreLocation
  } = {}) {
    const accuracy = errors / pattern.length;
    if (ignoreLocation) {
      return accuracy;
    }
    const proximity = Math.abs(expectedLocation - currentLocation);
    if (!distance) {
      return proximity ? 1 : accuracy;
    }
    return accuracy + proximity / distance;
  }
  function convertMaskToIndices(matchmask = [], minMatchCharLength = Config.minMatchCharLength) {
    let indices = [];
    let start = -1;
    let end = -1;
    let i = 0;
    for (let len = matchmask.length; i < len; i += 1) {
      let match = matchmask[i];
      if (match && start === -1) {
        start = i;
      } else if (!match && start !== -1) {
        end = i - 1;
        if (end - start + 1 >= minMatchCharLength) {
          indices.push([start, end]);
        }
        start = -1;
      }
    }
    if (matchmask[i - 1] && i - start >= minMatchCharLength) {
      indices.push([start, i - 1]);
    }
    return indices;
  }
  const MAX_BITS = 32;
  function search(text, pattern, patternAlphabet, {
    location = Config.location,
    distance = Config.distance,
    threshold = Config.threshold,
    findAllMatches = Config.findAllMatches,
    minMatchCharLength = Config.minMatchCharLength,
    includeMatches = Config.includeMatches,
    ignoreLocation = Config.ignoreLocation
  } = {}) {
    if (pattern.length > MAX_BITS) {
      throw new Error(PATTERN_LENGTH_TOO_LARGE(MAX_BITS));
    }
    const patternLen = pattern.length;
    const textLen = text.length;
    const expectedLocation = Math.max(0, Math.min(location, textLen));
    let currentThreshold = threshold;
    let bestLocation = expectedLocation;
    const computeMatches = minMatchCharLength > 1 || includeMatches;
    const matchMask = computeMatches ? Array(textLen) : [];
    let index;
    while ((index = text.indexOf(pattern, bestLocation)) > -1) {
      let score = computeScore$1(pattern, {
        currentLocation: index,
        expectedLocation,
        distance,
        ignoreLocation
      });
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
    let binMax = patternLen + textLen;
    const mask = 1 << patternLen - 1;
    for (let i = 0; i < patternLen; i += 1) {
      let binMin = 0;
      let binMid = binMax;
      while (binMin < binMid) {
        const score2 = computeScore$1(pattern, {
          errors: i,
          currentLocation: expectedLocation + binMid,
          expectedLocation,
          distance,
          ignoreLocation
        });
        if (score2 <= currentThreshold) {
          binMin = binMid;
        } else {
          binMax = binMid;
        }
        binMid = Math.floor((binMax - binMin) / 2 + binMin);
      }
      binMax = binMid;
      let start = Math.max(1, expectedLocation - binMid + 1);
      let finish = findAllMatches ? textLen : Math.min(expectedLocation + binMid, textLen) + patternLen;
      let bitArr = Array(finish + 2);
      bitArr[finish + 1] = (1 << i) - 1;
      for (let j = finish; j >= start; j -= 1) {
        let currentLocation = j - 1;
        let charMatch = patternAlphabet[text.charAt(currentLocation)];
        if (computeMatches) {
          matchMask[currentLocation] = +!!charMatch;
        }
        bitArr[j] = (bitArr[j + 1] << 1 | 1) & charMatch;
        if (i) {
          bitArr[j] |= (lastBitArr[j + 1] | lastBitArr[j]) << 1 | 1 | lastBitArr[j + 1];
        }
        if (bitArr[j] & mask) {
          finalScore = computeScore$1(pattern, {
            errors: i,
            currentLocation,
            expectedLocation,
            distance,
            ignoreLocation
          });
          if (finalScore <= currentThreshold) {
            currentThreshold = finalScore;
            bestLocation = currentLocation;
            if (bestLocation <= expectedLocation) {
              break;
            }
            start = Math.max(1, 2 * expectedLocation - bestLocation);
          }
        }
      }
      const score = computeScore$1(pattern, {
        errors: i + 1,
        currentLocation: expectedLocation,
        expectedLocation,
        distance,
        ignoreLocation
      });
      if (score > currentThreshold) {
        break;
      }
      lastBitArr = bitArr;
    }
    const result = {
      isMatch: bestLocation >= 0,
      // Count exact matches (those with a score of 0) to be "almost" exact
      score: Math.max(1e-3, finalScore)
    };
    if (computeMatches) {
      const indices = convertMaskToIndices(matchMask, minMatchCharLength);
      if (!indices.length) {
        result.isMatch = false;
      } else if (includeMatches) {
        result.indices = indices;
      }
    }
    return result;
  }
  function createPatternAlphabet(pattern) {
    let mask = {};
    for (let i = 0, len = pattern.length; i < len; i += 1) {
      const char = pattern.charAt(i);
      mask[char] = (mask[char] || 0) | 1 << len - i - 1;
    }
    return mask;
  }
  const stripDiacritics = String.prototype.normalize ? ((str) => str.normalize("NFD").replace(/[\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u07FD\u0816-\u0819\u081B-\u0823\u0825-\u0827\u0829-\u082D\u0859-\u085B\u08D3-\u08E1\u08E3-\u0903\u093A-\u093C\u093E-\u094F\u0951-\u0957\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u09FE\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0AFA-\u0AFF\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C00-\u0C04\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C81-\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D00-\u0D03\u0D3B\u0D3C\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F8D-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u109A-\u109D\u135D-\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B4-\u17D3\u17DD\u180B-\u180D\u1885\u1886\u18A9\u1920-\u192B\u1930-\u193B\u1A17-\u1A1B\u1A55-\u1A5E\u1A60-\u1A7C\u1A7F\u1AB0-\u1ABE\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAD\u1BE6-\u1BF3\u1C24-\u1C37\u1CD0-\u1CD2\u1CD4-\u1CE8\u1CED\u1CF2-\u1CF4\u1CF7-\u1CF9\u1DC0-\u1DF9\u1DFB-\u1DFF\u20D0-\u20F0\u2CEF-\u2CF1\u2D7F\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA674-\uA67D\uA69E\uA69F\uA6F0\uA6F1\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C5\uA8E0-\uA8F1\uA8FF\uA926-\uA92D\uA947-\uA953\uA980-\uA983\uA9B3-\uA9C0\uA9E5\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uAA7B-\uAA7D\uAAB0\uAAB2-\uAAB4\uAAB7\uAAB8\uAABE\uAABF\uAAC1\uAAEB-\uAAEF\uAAF5\uAAF6\uABE3-\uABEA\uABEC\uABED\uFB1E\uFE00-\uFE0F\uFE20-\uFE2F]/g, "")) : ((str) => str);
  class BitapSearch {
    constructor(pattern, {
      location = Config.location,
      threshold = Config.threshold,
      distance = Config.distance,
      includeMatches = Config.includeMatches,
      findAllMatches = Config.findAllMatches,
      minMatchCharLength = Config.minMatchCharLength,
      isCaseSensitive = Config.isCaseSensitive,
      ignoreDiacritics = Config.ignoreDiacritics,
      ignoreLocation = Config.ignoreLocation
    } = {}) {
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
      if (!this.pattern.length) {
        return;
      }
      const addChunk = (pattern2, startIndex) => {
        this.chunks.push({
          pattern: pattern2,
          alphabet: createPatternAlphabet(pattern2),
          startIndex
        });
      };
      const len = this.pattern.length;
      if (len > MAX_BITS) {
        let i = 0;
        const remainder = len % MAX_BITS;
        const end = len - remainder;
        while (i < end) {
          addChunk(this.pattern.substr(i, MAX_BITS), i);
          i += MAX_BITS;
        }
        if (remainder) {
          const startIndex = len - MAX_BITS;
          addChunk(this.pattern.substr(startIndex), startIndex);
        }
      } else {
        addChunk(this.pattern, 0);
      }
    }
    searchIn(text) {
      const { isCaseSensitive, ignoreDiacritics, includeMatches } = this.options;
      text = isCaseSensitive ? text : text.toLowerCase();
      text = ignoreDiacritics ? stripDiacritics(text) : text;
      if (this.pattern === text) {
        let result2 = {
          isMatch: true,
          score: 0
        };
        if (includeMatches) {
          result2.indices = [[0, text.length - 1]];
        }
        return result2;
      }
      const {
        location,
        distance,
        threshold,
        findAllMatches,
        minMatchCharLength,
        ignoreLocation
      } = this.options;
      let allIndices = [];
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
        if (isMatch) {
          hasMatches = true;
        }
        totalScore += score;
        if (isMatch && indices) {
          allIndices = [...allIndices, ...indices];
        }
      });
      let result = {
        isMatch: hasMatches,
        score: hasMatches ? totalScore / this.chunks.length : 1
      };
      if (hasMatches && includeMatches) {
        result.indices = allIndices;
      }
      return result;
    }
  }
  class BaseMatch {
    constructor(pattern) {
      this.pattern = pattern;
    }
    static isMultiMatch(pattern) {
      return getMatch(pattern, this.multiRegex);
    }
    static isSingleMatch(pattern) {
      return getMatch(pattern, this.singleRegex);
    }
    search() {
    }
  }
  function getMatch(pattern, exp) {
    const matches = pattern.match(exp);
    return matches ? matches[1] : null;
  }
  class ExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "exact";
    }
    static get multiRegex() {
      return /^="(.*)"$/;
    }
    static get singleRegex() {
      return /^=(.*)$/;
    }
    search(text) {
      const isMatch = text === this.pattern;
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [0, this.pattern.length - 1]
      };
    }
  }
  class InverseExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "inverse-exact";
    }
    static get multiRegex() {
      return /^!"(.*)"$/;
    }
    static get singleRegex() {
      return /^!(.*)$/;
    }
    search(text) {
      const index = text.indexOf(this.pattern);
      const isMatch = index === -1;
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [0, text.length - 1]
      };
    }
  }
  class PrefixExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "prefix-exact";
    }
    static get multiRegex() {
      return /^\^"(.*)"$/;
    }
    static get singleRegex() {
      return /^\^(.*)$/;
    }
    search(text) {
      const isMatch = text.startsWith(this.pattern);
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [0, this.pattern.length - 1]
      };
    }
  }
  class InversePrefixExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "inverse-prefix-exact";
    }
    static get multiRegex() {
      return /^!\^"(.*)"$/;
    }
    static get singleRegex() {
      return /^!\^(.*)$/;
    }
    search(text) {
      const isMatch = !text.startsWith(this.pattern);
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [0, text.length - 1]
      };
    }
  }
  class SuffixExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "suffix-exact";
    }
    static get multiRegex() {
      return /^"(.*)"\$$/;
    }
    static get singleRegex() {
      return /^(.*)\$$/;
    }
    search(text) {
      const isMatch = text.endsWith(this.pattern);
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [text.length - this.pattern.length, text.length - 1]
      };
    }
  }
  class InverseSuffixExactMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "inverse-suffix-exact";
    }
    static get multiRegex() {
      return /^!"(.*)"\$$/;
    }
    static get singleRegex() {
      return /^!(.*)\$$/;
    }
    search(text) {
      const isMatch = !text.endsWith(this.pattern);
      return {
        isMatch,
        score: isMatch ? 0 : 1,
        indices: [0, text.length - 1]
      };
    }
  }
  class FuzzyMatch extends BaseMatch {
    constructor(pattern, {
      location = Config.location,
      threshold = Config.threshold,
      distance = Config.distance,
      includeMatches = Config.includeMatches,
      findAllMatches = Config.findAllMatches,
      minMatchCharLength = Config.minMatchCharLength,
      isCaseSensitive = Config.isCaseSensitive,
      ignoreDiacritics = Config.ignoreDiacritics,
      ignoreLocation = Config.ignoreLocation
    } = {}) {
      super(pattern);
      this._bitapSearch = new BitapSearch(pattern, {
        location,
        threshold,
        distance,
        includeMatches,
        findAllMatches,
        minMatchCharLength,
        isCaseSensitive,
        ignoreDiacritics,
        ignoreLocation
      });
    }
    static get type() {
      return "fuzzy";
    }
    static get multiRegex() {
      return /^"(.*)"$/;
    }
    static get singleRegex() {
      return /^(.*)$/;
    }
    search(text) {
      return this._bitapSearch.searchIn(text);
    }
  }
  class IncludeMatch extends BaseMatch {
    constructor(pattern) {
      super(pattern);
    }
    static get type() {
      return "include";
    }
    static get multiRegex() {
      return /^'"(.*)"$/;
    }
    static get singleRegex() {
      return /^'(.*)$/;
    }
    search(text) {
      let location = 0;
      let index;
      const indices = [];
      const patternLen = this.pattern.length;
      while ((index = text.indexOf(this.pattern, location)) > -1) {
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
  }
  const searchers = [
    ExactMatch,
    IncludeMatch,
    PrefixExactMatch,
    InversePrefixExactMatch,
    InverseSuffixExactMatch,
    SuffixExactMatch,
    InverseExactMatch,
    FuzzyMatch
  ];
  const searchersLen = searchers.length;
  const SPACE_RE = / +(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/;
  const OR_TOKEN = "|";
  function parseQuery(pattern, options = {}) {
    return pattern.split(OR_TOKEN).map((item) => {
      let query = item.trim().split(SPACE_RE).filter((item2) => item2 && !!item2.trim());
      let results = [];
      for (let i = 0, len = query.length; i < len; i += 1) {
        const queryItem = query[i];
        let found = false;
        let idx = -1;
        while (!found && ++idx < searchersLen) {
          const searcher = searchers[idx];
          let token = searcher.isMultiMatch(queryItem);
          if (token) {
            results.push(new searcher(token, options));
            found = true;
          }
        }
        if (found) {
          continue;
        }
        idx = -1;
        while (++idx < searchersLen) {
          const searcher = searchers[idx];
          let token = searcher.isSingleMatch(queryItem);
          if (token) {
            results.push(new searcher(token, options));
            break;
          }
        }
      }
      return results;
    });
  }
  const MultiMatchSet = /* @__PURE__ */ new Set([FuzzyMatch.type, IncludeMatch.type]);
  class ExtendedSearch {
    constructor(pattern, {
      isCaseSensitive = Config.isCaseSensitive,
      ignoreDiacritics = Config.ignoreDiacritics,
      includeMatches = Config.includeMatches,
      minMatchCharLength = Config.minMatchCharLength,
      ignoreLocation = Config.ignoreLocation,
      findAllMatches = Config.findAllMatches,
      location = Config.location,
      threshold = Config.threshold,
      distance = Config.distance
    } = {}) {
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
      if (!query) {
        return {
          isMatch: false,
          score: 1
        };
      }
      const { includeMatches, isCaseSensitive, ignoreDiacritics } = this.options;
      text = isCaseSensitive ? text : text.toLowerCase();
      text = ignoreDiacritics ? stripDiacritics(text) : text;
      let numMatches = 0;
      let allIndices = [];
      let totalScore = 0;
      for (let i = 0, qLen = query.length; i < qLen; i += 1) {
        const searchers2 = query[i];
        allIndices.length = 0;
        numMatches = 0;
        for (let j = 0, pLen = searchers2.length; j < pLen; j += 1) {
          const searcher = searchers2[j];
          const { isMatch, indices, score } = searcher.search(text);
          if (isMatch) {
            numMatches += 1;
            totalScore += score;
            if (includeMatches) {
              const type = searcher.constructor.type;
              if (MultiMatchSet.has(type)) {
                allIndices = [...allIndices, ...indices];
              } else {
                allIndices.push(indices);
              }
            }
          } else {
            totalScore = 0;
            numMatches = 0;
            allIndices.length = 0;
            break;
          }
        }
        if (numMatches) {
          let result = {
            isMatch: true,
            score: totalScore / numMatches
          };
          if (includeMatches) {
            result.indices = allIndices;
          }
          return result;
        }
      }
      return {
        isMatch: false,
        score: 1
      };
    }
  }
  const registeredSearchers = [];
  function register(...args) {
    registeredSearchers.push(...args);
  }
  function createSearcher(pattern, options) {
    for (let i = 0, len = registeredSearchers.length; i < len; i += 1) {
      let searcherClass = registeredSearchers[i];
      if (searcherClass.condition(pattern, options)) {
        return new searcherClass(pattern, options);
      }
    }
    return new BitapSearch(pattern, options);
  }
  const LogicalOperator = {
    AND: "$and",
    OR: "$or"
  };
  const KeyType = {
    PATH: "$path",
    PATTERN: "$val"
  };
  const isExpression = (query) => !!(query[LogicalOperator.AND] || query[LogicalOperator.OR]);
  const isPath = (query) => !!query[KeyType.PATH];
  const isLeaf = (query) => !isArray(query) && isObject(query) && !isExpression(query);
  const convertToExplicit = (query) => ({
    [LogicalOperator.AND]: Object.keys(query).map((key) => ({
      [key]: query[key]
    }))
  });
  function parse(query, options, { auto = true } = {}) {
    const next = (query2) => {
      let keys = Object.keys(query2);
      const isQueryPath = isPath(query2);
      if (!isQueryPath && keys.length > 1 && !isExpression(query2)) {
        return next(convertToExplicit(query2));
      }
      if (isLeaf(query2)) {
        const key = isQueryPath ? query2[KeyType.PATH] : keys[0];
        const pattern = isQueryPath ? query2[KeyType.PATTERN] : query2[key];
        if (!isString(pattern)) {
          throw new Error(LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY(key));
        }
        const obj = {
          keyId: createKeyId(key),
          pattern
        };
        if (auto) {
          obj.searcher = createSearcher(pattern, options);
        }
        return obj;
      }
      let node = {
        children: [],
        operator: keys[0]
      };
      keys.forEach((key) => {
        const value = query2[key];
        if (isArray(value)) {
          value.forEach((item) => {
            node.children.push(next(item));
          });
        }
      });
      return node;
    };
    if (!isExpression(query)) {
      query = convertToExplicit(query);
    }
    return next(query);
  }
  function computeScore(results, { ignoreFieldNorm = Config.ignoreFieldNorm }) {
    results.forEach((result) => {
      let totalScore = 1;
      result.matches.forEach(({ key, norm: norm2, score }) => {
        const weight = key ? key.weight : null;
        totalScore *= Math.pow(
          score === 0 && weight ? Number.EPSILON : score,
          (weight || 1) * (ignoreFieldNorm ? 1 : norm2)
        );
      });
      result.score = totalScore;
    });
  }
  function transformMatches(result, data) {
    const matches = result.matches;
    data.matches = [];
    if (!isDefined(matches)) {
      return;
    }
    matches.forEach((match) => {
      if (!isDefined(match.indices) || !match.indices.length) {
        return;
      }
      const { indices, value } = match;
      let obj = {
        indices,
        value
      };
      if (match.key) {
        obj.key = match.key.src;
      }
      if (match.idx > -1) {
        obj.refIndex = match.idx;
      }
      data.matches.push(obj);
    });
  }
  function transformScore(result, data) {
    data.score = result.score;
  }
  function format(results, docs, {
    includeMatches = Config.includeMatches,
    includeScore = Config.includeScore
  } = {}) {
    const transformers = [];
    if (includeMatches) transformers.push(transformMatches);
    if (includeScore) transformers.push(transformScore);
    return results.map((result) => {
      const { idx } = result;
      const data = {
        item: docs[idx],
        refIndex: idx
      };
      if (transformers.length) {
        transformers.forEach((transformer) => {
          transformer(result, data);
        });
      }
      return data;
    });
  }
  class Fuse {
    constructor(docs, options = {}, index) {
      this.options = { ...Config, ...options };
      if (this.options.useExtendedSearch && false) ;
      this._keyStore = new KeyStore(this.options.keys);
      this.setCollection(docs, index);
    }
    setCollection(docs, index) {
      this._docs = docs;
      if (index && !(index instanceof FuseIndex)) {
        throw new Error(INCORRECT_INDEX_TYPE);
      }
      this._myIndex = index || createIndex(this.options.keys, this._docs, {
        getFn: this.options.getFn,
        fieldNormWeight: this.options.fieldNormWeight
      });
    }
    add(doc) {
      if (!isDefined(doc)) {
        return;
      }
      this._docs.push(doc);
      this._myIndex.add(doc);
    }
    remove(predicate = () => false) {
      const results = [];
      for (let i = 0, len = this._docs.length; i < len; i += 1) {
        const doc = this._docs[i];
        if (predicate(doc, i)) {
          this.removeAt(i);
          i -= 1;
          len -= 1;
          results.push(doc);
        }
      }
      return results;
    }
    removeAt(idx) {
      this._docs.splice(idx, 1);
      this._myIndex.removeAt(idx);
    }
    getIndex() {
      return this._myIndex;
    }
    search(query, { limit = -1 } = {}) {
      const {
        includeMatches,
        includeScore,
        shouldSort,
        sortFn,
        ignoreFieldNorm
      } = this.options;
      let results = isString(query) ? isString(this._docs[0]) ? this._searchStringList(query) : this._searchObjectList(query) : this._searchLogical(query);
      computeScore(results, { ignoreFieldNorm });
      if (shouldSort) {
        results.sort(sortFn);
      }
      if (isNumber(limit) && limit > -1) {
        results = results.slice(0, limit);
      }
      return format(results, this._docs, {
        includeMatches,
        includeScore
      });
    }
    _searchStringList(query) {
      const searcher = createSearcher(query, this.options);
      const { records } = this._myIndex;
      const results = [];
      records.forEach(({ v: text, i: idx, n: norm2 }) => {
        if (!isDefined(text)) {
          return;
        }
        const { isMatch, score, indices } = searcher.searchIn(text);
        if (isMatch) {
          results.push({
            item: text,
            idx,
            matches: [{ score, value: text, norm: norm2, indices }]
          });
        }
      });
      return results;
    }
    _searchLogical(query) {
      const expression = parse(query, this.options);
      const evaluate = (node, item, idx) => {
        if (!node.children) {
          const { keyId, searcher } = node;
          const matches = this._findMatches({
            key: this._keyStore.get(keyId),
            value: this._myIndex.getValueForItemAtKeyId(item, keyId),
            searcher
          });
          if (matches && matches.length) {
            return [
              {
                idx,
                item,
                matches
              }
            ];
          }
          return [];
        }
        const res = [];
        for (let i = 0, len = node.children.length; i < len; i += 1) {
          const child = node.children[i];
          const result = evaluate(child, item, idx);
          if (result.length) {
            res.push(...result);
          } else if (node.operator === LogicalOperator.AND) {
            return [];
          }
        }
        return res;
      };
      const records = this._myIndex.records;
      const resultMap = {};
      const results = [];
      records.forEach(({ $: item, i: idx }) => {
        if (isDefined(item)) {
          let expResults = evaluate(expression, item, idx);
          if (expResults.length) {
            if (!resultMap[idx]) {
              resultMap[idx] = { idx, item, matches: [] };
              results.push(resultMap[idx]);
            }
            expResults.forEach(({ matches }) => {
              resultMap[idx].matches.push(...matches);
            });
          }
        }
      });
      return results;
    }
    _searchObjectList(query) {
      const searcher = createSearcher(query, this.options);
      const { keys, records } = this._myIndex;
      const results = [];
      records.forEach(({ $: item, i: idx }) => {
        if (!isDefined(item)) {
          return;
        }
        let matches = [];
        keys.forEach((key, keyIndex) => {
          matches.push(
            ...this._findMatches({
              key,
              value: item[keyIndex],
              searcher
            })
          );
        });
        if (matches.length) {
          results.push({
            idx,
            item,
            matches
          });
        }
      });
      return results;
    }
    _findMatches({ key, value, searcher }) {
      if (!isDefined(value)) {
        return [];
      }
      let matches = [];
      if (isArray(value)) {
        value.forEach(({ v: text, i: idx, n: norm2 }) => {
          if (!isDefined(text)) {
            return;
          }
          const { isMatch, score, indices } = searcher.searchIn(text);
          if (isMatch) {
            matches.push({
              score,
              key,
              value: text,
              idx,
              norm: norm2,
              indices
            });
          }
        });
      } else {
        const { v: text, n: norm2 } = value;
        const { isMatch, score, indices } = searcher.searchIn(text);
        if (isMatch) {
          matches.push({ score, key, value: text, norm: norm2, indices });
        }
      }
      return matches;
    }
  }
  Fuse.version = "7.1.0";
  Fuse.createIndex = createIndex;
  Fuse.parseIndex = parseIndex;
  Fuse.config = Config;
  {
    Fuse.parseQuery = parse;
  }
  {
    register(ExtendedSearch);
  }
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
      init() {
        this.input = this.$root.querySelector(dataKey("input"));
        this.list = this.$root.querySelector("[role=listbox]");
        this.noRecords = this.$root.querySelector("[role=status]");
        this.refreshItems();
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
            if (!Number.isNaN(index)) {
              this.select(index);
            }
          },
          ["@mousemove"]: (e) => {
            if (this.lastInteraction === "keyboard" && e.movementX === 0 && e.movementY === 0) {
              return;
            }
            this.lastInteraction = "mouse";
            const item = e.target.closest("[role=option]");
            if (!item) return;
            const index = Number(item.dataset.index);
            if (Number.isNaN(index)) return;
            if (this.isDisabled(this.filteredItems[index])) return;
            if (this.index !== index) {
              this.index = index;
            }
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
      refreshItems() {
        this.items = Array.from(
          this.list.querySelectorAll("[role=option]")
        ).map((item) => {
          item.hidden = true;
          if (item?.firstElementChild?.disabled) {
            item.setAttribute("aria-disabled", "true");
          }
          return {
            title: normalize(item.querySelector("[data-item-content]")?.textContent, { removeSpaces: true }),
            el: item.firstElementChild,
            li: item
          };
        });
        const fuseIndex = Fuse.createIndex(["title"], this.items);
        this.fuse = new Fuse(
          this.items,
          {
            ignoreDiacritics: true,
            includeScore: true,
            threshold: 0.1,
            keys: ["title"],
            ...fuseOptions
          },
          fuseIndex
        );
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
        if (query) {
          results = this.fuse.search(query);
        } else if (!hideEmpty) {
          results = this.items.map((item) => ({ item }));
        }
        this.filteredItems = results.map((result, index) => {
          const li = result.item.li;
          li.hidden = false;
          li.dataset.index = index;
          fragment.appendChild(li);
          return result.item;
        });
        this.list.appendChild(fragment);
        this.$dispatch("listbox-items-changed", {
          list: this.list,
          items: this.items,
          filteredItems: this.filteredItems
        });
        if (this.filteredItems.length && query.length) {
          this.$nextTick(() => {
            this.index = 0;
          });
        }
        this.toggleNoRecords();
      },
      isDisabled(item) {
        return !!item?.el?.hasAttribute("disabled");
      },
      prev() {
        if (this.filteredItems.length === 0) return;
        let index = this.index === null ? this.filteredItems.length - 1 : (this.index - 1 + this.filteredItems.length) % this.filteredItems.length;
        for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) {
          index = (index - 1 + this.filteredItems.length) % this.filteredItems.length;
        }
        if (this.isDisabled(this.filteredItems[index])) return;
        this.index = index;
      },
      next() {
        if (this.filteredItems.length === 0) return;
        let index = this.index === null ? 0 : (this.index + 1) % this.filteredItems.length;
        for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) {
          index = (index + 1) % this.filteredItems.length;
        }
        if (this.isDisabled(this.filteredItems[index])) return;
        this.index = index;
      },
      first() {
        if (this.filteredItems.length === 0) return;
        let index = 0;
        while (index < this.filteredItems.length && this.isDisabled(this.filteredItems[index])) {
          index++;
        }
        if (index >= this.filteredItems.length) return;
        this.index = index;
      },
      last() {
        if (this.filteredItems.length === 0) return;
        let index = this.filteredItems.length - 1;
        while (index >= 0 && this.isDisabled(this.filteredItems[index])) {
          index--;
        }
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
        if (clearOnSelect) {
          setFieldValue(this.input, "");
        }
        this.$dispatch("listbox-item-selected", { index, item, button });
      },
      setActive(index) {
        this.clearActive();
        const item = this.filteredItems[index];
        if (!item) return;
        item.el.dataset.active = "true";
        item.li.setAttribute("aria-selected", "true");
        if (item.li.hasAttribute("id")) {
          this.list.setAttribute("aria-activedescendant", item.li.getAttribute("id"));
        }
        item.li.scrollIntoView({
          block: "nearest"
        });
        this.$dispatch("listbox-active-changed", { index, item });
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
        if (this.filteredItems.length === 0 && (this.input?.value && !hideEmpty)) {
          this.noRecords.removeAttribute("hidden");
          this.list.setAttribute("hidden", "");
        } else {
          this.noRecords.setAttribute("hidden", "");
          this.list.removeAttribute("hidden");
        }
      }
    };
  }
  const __vite_glob_0_26 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    listbox
  }, Symbol.toStringTag, { value: "Module" }));
  function autocomplete(options = {}) {
    const _popover = popover({ mode: "manual", position: "bottom", align: "start" });
    const _listbox = listbox({ hideEmpty: true, clearOnSelect: false, ...options });
    return {
      ..._popover,
      ..._listbox,
      init() {
        _popover.init.call(this);
        _listbox.init.call(this);
        bind(this.input, {
          ["@blur"]() {
            this.close();
          },
          ["@keydown.escape.prevent"]() {
            this.close();
          }
        });
        bind(this.$root, {
          ["@listbox-item-selected"]({ detail }) {
            setFieldValue(this.input, detail.item.title);
            this.close();
          }
        });
      },
      search() {
        _listbox.search.call(this);
        if (this.filteredItems.length) {
          this.open();
        } else {
          this.close();
        }
      },
      open() {
        this.popoverElement.style.width = `${this.input.offsetWidth}px`;
        _popover.open.call(this, false);
      },
      close() {
        _popover.close.call(this);
        this.clear();
      }
    };
  }
  const __vite_glob_0_4 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    autocomplete
  }, Symbol.toStringTag, { value: "Module" }));
  function badge() {
    return {
      ...dismissible("fade")
    };
  }
  const __vite_glob_0_5 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    badge
  }, Symbol.toStringTag, { value: "Module" }));
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
        const merged = { ...options, ...this.getDataOptions() };
        if (this.chart) {
          Object.assign(this.chart.config, merged);
          this.chart.update();
        } else {
          this.chart = new window.Chart(this.$el, merged);
        }
        this.$dispatch("rendered", { chart: this.chart });
      },
      destroy() {
        _loadable.destroy.call(this);
        this.chart?.destroy();
        this.chart = null;
      }
    };
  }
  const __vite_glob_0_6 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    chartjs
  }, Symbol.toStringTag, { value: "Module" }));
  function checkboxAll({ group = "" } = {}) {
    return {
      all: null,
      get checkboxes() {
        return Array.from(document.querySelectorAll(dataKey("checkbox-group", group)));
      },
      init() {
        this.all = this.$root.querySelector(dataKey("checkbox"));
        bind(this.all, {
          ["@change"]: () => this.toggleAll()
        });
        bind(this.checkboxes, {
          ["@change"]: () => this.updateState()
        });
      },
      toggleAll() {
        this.checkboxes.forEach((checkbox) => {
          checkbox.checked = !!this.all?.checked;
        });
      },
      updateState() {
        if (!this.all) return;
        const checkboxes = this.checkboxes;
        const total = checkboxes.length;
        const checked = checkboxes.filter((cb) => cb.checked).length;
        this.all.checked = total > 0 && checked === total;
        this.all.indeterminate = checked > 0 && checked < total;
      }
    };
  }
  const __vite_glob_0_7 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    checkboxAll
  }, Symbol.toStringTag, { value: "Module" }));
  function combobox({ value = null, multiple = false } = {}) {
    const _popover = popover({ mode: "manual", position: "bottom", align: "start" });
    const _listbox = listbox({ hideEmpty: false, clearOnSelect: !multiple });
    return {
      ..._popover,
      ..._listbox,
      value: value ?? (multiple ? [] : null),
      combobox: null,
      get selectedLabel() {
        if (multiple || this.value == null) return null;
        const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(this.value));
        return item ? item.el.querySelector("[data-item-content]")?.textContent?.trim() : null;
      },
      get selectedCount() {
        return this.items.filter((item) => this.isSelected(this.getElementValue(item.el))).length;
      },
      init() {
        _popover.init.call(this);
        _listbox.init.call(this);
        this.combobox = this.$root.querySelector(dataKey("combobox"));
        bind(this.combobox, {
          ["@click"]() {
            this.combobox.focus();
            this.toggle();
          },
          ["@keydown.enter.prevent"]() {
            if (this.opened) return;
            this.open();
          },
          ["@keydown.space.prevent"]() {
            if (this.opened) return;
            this.open();
          },
          ["@keydown.arrow-up.prevent"]: () => {
            if (this.opened) return;
            this.open();
          },
          ["@keydown.arrow-down.prevent"]: () => {
            if (this.opened) return;
            this.open();
          }
        });
        bind([this.combobox, this.popoverElement, this.input, this.list], {
          ["@keydown.escape.prevent"]() {
            this.closeAndFocus();
          }
        });
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
        this.popoverElement.style.width = `${this.combobox.offsetWidth}px`;
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
        if (!multiple) {
          return String(this.value ?? "") === String(v);
        }
        if (!Array.isArray(this.value) && this.value != null) {
          this.value = [this.value];
        }
        return this.value.map(String).includes(String(v));
      },
      pick(v) {
        if (multiple) {
          this.value = this.isSelected(v) ? this.value.filter((x) => String(x) !== String(v)) : [...this.value, v];
        } else {
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
  const __vite_glob_0_8 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    combobox
  }, Symbol.toStringTag, { value: "Module" }));
  function composer({ submit = false, placeholder = false } = {}) {
    return {
      value: null,
      init() {
        bind(this.$el, {
          "x-modelable": "value"
        });
        const modes = !submit ? [] : Array.isArray(submit) ? submit : [submit];
        const labelFor = this.$el.parentElement?.closest(dataKey("field"))?.querySelector(dataKey("label"))?.getAttribute("for") ?? null;
        bind(this.$el.querySelector(dataKey("control")), {
          "x-model": "value",
          ...labelFor && { id: labelFor },
          ...placeholder && { placeholder },
          ...modes.length && {
            ["@keydown"](e) {
              const shouldSubmit = modes.some((mode) => {
                switch (mode) {
                  case "enter":
                    return e.key === "Enter" && !e.shiftKey && !e.ctrlKey && !e.metaKey;
                  case "ctrl+enter":
                    return e.key === "Enter" && (e.ctrlKey || e.metaKey);
                  default:
                    return false;
                }
              });
              if (!shouldSubmit) return;
              e.preventDefault();
              this.$root?.closest("form")?.requestSubmit();
            }
          }
        });
      }
    };
  }
  const __vite_glob_0_9 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    composer
  }, Symbol.toStringTag, { value: "Module" }));
  function copy(targetId = null, content = null) {
    return {
      copied: false,
      timeout: null,
      findTarget() {
        if (targetId) {
          const target = document.getElementById(targetId);
          if (target) {
            return target;
          }
        }
        const controlKey = dataKey("control");
        return this.$el.closest(dataKey("field-control"))?.querySelector(controlKey) ?? this.$el.previousElementSibling?.querySelector(controlKey) ?? this.$el.parentElement?.previousElementSibling?.querySelector(controlKey) ?? null;
      },
      init() {
        const target = this.findTarget();
        if (!target && !content) {
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
            const text = content ?? ("value" in target ? target.value : target.innerText);
            await navigator.clipboard.writeText(text);
            target?.dispatchEvent(new Event("copied", { bubbles: true }));
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
  const __vite_glob_0_10 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    copy
  }, Symbol.toStringTag, { value: "Module" }));
  function creditCard(options = {}) {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      options: null,
      init() {
        _toggleable.init.call(this);
        this.options = {
          opened: true,
          types: {},
          holderName: null,
          number: null,
          type: null,
          expirationDate: null,
          cvv: null,
          ...options
        };
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
            return {
              "rotate-y-180": !this.isOpened()
            };
          }
        });
      },
      get typeOptions() {
        return this.options.types[this.options.type] ? this.options.types[this.options.type] : this.options.types.unknown;
      },
      update(options2 = {}) {
        this.options = { ...this.options, ...options2 };
        if ("opened" in options2) {
          this.opened = this.options.opened;
        }
      },
      flip(isBack = false) {
        if (isBack) {
          this.close();
        } else {
          this.open();
        }
      }
    };
  }
  const __vite_glob_0_11 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    creditCard
  }, Symbol.toStringTag, { value: "Module" }));
  function disclosureGroup({ exclusive = false } = {}) {
    return {
      observer: null,
      init() {
        const items = this.$root.querySelectorAll(dataKey("disclosure-item"));
        const observe = () => {
          items.forEach((item) => {
            this.observer.observe(item, { attributeFilter: ["data-open"] });
          });
        };
        this.observer = new MutationObserver((records) => {
          if (exclusive) {
            const opened = new Set(
              records.filter((record) => record.target.hasAttribute("data-open")).map((record) => record.target)
            );
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
  const __vite_glob_0_13 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    disclosureGroup
  }, Symbol.toStringTag, { value: "Module" }));
  function disclosure() {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      observer: null,
      init() {
        _toggleable.init.call(this, this.$root.hasAttribute("data-open"));
        const panel = this.$root.querySelector(":scope > button + *");
        if (panel && !panel.id) {
          panel.id = generateId("disclosure");
        }
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
  const __vite_glob_0_14 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    disclosure
  }, Symbol.toStringTag, { value: "Module" }));
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
        this.chart ??= window.echarts.init(this.$el);
        this.chart.setOption({ ...options, ...this.getDataOptions() });
        this.$dispatch("rendered", { chart: this.chart });
      },
      destroy() {
        _loadable.destroy.call(this);
        this.chart?.dispose();
        this.chart = null;
      }
    };
  }
  const __vite_glob_0_16 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    echarts
  }, Symbol.toStringTag, { value: "Module" }));
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
        if (this.url && auto !== false) {
          this.fetch();
        }
        if (!this.url && this.data) {
          this.complete();
        }
      },
      async fetch(url2 = null, options2 = {}, silent = false) {
        const _url = url2 || this.url;
        const _options = {
          ...this.options ?? {},
          ...options2,
          headers: { ...this.options?.headers ?? {}, ...options2.headers ?? {} }
        };
        this.url = _url;
        this.options = _options;
        if (!_url) {
          return;
        }
        this._controller?.abort();
        const controller = new AbortController();
        this._controller = controller;
        this.load(async () => {
          this.response = await window.fetch(_url, { ..._options, signal: controller.signal });
          if (!this.response.ok) {
            throw new Error(this.response.statusText);
          }
          this.data = _options.responseType ? await this.response[_options.responseType]() : this.response;
        }, silent);
      },
      reload() {
        return this.fetch();
      },
      update(url2 = null, options2 = {}) {
        return this.fetch(url2, options2, true);
      },
      destroy() {
        _loadable.destroy.call(this);
        this._controller?.abort();
        this._controller = null;
      }
    };
  }
  const __vite_glob_0_17 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    fetchable
  }, Symbol.toStringTag, { value: "Module" }));
  function form({
    focusError = null,
    toast: toast2 = null,
    errorMessage = null,
    successMessage = null
  } = {}) {
    return {
      livewireCommitCleanup: null,
      init() {
        if (window.Livewire) {
          this.watchLivewireCommits();
        } else if (focusError) {
          this.focusFirstInvalidField();
        }
      },
      watchLivewireCommits() {
        const offCommit = window.Livewire.hook("commit", ({ component, succeed }) => {
          succeed(({ snapshot }) => {
            if (!this.$el?.isConnected) return;
            if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return;
            const id = this.$el?.getAttribute("id") ?? component?.el.getAttribute("wire:id") ?? void 0;
            const hasErrors = Object.keys(snapshot?.memo?.errors ?? {}).length > 0 || !!this.$el.querySelector('[data-invalid], [aria-invalid="true"]');
            if (hasErrors) {
              if ((toast2 === true || toast2 === "error") && errorMessage) {
                this.$tallkit.toast().error({ message: errorMessage, id, duration: 3e3 });
              }
              if (focusError) {
                this.focusFirstInvalidField();
              }
              return;
            }
            if ((toast2 === true || toast2 === "success") && successMessage) {
              this.$tallkit.toast().success({ message: successMessage, id, duration: 3e3 });
              return;
            }
          });
        });
        this.livewireCommitCleanup = typeof offCommit === "function" ? offCommit : () => {
        };
      },
      focusFirstInvalidField() {
        const field = this.$el.querySelector('[data-invalid], [aria-invalid="true"]');
        if (!(field instanceof HTMLElement)) return;
        field.scrollIntoView({ behavior: "smooth", block: "center" });
        field.focus({ preventScroll: true });
      },
      destroy() {
        this.livewireCommitCleanup?.();
      }
    };
  }
  const __vite_glob_0_18 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    form
  }, Symbol.toStringTag, { value: "Module" }));
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
        this.chart?.destroy?.();
        this.chart = new window.frappe.Chart(this.$el, { ...options, ...this.getDataOptions() });
        this.$dispatch("rendered", { chart: this.chart });
      },
      destroy() {
        _loadable.destroy.call(this);
        this.chart?.destroy?.();
        this.chart = null;
      }
    };
  }
  const __vite_glob_0_19 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    frappeCharts
  }, Symbol.toStringTag, { value: "Module" }));
  function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
    const _loadable = loadable();
    return {
      ..._loadable,
      ...dataOptions(),
      fullCalendar: null,
      init() {
        const baseUrl = "https://cdn.jsdelivr.net/npm/fullcalendar@7";
        this.load(() => loadRemoteAssets(() => !!window.FullCalendar, [
          `${baseUrl}/all/global.min.js`,
          locale && locale !== "en" ? `${baseUrl}/locales/${String(locale).replace("_", "-").toLowerCase()}/global.min.js` : `${baseUrl}/locales-all/global.min.js`,
          `${baseUrl}/themes/${theme ?? "monarch"}/global.js`
        ], [
          `${baseUrl}/skeleton.css`,
          `${baseUrl}/themes/${theme ?? "monarch"}/theme.css`,
          `${baseUrl}/themes/${theme ?? "monarch"}/palettes/${palette ?? "blue"}.css`
        ]));
      },
      render() {
        this.fullCalendar?.destroy();
        this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
          locale,
          ...options,
          ...this.getDataOptions()
        });
        this.fullCalendar.render();
        this.$dispatch("rendered", { fullCalendar: this.fullCalendar });
      },
      destroy() {
        _loadable.destroy.call(this);
        this.fullCalendar?.destroy();
        this.fullCalendar = null;
      }
    };
  }
  const __vite_glob_0_20 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    fullCalendar
  }, Symbol.toStringTag, { value: "Module" }));
  function header() {
    return {
      ...sticky()
    };
  }
  const __vite_glob_0_21 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    header
  }, Symbol.toStringTag, { value: "Module" }));
  function highlightjs() {
    return {
      ...loadable(),
      init() {
        this.load(() => loadRemoteAssets(
          () => !!window.hljs,
          "https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js",
          "https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css"
        ));
      },
      render(code, language = null) {
        try {
          return language ? window.hljs.highlight(code, { language }).value : window.hljs.highlightAuto(code).value;
        } catch (e) {
          this.fail(e);
          return escapeHtml(code) ?? "";
        }
      }
    };
  }
  const __vite_glob_0_22 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    highlightjs
  }, Symbol.toStringTag, { value: "Module" }));
  function inputClearable() {
    return {
      hasValue: false,
      init() {
        const input = findFieldInput(this.$el);
        if (!input) {
          return;
        }
        this.hasValue = Boolean(input.value);
        bind(input, {
          ["@input"]() {
            this.hasValue = Boolean(input.value);
          }
        });
        bind(this.$el, {
          ["x-show"]() {
            return this.hasValue;
          },
          ["@click"]() {
            setFieldValue(input, "");
            input.dispatchEvent(new Event("cleared", { bubbles: true }));
            input.focus();
          }
        });
      }
    };
  }
  const __vite_glob_0_23 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    inputClearable
  }, Symbol.toStringTag, { value: "Module" }));
  function inputViewable() {
    return {
      viewed: false,
      inputObserver: null,
      originalType: "password",
      init() {
        const input = findFieldInput(this.$el);
        if (!input) {
          return;
        }
        if (input.type) {
          this.originalType = input.type;
        }
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
  const __vite_glob_0_24 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    inputViewable
  }, Symbol.toStringTag, { value: "Module" }));
  function label() {
    return {
      init() {
        if (this.$el.tagName.toLowerCase() === "label" && this.$el.hasAttribute("for") && !!document.getElementById(this.$el.getAttribute("for"))) {
          return;
        }
        let control = this.$el.parentElement?.closest(dataKey("field"))?.querySelector(dataKey("control"));
        if (control && !control.matches('input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]')) {
          control = control.querySelector('input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]');
        }
        if (!control) {
          return;
        }
        bind(this.$el, {
          ["@click"]() {
            const tag = control.tagName.toLowerCase();
            const type = control.getAttribute("type")?.toLowerCase();
            const isEditable = control.hasAttribute("contenteditable") || control.getAttribute("role") === "textbox";
            const isReadOnly = control.hasAttribute("readonly") || control.getAttribute("aria-readonly") === "true";
            const isDisabled = control.disabled;
            if (type === "checkbox") {
              if (!isDisabled && !isReadOnly) {
                setFieldChecked(control, !control.checked);
              }
              return;
            }
            if (type === "radio") {
              if (!isDisabled && !isReadOnly && !control.checked) {
                setFieldChecked(control, true);
              }
              return;
            }
            if ((isEditable || ["input", "select", "textarea"].includes(tag)) && typeof control.focus === "function" && !isDisabled) {
              control.focus();
            }
          }
        });
      }
    };
  }
  const __vite_glob_0_25 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    label
  }, Symbol.toStringTag, { value: "Module" }));
  function menuCheckbox(checked) {
    return {
      checked,
      get isControlled() {
        return this.value !== void 0;
      },
      get isArray() {
        return Array.isArray(this.value);
      },
      get isChecked() {
        if (!this.isControlled) {
          return this.checked;
        }
        if (this.isArray) {
          return this.value.some((v) => v == this.$root.value);
        }
        return this.value == this.$root.value;
      },
      init() {
        bind(this.$el, {
          ["@click"]: () => this.toggle(),
          [":data-checked"]: () => this.isChecked,
          [":aria-checked"]: () => this.isChecked
        });
      },
      toggle() {
        if (!this.isControlled) {
          this.checked = !this.checked;
          return;
        }
        if (this.isArray) {
          this.value = this.isChecked ? this.value.filter((v) => v != this.$root.value) : [...this.value, this.$root.value];
          return;
        }
        this.value = this.isChecked ? null : this.$root.value;
      }
    };
  }
  const __vite_glob_0_28 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    menuCheckbox
  }, Symbol.toStringTag, { value: "Module" }));
  function menuRadio(checked) {
    return {
      checked,
      get isControlled() {
        return this.value !== void 0;
      },
      get isChecked() {
        return this.isControlled ? this.value == this.$root.value : this.checked;
      },
      init() {
        bind(this.$el, {
          ["@click"]: () => this.toggle(),
          [":data-checked"]: () => this.isChecked,
          [":aria-checked"]: () => this.isChecked
        });
      },
      toggle() {
        if (this.isControlled) {
          this.value = this.$root.value;
        } else {
          this.checked = !this.checked;
        }
      }
    };
  }
  const __vite_glob_0_29 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    menuRadio
  }, Symbol.toStringTag, { value: "Module" }));
  function menu() {
    return {
      init() {
        const items = Array.from(this.$el.querySelectorAll(dataKey("menu-item"))).filter((item) => item.closest(dataKey("menu")) === this.$el);
        bind(items, {
          ["@mouseenter"]() {
            if (this.$el.disabled) {
              return;
            }
            this.$el.setAttribute("data-active", "");
          },
          ["@mouseleave"]() {
            if (this.$el.disabled) {
              return;
            }
            this.$el.removeAttribute("data-active");
          },
          ["@focus"]() {
            if (this.$el.disabled) {
              return;
            }
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
        if (direction === "first") {
          index = 0;
        } else if (direction === "last") {
          index = enabled.length - 1;
        } else if (currentIndex === -1) {
          index = direction === 1 ? 0 : enabled.length - 1;
        } else {
          index = (currentIndex + direction + enabled.length) % enabled.length;
        }
        enabled[index].focus();
      }
    };
  }
  const __vite_glob_0_30 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    menu
  }, Symbol.toStringTag, { value: "Module" }));
  function modalTrigger({ name = null, shortcut = null } = {}) {
    return {
      init() {
        bind(this.$el, {
          ["@click"]() {
            if (this.$el.querySelector("button[disabled]")) {
              return;
            }
            this.$dispatch("modal-show", { name });
          }
        });
        if (shortcut) {
          bindShortcut(this.$el, shortcut, () => this.$dispatch("modal-show", { name }));
        }
      }
    };
  }
  const __vite_glob_0_31 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    modalTrigger
  }, Symbol.toStringTag, { value: "Module" }));
  function modal({ name = null, dismissible: dismissible2 = null, persist = null, shortcut = null } = {}) {
    return {
      init() {
        const dialog = this.$el;
        bind(dialog.querySelectorAll(`${dataKey("modal-close")},${dataKey("modal-auto-close")}`), {
          ["@click"]() {
            dialog.close();
          }
        });
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
        const handleCloseAttempt = (event) => {
          event.preventDefault();
          if (persist) {
            const persistAnimation = typeof persist === "string" ? persist : "tilt-shaking";
            dialog.classList.remove(persistAnimation);
            dialog.focus();
            this.$nextTick(() => dialog.classList.add(persistAnimation));
            return;
          }
          if (dismissible2 !== false && (event.target === dialog || event.target.getAttribute("tabindex") === "0")) {
            dialog.close();
          }
        };
        bind(dialog, {
          ["@toggle"](event) {
            if (event.newState === "open") {
              dialog.querySelector('[tabindex="0"]')?.focus();
              this.$dispatch("opened", event);
            }
            if (event.newState === "closed") {
              this.$dispatch("closed", event);
            }
          },
          ["@click"](event) {
            handleCloseAttempt(event);
          },
          ["@keydown.escape.prevent"](event) {
            handleCloseAttempt(event);
          }
        });
        if (shortcut) {
          bindShortcut(dialog, shortcut, () => this.$dispatch("modal-show", { name }));
        }
      },
      show() {
        this.$dispatch("modal-show", { name });
      },
      close() {
        this.$dispatch("modal-close", { name });
      }
    };
  }
  const __vite_glob_0_32 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    modal
  }, Symbol.toStringTag, { value: "Module" }));
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
      move() {
        requestAnimationFrame(() => {
          const indicator = this.$el;
          const nav = indicator.closest(dataKey("nav")) ?? indicator.parentElement.previousElementSibling;
          const link = nav?.querySelector("a[data-current]");
          if (!link) return;
          const indicatorRect = indicator.getBoundingClientRect();
          const linkRect = link.getBoundingClientRect();
          const x = link.offsetLeft + nav.offsetLeft;
          const y = link.offsetTop + nav.offsetTop;
          if (linkRect.width <= 0 || linkRect.height <= 0 || linkRect.top <= 0 || linkRect.left <= 0) {
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
  const __vite_glob_0_33 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    navIndicator
  }, Symbol.toStringTag, { value: "Module" }));
  function notificationItem() {
    return {
      ...dismissible("collapse")
    };
  }
  const __vite_glob_0_34 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    notificationItem
  }, Symbol.toStringTag, { value: "Module" }));
  function notification({ channel = null } = {}) {
    return {
      init() {
        bind(this.$el.querySelectorAll(dataKey("notification-mark-all")), {
          ["@click"]() {
            this.$el.closest("[role=tabpanel]").querySelectorAll(dataKey("notification-item")).forEach((el) => el.dispatchEvent(new CustomEvent("dismiss")));
          }
        });
        if (!channel || !window.Echo || !this.$wire) {
          return;
        }
        window.Echo.private(channel).notification(() => {
          this.$wire.$refresh();
        });
      },
      destroy() {
        if (channel && window.Echo) {
          window.Echo.leave(channel);
        }
      }
    };
  }
  const __vite_glob_0_35 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    notification
  }, Symbol.toStringTag, { value: "Module" }));
  function otp(submit) {
    return {
      value: "",
      inputs: [],
      _syncing: false,
      init() {
        this.inputs = Array.from(this.$root.querySelectorAll("input[data-mode]"));
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
          ["@blur"]: () => this.$dispatch("otp-blur", { input, index }),
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
          this.$dispatch("otp-focus", { input, index });
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
        this.$dispatch("otp-paste", { pasted, index });
      },
      handleInput(input, index, inputs) {
        if (this._syncing) return;
        const mode = input.dataset.mode;
        const filtered = filterValue(input.value, mode);
        if (filtered.length > 1) {
          spreadValue(filtered, index, inputs);
        } else {
          input.value = filtered;
          if (filtered) inputs[index + 1]?.focus();
        }
        this.updateModel();
      },
      handleKeydown(e, input, _index, _inputs) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const mode = input.dataset.mode;
        if (!isValidKey(e.key, mode)) {
          e.preventDefault();
        }
      },
      handleBackspace(input, index, inputs) {
        if (input.value) {
          this._syncing = true;
          setFieldValue(input, "");
          this._syncing = false;
        } else {
          inputs[index - 1]?.select();
        }
        this.updateModel();
      },
      syncFromModel(val = this.value) {
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
          if (submit === "auto") {
            this.$root.closest("form")?.requestSubmit();
          } else if (submit && window.Livewire) {
            window.Livewire.dispatch(submit, this.value);
          }
        } else {
          this.$dispatch("otp-incomplete", { value: this.value });
        }
        if (filled === 0) {
          this.$dispatch("otp-clear");
        }
      }
    };
  }
  function filterValue(value, mode = "numeric") {
    const map = {
      numeric: /[0-9]/g,
      alpha: /[A-Z]/g,
      alphanumeric: /[A-Z0-9]/g
    };
    return (value.toUpperCase().match(map[mode]) || []).join("");
  }
  function isValidKey(key, mode) {
    const control = ["Backspace", "Delete", "Tab", "ArrowLeft", "ArrowRight"];
    if (control.includes(key)) return true;
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
    const next = inputs[Math.min(start + chars.length, inputs.length - 1)];
    next?.focus();
  }
  const __vite_glob_0_36 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    otp
  }, Symbol.toStringTag, { value: "Module" }));
  function prettyPrintJson() {
    return {
      ...loadable(),
      init() {
        this.load(() => loadRemoteAssets(
          () => !!window.prettyPrintJson,
          "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js",
          "https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css"
        ));
      },
      render(data = null, options = {}) {
        try {
          return window.prettyPrintJson.toHtml(data, options);
        } catch (e) {
          this.fail(e);
          return "";
        }
      }
    };
  }
  const __vite_glob_0_38 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    prettyPrintJson
  }, Symbol.toStringTag, { value: "Module" }));
  function progress(percentage = null) {
    return {
      value: 0,
      init() {
        this.updateValue(percentage ?? 0);
      },
      updateValue(n) {
        const num = Number(n);
        if (Number.isNaN(num)) {
          return;
        }
        this.value = Math.max(0, Math.min(100, num));
      }
    };
  }
  const __vite_glob_0_39 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    progress
  }, Symbol.toStringTag, { value: "Module" }));
  function sidebar(name, sticky$1, stashable) {
    const _toggleable = toggleable();
    const _sticky = sticky();
    return {
      ..._toggleable,
      ..._sticky,
      init() {
        _toggleable.init.call(this);
        if (sticky$1) {
          _sticky.init.call(this);
        }
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
        }
      },
      open() {
        this.$el.setAttribute("data-show-stashed-sidebar", "");
        _toggleable.open.call(this);
      },
      close() {
        this.$el.removeAttribute("data-show-stashed-sidebar");
        _toggleable.close.call(this);
      },
      destroy() {
        if (sticky$1) {
          _sticky.destroy.call(this);
        }
      }
    };
  }
  const __vite_glob_0_40 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    sidebar
  }, Symbol.toStringTag, { value: "Module" }));
  function slider() {
    return {
      input: null,
      init() {
        this.input = this.$root.querySelector(dataKey("control"));
        this.$nextTick(() => this.updateRange());
        if (this.$wire) {
          const prop = getWireModelInfo(this.input);
          if (prop) {
            this.$wire.$watch(prop.name, () => this.updateRange());
          }
        }
        bind(this.input, {
          ["@input"]: () => this.updateRange()
        });
        bind(this.$root.querySelector(dataKey("slider-ticks")), {
          ["@click"]: (e) => {
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
              let value = parseInt(closestTick.getAttribute("data-value"));
              if (isNaN(value)) {
                value = parseInt(closestTick.textContent.trim());
              }
              if (!isNaN(value)) {
                this.setValue(value);
              }
            }
          }
        });
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
        this.input.style.setProperty("--range-percent", `${p}%`);
        this.input.classList.toggle("before:rounded-r-none", p < 50);
      }
    };
  }
  const __vite_glob_0_41 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    slider
  }, Symbol.toStringTag, { value: "Module" }));
  function submenu() {
    const _popover = popover({ mode: "manual", position: "right", align: "start" });
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
  const __vite_glob_0_43 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    submenu
  }, Symbol.toStringTag, { value: "Module" }));
  function tab({ selectFirst = null } = {}) {
    return {
      selected: null,
      get tabs() {
        return Array.from(this.$root.querySelectorAll('[role="tab"]')).filter((el) => !el.disabled);
      },
      init() {
        const selected = this.$root.querySelector("[data-selected]")?.dataset.name;
        if (selected || selectFirst && this.tabs.length) {
          this.$nextTick(() => {
            this.select(selected ?? this.tabs[0]?.dataset.name);
          });
        }
        bind(this.$root, {
          ["@keydown.arrow-right.prevent"](event) {
            this.focusTab(1, event.target);
          },
          ["@keydown.arrow-left.prevent"](event) {
            this.focusTab(-1, event.target);
          },
          ["@keydown.home.prevent"](event) {
            this.focusTab("first", event.target);
          },
          ["@keydown.end.prevent"](event) {
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
        const tabs = this.tabs;
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
  const __vite_glob_0_44 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    tab
  }, Symbol.toStringTag, { value: "Module" }));
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
        this.observer.observe(tbody, { childList: true, subtree: true });
      },
      destroy() {
        this.observer?.disconnect();
      },
      update() {
        this.rows = Array.from(this.$el.querySelector("table > tbody").querySelectorAll(':scope > tr[role="row"]')).map((tr) => {
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
            bind(selection, {
              ["@click"]() {
                this._updateRowState(row);
                this._syncSelect();
              }
            });
          }
          const unboundExpanded = Array.from(expanded).filter((el) => !this.boundElements.has(el));
          if (unboundExpanded.length) {
            unboundExpanded.forEach((el) => this.boundElements.add(el));
            bind(unboundExpanded, {
              ["@click"]() {
                row.el.dataset.expanded = row.el.dataset.expanded === "open" ? "close" : "open";
              }
            });
          }
          return row;
        });
        this.rows.forEach((row) => {
          if (row.selection) {
            setFieldChecked(row.selection, this.selectAllChecked || this.selectedIds.includes(row.id));
          }
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
        if (row.selection) {
          row.el.dataset.state = row.selection.checked ? "checked" : "unchecked";
        }
        if (row.expanded.length && !row.el.dataset.expanded) {
          row.el.dataset.expanded = "close";
        }
      },
      _syncSelect() {
        this.selected = this.rows.filter((row) => row.selection?.checked);
        this.selectedIds = this.selected.map((row) => row.id);
        this.selectAllChecked = this.rows.length > 0 && this.rows.every((row) => row.selection?.checked);
      }
    };
  }
  const __vite_glob_0_45 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    table
  }, Symbol.toStringTag, { value: "Module" }));
  function textarea(maxRows) {
    return {
      init() {
        const minRows = parseInt(this.$el.getAttribute("rows"));
        if (minRows && minRows > 0 && maxRows && maxRows > minRows) {
          bind(this.$el, {
            ["@input"]() {
              this.autoRows(minRows, maxRows);
            }
          });
          this.autoRows(minRows, maxRows);
        }
      },
      autoRows(minRows, maxRows2) {
        this.$el.rows = minRows;
        const style = getComputedStyle(this.$el);
        const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
        const lineHeight = parseFloat(style.lineHeight) || parseFloat(style.fontSize) * 1.2 || 16;
        const rows = Math.round((this.$el.scrollHeight - padding) / lineHeight);
        this.$el.rows = Math.min(Math.max(rows, minRows), maxRows2);
      }
    };
  }
  const __vite_glob_0_46 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    textarea
  }, Symbol.toStringTag, { value: "Module" }));
  function toast$1() {
    return {
      toasts: [],
      positions: ["top-left", "top-center", "top-right", "bottom-left", "bottom-center", "bottom-right"],
      init() {
        bind(this.$el, {
          ["@toast.document"](e) {
            this.addToast(e.detail);
          }
        });
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
        ["mousemove", "mousedown", "keydown", "touchstart"].forEach((event) => {
          add(window, event, markActive, { passive: true });
        });
        this.resetIdleTimer = () => {
          clearTimeout(this.idleTimeout);
          this.idleTimeout = setTimeout(markIdle, this.idleDelay);
        };
        this.resetIdleTimer();
      },
      destroy() {
        this._listeners.forEach((off) => off());
      },
      syncAttention() {
        const shouldRun = this.isPageVisible && this.isUserActive;
        this.toasts.forEach((toast2) => {
          if (!toast2.duration || !toast2.attentionAware) return;
          if (shouldRun && toast2.pausedAt) {
            toast2.resume("attention");
          }
          if (!shouldRun && !toast2.pausedAt) {
            toast2.pause("attention");
          }
        });
      },
      addToast(props) {
        const position = normalizePosition(props.position);
        const maxStack = props.maxStack ?? 5;
        if (maxStack !== false) {
          const sameSlot = this.toasts.filter((t) => t.position === position);
          if (sameSlot.length >= maxStack) {
            const oldest = sameSlot.slice().sort((a, b) => a.createdAt - b.createdAt)[0];
            if (oldest) {
              this.removeToast(oldest.id);
            }
          }
        }
        const duration = props.duration ?? getDynamicDuration(props.title, props.message);
        const manager = this;
        const currentToast = props.id ? this.toasts.find((t) => t.id === props.id) : null;
        if (currentToast) {
          return this.updateToast(currentToast.id, props);
        }
        const toast2 = window.Alpine.reactive({
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
              if (this.progress) {
                this.progressValue = 1 - linear;
              }
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
            if (reason === "hover") {
              this.pausedByHover = true;
            } else {
              this.pausedByAttention = true;
            }
            if (this.pausedAt) return;
            this.pausedAt = performance.now();
            this.elapsedBeforePause += this.pausedAt - this.startTime;
            if (this.raf) {
              cancelAnimationFrame(this.raf);
              this.raf = null;
            }
          },
          resume(reason = "attention") {
            if (reason === "hover") {
              this.pausedByHover = false;
            } else {
              this.pausedByAttention = false;
            }
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
            if (!this.lockDirection) {
              this.lockDirection = Math.abs(this.currentX) > Math.abs(this.currentY) ? "x" : "y";
            }
            if (this.lockDirection === "x") {
              e.preventDefault();
            }
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
            const width = e.currentTarget.offsetWidth;
            const threshold = width * 0.4;
            if (Math.abs(this.currentX) > threshold) {
              manager.removeToast(this.id);
            } else {
              this.currentX = 0;
              this.currentY = 0;
              this.lockDirection = null;
            }
          }
        });
        this.toasts.push(toast2);
        this.$nextTick(() => {
          toast2.visible = true;
          toast2.start();
        });
        return toast2;
      },
      updateToast(id, data) {
        const toast2 = this.toasts.find((t) => t.id === id);
        if (!toast2) return;
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
        for (const key in data) {
          if (allowed.includes(key)) {
            toast2[key] = data[key];
          }
        }
        toast2.currentX = 0;
        toast2.swiping = false;
        if (data.duration !== void 0) {
          toast2.stop();
          toast2.pausedAt = null;
          toast2.pausedByHover = false;
          toast2.pausedByAttention = false;
          toast2.total = data.duration;
          toast2.elapsedBeforePause = 0;
          toast2.progressValue = 1;
          if (toast2.visible) {
            toast2.start();
          }
        }
        return toast2;
      },
      removeToast(id) {
        const toast2 = this.toasts.find((t) => t.id === id);
        if (!toast2) return;
        toast2.stop();
        toast2.raf = null;
        toast2.visible = false;
        setTimeout(() => {
          this.toasts = this.toasts.filter((t) => t.id !== id);
        }, 300);
      },
      getToastsByPosition(position) {
        return this.toasts.filter((t) => t.position === position);
      },
      positionTransform(position) {
        return {
          "top-left": "-translate-x-full opacity-0",
          "top-center": "-translate-y-full opacity-0",
          "top-right": "translate-x-full opacity-0",
          "bottom-left": "-translate-x-full opacity-0",
          "bottom-center": "translate-y-full opacity-0",
          "bottom-right": "translate-x-full opacity-0"
        }[String(position)];
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
        const toast2 = this.loading(messages.loading ?? "Loading...");
        const resolveMessage = (msg, data) => typeof msg === "function" ? msg(data) : msg;
        promise.then((data) => {
          if (!this.toasts.find((t) => t.id === toast2.id)) return;
          this.updateToast(toast2.id, {
            title: resolveMessage(messages.success, data) ?? "Success!",
            type: "success",
            duration: getDynamicDuration(resolveMessage(messages.success, data)),
            progress: true,
            swipe: true
          });
        }).catch((error) => {
          if (!this.toasts.find((t) => t.id === toast2.id)) return;
          this.updateToast(toast2.id, {
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
        const exists = this.toasts.find(
          (t) => t.title === props.title && t.type === props.type && now - t.createdAt < windowMs
        );
        if (exists) {
          return exists;
        }
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
    const base = 1e3;
    const weightedLength = title.length * 1.2 + message.length * 1.6;
    const readingSpeed = 16;
    let time = base + weightedLength / readingSpeed * 1e3;
    const lines = text.split("\n").length;
    time += lines * 300;
    return Math.min(max, Math.max(min, time));
  }
  const __vite_glob_0_47 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    toast: toast$1
  }, Symbol.toStringTag, { value: "Module" }));
  const PREVIEWABLE_TYPES = ["image", "video", "audio", "pdf"];
  function upload({
    wireModel = false,
    multiple = false,
    droppable = true,
    maxSize = null,
    maxFiles = null,
    sortable = false,
    invalid = false,
    files = [],
    tooLargeMessage = "This file is too large.",
    invalidTypeMessage = "This file type is not allowed.",
    tooManyFilesMessage = "Too many files selected.",
    previewName = null
  } = {}) {
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
        previewLoaded: PREVIEWABLE_TYPES.includes(file.type)
      })),
      queue: [],
      activeId: null,
      get multiple() {
        return this.$refs.fileInput?.multiple ?? multiple;
      },
      get accept() {
        return this.$refs.fileInput?.accept || null;
      },
      get activeFiles() {
        return this.files.filter((file) => file.status === "uploading" || file.status === "queued");
      },
      get hasPendingUploads() {
        return this.files.some((file) => file.status === "uploading" || file.status === "queued");
      },
      get aggregateProgress() {
        const active = this.activeFiles;
        if (!active.length) return 100;
        return Math.round(active.reduce((sum, file) => sum + file.progress, 0) / active.length);
      },
      get isUploading() {
        return this.activeFiles.length > 0;
      },
      get hasError() {
        return this.files.some((file) => file.status === "error");
      },
      get isInvalid() {
        return invalid || this.hasError;
      },
      get previewFile() {
        return this.find(this.previewId);
      },
      init() {
        bind(this.$refs.fileInput, {
          ["@change"](e) {
            this.addFiles(e.target.files);
            e.target.value = "";
          }
        });
        if (!droppable) return;
        bind(this.$root.querySelector(dataKey("upload-dropzone")), {
          ["@dragover.prevent"]() {
            this.dragOver = true;
          },
          ["@dragleave.prevent"](e) {
            if (e.currentTarget.contains(e.relatedTarget)) return;
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
        if (!this.previewFile) {
          this.previewId = null;
          return;
        }
        if (this.previewFile.previewLoaded) {
          this.$dispatch("modal-show", { name: previewName });
          return;
        }
        this.openFile();
      },
      openFile() {
        const url = this.previewFile?.url;
        if (!url) {
          return;
        }
        window.open(url, "_blank", "noopener");
      },
      addFiles(fileList) {
        if (!fileList?.length) return;
        if (!this.multiple) {
          if (this.activeId) {
            this.cancelUpload(this.activeId);
          }
          this.files.forEach((file) => this.revoke(file));
          this.files = [];
          this.queue = [];
        }
        const incoming = Array.from(fileList);
        const remaining = this.multiple ? maxFiles ? Math.max(maxFiles - this.files.length, 0) : Infinity : 1;
        const accepted = incoming.slice(0, remaining);
        const rejected = this.multiple && maxFiles ? incoming.slice(remaining) : [];
        accepted.forEach((raw) => {
          const entry = this.createFileEntry(raw);
          this.files.push(entry);
          if (!entry.error) {
            this.queue.push(entry.id);
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
        if (maxSize && file.size > maxSize * 1024) {
          return tooLargeMessage;
        }
        if (this.accept && !this.matchesAccept(file, this.accept)) {
          return invalidTypeMessage;
        }
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
        if (this.activeId || !this.queue.length) {
          return;
        }
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
        this.$wire.upload(
          wireModel,
          entry.raw,
          (tmpFilename) => {
            entry.status = "done";
            entry.progress = 100;
            entry.tmpFilename = tmpFilename;
            this.activeId = null;
            this.processQueue();
            this.syncFieldError();
          },
          (message) => {
            entry.status = "error";
            entry.error = message || "Upload failed.";
            this.activeId = null;
            this.processQueue();
            this.syncFieldError();
          },
          (e) => {
            entry.progress = e.detail.progress;
          },
          () => {
            entry.status = "cancelled";
            this.activeId = null;
            this.processQueue();
          }
        );
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
        if (entry.id === this.activeId) {
          this.cancelUpload(id);
        } else {
          this.queue = this.queue.filter((queuedId) => queuedId !== id);
        }
        this.detachFromWire(entry, this.files.filter((file) => file.id !== id));
        this.revoke(entry);
        this.files.splice(index, 1);
        this.syncFieldError();
      },
      replaceFile(index, fileList) {
        const raw = fileList?.[0];
        const entry = this.files[index];
        if (!raw || !entry) return;
        if (entry.id === this.activeId) {
          this.cancelUpload(entry.id);
        } else {
          this.queue = this.queue.filter((queuedId) => queuedId !== entry.id);
        }
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
        if (entry.tmpFilename) {
          this.$wire.removeUpload(wireModel, entry.tmpFilename);
        } else if (entry.value !== null) {
          if (this.multiple) {
            if (!this.hasPendingUploads) {
              this.$wire.set(wireModel, remainingFiles.filter((file) => file.value !== null).map((file) => file.value));
            }
          } else {
            this.$wire.set(wireModel, null);
          }
        }
      },
      syncFieldError() {
        if (this.isInvalid) return;
        this.$root.closest(dataKey("field"))?.querySelector(dataKey("error"))?.remove();
      },
      revoke(entry) {
        if (entry.raw && entry.url) {
          URL.revokeObjectURL(entry.url);
        }
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
        if (e.currentTarget.contains(e.relatedTarget)) return;
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
        if (this.multiple && this.$wire && wireModel && !this.hasPendingUploads) {
          this.$wire.set(wireModel, this.files.filter((file) => file.value !== null).map((file) => file.value));
        }
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
  const __vite_glob_0_49 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    upload
  }, Symbol.toStringTag, { value: "Module" }));
  async function loadAlpine() {
    if (window.Alpine) {
      return;
    }
    await loadScript([
      "https://unpkg.com/@alpinejs/resize@3.x.x/dist/cdn.min.js",
      "https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js",
      "https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
    ]);
  }
  function initAlpine() {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", loadAlpine);
    } else {
      loadAlpine();
    }
  }
  function setupAlpine() {
    if (!window.Alpine) {
      return;
    }
    registerAlpineComponents();
    window.Alpine.store("tallkit", tallkit);
    window.Alpine.magic("tallkit", () => Alpine.store("tallkit"));
    window.Alpine.magic("tk", () => Alpine.store("tallkit"));
  }
  function registerAlpineComponents() {
    const components = Object.fromEntries(
      Object.values([__vite_glob_0_0, __vite_glob_0_1, __vite_glob_0_2, __vite_glob_0_3, __vite_glob_0_4, __vite_glob_0_5, __vite_glob_0_6, __vite_glob_0_7, __vite_glob_0_8, __vite_glob_0_9, __vite_glob_0_10, __vite_glob_0_11, __vite_glob_0_12, __vite_glob_0_13, __vite_glob_0_14, __vite_glob_0_15, __vite_glob_0_16, __vite_glob_0_17, __vite_glob_0_18, __vite_glob_0_19, __vite_glob_0_20, __vite_glob_0_21, __vite_glob_0_22, __vite_glob_0_23, __vite_glob_0_24, __vite_glob_0_25, __vite_glob_0_26, __vite_glob_0_27, __vite_glob_0_28, __vite_glob_0_29, __vite_glob_0_30, __vite_glob_0_31, __vite_glob_0_32, __vite_glob_0_33, __vite_glob_0_34, __vite_glob_0_35, __vite_glob_0_36, __vite_glob_0_37, __vite_glob_0_38, __vite_glob_0_39, __vite_glob_0_40, __vite_glob_0_41, __vite_glob_0_42, __vite_glob_0_43, __vite_glob_0_44, __vite_glob_0_45, __vite_glob_0_46, __vite_glob_0_47, __vite_glob_0_48, __vite_glob_0_49]).flatMap(
        (module) => Object.entries(module).filter(([, v]) => typeof v === "function")
      )
    );
    for (const [name, fn] of Object.entries(components)) {
      window.Alpine.data(name, fn);
    }
  }
  const appearance = {
    mode: window.localStorage.getItem("tallkit.appearance") || "system",
    init() {
      this.apply(this.mode);
      document.addEventListener("livewire:navigated", () => this.apply(this.mode));
      const media = window.matchMedia("(prefers-color-scheme: dark)");
      media.addEventListener("change", () => {
        if (this.mode === "system") {
          this.apply("system");
        }
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
    apply(appearance2) {
      if (appearance2 === "system") {
        const media = window.matchMedia("(prefers-color-scheme: dark)");
        window.localStorage.removeItem("tallkit.appearance");
        if (media.matches) {
          this.applyDark(false);
        } else {
          this.applyLight(false);
        }
        this.mode = "system";
      } else if (appearance2 === "dark") {
        this.applyDark();
      } else if (appearance2 === "light") {
        this.applyLight();
      }
    },
    toggle(event, options = {}) {
      const isAppearanceTransition = typeof document !== "undefined" && document.startViewTransition && !window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      if (!isAppearanceTransition || !event) {
        return this.isDark() ? this.applyLight() : this.applyDark();
      }
      const transition = document.startViewTransition(() => this.isDark() ? this.applyLight() : this.applyDark());
      const x = event.clientX || 0;
      const y = event.clientY || 0;
      const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
      transition.ready.then(() => {
        const clipPath = [
          `circle(0px at ${x}px ${y}px)`,
          `circle(${endRadius}px at ${x}px ${y}px)`
        ];
        document.documentElement.animate(
          {
            clipPath: this.isDark() ? [...clipPath].reverse() : clipPath
          },
          {
            duration: 300,
            easing: "ease-in",
            ...options || {},
            pseudoElement: this.isDark() ? "::view-transition-old(root)" : "::view-transition-new(root)"
          }
        );
      });
    }
  };
  function toast(...args) {
    if (args.length === 0) {
      return {
        success: (...props) => toast({ ...parseArgs(...props), type: "success" }),
        error: (...props) => toast({ ...parseArgs(...props), type: "error" }),
        info: (...props) => toast({ ...parseArgs(...props), type: "info" }),
        warning: (...props) => toast({ ...parseArgs(...props), type: "warning" })
      };
    }
    document.dispatchEvent(new CustomEvent("toast", { detail: parseArgs(...args) }));
  }
  const parseArgs = (...args) => {
    if (typeof args[0] === "object" && args[0] !== null && !Array.isArray(args[0])) {
      return args[0];
    }
    const [message, title, type, duration, position, progress2, size] = args;
    return { message, title, type, duration, position, progress: progress2, size };
  };
  const tallkit$1 = {
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
      return {
        close: () => {
          document.dispatchEvent(new CustomEvent("modal-close", { detail: {} }));
        }
      };
    }
  };
  window.TALLKit = window.TK = window.tk = window.tallkit = tallkit$1;
  document.dispatchEvent(new CustomEvent("tallkit:init"));
  initAlpine();
  document.addEventListener("alpine:init", setupAlpine);
}));
