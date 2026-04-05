// frontend/api/index.ts
import { statusService } from './status'
import { tasksService } from './tasks'

export const $api = {
    status: statusService,
    tasks: tasksService,
    // Add more services here as you create them
}