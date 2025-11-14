<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Carbon\Carbon;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Administración del Sistema',
                'description' => 'Comandos para la administración y configuración del sistema operativo',
                'proposito' => 'Gestionar y configurar el sistema operativo, usuarios y servicios del sistema'
            ],
            [
                'name' => 'Gestión de Archivos',
                'description' => 'Operaciones con archivos y directorios',
                'proposito' => 'Crear, modificar, eliminar y organizar archivos y directorios'
            ],
            [
                'name' => 'Gestión de Procesos',
                'description' => 'Control y monitoreo de procesos del sistema',
                'proposito' => 'Monitorear, gestionar y controlar procesos en ejecución'
            ],
            [
                'name' => 'Copia de Seguridad y Recuperación',
                'description' => 'Comandos para backup y restauración de datos',
                'proposito' => 'Proteger datos mediante copias de seguridad y recuperar información'
            ],
            [
                'name' => 'Redes',
                'description' => 'Configuración y diagnóstico de redes',
                'proposito' => 'Configurar, diagnosticar y gestionar conexiones de red'
            ],
            [
                'name' => 'Seguridad',
                'description' => 'Comandos relacionados con seguridad del sistema',
                'proposito' => 'Proteger el sistema, gestionar permisos y auditorías de seguridad'
            ],
            [
                'name' => 'Automatización',
                'description' => 'Scripts y herramientas para automatizar tareas',
                'proposito' => 'Automatizar tareas repetitivas y flujos de trabajo'
            ],
            [
                'name' => 'DevOps',
                'description' => 'Herramientas para desarrollo y operaciones',
                'proposito' => 'Integrar desarrollo y operaciones con herramientas CI/CD'
            ],
            [
                'name' => 'Despliegue',
                'description' => 'Comandos para despliegue de aplicaciones',
                'proposito' => 'Desplegar y actualizar aplicaciones en diferentes entornos'
            ],
            [
                'name' => 'Monitoreo',
                'description' => 'Herramientas de monitoreo y métricas del sistema',
                'proposito' => 'Supervisar el rendimiento y estado del sistema'
            ],
            [
                'name' => 'Utilidades',
                'description' => 'Comandos útiles para diversas tareas',
                'proposito' => 'Proveer herramientas útiles para tareas comunes del sistema'
            ],
            [
                'name' => 'Desarrollo Web',
                'description' => 'Comandos para desarrollo de aplicaciones web',
                'proposito' => 'Desarrollar y mantener aplicaciones y servicios web'
            ],
            [
                'name' => 'Base de Datos',
                'description' => 'Operaciones con bases de datos',
                'proposito' => 'Gestionar, consultar y administrar bases de datos'
            ],
            [
                'name' => 'Pruebas',
                'description' => 'Comandos para testing y calidad de código',
                'proposito' => 'Ejecutar pruebas y verificar la calidad del código'
            ],
            [
                'name' => 'Depuración',
                'description' => 'Herramientas para depuración de código',
                'proposito' => 'Identificar y corregir errores en aplicaciones y scripts'
            ],
            [
                'name' => 'Script/Bash',
                'description' => 'Scripts y comandos de bash',
                'proposito' => 'Crear y ejecutar scripts de automatización en bash'
            ],
            [
                'name' => 'Scripts del Sistema',
                'description' => 'Scripts específicos del sistema operativo',
                'proposito' => 'Ejecutar scripts de mantenimiento y configuración del sistema'
            ],
            [
                'name' => 'Mantenimiento',
                'description' => 'Tareas de mantenimiento del sistema',
                'proposito' => 'Realizar tareas periódicas de mantenimiento y optimización'
            ]
        ];

        $timestamp = Carbon::now();

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'proposito' => $category['proposito'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]
            );
        }

        $this->command->info('✅ Tabla de categorías poblada exitosamente!');
    }
}