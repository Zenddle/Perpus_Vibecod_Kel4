<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Masuk') ?></title>
  <style>
    body{font-family:system-ui,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0;background:#f5f6f8}
    .box{background:#fff;padding:28px;border-radius:8px;width:320px;box-shadow:0 1px 4px rgba(0,0,0,.12)}
    input,button{width:100%;padding:10px;margin-top:10px;box-sizing:border-box;font:inherit}
    button{background:#1f3a5f;color:#fff;border:0;border-radius:4px;cursor:pointer}
    .ok{color:#1b7f3b}.err{color:#b42318}
  </style>
</head>
<body>
  <div class="box"><?= $content ?></div>
</body>
</html>
