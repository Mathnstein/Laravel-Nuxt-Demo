// frontend/api/index.ts
import { statusService } from './status'

export const $api = {
    status: statusService,
    // Add more services here as you create them
}