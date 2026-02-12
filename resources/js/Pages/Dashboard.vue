<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">
        Campaign metrics
      </h1>

      <div
        v-if="error"
        class="mb-6 rounded-lg bg-red-50 p-4 text-red-800"
        role="alert"
      >
        {{ error }}
      </div>

      <div
        v-if="!error && Object.keys(metrics).length === 0"
        class="rounded-lg bg-gray-100 p-6 text-gray-600"
      >
        No campaign data available.
      </div>

      <div
        v-else
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
      >
        <div
          v-for="(value, key) in metrics"
          :key="key"
          class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
        >
          <p class="text-sm font-medium uppercase tracking-wide text-gray-500">
            {{ formatLabel(key) }}
          </p>
          <p class="mt-1 text-2xl font-semibold text-gray-900">
            {{ formatValue(key, value) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  metrics: {
    type: Object,
    default: () => ({}),
  },
  error: {
    type: String,
    default: null,
  },
});

const metrics = computed(() => props.metrics || {});

function formatLabel(key) {
  return key
    .replace(/_/g, ' ')
    .replace(/([A-Z])/g, ' $1')
    .replace(/^\w/, (c) => c.toUpperCase())
    .trim();
}

const currencyKeys = ['spend', 'cost', 'amount', 'revenue'];
function formatValue(key, value) {
  const num = Number(value);
  if (Number.isNaN(num)) return value;
  const lower = key.toLowerCase();
  const isCurrency = currencyKeys.some((k) => lower.includes(k));
  if (isCurrency) {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(num);
  }
  return new Intl.NumberFormat('en-US', {
    maximumFractionDigits: 2,
  }).format(num);
}
</script>
