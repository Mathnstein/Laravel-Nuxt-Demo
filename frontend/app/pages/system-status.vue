<template>
  <div class="p-6">
    <Suspense>
      <StatusCard />

      <template #fallback>
        <div class="p-6 max-w-sm mx-auto bg-gray-100 animate-pulse rounded-xl">
          Loading System Status...
        </div>
      </template>
    </Suspense>
  </div>
</template>