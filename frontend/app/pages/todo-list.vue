<script setup lang="ts">
// 1. Reactive State for the Checklist
const tasks = ref([
  // Completed Milestones
  { id: 1, label: 'Spin up a fresh docker compose for laravel, redis, mysql', completed: true },
  { id: 2, label: 'Add nuxt to the containers', completed: true },
  { id: 3, label: 'Create a api resource contract system', completed: true },
  { id: 4, label: 'Create my own system status report', completed: true },
  { id: 5, label: 'Handle multi page on nuxt', completed: true },
  { id: 6, label: 'Handle request abortion', completed: true },
  
  // Upcoming Lab Goals
  { id: 7, label: 'Be able to read / write / delete from database', completed: false },
  { id: 8, label: 'Form handling and Validation', completed: false },
  { id: 9, label: 'Adding new services alongside new APIs', completed: false },
])

// 2. Simple Toggle Logic
const toggleTask = (id: number) => {
  const task = tasks.value.find(t => t.id === id)
  if (task) task.completed = !task.completed
}

// 3. Progress Calculation
const progress = computed(() => {
  const total = tasks.value.length
  const finished = tasks.value.filter(t => t.completed).length
  return Math.round((finished / total) * 100)
})
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-slate-900">Roadmap</h1>
      <p class="text-slate-500">Track your Full-Stack progress</p>
      
      <div class="mt-4 w-full bg-slate-200 rounded-full h-2.5">
        <div 
          class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" 
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
      <p class="text-xs text-right mt-1 text-slate-400">{{ progress }}% Complete</p>
    </header>

    <ul class="space-y-3">
      <li 
        v-for="task in tasks" 
        :key="task.id"
        @click="toggleTask(task.id)"
        class="flex items-center p-4 bg-white border rounded-xl shadow-sm cursor-pointer hover:border-blue-400 transition-colors"
        :class="{ 'bg-slate-50 border-slate-200': task.completed }"
      >
        <div 
          class="w-6 h-6 rounded-full border-2 mr-4 flex items-center justify-center transition-colors"
          :class="task.completed ? 'bg-green-500 border-green-500' : 'border-slate-300'"
        >
          <svg v-if="task.completed" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <span 
          class="text-lg font-medium transition-all"
          :class="task.completed ? 'text-slate-400 line-through' : 'text-slate-700'"
        >
          {{ task.label }}
        </span>
      </li>
    </ul>

    <footer class="mt-10 p-4 border-t border-dashed border-slate-200 text-center">
      <p class="text-sm text-slate-500 italic">
        "Persistence is the path to a bug-free life."
      </p>
    </footer>
  </div>
</template>