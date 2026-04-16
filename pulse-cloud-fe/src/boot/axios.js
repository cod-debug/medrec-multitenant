import { boot } from "quasar/wrappers";
import axios from "axios";
import { Notify } from "quasar";
import { useAuthStore } from "src/stores/auth";
import { clearTempStorage } from "src/utils/storePersistence";

axios.interceptors.response.use(
  response => response, // If the response is successful, return it.
  error => {
    if (error.response && error.response.status === 401) {
      const auth_store = useAuthStore();
      
      Notify.create({
        message: 'Unauthorized user. Please log in your account.',
        position: 'top-right',
        closeBtn: 'X',
        timeout: 2000,
        color: 'red',
        onDismiss: () => {
          // Clear auth store and all storage
          auth_store.clearUserData();
          clearTempStorage();
          window.location.reload();
        }
      });
    }
    return Promise.reject(error);
  }
);

const api = axios.create({ baseURL: process.env.VUE_APP_API_BASE_URL || "http://localhost:3006" });

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = api;
});

export { api };
