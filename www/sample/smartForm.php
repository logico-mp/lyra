<?php include_once 'config.php'; ?>
<?php include_once 'formToken.php'; ?>
<!DOCTYPE html>
<html>
   <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <!-- STEP :
         1 : load the JS librairy 
         2 : required public key with file config.php
         3 : the JS parameters langage EN and url sucess -->
      <script type="text/javascript"
         src="https://static.payzen.eu/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js" 
         kr-public-key="<?php echo PUBLIC_KEY; ?>";
         kr-post-url-success="paid.php";
         kr-language="en-EN"></script>
      <!-- 4 : theme neon should be loaded in the HEAD section   -->
      <link rel="stylesheet" href="https://static.payzen.eu/static/js/krypton-client/V4.0/ext/neon-reset.css">
      <script type="text/javascript" src="https://static.payzen.eu/static/js/krypton-client/V4.0/ext/neon.js"></script>
   </head>
   <body>
      <div class="kr-smart-form" kr-card-form-expanded kr-form-token="<?php echo $formToken; ?>" >
      </div>
   </body>
</html>