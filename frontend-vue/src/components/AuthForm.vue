<script setup lang="ts">
import { onBeforeMount, reactive, ref, toRaw } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { storeToRefs } from "pinia";
import axios from "axios";
import * as yup from "yup";
import { ValidationError } from "yup";

type Action = "login" | "register";

const props = defineProps<{
  action: Action;
}>();

const { authenticate } = useAuthStore();

const { errors } = storeToRefs(useAuthStore());

const loading = ref(props.action == "register");

const countries = ref<
  {
    id: number;
    name: string;
  }[]
>([]);

const languages = ref<
  {
    id: number;
    name: string;
  }[]
>([]);

const formData = reactive(
  props.action == "register"
    ? {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        country: "",
        language: "",
      }
    : {
        email: "",
        password: "",
      }
);

const schema = yup.object({
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup
    .string()
    .min(8, "Password must be at least 8 characters")
    .required("Password is required"),
  ...(props.action == "register"
    ? {
        country: yup.string().required("Country is required"),
        language: yup.string().required("Language is required"),
        name: yup
          .string()
          .min(3, "Name must be at least 3 characters")
          .required("Name is required"),
      }
    : {}),
});

onBeforeMount(async () => {
  errors.value = {};

  if (props.action == "register") {
    const { data } = await axios.get("/api/countries");

    countries.value = data;

    loading.value = false;
  }
});

async function fetchLanguages() {
  if (!formData.country || !countries) return;

  loading.value = true;

  const countryID = toRaw(countries.value).find(
    (c) => c.name === formData.country
  )?.id;

  if (countryID) {
    const { data } = await axios.get(`/api/countries/${countryID}/languages`);

    languages.value = data;
    loading.value = false;
  }
}
async function validateForm() {
  errors.value = {};
  try {
    await schema.validate(formData, { abortEarly: false });

    return true;
  } catch (err) {
    if (err instanceof ValidationError) {
      err.inner.forEach((err) => {
        if (typeof err.path !== "undefined") {
          errors.value![err.path] = [err.message];
        }
      });
    } else {
      throw new Error("Failed to validate Form");
    }

    return false;
  }
}
const handleSubmit = async () => {
  if (await validateForm()) {
    if (!loading.value) {
      loading.value = true;

      await authenticate(props.action, formData).catch(() => {
        loading.value = false;
      });
    }
  } else {
    return;
  }
};
</script>

