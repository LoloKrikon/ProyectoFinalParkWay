<!-- 
    HEADER / NAVBAR COMÚN
    Renderiza la barra superior con el logo, botón de perfil y navegación contextual.
-->
<header class="app-header" style="margin-bottom: 0; padding: 1rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2);">
    
    <!-- Logo Izquierda -->
    <div class="header-left">
        <h2 class="mini-logo">ParkWay</h2>
    </div>

    <div class="header-right">
        
        <!-- Botón Perfil (Condicional) -->
        <?php if (isset($showProfile) && $showProfile): ?>
            <a href="perfil.php" class="secondary-btn" title="Mi Perfil" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user"></i> Ver perfil
            </a>
        <?php endif; ?>
        
        <!-- Botón Volver (Condicional) -->
        <?php if (isset($backLink)): ?>
            <a href="<?php echo $backLink; ?>" class="icon-btn" title="Volver">
                <i class="fa-solid fa-arrow-left"></i> 
                <?php echo isset($backText) ? $backText : ''; ?>
            </a>
        <?php endif; ?>

        <!-- Botón Cerrar Sesión (Condicional) -->
        <?php if (isset($showLogout) && $showLogout): ?>
            <button id="logout-btn" class="secondary-btn" style="background: rgba(0,0,0,0.5); border-color: rgba(255,255,255,0.2);">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </button>
        <?php endif; ?>
    </div>
</header>
