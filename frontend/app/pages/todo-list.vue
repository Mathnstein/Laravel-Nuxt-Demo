<script setup lang="ts">
  import { ref, computed } from 'vue'
  import { $api } from '~/api'
  import type { LaravelResponse, Task } from '~/types'
  const signal = useAbortSignal()

  // Get tasks from the API with auto reactivity and built in loading/error states
  const {data: response, status} = useAsyncData<LaravelResponse<Task[]>>(
    'roadmap-tasks',
    () => $api.tasks.getTasks({ signal }), 
    { 
        server: false,
        default: () => {
          return {
            data: []
          }
        }
    }
  )

  // We use a local ref to manage the tasks in the UI
  const tasks = ref<Task[]>([])
  watch(response, (newVal) => {
    if (newVal?.data) {
      tasks.value = newVal.data
    }
  }, { immediate: true })

  // Helper to wrap our async logic and track all api requests
  const activeRequests = ref(new Set<number>())
  const trackRequest = async (id: number, callback: () => Promise<void>) => {
    activeRequests.value.add(id)
    try {
      await callback()
    } finally {
      // finally ensures it's removed even if the request fails
      activeRequests.value.delete(id)
    }
  }

  // Add Task
  const newTaskName = ref('')
  const addTask = async () => {
    if (!tasks.value) return
    const name = newTaskName.value.trim()
    if (!name) return

    // Optimistically add the task to the UI
    const newTask: Task = {
      id: Date.now(), // Temporary ID for UI purposes
      name,
      completed: false,
      isSyncing: true // Mark as syncing until we get a response from the server
    }
    tasks.value = [...tasks.value, newTask]
    newTaskName.value = ''

    await trackRequest(newTask.id, async () => {
      try {
        const response = await $api.tasks.createTask({ 
          ...newTask
        }, { signal })

        // Replace the temporary task with the one from the server (which has the real ID)
        tasks.value = tasks.value.map(t => t.id === newTask.id ? response : t)

      } catch (err) {
        // Log the error and revert the optimistic update, but keep the input value so the user doesn't lose their work
        console.error('Failed to add task:', err)
        tasks.value = tasks.value.filter(t => t.id !== newTask.id)
        newTaskName.value = name
      }
    })
  }

  // Patch Task
  const updateTask = async (id: number, toggle?: boolean, newName?: string) => {
    if (!tasks.value) return
    const taskIndex = tasks.value.findIndex(t => t.id === id)
    const taskToToggle = tasks.value[taskIndex]
    if (taskIndex === -1 || !taskToToggle || taskToToggle.isSyncing) return

    // Optimistically update the UI
    const previousState = [...tasks.value]
    tasks.value = tasks.value.map(task => 
      task.id === id ? { ...task, isSyncing: true, completed: toggle ? !task.completed : task.completed, name: newName ?? task.name } : task
    )

    // Send the update to the server
    await trackRequest(id, async () => {
      try {
        await $api.tasks.updateTask(id, { 
          completed: toggle ? !taskToToggle.completed : taskToToggle.completed,
          name: newName
        }, { signal })

        tasks.value = tasks.value.map(task => 
          task.id === id ? { ...task, isSyncing: false } : task
        )
      } catch (err) {
        console.error('Failed to update task:', err)
        tasks.value = previousState // Revert to previous state on error
      }
    })
}

  // Delete Task
  const deleteTask = async (id: number) => {
    const taskIndex = tasks.value.findIndex(t => t.id === id)
    const taskToDelete = tasks.value[taskIndex]
    if (taskIndex === -1 || !taskToDelete || taskToDelete.isSyncing) return

    // Optimistically remove the task from the UI
    const previousState = [...tasks.value]
    tasks.value = tasks.value.filter(task => task.id !== id)

    await trackRequest(id, async () => {
      try {
        await $api.tasks.deleteTask(id, { signal })
      } catch (err) {
        // Revert to previous state on error
        console.error('Failed to delete task:', err)
        tasks.value = previousState 
      }
    })
  }

  // Derived state for easier UI handling
  const todoTasks = computed(() => tasks.value.filter(t => !t.completed) ?? [])
  const completedTasks = computed(() => tasks.value.filter(t => t.completed) ?? [])
  const progress = computed(() => {
    const total = tasks.value.length ?? 0
    const finished = completedTasks.value.length
    return Math.round((finished / total) * 100)
  })
  const isSyncing = computed(() => activeRequests.value.size > 0)
