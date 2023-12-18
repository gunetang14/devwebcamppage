<div class="evento swiper-slide">
    <p class="evento__hora"><?php echo $evento->hora->hora; ?></p>
    <div class="evento__informacion">
        <h4 class="evento__nombre"><?php echo $evento->nombre; ?></h4>
        <p class="evento__introduccion"><?php echo $evento->descripcion; ?></p>
        <div class="evento__autor-info">
            <picture> 
                <source srcset="img/speakers/<?php echo $evento->ponente->imagen; ?>.webp" type="image/webp">
                <source srcset="img/speakers/<?php echo $evento->ponente->imagen; ?>.png" type="image/png">
                <img class="evento__imagen-autor" 
                    loading="lazy" 
                    src="img/speakers/<?php echo $evento->ponente->imagen; ?>.png" 
                    alt="Imagen Ponente" width="200" height="300">
            </picture>
            <p class="evento_autor-nombre">
                <?php echo $evento->ponente->nombre . " " . $evento->ponente->apellido ?>
            </p>
        </div>
        <div class="evento__contenedor-ver">
            <a class="evento__ver fa-sharp fa-solid fa-eye" href="/evento?id=<?php echo $evento->id; ?>"></a>
        </div>
    </div>
</div>