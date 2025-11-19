from flask import Flask, request, jsonify
from flask_cors import CORS
import tensorflow as tf
import numpy as np
from PIL import Image
import io
import logging

# --- CONFIGURACIÓN ---
MODEL_PATH = 'modelo_aprecia_pro.keras'
IMG_SIZE = 224 # El tamaño que usamos en el entrenamiento V2
CLASES = ['cataract', 'glaucoma', 'normal'] # Orden alfabético estricto

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app) # Permite que Laravel llame a Python sin problemas de seguridad

# Cargar modelo al iniciar
try:
    logger.info("Cargando modelo... esto puede tardar unos segundos.")
    model = tf.keras.models.load_model(MODEL_PATH)
    logger.info(f"¡Modelo cargado exitosamente! Clases esperadas: {CLASES}")
except Exception as e:
    logger.error(f"❌ ERROR CRÍTICO cargando modelo: {e}")
    model = None

def preparar_imagen(image_bytes):
    """Convierte bytes a tensor listo para la IA"""
    img = Image.open(io.BytesIO(image_bytes)).convert('RGB')
    img = img.resize((IMG_SIZE, IMG_SIZE))
    img_array = tf.keras.utils.img_to_array(img)
    img_array = tf.expand_dims(img_array, 0) # Crear lote de 1
    return img_array

@app.route('/api/prediccion', methods=['POST'])
def predecir():
    if model is None:
        return jsonify({'error': 'El modelo de IA no está disponible'}), 500

    # 1. Validaciones
    if 'imagen' not in request.files:
        return jsonify({'error': 'No se envió el campo "imagen"'}), 400
    
    file = request.files['imagen']
    if file.filename == '':
        return jsonify({'error': 'Archivo vacío'}), 400

    try:
        logger.info(f"Procesando imagen: {file.filename}")
        
        # 2. Preprocesamiento
        img_array = preparar_imagen(file.read())
        
        # 3. Predicción
        predictions = model.predict(img_array)
        score = predictions[0]
        
        # 4. Interpretar resultados
        indice_ganador = np.argmax(score)
        clase_ganadora = CLASES[indice_ganador]
        confianza = float(np.max(score) * 100) # Convertir a porcentaje (0-100)
        
        logger.info(f"Resultado: {clase_ganadora.upper()} ({confianza:.2f}%)")

        # Respuesta limpia para Laravel
        return jsonify({
            'resultado': clase_ganadora,   # 'cataract', 'glaucoma', 'normal'
            'probabilidad': confianza,     # 98.55
            'mensaje': 'Análisis completado exitosamente'
        })

    except Exception as e:
        logger.error(f"Error durante la predicción: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({'status': 'online', 'model_loaded': model is not None})

if __name__ == '__main__':
    app.run(debug=True, port=5000, host='0.0.0.0')