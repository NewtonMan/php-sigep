<?
$miniCor = (isset($this->request->data['EmpresaStyle']['cor1']) ? $this->request->data['EmpresaStyle']['cor1'] : '#428BCA');
$miniCorDark = adjustBrightness($miniCor, -30);
?>
.btn-primary {
  background-image: linear-gradient(to bottom, <?=$miniCor?> 0px, <?=$miniCorDark?> 100%);
  background-repeat: repeat-x;
}

.pagination > li > a, .pagination > li > span {
  background-color: #FFFFFF;
  border: 1px solid #DDDDDD;
  color: <?=$miniCor?>;
  float: left;
  line-height: 1.42857;
  margin-left: -1px;
  padding: 6px 12px;
  position: relative;
  text-decoration: none;
}

.panel-primary > .panel-heading {
  background-image: linear-gradient(to bottom, <?=$miniCor?> 0px, <?=$miniCorDark?> 100%);
  background-repeat: repeat-x;
}

a:hover, a:focus {
    color: <?=$miniCorDark?>;
}

.form-error {
    background-color: #F2DEDE;
    color: #A94442;
}

.pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
  background-color: <?=$miniCor?>;
  border-color: <?=$miniCorDark?>;
  color: #FFFFFF;
  cursor: default;
  z-index: 2;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary {
  background-color: <?=$miniCor?>;
  border-color: <?=$miniCorDark?>;
  color: #FFFFFF;
}

.panel-heading .btn-sm {
    margin-top: -7px;
}

.panel-heading .btn-xs {
    margin-top: -5px;
}

.item-fatura {
    display: none;
}

.panel-primary {
border-color: <?= $miniCorDark ?>;
}

.btn-primary, .panel-primary .panel-heading, .dropdown-menu li.active a, .dropdown-menu li:hover a, .dropdown-menu li:focus a {
background-color: <?= $miniCor ?>;
border-color: <?= $miniCorDark ?>;
}

.btn-primary:hover,.btn-primary:focus,.btn-primary:active {
background-color: <?= $miniCorDark ?>;
}

a {
color: <?= $miniCor ?>;
}

.nav-pills li.active a, .nav-pills li.active a:hover, .nav-pills li.active a:focus {
background-color: <?= $miniCor ?>;
}

.nav-pills li a:hover, a.list-group-item.active .badge, .nav-pills .active a .badge {
color: <?= $miniCorDark ?>;
}

.badge {
background-color: <?= $miniCor ?>;
}

.search-form {
display: none;
}

form {
clear: both;
margin-right: 0;
padding: 0;
width: 100%;
}
fieldset {
border: none;
margin-bottom: 1em;
padding: 16px 10px;
}
fieldset legend {
font-size: 160%;
font-weight: bold;
}
fieldset fieldset {
margin-top: 0;
padding: 10px 0 0;
}
fieldset fieldset legend {
font-size: 120%;
font-weight: normal;
}
fieldset fieldset div {
clear: left;
margin: 0 20px;
}
form div {
vertical-align: text-top;
}
form .required {
font-weight: bold;
}
form .required label:after {
color: #e32;
content: '*';
display:inline;
}
label {
display: block;
/*	font-size: 110%;*/
margin-bottom:1px;
}
input, textarea {
/*	font-size: 120%;*/
clear: both;
}
input[type=text], input[type=password], textarea {
width: 98%;
}
select {
clear: both;
/*	font-size: 120%;*/
vertical-align: text-bottom;
}
select[multiple=multiple] {
width: 100%;
}
option {
font-size: 120%;
padding: 0 3px;
}
input[type=checkbox] {
clear: left;
float: left;
margin: 0px 6px 7px 2px;
width: auto;
}
div.checkbox label {
display: inline;
}
input[type=radio] {
float:left;
width:auto;
margin: 6px 0;
padding: 0;
line-height: 26px;
}
.radio label {
margin: 0 0 6px 20px;
line-height: 26px;
}

