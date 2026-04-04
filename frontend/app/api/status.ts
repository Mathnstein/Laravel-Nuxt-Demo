import type { SystemStatus } from '~/types';

export const statusService = {
    getHealth: (options = {}) => {
        return useApi<SystemStatus>(
            `/status?t=${Date.now()}`,
            options
        );
    },
};