</script>

<template>
  <div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6">
    <header class="max-w-2xl mx-auto text-center">
      <div class="flex items-center justify-center gap-3">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Roadmap</h1>
        
        <div class="w-6 h-6 flex items-center justify-center">
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-75"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0 scale-75"
          >
            <svg 
              v-if="isSyncing" 
              class="animate-spin h-5 w-5 text-blue-500" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </transition>
        </div>
      </div>

      <div v-if="status === 'success'" class="mt-4 w-full bg-slate-200 rounded-full h-2.5">
        <div 
          class="bg-blue-600 h-2.5 rounded-full transition-all duration-700" 
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </header>

    <div v-if="['pending', 'idle'].includes(status)" class="flex flex-col md:flex-row gap-4 md:gap-12 opacity-50">
      <div v-for="i in 2" :key="i" class="w-full md:w-1/2 space-y-4">
        <div class="h-4 w-24 bg-slate-200 animate-pulse rounded" />
        <div v-for="j in 3" :key="j" class="h-16 w-full bg-slate-100 animate-pulse rounded-xl" />
      </div>
    </div>

    <main v-else-if="status === 'success'" class="flex flex-col md:flex-row gap-4 md:gap-12 items-start">
      <section class="w-full md:w-1/2">
        <div class="flex items-center justify-between mb-3 px-1">
          <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Upcoming</h2>
          <span class="text-[12px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">
            Tasks left: {{ todoTasks.length }}
          </span>
        </div>

        <div class="mb-6 relative group/input">
          <input 
            v-model="newTaskName"
            @keyup.enter="addTask"
            type="text"
            placeholder="Add a new goal..."
            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all shadow-sm text-sm"
          />
          <button 
            @click="addTask"
            :disabled="!newTaskName.trim()"
            :class="[
              'absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg transition-all duration-200',
              'enabled:bg-blue-600 enabled:text-white enabled:hover:bg-blue-700 enabled:shadow-sm',
              'disabled:opacity-0 disabled:pointer-events-none'
            ]"
            title="Add Goal"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </button>
        </div>
        
        <ul class="space-y-2 overflow-y-auto max-h-[60vh] md:max-h-[60vh] pr-2 scrollbar-thin">
          <Task 
            v-for="task in todoTasks" 
            :key="task.id" 
            :task="task" 
            @update="updateTask"
            @delete="deleteTask"
          />
        </ul>
      </section>

      <section class="w-full md:w-1/2 pt-4 md:pt-0 border-t border-slate-100 md:border-t-0">
        <div class="flex items-center justify-between mb-3 px-1">
          <h2 class="text-xs font-bold text-green-600 uppercase tracking-widest">Achieved</h2>
          <span class="text-[12px] font-bold bg-green-100 text-green-600 px-2 py-0.5 rounded-full">
            Tasks completed: {{ completedTasks.length }}
          </span>
        </div>

        <ul class="space-y-2 overflow-y-auto max-h-[60vh] md:max-h-[60vh] pr-2 opacity-75 scrollbar-thin">
          <Task 
            v-for="task in completedTasks" 
            :key="task.id" 
            :task="task" 
            @update="updateTask"
            @delete="deleteTask"
          />
        </ul>
      </section>
    </main>

    <footer class="pt-4 border-t border-slate-100 text-center">
      <p class="text-xs text-slate-400 italic">"Persistence is the path to a bug-free life."</p>
    </footer>
  </div>
</template>

