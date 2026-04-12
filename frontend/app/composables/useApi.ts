import { useRuntimeConfig } from "nuxt/app";

export const useApi = <T>(path: string, params?: Record<string, any>, options = {}) => {
    // Now Nuxt will recognize this because it's in the composables folder
    const config = useRuntimeConfig();

    // Example: Pulling the base URL from nuxt.config.ts
    const baseURL = config.public.apiBase;

    const apiPath = path.startsWith('/api') ? path : `/api${path.startsWith('/') ? '' : '/'}${path}`;

    return $fetch<T>(apiPath, {
        baseURL,
        params,
        ...options,
    });
};