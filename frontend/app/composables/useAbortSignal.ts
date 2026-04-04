export const useAbortSignal = () => {
    const controller = new AbortController();

    onBeforeUnmount(() => {
        controller.abort();
    });

    return controller.signal;
};