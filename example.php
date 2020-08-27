<?php 

include("template.php");

$tpl = new template();

// Now we set our template file
$tpl->setTemplate("index.html"); 
  $tpl->setIf("incorrect", 1 === 1); // If 1 is equal to 1 we will enable the text in setif
  $tpl->setText("welcome", "Welcome back!");
echo $tpl->render();    
