<?php 

include("template.php");

$tpl = new template();

$arrayLoop = array(
    array("testKey", "1"),
    array("testKey", "2"),
    array("testKey", "3"),
    array("testKey", "4")
)

// Now we set our template file
$tpl->setTemplate("index.html"); 
  $tpl->setText("welcome", "Welcome back!");
  $tpl->setIf("incorrect", 1 === 1); // If 1 is equal to 1 we will enable the text in setif
  $tpl->setLoop("testloop", $arrayLoop);
echo $tpl->render();    
