<main class="ponencia">
    <h2 class="ponencia__heading"><?php echo $titulo; ?></h2>
    <p class="ponencia__descripcion">Conoce más sobre este evento</p>
    <div class="ponencia__contenedor-boton">
        <a class="ponencia__boton" href="/workshops-conferencias">
            <i class="fa-solid fa-circle-arrow-left"></i>
            Volver
        </a>
    </div>
    <div class="ponencia__inf">
        <h3 class="ponencia__titulo"><?php echo $evento->nombre; ?></h3>
        <p class="ponencia__tipo"><?php echo $evento->categoria->nombre; ?></p>
        <div class="ponencia__contenedor-diahora">
            <p class="ponencia__dia"><span>Dia: </span><?php echo $evento->dia->nombre; ?></p>
            <p class="ponencia__hora"><span>Hora: </span><?php echo $evento->hora->hora; ?></p>
        </div>
        <p class="ponencia__descripcion"><?php echo $evento->descripcion; ?></p>
        <div class="ponencia_inf">
            <h3 class="ponencia__titulo">Ponente</h3>
            <div class="ponencia__autor">
                <picture> 
                    <source srcset="img/speakers/<?php echo $evento->ponente->imagen; ?>.webp" type="image/webp">
                    <source srcset="img/speakers/<?php echo $evento->ponente->imagen; ?>.png" type="image/png">
                    <img class="ponencia__imagen-autor" 
                        loading="lazy" 
                        src="img/speakers/<?php echo $evento->ponente->imagen; ?>.png" 
                        alt="Imagen Ponente" width="200" height="300">
                </picture>
                <div class="ponencia__autor--grid">
                    <p class="ponencia__autor-nombre">
                        <?php echo $evento->ponente->nombre . " " . $evento->ponente->apellido; ?>
                    </p>
                    <p class="ponencia__autor-lugar">
                        <?php echo $evento->ponente->ciudad . ", " . $evento->ponente->pais; ?>
                    </p>
                    <ul class="ponencia__listado-skills">
                    <?php $tags = explode(',', $evento->ponente->tags); 
                        foreach($tags as $tag) { ?>
                            <li class="ponencia__skill"><?php echo $tag; ?></li>
                    <?php    }  ?> 
                    </ul>
                    <div class="ponencia-redes">
                    <?php
                        $redes = json_decode($evento->ponente->redes); 
                        ?>
                        <?php if(!empty($redes->facebook)) { ?>
                            <a class="ponencia-redes__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->facebook; ?>">
                                <span class="ponencia-redes__ocultar">Facebook</span>
                            </a> 
                        <?php } ?>
                        <?php if(!empty($redes->twitter)) { ?>
                            <a class="ponencia-redes__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->twitter; ?>">
                                <span class="ponencia-redes__ocultar">Twitter</span>
                            </a> 
                        <?php } ?>
                        <?php if(!empty($redes->youtube)) { ?>
                            <a class="ponencia-redes__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->youtube; ?>">
                                <span class="ponencia-redes__ocultar">YouTube</span>
                            </a> 
                        <?php } ?>
                        <?php if(!empty($redes->instagram)) { ?>
                            <a class="ponencia-redes__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->instagram; ?>">
                                <span class="ponencia-redes__ocultar">Instagram</span>
                            </a> 
                        <?php } ?>
                        <?php if(!empty($redes->tiktok)) { ?>
                            <a class="speaker-sociales__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->tiktok; ?>">
                                <span class="ponencia-redes__ocultar">Tiktok</span>
                            </a>
                        <?php } ?>    
                        <?php if(!empty($redes->github)) { ?>
                            <a class="ponencia-redes__enlace" rel="noopener noreferrer" target="_blank" href="<?php echo $redes->github; ?>">
                                <span class="ponencia-redes__ocultar">GitHub</span>
                            </a>
                        <?php } ?>
                    </div>
                </div>

            </div>
            
        </div>
    </div>

</main> 