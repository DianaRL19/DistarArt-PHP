<?php
// —————————————————————————————————————————————
//    LIBRERÍA DE PETICIONES CURL PARA APIS
// —————————————————————————————————————————————

/**
 * Realiza una petición HTTP GET a la URL indicada
 * 
 * @param string $url → URL a consultar
 * @param string|null $parametros → Parámetros en formato "param1=val1&param2=val2"
 * @param array|null $headers → Headers adicionales (por defecto null)
 * @return string|false → Respuesta de la API o false si hay error
 */
function petCURLGet(string $url, ?string $parametros = null, ?array $headers = null): string|false {
    
    $curl = curl_init();
    
    // → Añadir parámetros a la URL si existen
    if ($parametros != null) {
        $url .= "?" . $parametros;
    }
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);              // → Timeout de 5 segundos
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);       // → Timeout de conexión
    curl_setopt($curl, CURLOPT_FAILONERROR, false);       // → No fallar en códigos 4xx/5xx
    
    // → Añadir headers personalizados si existen
    if ($headers != null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    
    $devuelto = curl_exec($curl);
    curl_close($curl);
    
    return $devuelto;
}

/**
 * Realiza una petición HTTP POST a la URL indicada
 * 
 * @param string $url → URL a consultar
 * @param string|null $parametros → Datos POST en formato "param1=val1&param2=val2"
 * @param array|null $headers → Headers adicionales (por defecto null)
 * @return string|false → Respuesta de la API o false si hay error
 */
function petCURLPost(string $url, ?string $parametros = null, ?array $headers = null): string|false {
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    // → Añadir datos POST si existen
    if ($parametros != null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
    }
    
    // → Añadir headers personalizados si existen
    if ($headers != null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    
    $devuelto = curl_exec($curl);
    curl_close($curl);
    
    return $devuelto;
}

/**
 * Realiza una petición HTTP PUT a la URL indicada
 * 
 * @param string $url → URL a consultar
 * @param string|null $parametros → Datos PUT en formato "param1=val1&param2=val2"
 * @param array|null $headers → Headers adicionales (por defecto null)
 * @return string|false → Respuesta de la API o false si hay error
 */
function petCURLPut(string $url, ?string $parametros = null, ?array $headers = null): string|false {
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    // → Añadir datos PUT si existen
    if ($parametros != null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
    }
    
    // → Añadir headers personalizados si existen
    if ($headers != null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    
    $devuelto = curl_exec($curl);
    curl_close($curl);
    
    return $devuelto;
}

/**
 * Realiza una petición HTTP DELETE a la URL indicada
 * 
 * @param string $url → URL a consultar
 * @param string|null $parametros → Datos DELETE en formato "param1=val1&param2=val2"
 * @param array|null $headers → Headers adicionales (por defecto null)
 * @return string|false → Respuesta de la API o false si hay error
 */
function petCURLDelete(string $url, ?string $parametros = null, ?array $headers = null): string|false {
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    // → Añadir datos DELETE si existen
    if ($parametros != null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
    }
    
    // → Añadir headers personalizados si existen
    if ($headers != null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    
    $devuelto = curl_exec($curl);
    curl_close($curl);
    
    return $devuelto;
}

/**
 * Procesa una respuesta JSON y la convierte a objeto/array
 * 
 * @param string|false $respuesta → Respuesta de la API
 * @param bool $comoArray → Si true devuelve array, si false devuelve object
 * @return array|object|false → Datos parseados o false si hay error
 */
function procesarJSON(string|false $respuesta, bool $comoArray = true): array|object|false {
    
    if ($respuesta === false) {
        return false;
    }
    
    try {
        return json_decode($respuesta, $comoArray, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        return false;
    }
}

/**
 * Procesa una respuesta XML y la convierte a SimpleXMLElement
 * 
 * @param string|false $respuesta → Respuesta de la API
 * @return SimpleXMLElement|false → Objeto XML o false si hay error
 */
function procesarXML(string|false $respuesta): SimpleXMLElement|false {
    
    if ($respuesta === false) {
        return false;
    }
    
    try {
        // → Sustituir xmlns por ns para que funcione correctamente
        $respuesta = str_replace('xmlns=', 'ns=', $respuesta);
        return new SimpleXMLElement($respuesta);
    } catch (Exception $e) {
        return false;
    }
}

?>
