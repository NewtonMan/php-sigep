<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Cadastro</title>
        <!-- core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
        <style>
            html, body {
                background-image: url(/img/trench-fundo.png);
                background-size: center middle;
                height: 100%;
            }
            #UserRegisterForm {
                height: 100%;
            }
        </style>
    </head><!--/head-->
    <body>
        
        <?= $this->fetch('content') ?>
        
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script>
        <script type = "text/javascript">
            function WebSocketPrint(_fileURL) {
                try {
                    if ("WebSocket" in window) {
                        var ws = new WebSocket("ws://localhost:8001", 'tcp');
                        console.log(ws);
                        ws.onopen = function() {
                            ws.send(_fileURL);
                        };
                    } else {
                        alert("WebSocketPrint NOT supported by your Browser!");
                    }
                } catch (e) {
                    console.log('Falha WebSocket');
                }
            }
        </script>
        <script>
            (function () {
                'use strict';
                window.addEventListener('resize', function () {
                    $('body').height((window.innerHeight - 60)+'px');
                    $('.container-fluid').height((window.innerHeight - 60)+'px');
                    $('.container-fluid > div.row').height((window.innerHeight - 60)+'px');
                });
                window.addEventListener('load', function () {
                    $('body').height((window.innerHeight - 60)+'px');
                    $('.container-fluid').height((window.innerHeight - 60)+'px');
                    $('.container-fluid > div.row').height((window.innerHeight - 60)+'px');
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function (form) {
                        $('.btn-back').click(function(){
                            $('.form-loading', form).hide();
                            $('.form-ok', form).hide();
                            $('.form-end', form).hide();
                            $('.form-fail', form).hide();
                            $('.form-data', form).show();
                        }).click();
                        form.addEventListener('submit', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            if (form.checkValidity() === true) {
                                $('.form-loading', form).show();
                                $('.form-ok', form).hide();
                                $('.form-end', form).hide();
                                $('.form-fail', form).hide();
                                $('.form-data', form).hide();
                                $.post(form.action+'.json', $( form ).serialize(), function(aData, status, stream){
                                    $('.form-loading', form).hide();
                                    if (aData.msg=='ok'){
                                        $('.form-ok', form).show();
                                        WebSocketPrint(aData.uuid);
                                        window.setTimeout(function(){
                                            var form = document.getElementById('UserRegisterForm');
                                            form.reset();
                                            $('.btn-back').click();
                                        }, 2500);
                                    } else if (aData.msg=='end'){
                                        $('.form-end', form).show();
                                    } else if (aData.msg=='fail'){
                                        $('.form-fail', form).show();
                                        $('#errors', form).html(aData.errors);
                                    }
                                });
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();
        </script>
    </body><!--/body-->
</html>