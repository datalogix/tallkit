(function(factory) {
  typeof define === "function" && define.amd ? define(factory) : factory();
})(function() {
  "use strict";
  function bind(el, bindings) {
    const elements = el instanceof Element ? [el] : el;
    elements?.forEach((element) => {
      window.Alpine.bind(element, bindings);
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
  function aside() {
    return {
      ...sticky()
    };
  }
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
  function header() {
    return {
      ...sticky()
    };
  }
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
  function badge() {
    return {
      init() {
        bind(this.$el.querySelector("[data-tallkit-badge-close]"), {
          ["@click"]() {
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
        });
      }
    };
  }
  function disclosure() {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      init() {
        _toggleable.init.call(this, this.$root.hasAttribute("data-open"));
        new MutationObserver(() => {
          this.opened = this.$root.hasAttribute("data-open");
        }).observe(this.$root, { attributeFilter: ["data-open"] });
        bind(this.$root.querySelector("button"), {
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
  function menuCheckbox() {
    return {
      get checked() {
        return this.$el.hasAttribute("data-checked");
      },
      set checked(value) {
        if (value) {
          this.$el.setAttribute("data-checked", "");
        } else {
          this.$el.removeAttribute("data-checked");
        }
      },
      init() {
        this.$el.setAttribute("aria-checked", this.checked ? "true" : "false");
        new MutationObserver(() => {
          this.$el.setAttribute("aria-checked", this.checked ? "true" : "false");
        }).observe(this.$el, { attributeFilter: ["data-checked"] });
        bind(this.$el, {
          ["@click"]() {
            if (this.$el.disabled) {
              return;
            }
            this.checked = !this.checked;
          }
        });
      }
    };
  }
  function menuRadio() {
    return {
      get checked() {
        return this.$el.hasAttribute("data-checked");
      },
      set checked(value) {
        if (value) {
          this.$el.setAttribute("data-checked", "");
        } else {
          this.$el.removeAttribute("data-checked");
        }
      },
      init() {
        this.$el.setAttribute("aria-checked", this.checked ? "true" : "false");
        new MutationObserver(() => {
          this.$el.setAttribute("aria-checked", this.checked ? "true" : "false");
        }).observe(this.$el, { attributeFilter: ["data-checked"] });
        bind(this.$el, {
          ["@click"]() {
            if (this.$el.disabled || this.checked) {
              return;
            }
            this.$el.closest("[data-tallkit-menu-radio-group]")?.querySelectorAll("[data-tallkit-menu-radio]")?.forEach((radio) => {
              if (radio !== this.$el) {
                radio.removeAttribute("data-checked");
              }
            });
            this.checked = true;
          }
        });
      }
    };
  }
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
            }
          },
          ["@click"](event) {
            handleCloseAttempt(event);
          },
          ["@keydown.escape.window.prevent"](event) {
            handleCloseAttempt(event);
          }
        });
      }
    };
  }
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
            ["@keyup.escape.window.prevent"]() {
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
  const components = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    addressForm,
    alertComponent,
    aside,
    badge,
    creditCard,
    disclosure,
    fullCalendar,
    header,
    inputClearable,
    inputCopyable,
    inputViewable,
    label,
    menu,
    menuCheckbox,
    menuRadio,
    modal,
    modalTrigger,
    popover,
    sidebar,
    sticky,
    submenu,
    table,
    toast: toast$1,
    toggleable,
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
    Object.entries(components).forEach(([name, component]) => {
      window.Alpine.data(name, component);
    });
    window.Alpine.store("tallkit", tallkit);
    window.Alpine.magic("tallkit", () => Alpine.store("tallkit"));
    window.Alpine.magic("tk", () => Alpine.store("tallkit"));
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
});
