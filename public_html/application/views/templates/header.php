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
         <script src="<?php echo base_url(); ?>assets/js/vendor/bootstrap.min.js"></script>
        
        <script src="<?php echo base_url(); ?>assets/js/plugins.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
        
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
          <a class="navbar-brand" href="<?= base_url() ?>">iRow <small>Test Service</small></a>
        </div>
        <div class="navbar-collapse collapse">
          <? if($this->l_auth->logged_in() ) { 
            $controller =  $this->router->class;  
            ?> 
          <ul class="nav navbar-nav">
            <li class="dropdown <?if(in_array($controller,array('me','dashboard','diary','profile','logbook'))){echo' active';}?>">
              <a href="<?= base_url() ?>dashboard" class="dropdown-toggle" data-toggle="dropdown">Me <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?= base_url() ?>dashboard"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a></li>
                <li><a href="<?= base_url() ?>diary"><span class="glyphicon glyphicon-calendar"></span> Diary</a></li>
                <li><a href="<?= base_url() ?>logbook"><span class="glyphicon glyphicon-list"></span> Logbook</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Your Account</li>
                <li><a href="<?= base_url() ?>profile/settings"><span class="glyphicon glyphicon-wrench"></span> Settings</a></li>
              </ul>
            </li>


             <? if($this->user_model->is_coach($this->l_auth->current_user_id())) { ?>
            <li class="dropdown <?if($controller=='coach'){echo' active';}?>"><a href="<?= base_url() ?>coach" class="dropdown-toggle" data-toggle="dropdown">Coach <b class="caret"></b></a>
             
              <ul class="dropdown-menu">
                <li><a href="<?= base_url() ?>coach/log"><span class="glyphicon glyphicon-edit"></span> Log Activity</a></li>
                <li><a href="<?= base_url() ?>coach/analyse"><span class="glyphicon glyphicon-stats"></span> Analytics Tool</a></li>
                <? /*<li><a href="<?= base_url() ?>nya"><span class="glyphicon glyphicon-calendar"></span> Diary</a></li>*/?>
                <li><a href="<?= base_url() ?>coach/logbook"><span class="glyphicon glyphicon-list"></span> Logbook</a></li>
               
                
              </ul>
              
            </li>
            <? } else { ?>
             <li><a href="<?= base_url() ?>coach">Coach</a></li>
             
            <? } ?>



            <li class="dropdown <?if($controller=='club'){echo' active';}?>">
              <a href="<?= base_url() ?>club" class="dropdown-toggle" data-toggle="dropdown">Club <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <? foreach($this->user_model->memberships($this->l_auth->current_user_id()) as $club): ?>
                  <li class="dropdown-header"><?= $club['name'] ?></li>
                  <li><a href="<?=base_url()?>club/profile/<?=$club['ref']?>"><span class="glyphicon glyphicon-flag"></span> Group Page</a></li>
                  <? if($club['level'] == 'manager') { ?>
                  <li><a href="<?=base_url()?>club/manage/<?=$club['ref']?>"><span class="glyphicon glyphicon-wrench"></span> Manage</a></li>
                  <? } ?>
                  
                <? endforeach; ?>
               
                <? /* <li class="divider"></li>
                <li class="dropdown-header">Your Clubs</li>
                <li><a href="<?=base_url()?>club/"><span class="glyphicon glyphicon-plus"></span> Add Club</a></li>*/?>
              </ul>
            </li>
            <? if($this->l_auth->is_admin_logged_in()) { ?>
            <li class="dropdown <?if($controller=='admin'){echo' active';}?>">
              <a href="<?= base_url() ?>admin" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?=base_url()?>admin/userlist"><span class="glyphicon glyphicon-user"></span> Manage Users</a></li>
                <li><a href="<?=base_url()?>admin/clublist"><span class="glyphicon glyphicon-flag"></span> Manage Clubs</a></li>
              </ul>
            </li>
            <? } ?>
            
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