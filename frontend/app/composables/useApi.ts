import { useRuntimeConfig } from "nuxt/app";

export const useApi = <T>(path: string, options = {}) => {
    // Now Nuxt will recognize this because it's in the composables folder
    const config = useRuntimeConfig();

    // Example: Pulling the base URL from nuxt.config.ts
    const baseURL = config.public.apiBase;

    return $fetch<T>(path, {
        baseURL,
        ...options,
    });
};