<?php
if(isset($logeado)){
    header('location: index.php');
    exit;
}
?>
<body id="body_welcome">
    <div id="welcome_index_reg">
        <div class="welcome_reg item_center">
            <center><h2 class="sociedadlibre">Sociedad Libre</h2></center>
            <div class="welcome_register_form">
                <form action="app/sys-register.php" method="post">
                    <div class="w_form_head">
                        <h2>Crea una cuenta</h2>
                        <h5>Es rápido y fácil.</h5>
                    </div>
                    <div class="space hr"></div>
                    <div class="w_input_reg">
                        <input class="item_center" type="text" name="username"placeholder="Nuevo de usuario" required>
                    </div>
                    <div class="w_input_reg">
                        <input class="item_center" type="email" name="correo" placeholder="Correo electronico" required>
                    </div>
                    <div class="w_input_reg">
                        <input class="item_center" type="password" name="password" placeholder="Nueva contraseña" required>
                    </div>
                    <div class="w_input_reg">
                        <p>Genero:</p>
                        <select class="item_center" name="genero" required>
                            <option value="1">Hombre</option>
                            <option value="2">Mujer</option>
                            <option value="3">Otro</option>
                        </select>
                    </div>
                    <p class="w_reg_terms">
                        Al hacer clic en Registrarte, aceptas las <a href="tyc">Condiciones</a>, la <a href="tyc">Política de privacidad</a>.
                    </p>
                    <button type="submit" class="item_center w_btn">Registrarte</button>
                    <div class="space hr"></div>
                    <?php
                            if (isset($_GET['env'])) {
                                $env = array(
                                    1 => 'Error al procesar el formulario, contacta con un administrador de sitio.',
                                    2 => 'No puedes dejar ninguna dato solicitado vacio.',
                                    3 => 'Formato de correo electronico invalido.',
                                    4 => 'Este correo electronico ya esta en uso.',
                                    5 => 'Este nombre de usuario ya esta en uso.',
                                    6 => 'El nombre de usuario debe ser mayor a 4 caracteres.',
                                    7 => 'La contraseña debe ser mayor a 8 caracteres.',
                                    8 => 'Error al procesar el genero.',
                                    9 => 'El nombre de usuario solo se permite (A-Z, _ , .).',
                                    10 => 'Formato de correo electronico invalido.'
                                );

                                if (isset($env[$_GET['env']])) {
                                    $envMessage = $env[$_GET['env']];
                                    echo "<div class='box-error' style='padding:10px;color:#fff;background:red;text-align: center;'>$envMessage</div>";
                                }
                            }

                        ?>
                </form>
            </div>
        </div>
        <div class="w_footer">
            <p><b>Sociedad Libre.</b> Todos los derechos reservados &copy; Derechos de autor. No tenemos relación con ninguna otra red social. Somos una organización independiente, sin influencias gubernamentales ni afiliación a partidos políticos.</p>
        </div>
    </div>