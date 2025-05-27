<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ForecastEntity } from '@/types';
import {
    Cloud,
    CloudRain,
    Sun,
    CloudSnow,
    CloudLightning,
    Moon,
    CloudSun,
    CloudMoon,
} from "lucide-vue-next";

const iconMap = {
    sun: Sun,
    moon: Moon,
    cloud: Cloud,
    "cloud-sun": CloudSun,
    "cloud-moon": CloudMoon,
    "cloud-rain": CloudRain,
    "cloud-lightning": CloudLightning,
    "cloud-snow": CloudSnow,
    "cloud-fog": Cloud,
};

defineProps<{
    forecast: ForecastEntity[];
}>();

const ForecastIcon = (icon: string) => {
    return iconMap[icon] || Cloud;
};
</script>

<template>
    <Card>
        <CardHeader class="text-2xl md:text-3xl font-semibold">
            Værvarsel
        </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow class="border-white/20 hover:bg-white/5">
                        <TableHead class="font-semibold text-muted-foreground">Dag</TableHead>
                        <TableHead class="font-semibold text-muted-foreground">Vær</TableHead>
                        <TableHead class="font-semibold text-muted-foreground">Forhold</TableHead>
                        <TableHead class="font-semibold text-muted-foreground text-right">Temperatur Høy</TableHead>
                        <TableHead class="font-semibold text-muted-foreground text-right">Temperatur Lav</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(day, index) in forecast"
                        :key="index"
                        class="dark:border-white/20 dark:hover:bg-white/5"
                    >
                        <TableCell class="font-medium py-5">{{ day.day }}</TableCell>
                        <TableCell>
                            <component :is="ForecastIcon(day.icon)" class="size-7 text-primary" />
                        </TableCell>
                        <TableCell class="">{{ day.condition }}</TableCell>
                        <TableCell class="text-right font-semibold">{{ day.high }}°C</TableCell>
                        <TableCell class="text-right">{{ day.low }}°C</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>

<style scoped>

</style>
