<template>
  <div class="p-6">
    <div class="min-h-screen bg-gray-50 flex flex-col items-center p-6 font-sans text-slate-900">
        <Suspense>
          <StatusCard />
          <template #fallback>
            <div class="p-6 max-w-sm mx-auto bg-gray-100 animate-pulse rounded-xl">
              Loading System Status...
            </div>
          </template>
      </Suspense>
      <p class="mt-8 text-gray-400 text-xs">Powered by Laravel & Nuxt</p>
    </div>
  </div>
</template>