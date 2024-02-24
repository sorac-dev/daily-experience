<header>
    <div class="collapse navbar-collapse" id="navbarNav">
            <nav class="navbar">
                <div class="navbar-left">
                  <a href="./"><img src="./assets/images/logo.png" alt="Sociedad Libre" class="logo-navbar"></a>
                  <ul class="navbar-social">
                    <li title="Unete a nuestro discord!"><a href="#"><i class="bi bi-discord"></i></a></li>
                  </ul>
                </div>
                <form action="" method="get" class="navbar-buscar f-inline">
                  <input type="search" name="buscar" id="buscar" placeholder="Buscas un lugar en especifico?" class="input_text_global">
                  <button type="submit" class="btn-navbar"><i class="bi bi-search"></i></button>
                </form>
                <div class="navbar-right">
                  <div class="user-nav">
                    <img src="<?=$myAvatar?>" alt="avatar" class="avatar-nav">
                    <font class="tUser-nav"><?=$myNombre?></font>
                    <a href="salir.php"><button  class="navar_boton_a"><i class="bi bi-door-open-fill"></i> Salir </button></a>
                  </div>
            </div>
        </nav> 
    </div>
</header>