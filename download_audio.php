<?php

$ctx = stream_context_create(['http' => ['header' => 'User-Agent: Mozilla/5.0']]);
file_put_contents('public/audio/lobby.ogg', file_get_contents('https://upload.wikimedia.org/wikipedia/commons/5/5a/Sneaky_Snitch.ogg', false, $ctx));
file_put_contents('public/audio/game.ogg', file_get_contents('https://upload.wikimedia.org/wikipedia/commons/e/eb/The_Builder.ogg', false, $ctx));
echo "Audio downloaded.\n";
