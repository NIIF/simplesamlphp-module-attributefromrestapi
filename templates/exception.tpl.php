<?php
$this->data['header'] = 'Error in attribute aggregation';

$this->includeAtTemplateBase('includes/header.php');
?>
<h1>We have a problem.</h1>

We catch an exception during fetching attributes from the external attribute source. The exception is:
<pre>

<?php
    echo $this->data['e'];
?>
</pre>

<?php
$this->includeAtTemplateBase('includes/footer.php');