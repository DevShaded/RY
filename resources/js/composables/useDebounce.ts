import type { Ref } from 'vue';
import { ref, watch } from 'vue';

export function useDebounce<T>(value: Ref<T>, delay: number = 300): Ref<T> {
    const debouncedValue = ref(value.value) as Ref<T>;
    let timeout: number;

    watch(value, (newValue) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            debouncedValue.value = newValue;
        }, delay);
    });

    return debouncedValue;
}
