(function(factory) {
  typeof define === "function" && define.amd ? define(factory) : factory();
})((function() {
  "use strict";
  function bind(el, bindings) {
    const elements = el instanceof Element ? [el] : el;
    elements?.forEach((element, index) => {
      window.Alpine.bind(element, typeof bindings === "function" ? bindings(element, index) : bindings);
    });
  }
  function timeout(callback, milliseconds, defaultMilliseconds = 500) {
    let timeoutId = void 0;
    clearTimeout(timeoutId);
    const ms = !milliseconds || isNaN(parseInt(milliseconds.toString())) ? defaultMilliseconds : parseInt(milliseconds.toString());
    timeoutId = setTimeout(callback, ms);
    return timeoutId;
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
  function levenshtein(a, b) {
    const alen = a.length, blen = b.length;
    if (!alen) return blen;
    if (!blen) return alen;
    const v0 = new Array(blen + 1).fill(0);
    const v1 = new Array(blen + 1).fill(0);
    for (let j = 0; j <= blen; j++) v0[j] = j;
    for (let i = 0; i < alen; i++) {
      v1[0] = i + 1;
      for (let j = 0; j < blen; j++) {
        const cost = a[i] === b[j] ? 0 : 1;
        v1[j + 1] = Math.min(
          v1[j] + 1,
          // insertion
          v0[j + 1] + 1,
          // deletion
          v0[j] + cost
          // substitution
        );
      }
      for (let j = 0; j <= blen; j++) v0[j] = v1[j];
    }
    return v1[blen];
  }
  function fuzzySubsequence(haystack, needle) {
    if (!needle) return true;
    let i = 0, j = 0;
    while (i < haystack.length && j < needle.length) {
      if (haystack[i] === needle[j]) j++;
      i++;
    }
    return j === needle.length;
  }
  function fuzzyMatch(haystack, needle, threshold = 0.35) {
    if (!needle) return true;
    if (haystack.includes(needle)) return true;
    if (fuzzySubsequence(haystack, needle)) return true;
    const dist = levenshtein(haystack, needle);
    const maxAllowed = Math.max(1, Math.floor(needle.length * threshold));
    return dist <= maxAllowed;
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
    if (opts?.removeSpaces) {
      str = str.replace(/\s+/g, " ").trim();
    }
    switch (opts.mode) {
      case "alpha":
        str = str.replace(/[^a-z]/gi, "");
        break;
      case "alphanumeric":
        str = str.replace(/[^a-z0-9]/gi, "");
        break;
      case "numeric":
        str = str.replace(/[^0-9]/g, "");
        break;
    }
    if (opts.uppercase && !opts.lowercase) {
      str = str.toUpperCase();
    } else if (opts.lowercase && !opts.uppercase) {
      str = str.toLowerCase();
    }
    return str;
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
  function addressForm() {
    return {
      get loading() {
        return this.$root.querySelector("[data-tallkit-loading]");
      },
      get zipcode() {
        return this.$root.querySelector("[data-tallkit-address-form-zipcode]");
      },
      get address() {
        return this.$root.querySelector("[data-tallkit-address-form-address]");
      },
      get number() {
        return this.$root.querySelector("[data-tallkit-address-form-number]");
      },
      get complement() {
        return this.$root.querySelector("[data-tallkit-address-form-complement]");
      },
      get neighborhood() {
        return this.$root.querySelector("[data-tallkit-address-form-neighborhood]");
      },
      get city() {
        return this.$root.querySelector("[data-tallkit-address-form-city]");
      },
      get state() {
        return this.$root.querySelector("[data-tallkit-address-form-state]");
      },
      abortController: null,
      init() {
        const that = this;
        bind(this.zipcode, {
          ["@keyup"]() {
            this.search.bind(that)(this.$el.value);
          }
        });
      },
      async search(value) {
        value = value.replace(/\D/g, "");
        if (value.length < 8) {
          return;
        }
        if (this.abortController) {
          this.abortController.abort();
        }
        this.abortController = new AbortController();
        this.loading.style.display = "block";
        this.address.disabled = true;
        this.neighborhood.disabled = true;
        this.city.disabled = true;
        this.state.disabled = true;
        try {
          const res = await fetch(`https://viacep.com.br/ws/${value}/json/`, { signal: this.abortController.signal });
          const data = await res.json();
          if (data.erro) throw new Error("Not found");
          this.address.value = data.logradouro;
          this.address.dispatchEvent(new Event("input", { bubbles: true }));
          this.address.dispatchEvent(new Event("change", { bubbles: true }));
          this.neighborhood.value = data.bairro;
          this.neighborhood.dispatchEvent(new Event("input", { bubbles: true }));
          this.neighborhood.dispatchEvent(new Event("change", { bubbles: true }));
          this.city.value = data.localidade;
          this.city.dispatchEvent(new Event("input", { bubbles: true }));
          this.city.dispatchEvent(new Event("change", { bubbles: true }));
          this.state.value = this.state.tagName.toLowerCase() === "input" || this.state.querySelector(`option[value="${data.estado}"]`) ? data.estado : data.uf;
          this.state.dispatchEvent(new Event("input", { bubbles: true }));
          this.state.dispatchEvent(new Event("change", { bubbles: true }));
          this.number.focus();
        } catch (e) {
          if (e.name === "AbortError") return;
          this.zipcode.focus();
        } finally {
          this.loading.style.display = "none";
          this.address.disabled = false;
          this.neighborhood.disabled = false;
          this.city.disabled = false;
          this.state.disabled = false;
        }
      }
    };
  }
  const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    addressForm
  }, Symbol.toStringTag, { value: "Module" }));
  function alertComponent(timeout$1) {
    return {
      timeoutId: null,
      init() {
        bind(this.$el.querySelectorAll("[data-tallkit-alert-close]"), {
          ["@click"]() {
            this.dismiss();
          }
        });
        if (timeout$1) {
          this.timeoutId = timeout(() => this.dismiss(), timeout$1, 7e3);
        }
      },
      dismiss() {
        clearTimeout(this.timeoutId);
        const root = this.$el.closest("[data-tallkit-alert]");
        if (!root) {
          return;
        }
        root.classList.remove("opacity-100");
        root.classList.add("opacity-0");
        root.addEventListener(
          "transitionend",
          () => root?.remove(),
          { once: true }
        );
      }
    };
  }
  const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    alertComponent
  }, Symbol.toStringTag, { value: "Module" }));
  function loadable() {
    return {
      empty: null,
      loaded: null,
      error: null,
      async load(cb) {
        if (!this.$el.hasAttribute("data-silent")) {
          this.start();
        }
        try {
          const result = await cb();
          this.complete();
          if (result) {
            this.$nextTick(result);
          }
        } catch (e) {
          this.fail(e);
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
        this.reset();
        this.loaded = false;
        this.$dispatch("started");
      },
      complete(milliseconds = 0) {
        timeout(() => {
          this.reset();
          this.loaded = true;
          this.$dispatch("completed");
        }, milliseconds);
      },
      fail(error, milliseconds = 0) {
        timeout(() => {
          this.reset();
          this.error = error;
          this.$dispatch("failed");
        }, milliseconds);
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
  const __vite_glob_0_22 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    loadable
  }, Symbol.toStringTag, { value: "Module" }));
  function apexcharts() {
    return {
      ...loadable(),
      chart: null,
      async init() {
        this.load(async () => {
          if (!window.ApexCharts) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/npm/apexcharts@5");
          }
        });
      },
      render(options = {}) {
        this.chart ??= new window.ApexCharts(this.$el, options);
        this.chart.render();
        this.$dispatch("rendered", { chart: this.chart });
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
        const e = this.$el.offsetTop;
        this.$el.style.position = "sticky";
        this.$el.style.top = `${e}px`;
        this.$el.style.maxHeight = `calc(100dvh - ${e}px)`;
      }
    };
  }
  const __vite_glob_0_33 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
  const __vite_glob_0_39 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    toggleable
  }, Symbol.toStringTag, { value: "Module" }));
  function popover({ mode, position, align }) {
    const _toggleable = toggleable();
    let _rAF = null;
    const component = {
      ..._toggleable,
      trigger: null,
      popoverElement: null,
      mouseX: 0,
      mouseY: 0,
      get isTouch() {
        return window.matchMedia("(hover: none)").matches;
      },
      init() {
        _toggleable.init.call(this);
        this.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root;
        this.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
        if (!this.popoverElement) return;
        this.$root.setAttribute("aria-haspopup", "true");
        this.$root.setAttribute("aria-expanded", "false");
        this.popoverElement.addEventListener("beforetoggle", (e) => {
          queueMicrotask(() => {
            if (e.newState === "open") {
              this.onOpen();
            } else {
              this.onClose();
            }
          });
        });
        if (this.isTouch || mode === "dropdown") {
          bind(this.trigger, {
            ["@click"]() {
              this.toggle();
            },
            ["@click.outside"](e) {
              if ((this.popoverElement.hasAttribute("data-keep-open") || e.target?.hasAttribute("data-keep-open") || e.target?.closest("[data-keep-open]")) && this.popoverElement.contains(e.target)) {
                return;
              }
              this.close();
            },
            ["@keyup.escape.window"]() {
              this.close();
            }
          });
        } else if (mode === "hover") {
          bind(this.trigger, {
            ["@mouseenter"]() {
              this.open();
            },
            ["@mouseleave"]() {
              this.close();
            },
            ["@keyup.escape.window"]() {
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
            },
            ["@keydown.escape.prevent"]() {
              this.close();
            }
          });
          bind(this.popoverElement, {
            ["@click.outside"]() {
              this.close();
            }
          });
        }
      },
      open() {
        this.popoverElement.showPopover();
      },
      close() {
        this.popoverElement.hidePopover();
      },
      onOpen() {
        _toggleable.open.call(this);
        this.$root.setAttribute("aria-expanded", "true");
        window.addEventListener("scroll", this.boundSetPosition, true);
        window.addEventListener("resize", this.boundSetPosition, true);
        this.resizeObserver = new ResizeObserver(this.boundSetPosition);
        this.resizeObserver.observe(this.trigger);
        this.resizeObserver.observe(this.popoverElement);
        this.mutationObserver = new MutationObserver(this.boundSetPosition);
        this.mutationObserver.observe(this.trigger, {
          childList: true,
          subtree: true
        });
        this.mutationObserver.observe(this.popoverElement, {
          childList: true,
          subtree: true
        });
        this.setPosition();
      },
      onClose() {
        _toggleable.close.call(this);
        this.$root.setAttribute("aria-expanded", "false");
        window.removeEventListener("scroll", this.boundSetPosition, true);
        window.removeEventListener("resize", this.boundSetPosition, true);
        this.resizeObserver?.disconnect();
        this.resizeObserver = null;
        this.mutationObserver?.disconnect();
        this.mutationObserver = null;
      },
      boundSetPosition() {
        if (_rAF) return;
        _rAF = requestAnimationFrame(() => {
          this.setPosition();
          _rAF = null;
        });
      },
      setPosition() {
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
      }
    };
    component.boundSetPosition = component.boundSetPosition.bind(component);
    return component;
  }
  const __vite_glob_0_29 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    popover
  }, Symbol.toStringTag, { value: "Module" }));
  function autocomplete() {
    const _popover = popover({ mode: "manual", position: "bottom", align: "start" });
    return {
      ..._popover,
      current: null,
      get input() {
        return this.$root.querySelector("[data-tallkit-autocomplete]");
      },
      get items() {
        return Array.from(
          this.$root.querySelectorAll("[data-tallkit-autocomplete-item-container]:has([data-tallkit-button-content])")
        );
      },
      get filteredItems() {
        return this.items.filter((item) => {
          if (item.hasAttribute("data-hidden")) return false;
          const button = item.querySelector("[data-tallkit-autocomplete-item]");
          return !button?.hasAttribute("disabled");
        });
      },
      setPosition() {
        _popover.setPosition.call(this);
        this.popoverElement.style.minWidth = `${this.input.offsetWidth}px`;
      },
      init() {
        _popover.init.call(this);
        _popover.trigger = this.input;
        _popover.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
        bind(this.$root.querySelectorAll("[data-tallkit-autocomplete-item]"), (element) => ({
          ["@click"]: () => this.filteredItems.forEach((item, index) => {
            if (item.querySelector("[data-tallkit-autocomplete-item]") === element) {
              this.current = index;
              this.selectActive();
              return;
            }
          }),
          ["@mouseenter"]: () => this.filteredItems.forEach((item, index) => {
            if (item.querySelector("[data-tallkit-autocomplete-item]") === element) {
              this.setActive(index);
              this.$dispatch("autocomplete-item-hover", { index, item });
              return;
            }
          })
        }));
        bind(this.input, {
          ["@input"]: () => {
            this.$dispatch("autocomplete-search-updated", { query: this.input.value });
            this.search();
            if (this.filteredItems.length === 0) {
              this.close();
            } else {
              this.open();
            }
          },
          ["@focus"]: (e) => {
            if (this.filteredItems.length === 0) {
              this.close();
            } else {
              this.open();
              this.setActive();
            }
          },
          ["@blur"]: () => {
            this.clearActive();
            timeout(() => this.close(), 100);
          },
          ["@keydown.enter.prevent"]: () => this.selectActive(),
          ["@keydown.arrow-down.prevent"]: () => this.next(),
          ["@keydown.arrow-up.prevent"]: () => this.prev(),
          ["@keyup.escape.window"]: () => this.close()
        });
        this.$nextTick(() => {
          this.search();
          this.clearActive();
        });
        this.$dispatch("autocomplete-initialized");
      },
      clearActive() {
        this.items.forEach((item) => {
          item.querySelector("[data-tallkit-autocomplete-item]")?.removeAttribute("data-active");
        });
        this.current = null;
      },
      prev() {
        if (!this.popoverElement?.matches(":popover-open")) {
          this.open();
          return;
        }
        if (this.current == null) return;
        this.setActive((this.current - 1 + this.filteredItems.length) % this.filteredItems.length);
      },
      next() {
        if (!this.popoverElement?.matches(":popover-open")) {
          this.open();
          return;
        }
        if (this.current == null) return;
        this.setActive((this.current + 1) % this.filteredItems.length);
      },
      search() {
        const normalizeOptions = {
          lowercase: true,
          replaceAccents: true,
          removeSpaces: true
        };
        const value = normalize(this.input.value, normalizeOptions);
        this.clearItems();
        if (value) {
          this.items.forEach((item) => {
            const span = item.querySelector("[data-tallkit-button-content]");
            const content = normalize(span?.textContent, normalizeOptions) || "";
            if (!content.includes(value)) {
              item.setAttribute("data-hidden", "");
            }
          });
        }
        this.$dispatch("autocomplete-items-changed", {
          items: this.filteredItems.length
        });
        this.setActive();
      },
      clearItems() {
        this.items.forEach((item) => {
          item.querySelector("[data-tallkit-autocomplete-item]")?.removeAttribute("data-active");
          item.removeAttribute("data-hidden");
        });
      },
      setActive(index = 0) {
        const items = this.filteredItems;
        if (index < 0 || index >= items.length) return;
        if (this.current !== null) {
          const last = items.at(this.current);
          last?.querySelector("[data-tallkit-autocomplete-item]")?.removeAttribute("data-active");
        }
        const item = items.at(index);
        if (!item) return;
        const button = item.querySelector("[data-tallkit-autocomplete-item]");
        if (button?.hasAttribute("disabled")) return;
        button?.setAttribute("data-active", "");
        this.current = index;
        item.scrollIntoView({
          behavior: "smooth",
          block: "nearest"
        });
        this.$dispatch("autocomplete-active-changed", { index, item, button });
      },
      selectActive() {
        if (this.current === null) return;
        const item = this.filteredItems.at(this.current);
        if (!item) return;
        const button = item.querySelector("[data-tallkit-autocomplete-item]");
        if (!button || button.hasAttribute("disabled")) return;
        this.input.value = button.querySelector("[data-tallkit-button-content]")?.textContent.trim() || "";
        this.input.dispatchEvent(new Event("input", { bubbles: true }));
        this.input.dispatchEvent(new Event("change", { bubbles: true }));
        this.close();
        this.$dispatch("autocomplete-item-selected", {
          index: this.current,
          item,
          button
        });
      }
    };
  }
  const __vite_glob_0_4 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    autocomplete
  }, Symbol.toStringTag, { value: "Module" }));
  function badge() {
    return {
      init() {
        bind(this.$el.querySelector("[data-tallkit-badge-close]"), {
          ["@click"]: () => this.close()
        });
      },
      close() {
        const root = this.$el.closest("[data-tallkit-badge]");
        if (!root) {
          return;
        }
        root.classList.remove("opacity-100");
        root.classList.add("opacity-0");
        root.addEventListener(
          "transitionend",
          () => {
            root?.remove();
            this.$dispatch("close");
          },
          { once: true }
        );
      }
    };
  }
  const __vite_glob_0_5 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    badge
  }, Symbol.toStringTag, { value: "Module" }));
  function chartjs() {
    return {
      ...loadable(),
      chart: null,
      async init() {
        this.load(async () => {
          if (!window.Chart) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/npm/chart.js@4");
          }
        });
      },
      render(options = {}) {
        this.chart ??= new window.Chart(this.$el, options);
        this.$dispatch("rendered", { chart: this.chart });
      }
    };
  }
  const __vite_glob_0_6 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    chartjs
  }, Symbol.toStringTag, { value: "Module" }));
  function command() {
    return {
      current: null,
      get input() {
        return this.$root.querySelector("[data-tallkit-command-input]");
      },
      get items() {
        return Array.from(
          this.$root.querySelectorAll("[data-tallkit-command-item-container]:has([data-tallkit-button-content])")
        );
      },
      get filteredItems() {
        return this.items.filter((item) => {
          if (item.hasAttribute("data-hidden")) return false;
          const button = item.querySelector("[data-tallkit-command-item]");
          return !button?.hasAttribute("disabled");
        });
      },
      get noRecords() {
        return this.$root.querySelector("[data-tallkit-command-no-records]");
      },
      init() {
        bind(this.$root, {
          ["@mouseleave"]: () => this.clearActive()
        });
        bind(this.$root.querySelectorAll("[data-tallkit-command-item]"), (element) => ({
          ["@click"]: () => this.filteredItems.forEach((item, index) => {
            if (item.querySelector("[data-tallkit-command-item]") === element) {
              this.current = index;
              this.selectActive();
              return;
            }
          }),
          ["@mouseenter"]: () => this.filteredItems.forEach((item, index) => {
            if (item.querySelector("[data-tallkit-command-item]") === element) {
              this.setActive(index);
              this.$dispatch("command-item-hover", { index, item });
              return;
            }
          })
        }));
        bind(this.input, {
          ["@input"]: () => {
            this.$dispatch("command-search-updated", { query: this.input.value });
            this.search();
          },
          ["@focus"]: (e) => {
            this.setActive();
          },
          ["@blur"]: () => this.clearActive(),
          ["@keydown.enter.prevent"]: () => this.selectActive(),
          ["@keydown.arrow-down.prevent"]: () => this.next(),
          ["@keydown.arrow-up.prevent"]: () => this.prev()
        });
        this.$nextTick(() => {
          this.search();
          this.clearActive();
        });
        this.$dispatch("command-initialized");
      },
      clearActive() {
        this.items.forEach((item) => {
          item.querySelector("[data-tallkit-command-item]")?.removeAttribute("data-active");
        });
        this.current = null;
      },
      prev() {
        if (this.current == null) return;
        this.setActive((this.current - 1 + this.filteredItems.length) % this.filteredItems.length);
      },
      next() {
        if (this.current == null) return;
        this.setActive((this.current + 1) % this.filteredItems.length);
      },
      search() {
        const normalizeOptions = {
          lowercase: true,
          replaceAccents: true,
          removeSpaces: true
        };
        const value = normalize(this.input.value, normalizeOptions);
        this.clearItems();
        if (value) {
          this.items.forEach((item) => {
            const span = item.querySelector("[data-tallkit-button-content]");
            const content = normalize(span?.textContent, normalizeOptions) || "";
            if (!fuzzyMatch(content, value)) {
              item.setAttribute("data-hidden", "");
            }
          });
        }
        this.$dispatch("command-items-changed", {
          items: this.filteredItems.length
        });
        this.toggleNoRecords();
        this.setActive();
      },
      clearItems() {
        this.items.forEach((item) => {
          item.querySelector("[data-tallkit-command-item]")?.removeAttribute("data-active");
          item.removeAttribute("data-hidden");
        });
      },
      toggleNoRecords() {
        if (!this.noRecords) return;
        if (this.filteredItems.length === 0) {
          this.noRecords.removeAttribute("hidden");
        } else {
          this.noRecords.setAttribute("hidden", "");
        }
      },
      setActive(index = 0) {
        const items = this.filteredItems;
        if (index < 0 || index >= items.length) return;
        if (this.current !== null) {
          const last = items.at(this.current);
          last?.querySelector("[data-tallkit-command-item]")?.removeAttribute("data-active");
        }
        const item = items.at(index);
        if (!item) return;
        const button = item.querySelector("[data-tallkit-command-item]");
        if (button?.hasAttribute("disabled")) return;
        button?.setAttribute("data-active", "");
        this.current = index;
        item.scrollIntoView({
          behavior: "smooth",
          block: "nearest"
        });
        this.$dispatch("command-active-changed", { index, item, button });
      },
      selectActive() {
        if (this.current === null) return;
        const item = this.filteredItems.at(this.current);
        if (!item) return;
        const button = item.querySelector("[data-tallkit-command-item]");
        if (!button || button.hasAttribute("disabled")) return;
        const lastCurrent = this.current;
        this.input.value = "";
        this.input.dispatchEvent(new Event("input", { bubbles: true }));
        this.input.dispatchEvent(new Event("change", { bubbles: true }));
        this.$dispatch("command-item-selected", {
          index: this.current,
          item,
          button
        });
        this.setActive(lastCurrent);
      }
    };
  }
  const __vite_glob_0_7 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    command
  }, Symbol.toStringTag, { value: "Module" }));
  function composer(submit, placeholder) {
    return {
      value: null,
      init() {
        bind(this.$el, {
          "x-modelable": "value"
        });
        const labelFor = this.$el.parentElement?.querySelector("[data-tallkit-label]")?.getAttribute("for");
        bind(this.$el.querySelector("[data-tallkit-control]"), {
          "x-model": "value",
          ...labelFor ? { "id": labelFor } : {},
          ...placeholder ? { "placeholder": placeholder } : {},
          ...submit === "enter" ? {
            ["@keydown.enter"](e) {
              if (e.shiftKey) return;
              e.preventDefault();
              this.$root.closest("form")?.dispatchEvent(new Event("submit"));
            }
          } : {}
        });
      }
    };
  }
  const __vite_glob_0_8 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    composer
  }, Symbol.toStringTag, { value: "Module" }));
  const CREDIT_CARD_DEFAULT = {
    opened: true,
    types: [],
    holderName: null,
    number: null,
    type: null,
    expirationDate: null,
    cvv: null
  };
  function creditCard(options = {}) {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      options: CREDIT_CARD_DEFAULT,
      init() {
        _toggleable.init.call(this);
        this.card = this.$data;
        this.options = { ...CREDIT_CARD_DEFAULT, ...options };
        this.opened = this.options.opened;
      },
      get typeOptions() {
        return this.options.types[this.options.type] ? this.options.types[this.options.type] : this.options.types.unknown;
      },
      update(options2 = {}) {
        this.options = { ...this.options, ...options2 };
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
  const __vite_glob_0_9 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    creditCard
  }, Symbol.toStringTag, { value: "Module" }));
  function disclosureGroup(exclusive) {
    return {
      init() {
        if (exclusive) {
          this.initExclusive();
        }
      },
      initExclusive() {
        const items = this.$root.querySelectorAll("[data-tallkit-disclosure-item]");
        const observe = () => {
          items.forEach((item) => {
            observer.observe(item, { attributeFilter: ["data-open"] });
          });
        };
        const observer = new MutationObserver((records) => {
          const current = records[0]?.target;
          items.forEach((item) => {
            if (item === current) return;
            if (item._x_dataStack && item?._x_dataStack[0] && typeof item?._x_dataStack[0].close === "function") {
              item?._x_dataStack[0].close();
            } else {
              item.removeAttribute("data-open");
            }
          });
          observer.disconnect();
          this.$nextTick(observe);
        });
        observe();
      }
    };
  }
  const __vite_glob_0_10 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    disclosureGroup
  }, Symbol.toStringTag, { value: "Module" }));
  function disclosure() {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      init() {
        _toggleable.init.call(this, this.$root.hasAttribute("data-open"));
        new MutationObserver(() => {
          this.opened = this.$root.hasAttribute("data-open");
        }).observe(this.$root, { attributeFilter: ["data-open"] });
        bind(this.$root.querySelectorAll(":scope > button"), {
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
      }
    };
  }
  const __vite_glob_0_11 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    disclosure
  }, Symbol.toStringTag, { value: "Module" }));
  function echarts() {
    return {
      ...loadable(),
      chart: null,
      async init() {
        this.load(async () => {
          if (!window.echarts) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/npm/echarts@6");
          }
        });
      },
      render(options = {}) {
        this.chart ??= window.echarts.init(this.$el);
        this.chart.setOption(options);
        this.$dispatch("rendered", { chart: this.chart });
      }
    };
  }
  const __vite_glob_0_12 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    echarts
  }, Symbol.toStringTag, { value: "Module" }));
  function fetchable(url, data, autofetch, options) {
    return {
      ...loadable(),
      url: null,
      response: null,
      data: null,
      options: null,
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
        if (this.url && autofetch !== false) {
          this.fetch();
        }
        if (!this.url && this.data) {
          this.complete();
        }
      },
      async fetch(url2 = null, options2 = {}, silent = false) {
        const _url = url2 || this.url;
        const _options = { ...this.options ?? {}, ...options2 };
        this.url = _url;
        this.options = _options;
        if (!_url) {
          return;
        }
        this.load(async () => {
          this.response = await window.fetch(_url, _options);
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
      }
    };
  }
  const __vite_glob_0_13 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    fetchable
  }, Symbol.toStringTag, { value: "Module" }));
  function frappeCharts() {
    return {
      ...loadable(),
      chart: null,
      async init() {
        this.load(async () => {
          if (!window.frappe?.Chart) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/npm/frappe-charts@1");
          }
        });
      },
      render(options = {}) {
        this.chart ??= new window.frappe.Chart(this.$el, options);
        this.$dispatch("rendered", { chart: this.chart });
      }
    };
  }
  const __vite_glob_0_14 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    frappeCharts
  }, Symbol.toStringTag, { value: "Module" }));
  function fullCalendar(options) {
    return {
      fullCalendar: null,
      getOptions() {
        return window.Alpine.evaluate(this.$el, this.$el.getAttribute("data-options") || "{}");
      },
      async init() {
        if (!window.FullCalendar) {
          await this.$tallkit.loadScript([
            "https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js",
            options.locale && options.locale !== "en" ? `https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales/${options.locale}.global.min.js` : "https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales-all.global.min.js"
          ]);
        }
        this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
          ...options,
          ...this.getOptions()
        });
        this.fullCalendar.render();
      }
    };
  }
  const __vite_glob_0_15 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    fullCalendar
  }, Symbol.toStringTag, { value: "Module" }));
  function header() {
    return {
      ...sticky()
    };
  }
  const __vite_glob_0_16 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    header
  }, Symbol.toStringTag, { value: "Module" }));
  function highlightjs() {
    return {
      ...loadable(),
      init() {
        this.load(async () => {
          if (!window.hljs) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js");
            await this.$tallkit.loadStyle("https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css");
          }
        });
      },
      render(code, language = null) {
        try {
          return language ? window.hljs.highlight(code, { language }).value : window.hljs.highlightAuto(code).value;
        } catch (e) {
          this.fail(e);
        }
      }
    };
  }
  const __vite_glob_0_17 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    highlightjs
  }, Symbol.toStringTag, { value: "Module" }));
  function inputClearable() {
    return {
      get input() {
        return this.$el.closest("[data-tallkit-input]").querySelector("input");
      },
      init() {
        const button = this.$el;
        if (!this.input) {
          return;
        }
        button.style.display = this.input.value ? "inline-flex " : "none";
        bind(this.input, {
          ["@input"]() {
            button.style.display = this.$el.value ? "inline-flex " : "none";
          }
        });
        bind(button, {
          ["@click"]() {
            this.input.value = "";
            this.input.dispatchEvent(new Event("input", { bubbles: false }));
            this.input.dispatchEvent(new Event("change", { bubbles: false }));
            this.input.dispatchEvent(new Event("cleared", { bubbles: false }));
            this.input.focus();
          }
        });
      }
    };
  }
  const __vite_glob_0_18 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    inputClearable
  }, Symbol.toStringTag, { value: "Module" }));
  function inputCopyable() {
    return {
      copied: false,
      timeout: null,
      get input() {
        return this.$el.closest("[data-tallkit-input]").querySelector("input");
      },
      init() {
        if (!this.input) {
          return;
        }
        bind(this.$el, {
          async ["@click"]() {
            clearTimeout(this.timeout);
            this.copied = true;
            this.popoverElement && this.popoverElement.showPopover();
            if (navigator.clipboard) {
              await navigator.clipboard.writeText(this.input.value);
              this.input.dispatchEvent(new Event("copied", { bubbles: false }));
            }
            this.timeout = setTimeout(() => {
              this.popoverElement && this.popoverElement.hidePopover();
              this.copied = false;
            }, 1e3);
          }
        });
      }
    };
  }
  const __vite_glob_0_19 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    inputCopyable
  }, Symbol.toStringTag, { value: "Module" }));
  function inputViewable() {
    return {
      viewed: false,
      get container() {
        return this.$el.closest("[data-tallkit-input]");
      },
      get input() {
        return this.container.querySelector("input");
      },
      init() {
        if (!this.input) {
          return;
        }
        this.input.setAttribute("type", this.viewed ? "text" : "password");
        bind(this.$el, {
          ["@click"]() {
            this.viewed = !this.viewed;
            this.input.setAttribute("type", this.viewed ? "text" : "password");
            this.input.dispatchEvent(new Event("viewed", { bubbles: false }));
          }
        });
        const inputObserver = new MutationObserver(() => {
          this.viewed = this.input.getAttribute("type") !== "password";
        });
        inputObserver.observe(this.input, {
          attributes: true,
          attributeFilter: ["type"]
        });
      }
    };
  }
  const __vite_glob_0_20 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    inputViewable
  }, Symbol.toStringTag, { value: "Module" }));
  function label() {
    return {
      get control() {
        let control = this.$el.parentElement?.querySelector("[data-tallkit-control]");
        const validSelectors = 'input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]';
        if (control && !control.matches(validSelectors)) {
          control = control.querySelector(validSelectors);
        }
        return control;
      },
      init() {
        if (this.$el.tagName.toLowerCase() === "label" && this.$el.hasAttribute("for") && !!document.getElementById(this.$el.getAttribute("for"))) {
          return;
        }
        if (!this.control) {
          return;
        }
        bind(this.$el, {
          ["@click"]() {
            const control = this.control;
            const tag = control.tagName.toLowerCase();
            const type = control.getAttribute("type")?.toLowerCase();
            const isEditable = control.hasAttribute("contenteditable") || control.getAttribute("role") === "textbox";
            const isReadOnly = control.hasAttribute("readonly") || control.getAttribute("aria-readonly") === "true";
            const isDisabled = control.disabled;
            if (type === "checkbox") {
              if (!isDisabled && !isReadOnly) {
                control.checked = !control.checked;
                control.dispatchEvent(new Event("input", { bubbles: true }));
                control.dispatchEvent(new Event("change", { bubbles: true }));
              }
              return;
            }
            if (type === "radio") {
              if (!isDisabled && !isReadOnly && !control.checked) {
                control.checked = true;
                control.dispatchEvent(new Event("input", { bubbles: true }));
                control.dispatchEvent(new Event("change", { bubbles: true }));
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
  const __vite_glob_0_21 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
          return this.value.includes(this.$root.value);
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
          this.value = this.isChecked ? this.value.filter((v) => v !== this.$root.value) : [...this.value, this.$root.value];
          return;
        }
        this.value = this.$root.value;
      }
    };
  }
  const __vite_glob_0_23 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
  const __vite_glob_0_24 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    menuRadio
  }, Symbol.toStringTag, { value: "Module" }));
  function menu() {
    return {
      init() {
        bind(this.$el.querySelectorAll("[data-tallkit-menu-item]"), {
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
          }
        });
      }
    };
  }
  const __vite_glob_0_25 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    menu
  }, Symbol.toStringTag, { value: "Module" }));
  function modalTrigger(name, shortcut) {
    return {
      init() {
        bind(this.$el, {
          ["@click"]() {
            if (this.$el.querySelector("button[disabled]")) return;
            this.$dispatch("modal-show", { name });
          }
        });
        if (shortcut) {
          bind(this.$el, {
            [`@keydown.${shortcut}.document`](event) {
              event.preventDefault();
              this.$dispatch("modal-show", { name });
            }
          });
        }
      }
    };
  }
  const __vite_glob_0_26 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    modalTrigger
  }, Symbol.toStringTag, { value: "Module" }));
  function modal(name, dismissible, persist) {
    return {
      init() {
        const dialog = this.$el;
        bind(dialog.querySelectorAll("[data-tallkit-modal-close],[data-tallkit-modal-auto-close]"), {
          ["@click"]() {
            dialog.close();
          }
        });
        bind(dialog, {
          ["@modal-show.document"](event) {
            if (event.detail.name === name && event.detail.scope === this.$wire?.id) dialog.showModal();
            if (event.detail.name === name && !event.detail.scope) dialog.showModal();
          },
          ["@modal-close.document"](event) {
            if (event.detail.name === name && event.detail.scope === this.$wire?.id) dialog.close();
            if (!event.detail.name || event.detail.name === name && !event.detail.scope) dialog.close();
          }
        });
        const handleCloseAttempt = (event) => {
          if (persist) {
            const persistAnimation = typeof persist === "string" ? persist : "tilt-shaking";
            dialog.classList.remove(persistAnimation);
            this.$nextTick(() => dialog.classList.add(persistAnimation));
            return;
          }
          if (dismissible !== false && event.target === dialog || event.target.getAttribute("tabindex") === "0") {
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
          ["@keyup.escape.window"](event) {
            handleCloseAttempt(event);
          }
        });
      }
    };
  }
  const __vite_glob_0_27 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    modal
  }, Symbol.toStringTag, { value: "Module" }));
  function otp(submit) {
    return {
      value: "",
      get inputs() {
        return Array.from(
          this.$root.querySelectorAll("[data-tallkit-otp-input]")
        );
      },
      get length() {
        return this.inputs.length;
      },
      init() {
        const inputs = this.inputs;
        this.$nextTick(() => this.updateModel());
        this.$watch("value", (newVal) => syncInputs(inputs, newVal));
        inputs.forEach((input, index) => {
          bind(input, {
            ["@focus"]() {
              input.select();
              this.$dispatch("otp-focus", { input, index });
            },
            ["@blur"]() {
              this.$dispatch("otp-blur", { input, index });
            },
            ["@paste"](e) {
              const pasted = e.clipboardData.getData("text");
              this.$dispatch("otp-paste", { input, index, pasted });
            },
            ["@input"]() {
              const value = filterValue(input.value, input.dataset.mode);
              if (value.length > 1) {
                spreadValue(value, index, inputs);
              } else {
                input.value = value;
                if (value) {
                  if (inputs[index + 1]) {
                    inputs[index + 1].focus();
                  } else {
                    inputs.filter((input2) => !input2.value).at(0)?.focus();
                  }
                }
              }
              this.updateModel();
            },
            ["@keydown.arrow-left.prevent"]: () => inputs[index - 1]?.focus(),
            ["@keydown.arrow-right.prevent"]: () => inputs[index + 1]?.focus(),
            ["@keydown.backspace"]: () => {
              if (!input.value && inputs[index - 1]) {
                inputs[index - 1].focus();
              }
            }
          });
        });
        syncInputs(inputs, this.value);
      },
      updateModel() {
        const old = this.value;
        this.value = this.inputs.map((i) => i.value || " ").join("");
        const len = this.value.replace(/\s+/g, "").length;
        if (old === this.value) {
          return;
        }
        this.$dispatch("otp-change", { value: this.value });
        if (len === this.length) {
          this.$dispatch("otp-complete", { value: this.value });
          if (submit === "auto") {
            this.$root.closest("form")?.dispatchEvent(new Event("submit"));
          }
        }
        if (len !== this.length) {
          this.$dispatch("otp-incomplete", { value: this.value });
        }
        if (len === 0) {
          this.$dispatch("otp-clear");
        }
      }
    };
  }
  function syncInputs(inputs, modelValue) {
    const chars = modelValue.padEnd(inputs.length).split("");
    inputs.forEach((input, i) => {
      input.value = filterValue(chars[i] ?? "", input.dataset.mode);
    });
  }
  function filterValue(value, mode) {
    return (value.toLocaleUpperCase().match(
      mode === "alpha" ? /[A-Z]/g : mode === "alphanumeric" ? /[A-Z0-9]/g : /[0-9]/g
    ) || []).join("");
  }
  function spreadValue(value, startIndex, inputs) {
    const chars = value.split("");
    chars.forEach((char, i) => {
      const target = inputs[startIndex + i];
      if (target) {
        target.value = filterValue(char, target.dataset.mode);
      }
    });
    const lastIndex = Math.min(
      startIndex + chars.length,
      inputs.length - 1
    );
    inputs[lastIndex]?.focus();
  }
  const __vite_glob_0_28 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    otp
  }, Symbol.toStringTag, { value: "Module" }));
  function prettyPrintJson() {
    return {
      ...loadable(),
      init() {
        this.load(async () => {
          if (!window.prettyPrintJson) {
            await this.$tallkit.loadScript("https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js");
            await this.$tallkit.loadStyle("https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css");
          }
        });
      },
      render(data = null, options = {}) {
        try {
          return window.prettyPrintJson.toHtml(data, options);
        } catch (e) {
          this.fail(e);
        }
      }
    };
  }
  const __vite_glob_0_30 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    prettyPrintJson
  }, Symbol.toStringTag, { value: "Module" }));
  function sidebar(name, sticky$1, stashable) {
    const _toggleable = toggleable();
    const _sticky = sticky();
    return {
      ..._toggleable,
      init() {
        _toggleable.init.call(this);
        if (sticky$1) {
          _sticky.init.call(this);
        }
        if (stashable) {
          this.$el.removeAttribute("data-mobile-cloak");
          this.screenLg = window.innerWidth >= 1024;
          bind(this.$el, {
            ["x-bind:data-stashed"]() {
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
      }
    };
  }
  const __vite_glob_0_31 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    sidebar
  }, Symbol.toStringTag, { value: "Module" }));
  function slider() {
    return {
      get input() {
        return this.$root.querySelector("[data-tallkit-control]");
      },
      init() {
        this.updateRange();
        bind(this.input, {
          ["@input"]: () => this.updateRange()
        });
        bind(this.$root.querySelector("[data-tallkit-slider-ticks]"), {
          ["@click"]: (e) => {
            const ticks = [...this.$root.querySelectorAll("[data-tallkit-slider-tick]")];
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
              this.setValue(closestTick.getAttribute("value"));
            }
          }
        });
      },
      setValue(value) {
        if (this.input.disabled) return;
        this.input.value = value;
        this.input.dispatchEvent(new Event("input", { bubbles: true }));
      },
      updateRange() {
        const min = Number(this.input.min || 0);
        const max = Number(this.input.max || 100);
        const val = Number(this.input.value);
        const p = (val - min) * 100 / (max - min);
        this.input.style.setProperty("--range-percent", `${p}%`);
      }
    };
  }
  const __vite_glob_0_32 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
        _popover.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root;
        _popover.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
        bind(_popover.popoverElement, {
          ["@mouseenter"]() {
            this.inside = true;
            _popover.trigger.setAttribute("data-active", "");
          },
          ["@mouseleave"]() {
            this.inside = false;
            this.timerToClose();
          }
        });
        bind(_popover.trigger, {
          ["@click"]() {
            this.open();
          },
          ["@mouseenter"]() {
            clearTimeout(this._i);
            this.open();
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
            _popover.trigger.removeAttribute("data-active");
          }
        }, 10);
      }
    };
  }
  const __vite_glob_0_34 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    submenu
  }, Symbol.toStringTag, { value: "Module" }));
  function tab() {
    return {
      selected: null,
      init() {
        const selected = this.$root.querySelector("[data-selected]")?.dataset.name;
        if (selected) {
          this.$nextTick(() => {
            this.select(selected);
          });
        }
      },
      isSelected(name) {
        return this.selected === name;
      },
      select(name) {
        this.selected = name;
      }
    };
  }
  const __vite_glob_0_35 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    tab
  }, Symbol.toStringTag, { value: "Module" }));
  function table() {
    return {
      processedRows: /* @__PURE__ */ new Set(),
      rows: [],
      selected: [],
      selectedIds: [],
      selectAllChecked: false,
      init() {
        this.update();
        const observer = new MutationObserver(() => this.update());
        observer.observe(this.$el.querySelector("tbody"), { childList: true, subtree: true });
      },
      update() {
        this.rows = Array.from(this.$el.querySelectorAll('tbody>tr[role="row"]')).map((tr) => {
          const selection = tr.querySelector("[role=row-selection]");
          const expanded = tr.querySelectorAll("[role=row-expanded]");
          const row = {
            el: tr,
            id: tr.dataset.id,
            selection,
            expanded
          };
          if (!this.processedRows.has(row.id)) {
            this.processedRows.add(row.id);
            bind(selection, {
              ["@click"]() {
                this._updateRowState(row);
                this._syncSelect();
              }
            });
            bind(expanded, {
              ["@click"]() {
                row.el.dataset.expanded = row.el.dataset.expanded === "open" ? "close" : "open";
              }
            });
          }
          return row;
        });
        this.rows.forEach((row) => {
          if (row.selection) {
            row.selection.checked = this.selectedIds.includes(row.id);
          }
          if (this.selectAllChecked) {
            row.selection.checked = true;
          }
          this._updateRowState(row);
        });
        this._syncSelect();
      },
      toggleAll() {
        this.rows.forEach((row) => {
          if (!row.selection) return;
          row.selection.checked = this.selectAllChecked;
          this._updateRowState(row);
        });
        this._syncSelect();
      },
      _updateRowState(row) {
        if (row.selection) {
          row.el.dataset.state = row.selection.checked ? "checked" : "unchecked";
        }
        if (row.expanded && !row.el.dataset.expanded) {
          row.el.dataset.expanded = "close";
        }
      },
      _syncSelect() {
        this.selected = this.rows.filter((row) => row.selection?.checked);
        this.selectedIds = this.selected.map((row) => row.id);
        this.selectAllChecked = this.rows.length && this.rows.every((row) => row.selection?.checked);
      }
    };
  }
  const __vite_glob_0_36 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
        }
      },
      autoRows(minRows, maxRows2) {
        this.$el.rows = minRows;
        const style = getComputedStyle(this.$el);
        const lineHeight = parseFloat(style.lineHeight);
        const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
        const rows = Math.round((this.$el.scrollHeight - padding) / lineHeight);
        this.$el.rows = Math.min(Math.max(rows, minRows), maxRows2);
      }
    };
  }
  const __vite_glob_0_37 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
      },
      addToast(props) {
        const toast2 = window.Alpine.reactive({
          id: Date.now() + Math.random(),
          ...props,
          duration: props.duration ?? 5e3,
          position: props.position ?? "bottom-right",
          visible: false
        });
        if (toast2.position === "top") {
          toast2.position = "top-right";
        }
        if (toast2.position === "bottom") {
          toast2.position = "bottom-right";
        }
        this.toasts.push(toast2);
        this.$nextTick(() => toast2.visible = true);
        if (toast2.duration) {
          setTimeout(
            () => this.removeToast(toast2.id),
            toast2.duration
          );
        }
      },
      removeToast(id) {
        const toast2 = this.toasts.find((t) => t.id === id);
        if (!toast2) return;
        toast2.visible = false;
        setTimeout(() => {
          this.toasts = this.toasts.filter((t) => t.id !== id);
        }, 300);
      },
      getToastsByPosition(position) {
        return this.toasts.filter((t) => t.position === position);
      }
    };
  }
  const __vite_glob_0_38 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    toast: toast$1
  }, Symbol.toStringTag, { value: "Module" }));
  function upload({ droppable = false } = {}) {
    return {
      dragOver: false,
      get multiple() {
        return this.$refs.fileInput?.multiple ?? false;
      },
      init() {
        if (!droppable) return;
        bind(this.$root.querySelector("[data-tallkit-upload-button]"), {
          ["@dragover.prevent"]() {
            this.dragOver = true;
          },
          ["@dragleave.prevent"]() {
            this.dragOver = false;
          },
          ["@drop.prevent"](event) {
            this.$refs.fileInput.files = event.dataTransfer.files;
            this.$refs.fileInput.dispatchEvent(new Event("change", { bubbles: true }));
          }
        });
      },
      selectFile() {
        this.$refs.fileInput.click();
      },
      removeFile(name) {
        if (!confirm("Are you sure you want to proceed?")) {
          return;
        }
        if (this.$wire) {
          this.$wire.set(name, null);
        }
      }
    };
  }
  const __vite_glob_0_40 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
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
      Object.values([__vite_glob_0_0, __vite_glob_0_1, __vite_glob_0_2, __vite_glob_0_3, __vite_glob_0_4, __vite_glob_0_5, __vite_glob_0_6, __vite_glob_0_7, __vite_glob_0_8, __vite_glob_0_9, __vite_glob_0_10, __vite_glob_0_11, __vite_glob_0_12, __vite_glob_0_13, __vite_glob_0_14, __vite_glob_0_15, __vite_glob_0_16, __vite_glob_0_17, __vite_glob_0_18, __vite_glob_0_19, __vite_glob_0_20, __vite_glob_0_21, __vite_glob_0_22, __vite_glob_0_23, __vite_glob_0_24, __vite_glob_0_25, __vite_glob_0_26, __vite_glob_0_27, __vite_glob_0_28, __vite_glob_0_29, __vite_glob_0_30, __vite_glob_0_31, __vite_glob_0_32, __vite_glob_0_33, __vite_glob_0_34, __vite_glob_0_35, __vite_glob_0_36, __vite_glob_0_37, __vite_glob_0_38, __vite_glob_0_39, __vite_glob_0_40]).flatMap(
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
        media.matches ? this.applyDark(false) : this.applyLight(false);
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
    let detail;
    if (typeof args[0] === "object" && args[0] !== null && !Array.isArray(args[0])) {
      detail = args[0];
    } else {
      const [text, heading, type, duration, position] = args;
      detail = { text, heading, type, duration, position };
    }
    document.dispatchEvent(new CustomEvent("toast", { detail }));
  }
  const tallkit$1 = {
    appearance,
    toast,
    loadScript,
    loadStyle,
    modal: (name) => {
      const dialog = document.querySelector(`dialog[data-modal="${name}"]`);
      return {
        show: () => {
          dialog?.showModal();
        },
        close: () => {
          dialog?.close();
        }
      };
    },
    modals: () => {
      const dialogs = document.querySelectorAll(`dialog[data-tallkit-modal]`);
      return {
        close: () => {
          dialogs.forEach((modal2) => modal2.close());
        }
      };
    }
  };
  window.TALLKit = window.TK = window.tk = window.tallkit = tallkit$1;
  document.dispatchEvent(new CustomEvent("tallkit:init"));
  initAlpine();
  document.addEventListener("alpine:init", setupAlpine);
}));
