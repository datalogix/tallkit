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
  function collapseAndRemove(root, options = {}) {
    let fallbackId = null;
    let onTransitionEnd = null;
    let finished = false;
    const cleanup = () => {
      if (fallbackId !== null) {
        clearTimeout(fallbackId);
        fallbackId = null;
      }
      if (onTransitionEnd) {
        root.removeEventListener("transitionend", onTransitionEnd);
        onTransitionEnd = null;
      }
    };
    const finish = () => {
      if (finished) {
        return;
      }
      finished = true;
      cleanup();
      if (root.isConnected) {
        root.remove();
      }
      options.onDone?.();
    };
    if (!options.animated || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      finish();
      return cleanup;
    }
    const height = root.offsetHeight;
    const style = window.getComputedStyle(root);
    root.style.height = `${height}px`;
    root.style.overflow = "hidden";
    root.style.willChange = "height, opacity, margin-bottom, padding-top, padding-bottom";
    root.style.marginBottom = style.marginBottom;
    root.style.paddingTop = style.paddingTop;
    root.style.paddingBottom = style.paddingBottom;
    root.getBoundingClientRect();
    requestAnimationFrame(() => {
      root.style.opacity = "0";
      root.style.height = "0px";
      root.style.marginBottom = "0px";
      root.style.paddingTop = "0px";
      root.style.paddingBottom = "0px";
    });
    onTransitionEnd = (event) => {
      if (event.target !== root) return;
      if (event.propertyName !== "height") return;
      finish();
    };
    root.addEventListener("transitionend", onTransitionEnd);
    const transitionTimeout = getTransitionTimeout(root);
    if (transitionTimeout === 0) {
      finish();
    } else {
      fallbackId = window.setTimeout(() => finish(), transitionTimeout + 50);
    }
    return cleanup;
  }
  function timeout(callback, milliseconds, defaultMilliseconds = 500) {
    let timeoutId = void 0;
    clearTimeout(timeoutId);
    const ms = !milliseconds || isNaN(parseInt(milliseconds.toString())) ? defaultMilliseconds : parseInt(milliseconds.toString());
    timeoutId = setTimeout(callback, ms);
    return timeoutId;
  }
  function getWireModelInfo(element) {
    for (let attr of element.attributes) {
      if (attr.name.startsWith("wire:model")) {
        let modifier = attr.name.includes(".") ? attr.name.split(".").slice(1).join(".") : "";
        return {
          name: attr.value,
          modifier
        };
      }
    }
    return null;
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
        this.loading?.classList.remove("hidden");
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
          this.loading?.classList.add("hidden");
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
  function alertComponent(timeout$1, animation) {
    return {
      timeoutId: null,
      cancelDismiss: null,
      isDismissing: false,
      init() {
        bind(this.$el.querySelectorAll("[data-tallkit-alert-close]"), {
          ["@click"]: () => this.dismiss()
        });
        if (timeout$1) {
          this.timeoutId = timeout(() => this.dismiss(), timeout$1, 7e3);
        }
        this.$el.addEventListener("alpine:destroy", () => this.cleanup());
      },
      cleanup() {
        clearTimeout(this.timeoutId);
        if (this.cancelDismiss) {
          this.cancelDismiss();
        }
        this.timeoutId = null;
        this.cancelDismiss = null;
        this.isDismissing = false;
      },
      dismiss() {
        if (this.isDismissing) {
          return;
        }
        this.isDismissing = true;
        clearTimeout(this.timeoutId);
        this.timeoutId = null;
        const root = this.$el.closest("[data-tallkit-alert]");
        if (!root) {
          this.isDismissing = false;
          return;
        }
        root.classList.remove("opacity-100");
        root.classList.add("opacity-0");
        this.cancelDismiss = collapseAndRemove(root, {
          animated: animation,
          onDone: () => {
            this.cancelDismiss = null;
            this.isDismissing = false;
          }
        });
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
    const cache = /* @__PURE__ */ new Map();
    const m = Math.pow(10, mantissa);
    return {
      get(value) {
        const numTokens = value.match(SPACE).length;
        if (cache.has(numTokens)) {
          return cache.get(numTokens);
        }
        const norm2 = 1 / Math.pow(numTokens, 0.5 * weight);
        const n = parseFloat(Math.round(norm2 * m) / m);
        cache.set(numTokens, n);
        return n;
      },
      clear() {
        cache.clear();
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
        this.popoverElement = this.$root.lastElementChild?.matches("[popover]") && this.$root.lastElementChild;
        this.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root;
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
        const offCommit = Livewire?.hook("commit", ({ succeed }) => {
          succeed(() => {
            if (!this.popoverElement?.matches(":popover-open")) return;
            if (!this.$root?.isConnected) return;
            this.boundSetPosition();
          });
        });
        this.livewireCommitCleanup = typeof offCommit === "function" ? offCommit : () => {
        };
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
        if (!this.popoverElement?.isConnected) return;
        if (this.popoverElement.matches(":popover-open")) return;
        this.popoverElement.showPopover();
      },
      close() {
        if (!this.popoverElement?.isConnected) return;
        if (!this.popoverElement.matches(":popover-open")) return;
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
          childList: true
        });
        this.mutationObserver.observe(this.popoverElement, {
          childList: true
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
        if (_rAF) {
          cancelAnimationFrame(_rAF);
          _rAF = null;
        }
      },
      boundSetPosition() {
        if (_rAF) return;
        _rAF = requestAnimationFrame(() => {
          this.setPosition();
          _rAF = null;
        });
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
      }
    };
    component.boundSetPosition = component.boundSetPosition.bind(component);
    return component;
  }
  const __vite_glob_0_29 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    popover
  }, Symbol.toStringTag, { value: "Module" }));
  function autocomplete(options = {}) {
    const _popover = popover({ mode: "manual", position: "bottom", align: "start" });
    return {
      ..._popover,
      uid: crypto?.randomUUID?.() || Math.random().toString(36).slice(2, 8),
      abortController: null,
      prefetchController: null,
      scrollBehavior: options.scrollBehavior ?? "auto",
      useCache: options.cache ?? true,
      usePagination: options.pagination ?? true,
      useVirtualization: options.virtualization ?? true,
      fuseOptions: options.fuseOptions || {},
      highlightMatches: options.highlightMatches ?? true,
      state: "idle",
      query: "",
      selected: null,
      current: null,
      _renderStatesRAF: null,
      _items: [],
      _filtered: [],
      _itemsVersion: 0,
      fuse: null,
      lastQuery: "",
      minLength: options.minLength || 1,
      delay: options.delay || 300,
      debounceTimer: null,
      page: 1,
      perPage: options.perPage || 20,
      hasMore: true,
      loadingMore: false,
      itemHeight: options.itemHeight || 40,
      overscan: options.overscan || 5,
      start: 0,
      end: 0,
      get totalHeight() {
        return this._filtered.length * this.itemHeight;
      },
      get visibleItems() {
        return this._filtered.slice(this.start, this.end);
      },
      cache: /* @__PURE__ */ new Map(),
      requestId: 0,
      lastRequestId: 0,
      getCacheKey(page = this.page) {
        return `${this.query}::${page}`;
      },
      escapeHtml(str = "") {
        return str.replace(/[&<>"']/g, (tag) => ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;"
        })[tag]);
      },
      dedupe(items) {
        const seen = /* @__PURE__ */ new Set();
        return items.filter((i) => {
          const key = i.value ?? i.label;
          if (seen.has(key)) return false;
          seen.add(key);
          return true;
        });
      },
      abortAllRequests() {
        if (this.abortController) this.abortController.abort();
        if (this.prefetchController) this.prefetchController.abort();
        this.abortController = null;
        this.prefetchController = null;
      },
      init() {
        _popover.init.call(this);
        this.input = this.$root.querySelector("[data-tallkit-control]");
        this.items = this.$root.querySelector("[role=listbox]");
        this.setupARIA();
        this.bind();
        this.refreshItems();
        this.setupFuse();
        this.updateWindow();
        this.render();
        this.renderStates();
        this.$root.addEventListener("autocomplete-search", (e) => {
          this.onItemsUpdated(e.detail);
        });
      },
      refreshItems() {
        const nodes = Array.from(this.items.querySelectorAll("[role=option]"));
        this._items = nodes.map((el, index) => {
          const button = el.querySelector("button");
          const content = el.querySelector("[data-tallkit-button-content]");
          return {
            el,
            button,
            content,
            value: button?.value ?? content?.textContent?.trim(),
            label: content?.textContent?.trim(),
            index
          };
        });
        this._itemsVersion++;
      },
      setupFuse() {
        if (this.fuse) {
          this.fuse.setCollection(this._items);
        } else {
          this.fuse = new Fuse(this._items, {
            keys: ["label"],
            threshold: 0.4,
            ignoreLocation: true,
            ...this.fuseOptions
          });
        }
      },
      triggerSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
          this.resetSearchState();
          this.state = (this.query ?? "").trim().length < this.minLength ? "idle" : "loading";
          this.renderStates();
          this.updateARIA();
          if (this.state === "idle") {
            return;
          }
          this.fetch();
        }, this.delay);
      },
      fetch() {
        const key = this.getCacheKey();
        if (this.useCache && this.cache.has(key)) {
          this.onItemsUpdated(this.cache.get(key), true);
          return;
        }
        if (this._items.length > 0) {
          this.search();
          return;
        }
        this.abortAllRequests();
        this.abortController = new AbortController();
        const id = ++this.requestId;
        this.lastRequestId = id;
        this.$dispatch("autocomplete-search", {
          query: this.query,
          page: this.usePagination ? this.page : 1,
          perPage: this.perPage,
          requestId: id,
          signal: this.abortController.signal
        });
      },
      prefetch() {
        if (!this.usePagination || !this.hasMore) return;
        if (this.useCache && this.cache.has(this.getCacheKey(this.page + 1))) return;
        if (this.prefetchController) this.prefetchController.abort();
        this.prefetchController = new AbortController();
        this.$dispatch("autocomplete-search", {
          query: this.query,
          page: this.page + 1,
          perPage: this.perPage,
          prefetch: true,
          signal: this.prefetchController.signal
        });
      },
      onItemsUpdated(payload, fromCache = false, backend = false) {
        if (!payload || !payload.items) {
          this.search(backend);
          return;
        }
        const { items, hasMore = false, requestId } = payload;
        if (!fromCache && requestId && requestId !== this.lastRequestId) return;
        this.state = "open";
        this.loadingMore = false;
        const mapped = items.map((item, index) => ({
          ...item,
          index: this._items.length + index
        }));
        const merged = this.usePagination ? [...this._items, ...mapped] : mapped;
        this._items = this.dedupe(merged);
        this._itemsVersion++;
        this.hasMore = this.usePagination ? hasMore : false;
        if (this.useCache) {
          this.cache.set(this.getCacheKey(), payload);
        }
        if (!backend) {
          this.setupFuse();
        }
        this.search(backend);
        if (this.usePagination) {
          this.prefetch();
        }
      },
      search(backend = false) {
        if (backend) {
          this._filtered = [...this._items];
        } else if (this.query !== this.lastQuery && this.fuse) {
          this._filtered = this.fuse.search(this.query).map((r) => r.item);
        } else if (!this.query) {
          this._filtered = [...this._items];
        }
        this.lastQuery = this.query;
        this.state = this._filtered.length ? "open" : "empty";
        this.open();
        this.updateWindow();
        this.render();
        this.renderStates();
        this.updateARIA();
        if (this._filtered.length && this.current == null) {
          this.setActive(0);
        }
      },
      highlight(item) {
        if (!item.content) return;
        if (!this.highlightMatches || !this.query) {
          item.content.textContent = item.label;
          return;
        }
        let text = this.escapeHtml(item.label);
        const words = this.query.split(/\s+/).filter(Boolean);
        words.forEach((word) => {
          const escaped = word.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
          const regex = new RegExp(`(${escaped})`, "gi");
          text = text.replace(regex, "<mark>$1</mark>");
        });
        item.content.innerHTML = text;
      },
      renderStates() {
        if (this._renderStatesRAF) cancelAnimationFrame(this._renderStatesRAF);
        this._renderStatesRAF = requestAnimationFrame(() => {
          const toggle = (selector, show) => {
            this.$root.querySelectorAll(selector).forEach((el) => {
              el.style.display = show ? "" : "none";
            });
          };
          toggle("[data-tallkit-autocomplete-loading]", this.state === "loading");
          toggle("[data-tallkit-autocomplete-empty]", this.state === "empty");
          toggle("[data-tallkit-autocomplete-error]", this.state === "error");
          toggle("[data-tallkit-autocomplete-loading-more]", this.loadingMore);
          this._renderStatesRAF = null;
        });
      },
      calculateItemHeight() {
        if (!this._items.length) return;
        const firstItem = this._items.find((i) => i.el);
        if (!firstItem) return;
        const rect = firstItem.el.getBoundingClientRect();
        this.itemHeight = rect.height || this.itemHeight;
      },
      updateWindow() {
        if (!this.useVirtualization || !this.itemHeight) return;
        if (!this.items) return;
        const scrollTop = this.items.scrollTop;
        const height = this.items.clientHeight;
        const start = Math.floor(scrollTop / this.itemHeight);
        const visible = Math.ceil(height / this.itemHeight);
        this.start = Math.max(0, start - this.overscan);
        this.end = start + visible + this.overscan;
      },
      render() {
        if (!this.itemHeight) this.calculateItemHeight();
        if (!this.items) return;
        if (!this.useVirtualization) {
          this._filtered.forEach((item) => {
            if (!item.el) return;
            item.el.style.display = "";
            item.el.style.position = "";
            item.el.style.transform = "";
            this.highlight(item);
          });
          return;
        }
        this.items.style.position = "relative";
        this.items.style.height = `${this.totalHeight}px`;
        this._items.forEach((item) => {
          if (item.el) item.el.style.display = "none";
        });
        this.visibleItems.forEach((item, i) => {
          if (!item.el) return;
          const index = this.start + i;
          item.el.style.display = "";
          item.el.style.position = "absolute";
          item.el.style.left = "0";
          item.el.style.right = "0";
          item.el.style.transform = `translateY(${index * this.itemHeight}px)`;
          this.highlight(item);
        });
      },
      bind() {
        let ticking = false;
        bind(this.input, {
          ["@input"]: (e) => {
            this.query = e.target.value;
            this.triggerSearch();
          },
          ["@keydown.arrow-down.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.next();
          },
          ["@keydown.arrow-up.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.prev();
          },
          ["@keydown.home.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.setActive(0);
          },
          ["@keydown.end.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.setActive(this._filtered.length - 1);
          },
          ["@keydown.page-down.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.pageDown();
          },
          ["@keydown.page-up.prevent"]: () => {
            this.scrollBehavior = "auto";
            this.pageUp();
          },
          ["@keydown.enter.prevent"]: () => this.select(this.current)
        });
        if (this.items) {
          bind(this.items, {
            ["@click"]: (e) => {
              const itemEl = e.target.closest("[data-tallkit-autocomplete-item-container]");
              if (!itemEl) return;
              const index = this._items.findIndex((i) => i.el === itemEl);
              if (index !== -1) this.select(index);
            },
            ["@mouseover"]: (e) => {
              const itemEl = e.target.closest("[data-tallkit-autocomplete-item-container]");
              if (!itemEl) return;
              const index = this._items.findIndex((i) => i.el === itemEl);
              if (index !== -1) this.setActive(index);
            },
            ["@scroll"]: () => {
              if (!ticking) {
                requestAnimationFrame(() => {
                  this.updateWindow();
                  this.render();
                  if (this.usePagination) {
                    const nearBottom = this.items.scrollTop + this.items.clientHeight >= this.items.scrollHeight - 100;
                    if (nearBottom) this.loadMore();
                  }
                  ticking = false;
                });
                ticking = true;
              }
            }
          });
        }
      },
      loadMore() {
        if (!this.usePagination) return;
        if (!this.hasMore || this.loadingMore) return;
        this.loadingMore = true;
        this.renderStates();
        this.page++;
        this.fetch();
      },
      next() {
        if (!this._filtered.length) return;
        this.current = this.current == null ? 0 : (this.current + 1) % this._filtered.length;
        this.ensureVisible();
        this.updateActive();
      },
      prev() {
        if (!this._filtered.length) return;
        this.current = this.current == null ? 0 : (this.current - 1 + this._filtered.length) % this._filtered.length;
        this.ensureVisible();
        this.updateActive();
      },
      pageDown() {
        if (!this._filtered.length) return;
        if (!this.items) return;
        const visibleCount = Math.floor(this.items.clientHeight / this.itemHeight);
        let nextIndex = (this.current ?? 0) + visibleCount;
        if (nextIndex >= this._filtered.length) nextIndex = this._filtered.length - 1;
        this.setActive(nextIndex);
      },
      pageUp() {
        if (!this._filtered.length) return;
        if (!this.items) return;
        const visibleCount = Math.floor(this.items.clientHeight / this.itemHeight);
        let prevIndex = (this.current ?? 0) - visibleCount;
        if (prevIndex < 0) prevIndex = 0;
        this.setActive(prevIndex);
      },
      setActive(index) {
        if (index < 0 || index >= this._filtered.length) return;
        this.current = index;
        this.ensureVisible();
        this.updateActive();
      },
      updateActive() {
        this._items.forEach((i) => {
          i.button?.removeAttribute("data-active");
          i.el?.removeAttribute("id");
        });
        const item = this._filtered[this.current];
        if (!item) return;
        const id = `ac-${this.uid}-item-${item.index}`;
        item.el?.setAttribute("id", id);
        item.button?.setAttribute("data-active", "");
        this.input?.setAttribute("aria-activedescendant", id);
      },
      ensureVisible() {
        if (!this.items) return;
        const top = this.current * this.itemHeight;
        const bottom = top + this.itemHeight;
        const isAbove = top < this.items.scrollTop;
        const isBelow = bottom > this.items.scrollTop + this.items.clientHeight;
        if (!isAbove && !isBelow) return;
        this.items.scrollTo({
          top: isAbove ? top : bottom - this.items.clientHeight,
          behavior: this.scrollBehavior
        });
      },
      select(index) {
        if (index == null) return;
        const item = this._filtered[index];
        if (!item) return;
        this.scrollBehavior = "smooth";
        this.selected = item.value;
        this.query = item.label;
        this.input.value = item.label;
        this.$dispatch("input", item.value);
        this.$dispatch("autocomplete-selected", item);
        this.close();
      },
      resetSearchState() {
        this.abortAllRequests();
        this.current = null;
        this.loadingMore = false;
        this.state = "idle";
        this._filtered = [];
        this.page = 1;
        this.hasMore = true;
      },
      reset() {
        this.resetSearchState();
        this._items = [];
        this.updateWindow();
        this.render();
        this.renderStates();
        this.updateARIA();
      },
      close() {
        this.abortAllRequests();
        this.state = "idle";
        this.current = null;
        this.updateActive();
        this.updateARIA();
        _popover.close.call(this);
      },
      setupARIA() {
        this.items?.setAttribute("id", `ac-${this.uid}-list`);
        this.items?.setAttribute("role", "listbox");
        this.input?.setAttribute("role", "combobox");
        this.input?.setAttribute("aria-autocomplete", "list");
        this.input?.setAttribute("aria-expanded", "false");
        this.input?.setAttribute("aria-controls", `ac-${this.uid}-list`);
        this.input?.setAttribute("aria-haspopup", "listbox");
        this.input?.setAttribute("aria-live", "polite");
      },
      updateARIA() {
        this.input?.setAttribute("aria-expanded", this.state === "open" ? "true" : "false");
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
              this.select(index);
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
          ["@focus"]: () => this.setActive(),
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
        button.dispatchEvent(new Event("click", { bubbles: true }));
      },
      select(index) {
        const item = this.filteredItems.at(index);
        if (!item) return;
        const button = item.querySelector("[data-tallkit-command-item]");
        if (!button || button.hasAttribute("disabled")) return;
        this.input.value = "";
        this.input.dispatchEvent(new Event("input", { bubbles: true }));
        this.input.dispatchEvent(new Event("change", { bubbles: true }));
        this.$dispatch("command-item-selected", { index, item, button });
        this.setActive(index);
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
  function creditCard(options = {}) {
    const _toggleable = toggleable();
    return {
      ..._toggleable,
      init() {
        _toggleable.init.call(this);
        this.card = this.$data;
        this.options = {
          opened: true,
          types: [],
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
        return this.$el.closest("[data-tallkit-field-control]")?.querySelector("input");
      },
      init() {
        if (!this.input) {
          return;
        }
        const button = this.$el;
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
        return this.$el.closest("[data-tallkit-field-control]")?.querySelector("input");
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
      get input() {
        return this.$el.closest("[data-tallkit-field-control]")?.querySelector("input");
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
          this.viewed = this.input?.getAttribute("type") !== "password";
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
  function modal(name, dismissible, persist, shortcut) {
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
          },
          ...shortcut ? {
            [`@keydown.${shortcut}.document`](event) {
              event.preventDefault();
              this.$dispatch("modal-show", { name });
            }
          } : {}
        });
      },
      show() {
        this.$dispatch("modal-show", { name });
      },
      close() {
        this.$dispatch("modal-close", { name });
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
          this.$root.querySelectorAll("input")
        );
      },
      get length() {
        return this.inputs.length;
      },
      init() {
        const inputs = this.inputs;
        this.$nextTick(() => this.updateModel());
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
        this.value = this.inputs.map((i) => i.value ?? " ").join("");
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
    const chars = String(modelValue).padEnd(inputs.length).split("");
    inputs.forEach((input, i) => {
      input.value = filterValue(chars[i] ?? "", input.dataset.mode);
    });
  }
  function filterValue(value, mode) {
    return (String(value).toLocaleUpperCase().match(
      mode === "alpha" ? /[A-Z]/g : mode === "alphanumeric" ? /[A-Z0-9]/g : /[0-9]/g
    ) || []).join("");
  }
  function spreadValue(value, startIndex, inputs) {
    const chars = String(value).split("");
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
        observer.observe(this.$el.querySelector("table > tbody"), { childList: true, subtree: true });
      },
      update() {
        this.rows = Array.from(this.$el.querySelector("table > tbody").querySelectorAll(':scope > tr[role="row"]')).map((tr) => {
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
        this.$el.addEventListener("alpine:destroy", () => {
          this._listeners.forEach((off) => off());
        });
      },
      syncAttention() {
        const shouldRun = this.isPageVisible && this.isUserActive;
        this.toasts.forEach((toast2) => {
          if (!toast2.duration || !toast2.attentionAware) return;
          if (shouldRun && toast2.pausedAt) {
            toast2.resume();
          }
          if (!shouldRun && !toast2.pausedAt) {
            toast2.pause();
          }
        });
      },
      addToast(props) {
        const duration = props.duration ?? getDynamicDuration(props.title, props.message);
        const toast2 = window.Alpine.reactive({
          id: crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`,
          createdAt: Date.now(),
          ...props,
          duration,
          position: normalizePosition(props.position),
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
              if (!this.$root.toasts.find((t) => t.id === this.id)) {
                this.stop();
                return;
              }
              if (this.pausedAt) return;
              const elapsed = this.elapsedBeforePause + (time - this.startTime);
              const linear = Math.min(elapsed / this.total, 1);
              const eased = linear === 1 ? 1 : 1 - Math.pow(2, -10 * linear);
              if (this.progress) {
                this.progressValue = 1 - eased;
              }
              if (linear >= 1) {
                this.$root.removeToast(this.id);
                return;
              }
              this.raf = requestAnimationFrame(loop);
            };
            this.raf = requestAnimationFrame(loop);
          },
          pause() {
            if (!this.duration || this.pausedAt) return;
            this.pausedAt = performance.now();
            this.elapsedBeforePause += this.pausedAt - this.startTime;
            if (this.raf) {
              cancelAnimationFrame(this.raf);
              this.raf = null;
            }
          },
          resume() {
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
              this.$root.removeToast(this.id);
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
          toast2.total = data.duration;
          toast2.elapsedBeforePause = 0;
          toast2.progressValue = 1;
          if (toast2.visible) {
            toast2.start();
          }
        }
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
          type: "danger",
          duration: 7e3,
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
        const toast2 = this.loading(messages.loading ?? "Carregando...");
        const resolveMessage = (msg, data) => typeof msg === "function" ? msg(data) : msg;
        promise.then((data) => {
          if (!this.toasts.find((t) => t.id === toast2.id)) return;
          this.updateToast(toast2.id, {
            title: resolveMessage(messages.success, data) ?? "Sucesso!",
            type: "success",
            duration: getDynamicDuration(resolveMessage(messages.success, data)),
            progress: true,
            swipe: true
          });
        }).catch((error) => {
          if (!this.toasts.find((t) => t.id === toast2.id)) return;
          this.updateToast(toast2.id, {
            title: resolveMessage(messages.error, error) ?? "Erro!",
            type: "danger",
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
      const [message, title, type, duration, position, progress, size] = args;
      detail = { message, title, type, duration, position, progress, size };
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
