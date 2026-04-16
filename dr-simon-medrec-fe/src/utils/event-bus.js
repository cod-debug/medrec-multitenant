import { reactive } from "vue";

export const eventBus = reactive({
  events: {},
  emit(event, payload) {
    (this.events[event] || []).forEach(cb => cb(payload));
  },
  on(event, cb) {
    this.events[event] = this.events[event] || [];
    this.events[event].push(cb);
  },
  off(event, cb) {
    this.events[event] =
      (this.events[event] || []).filter(fn => fn !== cb);
  },
});