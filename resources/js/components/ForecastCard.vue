<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ForecastEntity } from '@/types';
import { Cloud, CloudLightning, CloudMoon, CloudRain, CloudSnow, CloudSun, Moon, Sun } from 'lucide-vue-next';

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
    forecast?: ForecastEntity[] | null;
}>();

const ForecastIcon = (icon: string) => {
    return iconMap[icon] || Cloud;
};
</script>

<template>
    <Card>
        <CardHeader class="text-2xl font-semibold md:text-3xl"> Værvarsel </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow class="border-white/20 hover:bg-white/5">
                        <TableHead class="text-muted-foreground font-semibold">Dag</TableHead>
                        <TableHead class="text-muted-foreground font-semibold">Vær</TableHead>
                        <TableHead class="text-muted-foreground font-semibold">Forhold</TableHead>
                        <TableHead class="text-muted-foreground text-right font-semibold">Temperatur Høy</TableHead>
                        <TableHead class="text-muted-foreground text-right font-semibold">Temperatur Lav</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(day, index) in forecast" :key="index" class="dark:border-white/20 dark:hover:bg-white/5">
                        <TableCell class="py-5 font-medium">{{ day.day }}</TableCell>
                        <TableCell>
                            <component :is="ForecastIcon(day.icon)" class="text-primary size-7" />
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

<style scoped></style>
