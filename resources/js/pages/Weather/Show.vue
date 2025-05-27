<script setup lang="ts">
import ForecastCard from '@/components/ForecastCard.vue';
import WeatherCard from '@/components/WeatherCard.vue';
import { Weather } from '@/types';

defineProps<{
    weather: Weather;
}>();
</script>

<template>
    <div class="min-h-screen">
        <div class="max-w-5xl mx-auto">
            <section class="space-y-6 py-12">
                <WeatherCard :weather="weather" />

                <ForecastCard :forecast="weather.data.forecast" />
            </section>
        </div>
    </div>
</template>

<style scoped>

</style>
