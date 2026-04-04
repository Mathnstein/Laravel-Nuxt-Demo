<script setup lang="ts">
import { ref, computed } from 'vue'

const tasks = ref([
  { id: 1, label: 'Spin up a fresh docker compose for laravel, redis, mysql', completed: true },
  { id: 2, label: 'Add nuxt to the containers', completed: true },
  { id: 3, label: 'Create a api resource contract system', completed: true },
  { id: 4, label: 'Create my own system status report', completed: true },
  { id: 5, label: 'Handle multi page on nuxt', completed: true },
  { id: 6, label: 'Handle request abortion', completed: true },
  { id: 7, label: 'Be able to read / write / delete from database', completed: false },
  { id: 8, label: 'Form handling and Validation', completed: false },
  { id: 9, label: 'Adding new services alongside new APIs', completed: false },
])

const todoTasks = computed(() => tasks.value.filter(t => !t.completed))
const completedTasks = computed(() => tasks.value.filter(t => t.completed))

const toggleTask = (id: number) => {
  const task = tasks.value.find(t => t.id === id)
  if (task) task.completed = !task.completed
}

const progress = computed(() => {
  const total = tasks.value.length
  const finished = completedTasks.value.length
  return Math.round((finished / total) * 100)
})
</script>

<template>
  <div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6">
    
    <header class="max-w-2xl mx-auto text-center">
      <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Lab Roadmap</h1>
      <div class="mt-4 w-full bg-slate-200 rounded-full h-2.5">
        <div 
          class="bg-blue-600 h-2.5 rounded-full transition-all duration-700" 
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </header>

    <main class="flex flex-col md:flex-row gap-4 md:gap-12 items-start">
      
      <section class="w-full md:w-1/2">
        <div class="flex items-center justify-between mb-3 px-1">
          <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Upcoming</h2>
          <span class="text-[10px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">
            Tasks left: {{ todoTasks.length }}
          </span>
        </div>
        
        <ul class="space-y-2 overflow-y-auto max-h-[60vh] md:h-[50vh] pr-2 scrollbar-thin">
          <li 
            v-for="task in todoTasks" :key="task.id"
            @click="toggleTask(task.id)"
            class="flex items-start p-4 bg-white border border-slate-200 rounded-xl shadow-sm cursor-pointer hover:border-blue-500 transition-all"
          >
            <div class="w-5 h-5 rounded-full border-2 border-slate-300 mr-3 mt-0.5 flex-shrink-0" />
            <span class="text-sm md:text-base font-semibold text-slate-700 leading-tight">{{ task.label }}</span>
          </li>
        </ul>
      </section>

      <section class="w-full md:w-1/2 pt-4 md:pt-0 border-t border-slate-100 md:border-t-0">
        <div class="flex items-center justify-between mb-3 px-1">
          <h2 class="text-xs font-bold text-green-600 uppercase tracking-widest">Achieved</h2>
        </div>

        <ul class="space-y-2 overflow-y-auto max-h-[60vh] md:h-[50vh] pr-2 opacity-75 scrollbar-thin">
          <li 
            v-for="task in completedTasks" :key="task.id"
            @click="toggleTask(task.id)"
            class="flex items-start p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer"
          >
            <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-sm md:text-base font-medium text-slate-400 line-through leading-tight">{{ task.label }}</span>
          </li>
        </ul>
      </section>

    </main>

    <footer class="pt-4 border-t border-slate-100 text-center">
      <p class="text-xs text-slate-400 italic">"Persistence is the path to a bug-free life."</p>
    </footer>
  </div>
</template>

