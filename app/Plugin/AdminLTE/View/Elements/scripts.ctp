<!-- jQuery 3 -->
<script src="/vendor/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/vendor/adminlte/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap -->
<script src="/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/vendor/adminlte/dist/js/adminlte.min.js"></script>
<!-- Adicionais -->
<script src="/vendor/jquery-mask/jquery.mask.min.js"></script>
<script src="/vendor/xdan-datetimepicker/jquery.datetimepicker.js"></script>
<script>
    window.addEventListener('load', function(){
        var _size = window.innerHeight - 102;
        $('body').css('height', _size+'px');
        $('.content-wrapper').css('height', _size+'px');
        $('.vcenter').css('height', window.innerHeight+'px');
    });
    window.addEventListener('resize', function(){
        var _size = window.innerHeight - 102;
        $('body').css('height', _size+'px');
        $('.content-wrapper').css('height', _size+'px');
        $('.vcenter').css('height', window.innerHeight+'px');
    });
</script>
<script>
    $('.datetime-picker').datetimepicker({
        format:'d/m/Y H:i',
        lang: 'pt',
        step: 10
      });
    $('.mask-date').mask('00/00/0000');
    $('.mask-time').mask('00:00:00');
    $('.mask-date-time').mask('00/00/0000 00:00:00');
    $('.mask-cep').mask('00000-000');
    $('.mask-ip-address').mask('099.099.099.099');
    $('.mask-percent').mask('##0,00%', {reverse: true});
    $('.mask-cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.mask-cpf').mask('000.000.000-00', {reverse: true});
    $('.mask-money').mask('#.##0,00', {reverse: true});
    var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions = {
      onKeyPress: function(val, e, field, options) {
          field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('.mask-mobile').mask(SPMaskBehavior, spOptions);
    $('.mask-phone').mask(SPMaskBehavior, spOptions);
<?=$this->fetch('script-onload');?>
</script>