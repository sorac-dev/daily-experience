<body id="body_welcome">
    <div id="welcome_index">
        <div class="welcome">
            <div class="item_center w_img sombra"></div>
        </div>
        <h2 class="w_title">Sociedad Libre</h2>
        <div class="welcome_login">
            <form action="app/sys-login.php" method="post" enctype="multipart/form-data">
                <div class="w_form_head">
                    <h2>Entra a tu cuenta</h2>
                    <h5>Tu instancia con nosotros, es de agrado.</h5>
                </div>
                <div class="space hr"></div>
                <center><h2 class="sociedadlibre">Sociedad Libre</h2></center>
                <div class="space hr"></div>
                <br>
                <div class="w_input">
                    <input class="item_center" type="text" name="username" placeholder="Ingresa tu usuario" required />
                </div>
                <div class="w_input">
                    <input class="item_center" type="password" name="password" placeholder="Contraseña" required />
                </div>
                <div class="w_recordarme">
                    <input type="checkbox" name="recordarme" title="Habilita y tu sesion se mantendra abierta." />
                    <span>Recuerdame</span>
                </div>
                <button type="submit" class="item_center w_btn">Iniciar sesión</button>
                <button class="item_center w_btn_op"><a href="#">¿Olvidaste tu contraseña?</a></button>
                <div class="space hr"></div>
                <?php
                    if (isset($_GET['env'])) {
                    $env = array(
                            1 => 'Error al procesar el formulario, contacta con un administrador de sitio.',
                            2 => 'No puedes dejar ninguna dato solicitado vacio.',
                            3 => 'No existe ninguna cuenta con los datos ingresados.',
                            4 => 'La contraseña ingresada es incorrecta.'
                    );
                    if (isset($env[$_GET['env']])) {
                            $envMessage = $env[$_GET['env']];
                            echo "<br><div class='box-error' style='padding:10px;color:#fff;background:red;text-align: center;margin-top:8px;'>$envMessage</div>";
                        }
                    }
                ?>  
                <div class="item_center w_reg">¿Aún no tienes una cuenta con nosotros? <a href="r">Regístrate ahora</a> ¡es completamente gratis!</div>
                <div class="space hr"></div>
            </form>   
        </div>
        <div class="w_footer">
            <p><b>Sociedad Libre.</b> Todos los derechos reservados &copy; Derechos de autor. No tenemos relación con ninguna otra red social. Somos una organización independiente, sin influencias gubernamentales ni afiliación a partidos políticos.</p>
        </div>
    </div>
</body>
