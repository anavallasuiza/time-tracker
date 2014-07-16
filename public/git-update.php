<?php
echo '<pre><code>';
echo shell_exec('cd ../; git pull -u origin master;');
echo '</code></pre>';
