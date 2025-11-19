<x-filament-panels::page>
    <div class="space-y-6 bg-gray-900 min-h-screen p-6">
        <!-- Botón para ir al inicio -->
        <div class="flex justify-end">
            <a href="{{ $this->getResource()::getUrl('index') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                LISTO GUARDAR
            </a>
        </div>

        <!-- Información del diagnóstico -->
        <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
            <h2 class="text-lg font-semibold text-white mb-4">Información del Diagnóstico</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Datos del paciente -->
                <div>
                    <h3 class="text-sm font-medium text-gray-300 mb-2">Paciente</h3>
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <p class="text-lg font-semibold text-white">{{ $record->patient->name }}</p>
                        <p class="text-sm text-gray-300">{{ $record->patient->email }}</p>
                        <p class="text-sm text-gray-300">{{ $record->patient->phone }}</p>
                    </div>
                </div>

                <!-- Datos del doctor -->
                <div>
                    <h3 class="text-sm font-medium text-gray-300 mb-2">Doctor</h3>
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <p class="text-lg font-semibold text-white">{{ $record->doctor->name }}</p>
                        <p class="text-sm text-gray-300">{{ $record->doctor->specialty }}</p>
                        <p class="text-sm text-gray-300">{{ $record->doctor->email }}</p>
                    </div>
                </div>

                <!-- Estado del diagnóstico -->
                <div>
                    <h3 class="text-sm font-medium text-gray-300 mb-2">Estado</h3>
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-center space-x-2">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-gray-500',
                                    'analyzing' => 'bg-blue-500',
                                    'completed' => 'bg-green-500',
                                    'error' => 'bg-red-500'
                                ];
                                $statusColor = $statusColors[$record->status] ?? 'bg-gray-500';
                            @endphp
                            <div class="w-3 h-3 {{ $statusColor }} rounded-full"></div>
                            <span class="text-lg font-semibold text-white">{{ $record->status_label }}</span>
                        </div>
                        <p class="text-sm text-gray-300 mt-1">ID: {{ $record->id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágenes y resultados de ambos ojos -->
        <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Imágenes y Resultados por Ojo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Ojo Derecho -->
                <div>
                    <h3 class="text-md font-bold text-white mb-2 text-center">Ojo Derecho</h3>
                    <div class="flex justify-center mb-2">
                        @if($record->right_eye_image)
                            <img 
                                src="{{ asset('storage/' . $record->right_eye_image) }}" 
                                alt="Imagen Ojo Derecho"
                                class="max-w-xs rounded-lg shadow-lg border-2 border-gray-600"
                                style="max-height: 300px; object-fit: contain;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                            >
                            <div class="hidden max-w-xs rounded-lg shadow-lg border-2 border-gray-600 bg-gray-700 p-8 text-center" style="max-height: 300px;">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-400">No se pudo cargar la imagen</p>
                            </div>
                        @else
                            <div class="max-w-xs rounded-lg shadow-lg border-2 border-gray-600 bg-gray-700 p-8 text-center" style="max-height: 300px;">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-400">No hay imagen disponible</p>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600 mt-2">
                        <h4 class="text-sm font-semibold text-gray-300 mb-2">Resultado Ojo Derecho</h4>
                        <pre class="whitespace-pre-wrap text-sm text-gray-200 font-mono min-h-[40px]">{{ $record->right_eye_result ?: 'Sin resultado' }}</pre>
                    </div>
                </div>
                <!-- Ojo Izquierdo -->
                <div>
                    <h3 class="text-md font-bold text-white mb-2 text-center">Ojo Izquierdo</h3>
                    <div class="flex justify-center mb-2">
                        @if($record->left_eye_image)
                            <img 
                                src="{{ asset('storage/' . $record->left_eye_image) }}" 
                                alt="Imagen Ojo Izquierdo"
                                class="max-w-xs rounded-lg shadow-lg border-2 border-gray-600"
                                style="max-height: 300px; object-fit: contain;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                            >
                            <div class="hidden max-w-xs rounded-lg shadow-lg border-2 border-gray-600 bg-gray-700 p-8 text-center" style="max-height: 300px;">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-400">No se pudo cargar la imagen</p>
                            </div>
                        @else
                            <div class="max-w-xs rounded-lg shadow-lg border-2 border-gray-600 bg-gray-700 p-8 text-center" style="max-height: 300px;">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-400">No hay imagen disponible</p>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600 mt-2">
                        <h4 class="text-sm font-semibold text-gray-300 mb-2">Resultado Ojo Izquierdo</h4>
                        <pre class="whitespace-pre-wrap text-sm text-gray-200 font-mono min-h-[40px]">{{ $record->left_eye_result ?: 'Sin resultado' }}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultado del análisis global (detalles y comparación) -->
        <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
            <h2 class="text-lg font-semibold text-white mb-4">Resultado del Análisis AI</h2>
            
            <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                @if($isAnalyzing)
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-400"></div>
                        <p class="text-gray-200 font-medium">{{ $analysisResult }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        <!-- Estado del análisis -->
                        <div class="flex items-center space-x-2">
                            @if($analysisComplete && !str_contains($analysisResult, '❌'))
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-green-400">Análisis completado</span>
                            @else
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm font-medium text-red-400">Error en el análisis</span>
                            @endif
                        </div>

                        <!-- Diagnóstico principal -->
                        @if(str_contains($analysisResult, 'DIAGNÓSTICO PRINCIPAL:'))
                            <div class="bg-gray-800 rounded-lg p-4 border border-gray-600">
                                <h3 class="text-lg font-semibold text-white mb-3">📊 Diagnóstico Principal</h3>
                                
                                @php
                                    // Extraer información del resultado
                                    $lines = explode("\n", $analysisResult);
                                    $diagnostico = '';
                                    $confianza = '';
                                    $tiempo_procesamiento = '';
                                    $prob_glaucoma = '';
                                    $prob_normal = '';
                                    $estado = '';
                                    
                                    foreach($lines as $line) {
                                        if(str_contains($line, 'Resultado:')) {
                                            $diagnostico = trim(str_replace('Resultado:', '', $line));
                                        }
                                        if(str_contains($line, 'Confianza:')) {
                                            $confianza = trim(str_replace('Confianza:', '', $line));
                                        }
                                        if(str_contains($line, 'Tiempo de procesamiento:')) {
                                            $tiempo_procesamiento = trim(str_replace('Tiempo de procesamiento:', '', $line));
                                        }
                                        if(str_contains($line, '🔴 Glaucoma:')) {
                                            $prob_glaucoma = trim(str_replace('🔴 Glaucoma:', '', $line));
                                        }
                                        if(str_contains($line, '🟢 Normal:')) {
                                            $prob_normal = trim(str_replace('🟢 Normal:', '', $line));
                                        }
                                        if(str_contains($line, 'DETECTADO POSIBLE GLAUCOMA')) {
                                            $estado = 'glaucoma';
                                        }
                                        if(str_contains($line, 'RESULTADO NORMAL')) {
                                            $estado = 'normal';
                                        }
                                    }
                                @endphp
                                
                                <div class="space-y-4">
                                    <!-- Estado del diagnóstico -->
                                    <div class="text-center">
                                        <div class="text-2xl font-bold mb-2">
                                            @if($estado === 'glaucoma')
                                                <span class="text-red-400">⚠️ GLAUCOMA</span>
                                            @elseif($estado === 'normal')
                                                <span class="text-green-400">✅ NORMAL</span>
                                            @else
                                                <span class="text-yellow-400">❓ INDETERMINADO</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-300 text-sm">Estado del diagnóstico</p>
                                    </div>
                                    
                                    <!-- Información de confianza y tiempo -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-gray-300 text-sm">Confianza del análisis</span>
                                                <span class="text-white font-semibold">{{ $confianza }}</span>
                                            </div>
                                            @php
                                                $confianza_num = 0;
                                                if(preg_match('/(\d+(?:\.\d+)?)%/', $confianza, $matches)) {
                                                    $confianza_num = (float)$matches[1];
                                                }
                                                $color_confianza = $estado === 'glaucoma' ? 'bg-red-500' : 'bg-green-500';
                                            @endphp
                                            <div class="w-full bg-gray-600 rounded-full h-3">
                                                <div class="{{ $color_confianza }} h-3 rounded-full transition-all duration-500" 
                                                     style="width: {{ $confianza_num }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <div class="text-center">
                                                <span class="text-gray-300 text-sm">Tiempo de procesamiento</span>
                                                <div class="text-white font-semibold text-lg">{{ $tiempo_procesamiento }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Probabilidades detalladas -->
                                    @if($prob_glaucoma && $prob_normal)
                                        <div class="bg-gray-700 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-300 mb-3">📈 Probabilidades Detalladas</h4>
                                            
                                            <div class="space-y-3">
                                                <!-- Probabilidad Glaucoma -->
                                                <div>
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="text-red-400 text-sm font-medium">🔴 Glaucoma</span>
                                                        <span class="text-white font-semibold">{{ $prob_glaucoma }}</span>
                                                    </div>
                                                    @php
                                                        $glaucoma_num = 0;
                                                        if(preg_match('/(\d+(?:\.\d+)?)%/', $prob_glaucoma, $matches)) {
                                                            $glaucoma_num = (float)$matches[1];
                                                        }
                                                    @endphp
                                                    <div class="w-full bg-gray-600 rounded-full h-2">
                                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-500" 
                                                             style="width: {{ $glaucoma_num }}%"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Probabilidad Normal -->
                                                <div>
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="text-green-400 text-sm font-medium">🟢 Normal</span>
                                                        <span class="text-white font-semibold">{{ $prob_normal }}</span>
                                                    </div>
                                                    @php
                                                        $normal_num = 0;
                                                        if(preg_match('/(\d+(?:\.\d+)?)%/', $prob_normal, $matches)) {
                                                            $normal_num = (float)$matches[1];
                                                        }
                                                    @endphp
                                                    <div class="w-full bg-gray-600 rounded-full h-2">
                                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                                             style="width: {{ $normal_num }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Mensaje de análisis completado -->
        @if($analysisComplete)
            <div class="bg-green-900 border border-green-700 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-green-200 font-medium">
                            ✅ Análisis completado exitosamente. El diagnóstico ha sido guardado en la base de datos.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page> 