<!-- 
    HEAD COMÚN
    Este archivo contiene todas las meta-etiquetas esenciales y las importaciones de estilos (CSS)
    para evitar duplicidad de código en cada página.
-->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Título dinámico: Usa la variable $pageTitle si existe, sino usa 'ParkWay' por defecto -->
<title><?php echo isset($pageTitle) ? $pageTitle : 'ParkWay'; ?></title>

<!-- Fuentes Google: Outfit -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

<!-- Estilos Principales -->
<link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>_4">

<!-- Iconos FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- CSS Extra (Opcional, inyectado desde la página padre) -->
<?php if (isset($extraCss)) echo $extraCss; ?>

<!-- Control de Transiciones (Fade In/Out) -->
<script src="js/transitions.js" defer></script>
