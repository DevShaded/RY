<script setup lang="ts">
import CitySearch from '@/components/CitySearch.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/vue3';
import { Cloud, CloudLightning, CloudMoon, CloudRain, CloudSnow, CloudSun, Moon, Sun } from 'lucide-vue-next';

export interface WeatherList {
    data?: DataEntity[] | null;
}
export interface DataEntity {
    id: number;
    name: string;
    country: string;
    temperature: number;
    icon: string;
}

const iconMap: Record<string, typeof Cloud> = {
    sun: Sun,
    moon: Moon,
    cloud: Cloud,
    'cloud-sun': CloudSun,
    'cloud-moon': CloudMoon,
    'cloud-rain': CloudRain,
    'cloud-lightning': CloudLightning,
    'cloud-snow': CloudSnow,
    'cloud-fog': Cloud,
};

defineProps<{
    weathers: WeatherList;
}>();

const locationIcon = (icon: string) => {
    return iconMap[icon] || Cloud;
};
</script>

<template>
    <Head>
        <title>Din værdata</title>
    </Head>
    <div class="container mx-auto min-h-screen max-w-4xl p-4">
        <div class="mb-8 flex justify-center">
            <CitySearch placeholder="Search for a city..." />
        </div>

        <div v-if="!weathers.data || weathers.data.length === 0" class="py-12 text-center">
            <Cloud class="text-primary mx-auto mb-4 h-16 w-16" />
            <h3 class="mb-2 text-lg font-medium">Ingen værdata funnet</h3>
            <p class="text-secondary-foreground">Prøv å søke etter en annen by.</p>
        </div>

        <div v-else class="space-y-2">
            <h2 class="mb-4 text-2xl font-semibold">Din Værdata</h2>
            <Link v-for="weather in weathers.data" :key="weather.id" :href="route('weather.show', { location: weather.name })" prefetch="hover">
                <Card class="hover:border-primary my-5 block border border-transparent">
                    <CardContent class="p-4">
                        <div class="mx-5 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <h3 class="font-medium">{{ weather.name }}</h3>
                                    <p class="text-sm">{{ weather.country }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-6">
                                <div class="flex items-center space-x-2">
                                    <component :is="locationIcon(weather.icon)" class="text-primary size-7" />
                                    <span class="text-lg font-semibold">{{ weather.temperature }}°</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

<style scoped></style>
