<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useDebounce } from '@/composables/useDebounce';
import { Search, X } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  placeholder?: string;
}>();

const search = ref('');
const results = ref<{ id: number; name: string; country?: string }[]>([]);
const loading = ref(false);
const showResults = ref(false);
const selectedIndex = ref(-1);
const searchInput = ref<HTMLInputElement | null>(null);
const searchContainer = ref<HTMLDivElement | null>(null);

const debouncedSearch = useDebounce(search, 300);

const displayResults = computed(() => {
  return showResults.value && results.value.length > 0;
});

const clearSearch = () => {
  search.value = '';
  results.value = [];
  showResults.value = false;
  selectedIndex.value = -1;
};

const selectCity = (city: { name: string }) => {
  router.visit(`/weather/${city.name}`);
  clearSearch();
};

const handleKeyDown = (event: KeyboardEvent) => {
  if (!showResults.value || results.value.length === 0) return;

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault();
      selectedIndex.value = selectedIndex.value < results.value.length - 1
        ? selectedIndex.value + 1
        : 0;
      break;
    case 'ArrowUp':
      event.preventDefault();
      selectedIndex.value = selectedIndex.value > 0
        ? selectedIndex.value - 1
        : results.value.length - 1;
      break;
    case 'Enter':
      event.preventDefault();
      if (selectedIndex.value >= 0 && selectedIndex.value < results.value.length) {
        selectCity(results.value[selectedIndex.value]);
      }
      break;
    case 'Escape':
      event.preventDefault();
      showResults.value = false;
      selectedIndex.value = -1;
      searchInput.value?.blur();
      break;
  }
};

const handleClickOutside = (event: MouseEvent) => {
  if (searchContainer.value && !searchContainer.value.contains(event.target as Node)) {
    showResults.value = false;
  }
};

watch(debouncedSearch, async (value) => {
  if (!value || value.length < 2) {
    results.value = [];
    selectedIndex.value = -1;
    return;
  }

  loading.value = true;

  try {
    const response = await fetch(`/api/cities/search?query=${encodeURIComponent(value)}`);
    const data = await response.json();

    if (Array.isArray(data)) {
      results.value = data;
      selectedIndex.value = -1;
      showResults.value = true;
    }
  } catch (error) {
    console.error('Error fetching city suggestions:', error);
  } finally {
    loading.value = false;
  }
});

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div ref="searchContainer" class="relative w-full max-w-xs">
    <div class="relative">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <Search :class="[loading ? 'animate-pulse' : '', 'h-4 w-4 text-neutral-400']" />
      </div>
      <Input
        ref="searchInput"
        v-model="search"
        type="text"
        :placeholder="placeholder || 'Søk etter by...'"
        class="w-full pl-10 pr-10"
        @focus="showResults = true"
        @keydown="handleKeyDown"
      />
      <Button
        v-if="search"
        variant="ghost"
        size="icon"
        class="absolute inset-y-0 right-0 h-auto w-auto px-3"
        @click="clearSearch"
      >
        <X class="h-4 w-4" />
      </Button>
    </div>

    <Card
      v-if="displayResults"
      class="absolute z-10 mt-1 w-full shadow-lg"
    >
      <ul class="max-h-60 overflow-auto py-1">
        <li
          v-for="(city, index) in results"
          :key="city.id"
          :class="[
            'cursor-pointer px-4 py-2 transition-colors',
            selectedIndex === index
              ? 'bg-primary text-primary-foreground'
              : 'hover:bg-neutral-100 dark:hover:bg-neutral-800'
          ]"
          @click="selectCity(city)"
          @mouseenter="selectedIndex = index"
        >
          <div class="flex items-center">
            <span class="text-sm font-medium">{{ city.name }}</span>
            <span v-if="city.country" class="ml-1 text-xs opacity-70">{{ city.country }}</span>
          </div>
        </li>
      </ul>
    </Card>
  </div>
</template>
