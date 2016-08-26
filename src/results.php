<?php if (strstr($_GET['file'], '..')) exit; ?>
<h1 style='font-family: arial'>
<?php echo date('D, d M Y H:i:s', array_pop(split('/', $_GET['file']))); ?>
&nbsp;
(<?php echo array_pop(split('/', $_GET['file'])); ?>)
</h1>
<pre>
<?php
echo file_get_contents('./results/'.$_GET['file']);
?>
</pre>
