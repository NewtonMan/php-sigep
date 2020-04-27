<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Acesso Operador Logístico</a>
            </h2>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
                <form method="POST" action="/login" accept-charset="UTF-8">
                    <input class="span3 form-control" placeholder="Email" type="email" name="data[Usuario][email]">
                    <input class="span3 form-control" placeholder="Password" type="password" name="data[Usuario][senha]"> 
                    <button class="btn-success btn" type="submit">Login</button><button class="btn btn-default" type="button" onclick="window.location.href='/usuario/esqueci';">Esqueci Minha Senha</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Acesso para Clientes</a>
            </h2>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse">
            <div class="panel-body">
                <form method="POST" action="/acesso_cliente/login" accept-charset="UTF-8">
                    <input class="span3 form-control" placeholder="Email" type="email" name="data[Usuario][email]">
                    <input class="span3 form-control" placeholder="Password" type="password" name="data[Usuario][senha]"> 
                    <button class="btn btn-success" type="submit">Login</button><button class="btn btn-default" type="button" onclick="window.location.href='/acesso_cliente/usuario/esqueci';">Esqueci Minha Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>
