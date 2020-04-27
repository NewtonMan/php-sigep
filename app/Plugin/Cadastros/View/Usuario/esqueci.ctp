<div class="row">
    <div class="span4">
      <div class="well" style="margin: 0 auto; width: 400px;">
        <legend>Recuperar Acesso</legend>
        <form method="POST" action="" accept-charset="UTF-8">
            <? echo $this->Session->flash(); ?>
            <input class="span3" placeholder="Email" type="text" name="data[Usuario][email]">
            <button class="btn-success btn" type="submit">Recuperar</button>
        </form>
      </div>
    </div>
</div>