form .error {
background: #FFDACC;
-moz-border-radius: 4px;
-webkit-border-radius: 4px;
border-radius: 4px;
font-weight: normal;
}
form .error-message {
-moz-border-radius: none;
-webkit-border-radius: none;
border-radius: none;
border: none;
background: none;
margin: 0;
padding-left: 4px;
padding-right: 0;
}
form .error,
form .error-message {
color: #9E2424;
-webkit-box-shadow: none;
-moz-box-shadow: none;
-ms-box-shadow: none;
-o-box-shadow: none;
box-shadow: none;
text-shadow: none;
}

form .required {
font-weight: bold;
}
form .required label:after {
color: #e32;
content: '*';
display:inline;
}

/** Notices and Errors **/
.message {
clear: both;
color: #fff;
font-size: 140%;
font-weight: bold;
margin: 0 0 1em 0;
padding: 5px;
}

.success,
.message,
.cake-error,
.cake-debug,
.notice,
p.error,
.error-message {
background: #ffcc00;
background-repeat: repeat-x;
background-image: -moz-linear-gradient(top, #ffcc00, #E6B800);
background-image: -ms-linear-gradient(top, #ffcc00, #E6B800);
background-image: -webkit-gradient(linear, left top, left bottom, from(#ffcc00), to(#E6B800));
background-image: -webkit-linear-gradient(top, #ffcc00, #E6B800);
background-image: -o-linear-gradient(top, #ffcc00, #E6B800);
background-image: linear-gradient(top, #ffcc00, #E6B800);
text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
border: 1px solid rgba(0, 0, 0, 0.2);
margin-bottom: 18px;
padding: 7px 14px;
color: #404040;
text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
}
.success,
.message,
.cake-error,
p.error,
.error-message {
clear: both;
color: #fff;
background: #c43c35;
border: 1px solid rgba(0, 0, 0, 0.5);
background-repeat: repeat-x;
background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
background-image: -ms-linear-gradient(top, #ee5f5b, #c43c35);
background-image: -webkit-gradient(linear, left top, left bottom, from(#ee5f5b), to(#c43c35));
background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
background-image: linear-gradient(top, #ee5f5b, #c43c35);
text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
}
.success {
clear: both;
color: #fff;
border: 1px solid rgba(0, 0, 0, 0.5);
background: #3B8230;
background-repeat: repeat-x;
background-image: -webkit-gradient(linear, left top, left bottom, from(#76BF6B), to(#3B8230));
background-image: -webkit-linear-gradient(top, #76BF6B, #3B8230);
background-image: -moz-linear-gradient(top, #76BF6B, #3B8230);
background-image: -ms-linear-gradient(top, #76BF6B, #3B8230);
background-image: -o-linear-gradient(top, #76BF6B, #3B8230);
background-image: linear-gradient(top, #76BF6B, #3B8230);
text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
}
p.error {
font-family: Monaco, Consolas, Courier, monospace;
font-size: 120%;
padding: 0.8em;
margin: 1em 0;
}
p.error em {
font-weight: normal;
line-height: 140%;
}
.notice {
color: #000;
display: block;
font-size: 120%;
padding: 0.8em;
margin: 1em 0;
}
.success {
color: #fff;
}

/*!
* Datepicker for Bootstrap
*
* Copyright 2012 Stefan Petre
* Licensed under the Apache License v2.0
* http://www.apache.org/licenses/LICENSE-2.0
*
*/
.datepicker {
top: 0;
left: 0;
padding: 4px;
margin-top: 1px;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
/*.dow {
border-top: 1px solid #ddd !important;
}*/

}
.datepicker:before {
content: '';
display: inline-block;
border-left: 7px solid transparent;
border-right: 7px solid transparent;
border-bottom: 7px solid #ccc;
border-bottom-color: rgba(0, 0, 0, 0.2);
position: absolute;
top: -7px;
left: 6px;
}
.datepicker:after {
content: '';
display: inline-block;
border-left: 6px solid transparent;
border-right: 6px solid transparent;
border-bottom: 6px solid #ffffff;
position: absolute;
top: -6px;
left: 7px;
}
.datepicker > div {
display: none;
}
.datepicker table {
width: 100%;
margin: 0;
}
.datepicker td,
.datepicker th {
text-align: center;
width: 20px;
height: 20px;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
}
.datepicker td.day:hover {
background: #eeeeee;
cursor: pointer;
}
.datepicker td.day.disabled {
color: #eeeeee;
}
.datepicker td.old,
.datepicker td.new {
color: #999999;
}
.datepicker td.active,
.datepicker td.active:hover {
color: #ffffff;
background-color: #006dcc;
background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
background-image: -o-linear-gradient(top, #0088cc, #0044cc);
background-image: linear-gradient(to bottom, #0088cc, #0044cc);
background-repeat: repeat-x;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);
border-color: #0044cc #0044cc #002a80;
border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
*background-color: #0044cc;
/* Darken IE7 buttons by default so they stand out more given they won't have borders */

filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
color: #fff;
text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.datepicker td.active:hover,
.datepicker td.active:hover:hover,
.datepicker td.active:focus,
.datepicker td.active:hover:focus,
.datepicker td.active:active,
.datepicker td.active:hover:active,
.datepicker td.active.active,
.datepicker td.active:hover.active,
.datepicker td.active.disabled,
.datepicker td.active:hover.disabled,
.datepicker td.active[disabled],
.datepicker td.active:hover[disabled] {
color: #ffffff;
background-color: #0044cc;
*background-color: #003bb3;
}
.datepicker td.active:active,
.datepicker td.active:hover:active,
.datepicker td.active.active,
.datepicker td.active:hover.active {
background-color: #003399 \9;
}
.datepicker td span {
display: block;
width: 47px;
height: 54px;
line-height: 54px;
float: left;
margin: 2px;
cursor: pointer;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
}
.datepicker td span:hover {
background: #eeeeee;
}
.datepicker td span.active {
color: #ffffff;
background-color: #006dcc;
background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
background-image: -o-linear-gradient(top, #0088cc, #0044cc);
background-image: linear-gradient(to bottom, #0088cc, #0044cc);
background-repeat: repeat-x;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);
border-color: #0044cc #0044cc #002a80;
border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
*background-color: #0044cc;
/* Darken IE7 buttons by default so they stand out more given they won't have borders */

filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
color: #fff;
text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.datepicker td span.active:hover,
.datepicker td span.active:focus,
.datepicker td span.active:active,
.datepicker td span.active.active,
.datepicker td span.active.disabled,
.datepicker td span.active[disabled] {
color: #ffffff;
background-color: #0044cc;
*background-color: #003bb3;
}
.datepicker td span.active:active,
.datepicker td span.active.active {
background-color: #003399 \9;
}
.datepicker td span.old {
color: #999999;
}
.datepicker th.switch {
width: 145px;
}
.datepicker th.next,
.datepicker th.prev {
font-size: 21px;
}
.datepicker thead tr:first-child th {
cursor: pointer;
}
.datepicker thead tr:first-child th:hover {
background: #eeeeee;
}
.input-append.date .add-on i,
.input-prepend.date .add-on i {
display: block;
cursor: pointer;
width: 16px;
height: 16px;
}

.armazem-area {
position: relative;
padding: 0 0 0 0;
margin: 0 0 0 0;
}

.objeto-mapa {
overflow: visible;
float: left;
padding: 0 0 0 0;
margin: 0 0 0 0;
}

.objeto-mapa-elevado {
display: none;
}

.objeto-mapa-span {
float: left;
padding: 5px 5px 5px 5px;
margin: -32px 0 0 0;
font-size: 14px;
color: #FFF;
background-color: #000;
}

.etapa-transporte {
width: 100%;
border: dotted 1px #000;
}

.master-topo {
clear: both;
width: 100%;
margin: 0 0 0 0;
padding: 0 0 0 0;
}

.armazem-area {
background-color: #ccc;
padding: 10px 10px 10px 10px;
}

.armazem-area-corredor {
width: 40px;
float: left;
position: relative;
border: none;
margin-right: 30px;
margin-left: 30px;
background-color: #fff;
-moz-box-sizing:    border-box;
-webkit-box-sizing: border-box;
box-sizing:        border-box;
}

.armazem-area-corredor-prateleira, .andar {
width: 40px;
height: 40px;
line-height: 34px;
-moz-box-sizing:    border-box;
-webkit-box-sizing: border-box;
box-sizing:        border-box;
position: relative;
text-align: center;
}

.armazem-area-corredor-legenda {
line-height: 40px;
width: 40px;
text-align: center;
height: 40px;
}

.andar {
background-color: green;
float: left;
position: absolute;
}

.andares {
margin-top: -37px;
}

.andares, .prateleiras {
display: none;
}

.itemVolume {
background-color: #D4AC61;
float: left;
width: 500px;
height: 320px;
border: dotted 1px #000;
margin: 5px 5px 5px 5px;
padding: 0;
clear: none;
}

.itemVolume div {
clear: none;
padding: 0;
}

.itemVolume h4 {
text-align: center;
background-color: #8C6316;
color: #FFF;
line-height: 35px;
margin: 0 0 0 0;
}

.itemVolume div.itemVolumeProdutos {
background-color: #D4AC61;
width: 498px;
height: 190px;
overflow: auto;
margin: 0 0 0 0;
}

.produto-clientes {
max-height: 200px;
border: dotted 1px #DDD;
overflow: auto;
}

.item-revisao {
float: left;
border: dotted 1px #000;
background-color: #DDD;
width: 400px;
height: 300px;
padding: 5px 5px 5px 5px;
overflow: auto;
}

.fluxo-produtos {
display: none;
}

.lista-fluxo-entrada {
padding: 0 0 0 0;
}

.lista-fluxo-entrada li {
background-image: url(/img/bg-fluxo-entrada.png);
line-height: 30px;
list-style: none;
}

.lista-fluxo-entrada li ul {
padding: 0 0 0 0;
margin: 0 0 0 0;
}

.lista-fluxo-entrada li ul li {
background-image: none;
background-color: #fff;
line-height: 30px;
list-style: none;
}

.produto-div-entrada {
clear: none;
border: dotted 1px #ccc;
width: 400px;
height: 200px;
float: left;
position: relative;
margin: 5px 5px 5px 5px;
}

.produto-div-foto {
float: left;
clear: none;
width: 150px;
height: 150px;
border: solid 1px #ccc;
background-position: center center;
background-color: #fff;
background-repeat: no-repeat;
overflow: hidden;
}

.produto-div-data {
float: left;
clear: none;
width: 220px;
height: 150px;
background-color: #fff;
font-size: 11px;
line-height: 20px;
margin-left: 10px;
}

.pagination {
margin: 0 0 0 0;
}

.navbar-nav.navbar-right:last-child {
margin-right: 0;
}

.align-left {
text-align: left;
}

.align-center {
text-align: center;
}

.align-right {
text-align: right;
}

/*STEPBAR*/
.stepbar {
margin-left: -40px;
margin-top: 10px;
margin-bottom: 10px;
overflow: hidden;
/*CSS counters to number the steps*/
counter-reset: step;
}
.stepbar li {
list-style-type: none;
color: #000;
text-transform: uppercase;
float: left;
text-align: center;
position: relative;
}
.stepbar li:before {
content: counter(step);
counter-increment: step;
width: 50px;
line-height: 50px;
display: block;
font-size: 25px;
color: white;
background: gray;
border-radius: 50%;
margin: 0 auto 5px auto;
text-shadow: 2px 2px graytext;
}
/*progressbar connectors*/
.stepbar li:after {
content: '';
width: 100%;
height: 10px;
background: gray;
position: absolute;
left: -50%;
top: 20px;
color: white;
z-index: -1; /*put it behind the numbers*/
text-shadow: 2px 2px graytext;
}
.stepbar li:first-child:after {
/*connector not needed before the first step*/
content: none; 
}
/*marking active/completed steps green*/
/*The number of the step and the connector before it = green*/
.stepbar li.active:before,  .stepbar li.active:after{
background: #27AE60;
color: white;
}

.bubble
{
position: relative;
width: 100%;
min-height: 30px;
padding: 5px;
background: #E2E2E2;
-webkit-border-radius: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
border: #CCCCCC solid 2px;
}

.bubble:after
{
content: '';
position: absolute;
border-style: solid;
border-width: 7px 20px 7px 0;
border-color: transparent #E2E2E2;
display: block;
width: 0;
z-index: 1;
margin-top: 0;
left: -20px;
top: 29%;
}

.bubble:before
{
content: '';
position: absolute;
border-style: solid;
border-width: 8px 21px 8px 0;
border-color: transparent #CCCCCC;
display: block;
width: 0;
z-index: 0;
margin-top: -1px;
left: -23px;
top: 29%;
}

.pin {
  text-align: center;
  font-size: 110px;
  width: 150px;
  height: 150px;
  line-height: 150px;
  margin: 0 auto;
  background-color: <?=$miniCor?>;
  text-shadow: 4px 4px 0 <?=$miniCorDark?>;
  color: #FFF;
  border-radius: 50%;
}

.estoque-produto-foto {
    width: 150px;
    height: 150px;
    background-repeat: no-repeat;
    background-position: center center;
    border: dotted 1px #DDD;
}

.fieldset-cte {
    padding: 0;
    margin: 0;
}

.fieldset-cte legend {
    font-size: 100%;
    padding: 0;
    margin: 0;
    margin-bottom: 3px;
}

.fieldset-cte div {
    padding: 0;
}

.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus {
  background-color: <?=$miniCor?>;
  background-image: linear-gradient(to bottom, <?=$miniCor?> 0px, <?=$miniCorDark?> 100%);
  background-repeat: repeat-x;
}

h1, .h1, h2, .h2, h3, .h3 {
  margin-bottom: 0;
  margin-top: 5px;
}

.nav-condensed > li > a {
  display: block;
  padding: 5px 10px;
  position: relative;
}

.nav-tabs > li > a {
  border-radius: 8px 8px 0 0;
}

.nav-tabs > li.active a, .nav-tabs > li.active a:hover, .nav-tabs > li.active a:active {
    background-color: #e9e9e9;
}

.table-text-responsive tbody tr td p {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-text-responsive tbody tr th a {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.no-margin {
    margin: 0;
}

form div.panel table tbody td div.col-xs-1, form div.panel table tbody td div.col-sm-1, form div.panel table tbody td div.col-md-1, form div.panel table tbody td div.col-lg-1, form div.panel table tbody td div.col-xs-2, form div.panel table tbody td div.col-sm-2, form div.panel table tbody td div.col-md-2, form div.panel table tbody td div.col-lg-2, form div.panel table tbody td div.col-xs-3, form div.panel table tbody td div.col-sm-3, form div.panel table tbody td div.col-md-3, form div.panel table tbody td div.col-lg-3, form div.panel table tbody td div.col-xs-4, form div.panel table tbody td div.col-sm-4, form div.panel table tbody td div.col-md-4, form div.panel table tbody td div.col-lg-4, form div.panel table tbody td div.col-xs-5, form div.panel table tbody td div.col-sm-5, form div.panel table tbody td div.col-md-5, form div.panel table tbody td div.col-lg-5, form div.panel table tbody td div.col-xs-6, form div.panel table tbody td div.col-sm-6, form div.panel table tbody td div.col-md-6, form div.panel table tbody td div.col-lg-6, form div.panel table tbody td div.col-xs-7, form div.panel table tbody td div.col-sm-7, form div.panel table tbody td div.col-md-7, form div.panel table tbody td div.col-lg-7, form div.panel table tbody td div.col-xs-8, form div.panel table tbody td div.col-sm-8, form div.panel table tbody td div.col-md-8, form div.panel table tbody td div.col-lg-8, form div.panel table tbody td div.col-xs-9, form div.panel table tbody td div.col-sm-9, form div.panel table tbody td div.col-md-9, form div.panel table tbody td div.col-lg-9, form div.panel table tbody td div.col-xs-10, form div.panel table tbody td div.col-sm-10, form div.panel table tbody td div.col-md-10, form div.panel table tbody td div.col-lg-10, form div.panel table tbody td div.col-xs-11, form div.panel table tbody td div.col-sm-11, form div.panel table tbody td div.col-md-11, form div.panel table tbody td div.col-lg-11, form div.panel table tbody td div.col-xs-12, form div.panel table tbody td div.col-sm-12, form div.panel table tbody td div.col-md-12, form div.panel table tbody td div.col-lg-12 {
  min-height: 1px;
  padding-left: 0;
  padding-right: 0;
  position: relative;
}
