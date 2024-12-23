import { defineStore } from "pinia";
import type { Router } from "vue-router";

interface FormData {
  name?: string;
  email: string;
  password: string;
  password_confirmation?: string;
}

type ApiRoute = "register" | "login";

type Erros = { [key: string]: [string] } | null;

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: null;
  country: string;
  language: string;
  created_at: string;
  updated_at: string;
}

export const useAuthStore = defineStore("authStore", {
  state: () => ({
    user: <User | null>null,
    errors: <Erros>{},
    router: <Router>{},
  }),
  getters: {},
  actions: {
    async getUser() {
      if (localStorage.getItem("token")) {
        const res = await fetch("/api/user", {
          headers: {
            authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        const data = await res.json();

        if (res.ok) {
          this.user = data;
        }
      }
    },

    async authenticate(apiRoute: ApiRoute, formData: FormData) {
      const res = await fetch(`api/${apiRoute}`, {
        method: "post",
        body: JSON.stringify(formData),
      });

      const data = await res.json();

      if (data.errors) {
        this.errors = data.errors;
        throw new Error("Failed to Authenticate");
      } else {
        this.errors = {};
        localStorage.setItem("token", data.token);
        this.user = data.user;
        this.router.push({ name: "home" });
      }
    },

    async logout() {
      const res = await fetch(`api/logout`, {
        method: "post",
        headers: {
          authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        this.user = null;
        this.errors = {};
        localStorage.removeItem("token");
        this.router.push({ name: "login" });
      } else {
        throw new Error("Failed to Logout");
      }
    },
  },
});
