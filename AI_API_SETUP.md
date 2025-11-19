# Configuración de la API de Diagnóstico AI

## Integración con tu software de entrenamiento de IA

Este sistema está configurado para integrarse con tu API de diagnóstico de glaucoma. El servicio utiliza el mismo método que tu `FileController` original.

## Configuración

### 1. Variables de entorno

Agrega estas variables a tu archivo `.env`:

```env
# Configuración de la API de diagnóstico AI
AI_DIAGNOSIS_API_URL=http://127.0.0.1:5000/api/prediccion
AI_DIAGNOSIS_API_TOKEN=
AI_DIAGNOSIS_API_TIMEOUT=30
AI_DIAGNOSIS_IMAGE_FORMAT=file
AI_DIAGNOSIS_MAX_SIZE=10
AI_DIAGNOSIS_FALLBACK_SIMULATION=true
```

### 2. Configuración de la API

- **AI_DIAGNOSIS_API_URL**: URL de tu API (por defecto: `http://127.0.0.1:5000/api/prediccion`)
- **AI_DIAGNOSIS_API_TOKEN**: Token de autenticación (si es necesario)
- **AI_DIAGNOSIS_API_TIMEOUT**: Timeout en segundos (por defecto: 30)
- **AI_DIAGNOSIS_MAX_SIZE**: Tamaño máximo de imagen en MB (por defecto: 10)
- **AI_DIAGNOSIS_FALLBACK_SIMULATION**: Habilitar modo simulación si la API falla

## Uso

### 1. En el panel de administración

1. Ve a **Diagnósticos AI** en el menú lateral
2. Haz clic en **"Nuevo Diagnóstico"**
3. Selecciona un doctor
4. Sube una imagen del ojo
5. El sistema enviará la imagen a tu API y mostrará el resultado

### 2. Formato de respuesta esperado

Tu API debe devolver un JSON con este formato:

```json
{
    "resultado": "glaucoma",
    "probabilidad": 0.85
}
```

O para casos normales:

```json
{
    "resultado": "normal",
    "probabilidad": 0.92
}
```

### 3. Probar la conexión

Usa el comando Artisan para probar la conexión:

```bash
# Solo probar conectividad
php artisan ai:test-api

# Probar con una imagen específica
php artisan ai:test-api --image=/ruta/a/tu/imagen.jpg
```

## Funcionamiento

### Flujo de análisis

1. **Subida de imagen**: El usuario sube una imagen desde el panel de Filament
2. **Envío a API**: El servicio usa `Http::attach()` para enviar la imagen como archivo adjunto
3. **Procesamiento**: Tu API analiza la imagen y devuelve el resultado
4. **Interpretación**: El sistema interpreta el resultado y genera recomendaciones médicas
5. **Almacenamiento**: Se guarda la imagen y el resultado en la base de datos

### Fallback

Si tu API no está disponible, el sistema:
- Mostrará un mensaje de advertencia
- Usará un análisis simulado
- Guardará el registro con el análisis simulado

## Personalización

### Modificar el procesamiento de respuesta

Edita el método `processApiResponse()` en `app/Services/AiDiagnosisService.php` para adaptarlo a tu formato de respuesta específico.

### Agregar más tipos de diagnóstico

Modifica la lógica de interpretación en el mismo método para manejar otros tipos de diagnóstico además de "glaucoma" y "normal".

### Configurar autenticación

Si tu API requiere autenticación, configura el token en las variables de entorno y el servicio lo enviará automáticamente.

## Troubleshooting

### Error: "No se pudo contactar con la IA"

1. Verifica que tu API esté ejecutándose en la URL configurada
2. Usa el comando `php artisan ai:test-api` para diagnosticar
3. Revisa los logs de Laravel en `storage/logs/laravel.log`

### Error: "Formato de respuesta incorrecto"

1. Verifica que tu API devuelva el formato JSON esperado
2. Ajusta el método `processApiResponse()` si es necesario

### Imagen no se envía correctamente

1. Verifica que la imagen sea menor a 10MB
2. Asegúrate de que sea un formato válido (jpg, jpeg, png)
3. Revisa los permisos de escritura en `storage/app/public/` 