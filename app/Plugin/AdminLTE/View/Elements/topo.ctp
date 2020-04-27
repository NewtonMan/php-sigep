            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <?php
                    if (!isset($titulo)){
                        $titulo = (!empty($this->request->plugin) ? Inflector::humanize($this->request->plugin) . ' > ':'') . Inflector::humanize($this->request->controller) . ' > ' . Inflector::humanize($this->request->action);
                    }
                    if (!isset($subtitulo)){
                        $subtitulo = '';
                    }
                    ?>
                    <p class="navbar-text" style="font-weight: 900; color: #FFF;"><?php echo "{$titulo} {$subtitulo}"; ?></p>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="/logout">Sair <i class="fa fa-sign-out"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
