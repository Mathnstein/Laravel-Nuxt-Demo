import type { LaravelResponse } from '~/types/api';
import type { Task } from '../types/tasks';

export const tasksService = {
    getTasks: (params?: { search?: string }, options = {}) => {
        return useApi<LaravelResponse<Task[]>>(
            `/tasks`,
            params,
            options
        );
    },
    createTask: (data: Omit<Task, 'id'>, options = {}) => {
        return useApi<Task>(
            `/tasks`,
            {},
            {
                method: 'POST',
                body: JSON.stringify(data),
                ...options,
            }
        );
    },
    updateTask: (id: number, data: Partial<Omit<Task, 'id'>>, options = {}) => {
        return useApi<Task>(
            `/tasks/${id}`,
            {},
            {
                method: 'PATCH',
                body: JSON.stringify(data),
                ...options,
            }
        );
    },
    deleteTask: (id: number, options = {}) => {
        return useApi<void>(
            `/tasks/${id}`,
            {},
            {
                method: 'DELETE',
                ...options,
            }
        );
    },
};
