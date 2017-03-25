<?php

echo "ok";
echo `whoami`;

$output = `sudo /var/tmp/tpb/sync.sh`;
echo "<pre>$output</pre>";
?>
