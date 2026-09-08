export function dataOptions() {
  return {
    getDataOptions(el = this.$el) {
      return window.Alpine.evaluate(el, el.getAttribute('data-options') || '{}');
    }
  };
}