<template>
  <div
    class="w-full mb-56 overflow-hidden text-gray-500 bg-gray-100 shadow-xl h-fit rounded-3xl"
    style="max-width: 1000px"
  >
    <div class="w-full md:flex">
      <div class="hidden w-1/2 px-10 py-10 bg-indigo-500 md:block">
        <div class="flex items-center h-full">
          <img class="" src="/authImage.svg" />
        </div>
      </div>
      <form
        @submit.prevent="handleSubmit"
        class="relative w-full px-5 py-10 md:w-1/2 md:px-10"
        v-bind:class="loading && 'animate-pulse pointer-events-none'"
      >
        <!--↓↓↓↓↓/// LOADING OVERLAY ///↓↓↓↓↓-->
        <div
          v-if="loading"
          className="absolute z-20 animate-pulse top-0 left-0 w-full flex justify-center items-center h-full bg-gray-800 backdrop-blur-[2px] bg-opacity-70"
        >
          <div role="status">
            <svg
              aria-hidden="true"
              class="w-40 h-40 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
              viewBox="0 0 100 101"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                fill="currentColor"
              />
              <path
                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                fill="currentFill"
              />
            </svg>
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <!--↓↓↓↓↓/// TITLE & DESCRIPTION ///↓↓↓↓↓-->
        <div class="mb-10 text-center">
          <h1 class="text-3xl font-bold text-gray-900">
            {{ props.action.toUpperCase() }}
          </h1>
          <p>Enter your information to {{ props.action }}</p>
        </div>

        <div>
          <!--↓↓↓↓↓/// NAME INPUT ///↓↓↓↓↓-->
          <div v-if="props.action == 'register'" class="flex -mx-3">
            <div class="w-full px-3 mb-5">
              <label for="name" class="px-1 text-xs font-semibold">Name</label>
              <div class="flex">
                <div
                  class="z-10 flex items-center justify-center w-10 pl-1 text-center pointer-events-none"
                >
                  <i class="text-lg text-gray-400 mdi mdi-lock-outline"></i>
                </div>

                <input
                  id="name"
                  type="text"
                  class="w-full py-2 pl-10 pr-3 -ml-10 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                  placeholder="Name"
                  v-model="formData.name"
                />
              </div>
              <p v-if="errors?.name" class="error-msg">{{ errors.name[0] }}</p>
            </div>
          </div>

          <!--↓↓↓↓↓/// EMAIL INPUT ///↓↓↓↓↓-->
          <div class="flex -mx-3">
            <div class="w-full px-3 mb-5">
              <label for="email" class="px-1 text-xs font-semibold"
                >Email</label
              >
              <div class="flex">
                <div
                  class="z-10 flex items-center justify-center w-10 pl-1 text-center pointer-events-none"
                >
                  <i class="text-lg text-gray-400 mdi mdi-email-outline"></i>
                </div>
                <input
                  type="email"
                  id="email"
                  class="w-full py-2 pl-10 pr-3 -ml-10 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                  placeholder="email@email.com"
                  v-model="formData.email"
                />
              </div>
              <p v-if="errors?.email" class="error-msg">
                {{ errors.email[0] }}
              </p>
            </div>
          </div>

          <div v-if="props.action == 'register'" class="flex -mx-3">
            <!--↓↓↓↓↓/// COUNTRY SELECT ///↓↓↓↓↓-->
            <div class="px-3 mb-5 w-[50%]">
              <label for="country" class="px-1 text-xs font-semibold"
                >Country</label
              >
              <select
                class="w-full py-2 pl-1 pr-3 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                id="country"
                v-model="formData.country"
                @change="fetchLanguages"
              >
                <option value="" disabled>Select a country</option>
                <option
                  v-for="country in countries"
                  :key="country.name"
                  :value="country.name"
                >
                  {{ country.name }}
                </option>
              </select>
              <p v-if="errors?.country" class="error-msg">
                {{ errors.country[0] }}
              </p>
            </div>

            <!--↓↓↓↓↓/// LANGUAGE SELECT ///↓↓↓↓↓-->
            <div class="w-[50%] px-3 mb-5">
              <label for="language" class="px-1 text-xs font-semibold"
                >Language</label
              >
              <select
                class="w-full py-2 pl-1 pr-3 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                v-model="formData.language"
              >
                <option value="" disabled>Select a language</option>
                <option
                  v-for="language in languages"
                  :key="language.name"
                  :value="language.name"
                >
                  {{ language.name }}
                </option>
              </select>

              <p v-if="errors?.language" class="error-msg">
                {{ errors.language[0] }}
              </p>
            </div>
          </div>

          <!--↓↓↓↓↓/// PASSWORD INPUT ///↓↓↓↓↓-->
          <div class="flex -mx-3">
            <div class="w-full px-3 mb-5">
              <label for="password" class="px-1 text-xs font-semibold"
                >Password</label
              >
              <div class="flex">
                <div
                  class="z-10 flex items-center justify-center w-10 pl-1 text-center pointer-events-none"
                >
                  <i class="text-lg text-gray-400 mdi mdi-account-outline"></i>
                </div>
                <input
                  id="password"
                  type="password"
                  class="w-full py-2 pl-10 pr-3 -ml-10 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                  placeholder="************"
                  v-model="formData.password"
                />
              </div>
              <p v-if="errors?.password" class="error-msg">
                {{ errors.password[0] }}
              </p>
            </div>
          </div>

          <!--↓↓↓↓↓/// PASSWORD CONFIRM INPUT ///↓↓↓↓↓-->
          <div v-if="props.action == 'register'" class="flex -mx-3">
            <div class="w-full px-3 mb-5">
              <label for="confirm-password" class="px-1 text-xs font-semibold"
                >Confirm Password</label
              >
              <div class="flex">
                <div
                  class="z-10 flex items-center justify-center w-10 pl-1 text-center pointer-events-none"
                >
                  <i class="text-lg text-gray-400 mdi mdi-account-outline"></i>
                </div>
                <input
                  id="confirm-password"
                  type="password"
                  class="w-full py-2 pl-10 pr-3 -ml-10 border-2 border-gray-200 rounded-lg outline-none focus:border-indigo-500"
                  placeholder="************"
                  v-model="formData.password_confirmation"
                />
              </div>
            </div>
          </div>

          <!--↓↓↓↓↓/// ACTION BUTTONS ///↓↓↓↓↓-->
          <div class="flex -mx-3">
            <div class="w-full px-3 mb-5">
              <button
                v-bind:disabled="loading"
                class="block w-full max-w-xs px-3 py-3 mx-auto font-semibold text-white bg-indigo-500 rounded-lg d hover:bg-indigo-700 focus:bg-indigo-700"
              >
                {{ props.action.toUpperCase() }} NOW
              </button>
            </div>
            <div class="w-full px-3 mb-5">
              <RouterLink
                class="block w-full max-w-xs px-3 py-3 mx-auto font-semibold text-center text-black bg-indigo-300 rounded-lg hover:bg-indigo-400 focus:bg-indigo-700"
                :to="{ name: props.action == 'login' ? 'register' : 'login' }"
                >{{
                  props.action == "login" ? "REGISTRATE" : "LOGIN"
                }}</RouterLink
              >
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<style>
.error-msg {
  color: red;
}
</style>
