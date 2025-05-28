<script setup lang="ts">
import CitySearch from '@/components/CitySearch.vue';
import ExternalTextLink from '@/components/ExternalTextLink.vue';
import ForecastCard from '@/components/ForecastCard.vue';
import WeatherCard from '@/components/WeatherCard.vue';
import { formatDateTime } from '@/lib/utils';
import { Weather } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';

defineProps<{
    weather: Weather;
}>();
</script>

<template>
    <Head>
        <title>{{ weather.data.name }}</title>
    </Head>

    <div class="min-h-screen">
        <div class="mx-auto max-w-5xl">
            <section class="space-y-6 py-12">
                <div class="mb-8 flex justify-center">
                    <CitySearch placeholder="Søk etter by..." />
                </div>

                <div>
                    <!-- Go back link -->
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        <ExternalTextLink :href="route('home')" class="flex hover:underline">
                            <ArrowLeftIcon class="size-5" /> Tilbake til væroversikt
                        </ExternalTextLink>
                    </p>
                </div>
                <WeatherCard :weather="weather" />

                <ForecastCard :forecast="weather.data.forecast" />

                <div class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                    <p>
                        Værdataene er hentet fra <ExternalTextLink href="https://openweathermap.org" target="_blank">Openweathermap</ExternalTextLink>
                    </p>
                    <p class="mt-3">Sist oppdatert {{ formatDateTime(weather.data.updated_at) }}</p>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped></style>
