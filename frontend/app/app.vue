<template>
  <div class="h-screen flex flex-col overflow-hidden bg-slate-50 font-sans">    
    <header class="flex justify-between h-16 bg-white border-b items-center px-6 shrink-0">
      
      <NuxtLink to="/" custom v-slot="{ navigate }">
        <h3 
          @click="navigate" 
          class="font-bold text-xl text-slate-900 cursor-pointer hover:text-slate-600 transition-colors"
        >
          Nuxt + Laravel Demo
        </h3>
      </NuxtLink>
      
      <nav class="flex space-x-2">
        <NuxtLink to="/" class="nav-btn">
          Home
        </NuxtLink>
        
        <NuxtLink to="/todo-list" class="nav-btn">
          Todo List
        </NuxtLink>
        
        <NuxtLink to="/system-status" class="nav-btn">
          System Status
        </NuxtLink>
      </nav>
    </header>

    <main class="flex-1 overflow-y-auto p-6 md:p-10">
      <div class="max-w-4xl mx-auto">
        <NuxtPage />
      </div>
    </main>
  </div>
</template>

<style scoped>
/* 1. Base Button Style (Normal State) */
.nav-btn {
  @apply px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium 
         transition-all duration-150 ease-in-out
         hover:bg-slate-700 hover:shadow-sm
         active:scale-95 active:bg-slate-800;
}

/* 2. Active Button Style (Sticky State) 
   This ONLY targets elements with the .nav-btn class 
*/
.nav-btn.router-link-active {
  @apply bg-blue-600 hover:bg-blue-700 shadow-inner scale-100;
}
</style>