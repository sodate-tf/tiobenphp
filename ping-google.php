<?php

$sitemap = "https://www.iatioben.com.br/sitemap.xml";

$url = "https://www.google.com/ping?sitemap=" . urlencode($sitemap);

file_get_contents($url);

echo "Ping enviado.";