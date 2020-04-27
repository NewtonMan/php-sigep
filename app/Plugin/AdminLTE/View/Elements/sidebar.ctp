            <aside class="main-sidebar" style="padding-top: 0;">
                <section class="sidebar">
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">
                            <a href="/"><img src="/img/logo.png" width="100%" /></a>
                        </li>
                        <li<?=($this->request->params['plugin']=='cadastros' ? ' class="active"':'')?>><a href="/cadastros/gerenciar"><i class="fa fa-user-circle"></i> CADASTROS</a></li>
                        <li<?=($this->request->params['plugin']=='total_express' ? ' class="active"':'')?>><a href="/total_express/gerenciar/contas"><i class="fa fa-user-circle"></i> TOTAL EXPRESS</a></li>
                        <li<?=($this->request->params['plugin']=='sigepweb' ? ' class="active"':'')?>><a href="/sigepweb/gerenciar/contas"><i class="fa fa-user-circle"></i> SIGEPWEB</a></li>
                    </ul>
                </section>
            </aside>
