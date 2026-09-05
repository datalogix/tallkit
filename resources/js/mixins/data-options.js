export function dataOptions() {
  return {
    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}');
    }
  };
}
