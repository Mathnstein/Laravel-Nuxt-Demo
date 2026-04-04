<script setup lang="ts">
  import { $api } from '~/api'
  const signal = useAbortSignal()
  const { data, pending, error, refresh } = await useAsyncData(
      () => $api.status.getHealth({ signal }), 
      { 
          server: false,
          lazy: true,
          default: () => null
      }
)
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center p-6 font-sans text-slate-900">
    
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
      
      <div class="p-8 pb-0 flex justify-between items-start">
        <div>
          <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">System Status</h1>
          <p class="text-gray-500 text-sm mt-1">Nuxt + Laravel Handshake</p>
        </div>
        
        <div class="flex items-center gap-2 px-3 py-1 rounded-full border transition-all duration-500"
          :class="{
            // Blue: If we are fetching OR if data is simply missing but no error yet
            'bg-blue-50 border-blue-100': pending || (!data && !error),
            
            // Green: Success
            'bg-green-50 border-green-100': !pending && data?.redis_status === 'Online',
            
            // Red: Only if we have an explicit error or bad data status
            'bg-red-50 border-red-100': !pending && (error || (data && data.redis_status !== 'Online'))
          }">
        
          <span class="relative flex h-2 w-2 text-center">
            <svg v-if="pending" class="animate-spin h-2 w-2 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <template v-else-if="data?.redis_status === 'Online'">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </template>

            <span v-else class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
          </span>

          <span class="text-[10px] font-bold uppercase tracking-wider transition-colors duration-500"
            :class="{
              'text-blue-700': pending || (!data && !error),
              'text-green-700': !pending && data?.redis_status === 'Online',
              'text-red-700': !pending && (error || (data && data.redis_status !== 'Online'))
            }">
            {{ (pending || (!data && !error)) ? 'Syncing...' : (data?.redis_status === 'Online' ? 'Live' : 'System Error') }}
          </span>
        </div>
      </div>

      <div class="p-8">
        
        <div v-if="pending" class="space-y-4 animate-pulse">
          <div class="h-12 bg-gray-100 rounded-xl w-full"></div>
          <div class="h-12 bg-gray-100 rounded-xl w-3/4"></div>
        </div>

        <div v-else-if="error" class="p-4 bg-red-50 border border-red-100 rounded-2xl">
          <div class="flex items-center gap-3 text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">Connection Failed</span>
          </div>
          <p class="text-xs text-red-600 mt-1">Backend at localhost:8080 is unreachable. Check Sail & CORS.</p>
        </div>

        <div v-else-if="data" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">DB Version</p>
              <p class="text-gray-800 font-semibold text-sm">{{ data.database_version }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Redis Status</p>
              <p :class="data.redis_status === 'Online' ? 'text-green-600' : 'text-red-600'" class="font-bold text-sm">
                {{ data.redis_status }}
              </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Last Sync</p>
              <p class="text-gray-800 font-mono text-sm">{{ data.server_time }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">PHP Engine</p>
              <p class="text-gray-800 font-semibold text-sm">v{{ data.php_version }}</p>
            </div>

          </div>
        </div>
      </div>

      <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-center">
        <button 
          @click="() => refresh()"
          :disabled="pending"
          class="bg-black hover:bg-gray-800 text-white px-8 py-3 rounded-2xl font-bold text-sm transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <svg v-if="pending" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ pending ? 'Updating...' : 'Refresh Status' }}
        </button>
      </div>
    </div>

    <p class="mt-8 text-gray-400 text-xs">Powered by Laravel Sail & Nuxt 3</p>
  </div>
</template>