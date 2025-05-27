<script setup lang="ts">
import CitySearch from '@/components/CitySearch.vue';
import ForecastCard from '@/components/ForecastCard.vue';
import WeatherCard from '@/components/WeatherCard.vue';
import { Weather } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    weather: Weather;
}>();
</script>

<template>
    <Head>
        <title>{{ weather.data.name }}</title>
    </Head>

    <div class="min-h-screen">
        <div class="max-w-5xl mx-auto">
            <section class="space-y-6 py-12">
                <div class="flex justify-center mb-8">
                    <CitySearch placeholder="Søk etter by..." />
                </div>

                <WeatherCard :weather="weather" />

                <ForecastCard :forecast="weather.data.forecast" />
            </section>
        </div>
    </div>
</template>

<style scoped>

</style>
