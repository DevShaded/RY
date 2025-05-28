<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Weather } from '@/types';
import {
    Cloud,
    CloudLightning,
    CloudMoon,
    CloudRain,
    CloudSnow,
    CloudSun,
    Droplets,
    Eye,
    MapPin,
    Moon,
    Sun,
    Thermometer,
    Wind,
} from 'lucide-vue-next';
import { computed } from 'vue';

const iconMap = {
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

const props = defineProps<{
    weather: Weather;
}>();

const CurrentIcon = computed(() => {
    return iconMap[props.weather.data.icon] || Cloud;
});

const getCountryFlagEmoji = (countryCode: string): string => {
    const codePoints = countryCode
        .toUpperCase()
        .split('')
        .map((char) => 127397 + char.charCodeAt(0));
    return String.fromCodePoint(...codePoints);
};
</script>

<template>
    <Card>
        <CardHeader class="flex items-center gap-2 text-2xl font-semibold md:text-3xl">
            <MapPin class="text-primary size-6" />
            {{ weather.data.name }} {{ getCountryFlagEmoji(weather.data.country) }}
        </CardHeader>
        <CardContent>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="flex items-center space-x-4">
                    <CurrentIcon class="text-primary h-16 w-16" />
                    <div>
                        <div class="text-5xl font-bold">{{ weather.data.temperature }}°C</div>
                        <div class="text-xl">{{ weather.data.condition }}</div>
                        <div class="text-muted-foreground">Føles ut som {{ weather.data.feels_like }}°C</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center space-x-2">
                        <Droplets class="text-primary h-5 w-5" />
                        <div>
                            <div class="text-muted-foreground text-sm">Fuktighet</div>
                            <div class="font-semibold">{{ weather.data.humidity }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Wind class="text-primary h-5 w-5" />
                        <div>
                            <div class="text-muted-foreground text-sm">Vindhastighet</div>
                            <div class="font-semibold">{{ weather.data.wind_speed }} m/s</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Eye class="text-primary h-5 w-5" />
                        <div>
                            <div class="text-muted-foreground text-sm">Synlighet</div>
                            <div class="font-semibold">{{ weather.data.visibility }} km</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Thermometer class="text-primary h-5 w-5" />
                        <div>
                            <div class="text-muted-foreground text-sm">Føles ut som</div>
                            <div class="font-semibold">{{ weather.data.feels_like }}°C</div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<style scoped></style>
