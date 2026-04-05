export interface Task {
    id: number;
    name: string;
    completed: boolean;
    isSyncing?: boolean; // Optional property to track if the task is currently syncing with the server
}