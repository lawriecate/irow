<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?= $title ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css">
<script src="<?php echo base_url(); ?>assets/js/vendor/jquery-1.10.1.min.js"></script>
        <script src="assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?= base_url() ?>">iRow</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown active">
              <a href="<?= base_url() ?>dashboard" class="dropdown-toggle" data-toggle="dropdown">Me <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?= base_url() ?>dashboard">Dashboard</a></li>
                <li><a href="<?= base_url() ?>diary">Diary</a></li>
                <li><a href="<?= base_url() ?>logbook">Logbook</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">My Account</li>
                <li><a href="<?= base_url() ?>profile/settings">Settings</a></li>
              </ul>
            </li>
            <li><a href="<?= base_url() ?>coach">Coach</a></li>
            <li class="dropdown">
              <a href="<?= base_url() ?>club" class="dropdown-toggle" data-toggle="dropdown">Club <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
            <? if($this->l_auth->is_admin_logged_in()) { ?>
            <li class="dropdown">
              <a href="<?= base_url() ?>admin" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?=base_url()?>admin/userlist">Manage Users</a></li>
                <li><a href="<?=base_url()?>admin/clublist">Manage Clubs</a></li>
              </ul>
            </li>
            <? } ?>
            
             
              <form action="<?=base_url()?>search" type="GET" class="navbar-form navbar-right" role="search" style="width: 200px;">
                 <div class="form-group">
              <div class="input-group">
                <input name="q" placeholder="Search" type="text" class="form-control">
                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                </div>
              </div>
              </div>
            </form>
          </ul>
          <? /* <form action="<?=base_url()?>login/" class="navbar-form navbar-right">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form> */ ?>
        </div><!--/.navbar-collapse -->
      </div>
    </div>