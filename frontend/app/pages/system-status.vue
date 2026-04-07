<template>
  <div class="flex flex-1 flex-col bg-gray-50 font-sans text-slate-900">
    <main class="flex-1 flex flex-col items-center overflow-y-auto justify-center p-6 min-h-0">
      <Suspense>
        <StatusCard />
        <template #fallback>
          <div class="p-6 max-w-sm w-full bg-gray-100 animate-pulse rounded-xl">
            Loading System Status...
          </div>
        </template>
      </Suspense>
      <p class="mt-8 text-gray-400 text-xs text-center">
        Powered by Laravel & Nuxt
      </p>
    </main>
  </div>
</template>