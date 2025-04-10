<?php

use Intervention\Image\ImageManager;

class ImageHandler
{
    private $maxWidth;
    private $quality;

    /**
     * Constructor para configurar el tamaño máximo y la calidad.
     * 
     * @param int $maxWidth Ancho máximo para la redimensión de la imagen.
     * @param int $quality Calidad de la imagen (para JPEG).
     */
    public function __construct($maxWidth = 1200, $quality = 80)
    {
        $this->maxWidth = $maxWidth;
        $this->quality = $quality;
    }

    /**
     * Redimensiona y optimiza la imagen.
     * 
     * @param string $imagePath Ruta de la imagen a optimizar.
     * 
     * @throws Exception Si no se encuentra la imagen o ocurre un error en el procesamiento.
     */
    public function optimize($imagePath)
    {
        // Verificar si la imagen existe
        if (!file_exists($imagePath)) {
            throw new Exception("La imagen no existe: $imagePath");
        }

        // Crear el manejador de la imagen
        $manager = new ImageManager($imagePath); // Usando el driver GD (por defecto)
        $image = $manager->make($imagePath); // Cargar la imagen

        // Redimensionar la imagen si el ancho excede el máximo
        if ($image->width() > $this->maxWidth) {
            $image->resize($this->maxWidth, null, function ($constraint) {
                $constraint->aspectRatio(); // Mantener la relación de aspecto
                $constraint->upsize(); // Evitar agrandar la imagen si es más pequeña que el tamaño máximo
            });
        }

        // Si la imagen es JPEG, se guarda con la calidad especificada
        if ($image->mime() === 'image/jpeg') {
            $image->save($imagePath, $this->quality); // Guardar con la calidad especificada
        } else {
            $image->save($imagePath); // Si no es JPEG, simplemente guardamos sin cambiar la calidad
        }
    }

    /**
     * Redimensiona una imagen a un tamaño específico, sin alterar la relación de aspecto.
     * 
     * @param string $imagePath Ruta de la imagen.
     * @param int $width Nuevo ancho de la imagen.
     * @param int $height Nuevo alto de la imagen.
     * 
     * @throws Exception Si la imagen no existe.
     */
    public function resizeImage($imagePath, $width, $height)
    {
        if (!file_exists($imagePath)) {
            throw new Exception("La imagen no existe: $imagePath");
        }

        // Crear el manejador de la imagen
        $manager = new ImageManager($imagePath);
        $image = $manager->make($imagePath);

        // Redimensionar la imagen a las dimensiones específicas
        $image->resize($width, $height);

        // Guardar la imagen
        $image->save($imagePath);
    }

    /**
     * Recorta la imagen a las dimensiones dadas.
     * 
     * @param string $imagePath Ruta de la imagen.
     * @param int $x Coordenada X del recorte.
     * @param int $y Coordenada Y del recorte.
     * @param int $width Ancho del recorte.
     * @param int $height Alto del recorte.
     * 
     * @throws Exception Si la imagen no existe.
     */
    public function cropImage($imagePath, $x, $y, $width, $height)
    {
        if (!file_exists($imagePath)) {
            throw new Exception("La imagen no existe: $imagePath");
        }

        // Crear el manejador de la imagen
        $manager = new ImageManager($imagePath);
        $image = $manager->make($imagePath);

        // Recortar la imagen
        $image->crop($width, $height, $x, $y);

        // Guardar la imagen
        $image->save($imagePath);
    }
}
