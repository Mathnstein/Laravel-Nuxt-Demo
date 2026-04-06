<script setup lang="ts">
import type { Task } from '~/types';

// Define what data this component needs
const props = defineProps<{
  task: Task
}>()

// Define the actions this component can trigger
const emit = defineEmits<{
  (e: 'update', id: number, toggle?: boolean, newName?: string): void
  (e: 'delete', id: number): void
}>()

const isEditing = ref(false)
const editedName = ref(props.task.name)
const editInput = ref<HTMLInputElement | null>(null)

const startEdit = () => {
  if (props.task.completed || props.task.isSyncing) return
  editedName.value = props.task.name
  isEditing.value = true
  // Auto-focus the input on next tick
  nextTick(() => editInput.value?.focus())
}

const saveEdit = () => {
  if (editedName.value.trim() && editedName.value !== props.task.name) {
    emit('update', props.task.id, false, editedName.value.trim())
  }
  isEditing.value = false
}

const cancelEdit = () => {
  editedName.value = props.task.name
  isEditing.value = false
}
</script>

<template>
  <li 
    :class="[
        'relative overflow-hidden flex items-start group border border-slate-200 rounded-xl',
        // Conditional styling based on task status
        task.completed 
            ? 'p-3 bg-slate-50' 
            : 'p-4 bg-white',
        task.isSyncing 
            ? 'opacity-50 cursor-wait' 
            : 'transition-all shadow-sm hover:border-blue-500 focus-within:border-blue-500 group-focus:border-blue-500',
        isEditing 
            ? 'ring-2 ring-blue-500 border-transparent'
            : ''
    ]"
  >
    <!-- Shimmer effect for syncing tasks -->
    <div
        v-if="task.isSyncing"
        class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-r from-transparent via-blue-400/30 to-transparent animate-shimmer"
        style="width: 100%;"
    />

    <button
        @click.stop="emit('update', task.id, true)" 
        :disabled="task.isSyncing"
        :class="[
            'group/btn w-5 h-5 rounded-full mt-0.5 mr-3 border-2 flex-shrink-0 flex items-center justify-center transition-all duration-200',
            
            // Status: Completed (Solid Green -> Hollow on Hover)
            task.completed 
            ? 'bg-green-500 border-green-500 hover:bg-transparent hover:border-slate-300' 
            : 'border-slate-300',

            // Status: Pending (Hollow -> Solid Green on Hover)
            !task.completed && !task.isSyncing 
            ? 'bg-transparent hover:bg-green-600 hover:border-green-600' 
            : '',

            task.isSyncing 
            ? 'bg-slate-200 border-transparent cursor-wait' 
            : 'cursor-pointer'
        ]"
    >
        <svg 
            v-if="task.completed" 
            class="w-3 h-3 text-white transition-opacity duration-200 group-hover/btn:opacity-0" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
        </svg>
    </button>

    <div class="flex-grow">
        <input 
            v-if="isEditing"
            ref="editInput"
            v-model="editedName"
            @blur="saveEdit"
            @keyup.enter="saveEdit"
            @keyup.esc="cancelEdit"
            class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm md:text-base font-semibold text-slate-700 leading-tight"
        />

        <span 
            v-else
            :tabindex="task.isSyncing || task.completed ? -1 : 0"
            @dblclick.stop.prevent="startEdit"
            @keyup.enter.prevent="startEdit"
            @keyup.space.prevent="startEdit"
            :class="[
            'text-sm md:text-base leading-tight block',
            task.completed ? 'font-medium text-slate-400 line-through' : 'font-semibold text-slate-700 cursor-text'
            ]"
        >
            {{ task.name }}
        </span>
    </div>


    <button
        v-if="!task.isSyncing"
        @click.stop="emit('delete', task.id)"
        :class="[
            'ml-2 p-1 transition-opacity',
            'opacity-0 focus:opacity-100 group-hover:opacity-100 group-focus:opacity-100 text-slate-300 hover:text-red-500 focus:text-red-500',
        ]"
        title="Delete task"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
      </svg>
    </button>
  </li>
</template>

<style scoped>
    @keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
    }

    .animate-shimmer {
    /* Shortened to 1s to make it very obvious for debugging */
    animation: shimmer 1s infinite linear;
    }
</style>