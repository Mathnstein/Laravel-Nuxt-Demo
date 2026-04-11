import { useRuntimeConfig } from "nuxt/app";

export const useApi = <T>(path: string, params?: Record<string, any>, options = {}) => {
    // Now Nuxt will recognize this because it's in the composables folder
    const config = useRuntimeConfig();

    // Example: Pulling the base URL from nuxt.config.ts
    const baseURL = config.public.apiBase;

    return $fetch<T>(path, {
        baseURL,
        params,
        ...options,
    });
};