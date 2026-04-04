/** * Standard Laravel Resource wrapper 
 */
export interface LaravelResponse<T> {
    data: T;
    links?: any; // For pagination later
    meta?: any;  // For pagination later
}

declare module 'nuxt/schema' {
    interface PublicRuntimeConfig {
        apiBase: string
    }
}

// Crucial: This ensures the file is treated as a module
export { };

